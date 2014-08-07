[![Build Status](https://travis-ci.org/sakren/nette-menu.png?branch=master)](https://travis-ci.org/sakren/nette-menu)
[![Donate](http://b.repl.ca/v1/donate-PayPal-brightgreen.png)](https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=HPH6AC9D5LGHY)

# Nette menu

Nette component for creating menus and breadcrumbs.

## Installation

```
$ composer require sakren/nette-menu
$ composer update
```

Now you need to register this as Nette extension in your config.neon file.

```
extensions:
	menu: DK\Menu\DI\Extension

includes:
	- ./menu.neon
```

Than you can create new `menu` section for example in menu.neon.

```
menu:

	default:

		items:
			Homepage: Home:default

			Books:
				target: Book:default
				items:
					By author: Book:byAuthor
```

This will create this menu:

* Homepage         (Home:default)
* Books            (Book:default)
	+ By author    (Book:byAuthor)

Of course you can create more nested menu.

Also you can see that there are more ways how to write link. If you have got single link without children, you can use
this.

```
Title of link: Presenter:action
```

or links with children

```
Title of link:
	target: Presenter:action
	items: []
```

there is actually also third way, but it is useful just for specific cases and is written about below.

## Render menu and breadcrumb

```
class BasePresenter extends Nette\Application\UI\Presenter
{


	/** @var \DK\Menu\UI\IControlFactory @inject */
	public $menuFactory;


	/**
	 * @return \DK\Menu\UI\Control
	 */
	protected function createComponentMenu()
	{
		return $this->menuFactory->create();
	}

}
```

@layout.latte:

```
{control menu:breadcrumb}

{control menu}
```

Generating sitemap.xml 

Router
```
$router[] = new Route('sitemap.xml', 'Homepage:sitemap');
```

Homepage/sitemap.latte
```
{extends none}
{control menu:sitemapXml}
```



## Authorization

You can hide some links for example for users without specific role, guest users, actions in other module or when
needed presenter parameter is missing.

```
menu:

	default:

		items:
			Settings:
				target: Settings:default
				allow:
					loggedIn: true
				items:
					Users:
						target: User:default
						allow:
							roles: [admin]
					Books:
						target: Book:default
						allow:
							module: admin
					Images:
						target: Image:default
						allow:
							parameters:
								[allowed: maybe]
					Authors:
						target: Authors:default
						allow:
							acl:
								resource: authors
								permission: view #optional - 'view' is default permission
```

or whole menu can be allowed for example just logged users:

```
menu:

	default:

		allow:
			loggedIn: true

		items: []
```

## Hidden links

Imagine that you have for example link to books settings and page for adding book which you do not wish to add to your
menu but at the same time you want base books settings link to be highlighted.

```
menu:

	default:

		Books settings:
			target: Book:default
			items:
				add:
					target: Book:add
					visual: false
```

**This `visual` option does not affects breadcrumb component.**

if you want to highlight "Books settings" link for all actions in BookPresenter, you can include all targets with
regexp.

```
menu:

	default:

		Books settings:
			target: Book:default
			include: '^Book\:[a-zA-Z]+$'
```

or with array of included targets.

```
menu:

	default:

		Books settings:
			target: Book:default
			include:
				- Book:add
				- Book:edit
				- Front:Book:detail			# in module
				- <module>:Book:delete		# "dynamic" module
```

## Custom templates

```
menu:

	default:

		template:
			menu: %appDir%/templates/components/menu/menu.latte
			breadcrumb: %appDir%/templates/components/menu/breadcrumb.latte

		items: []
```

## Translated titles

```
menu:

	default:

		translator: true
```

## More menus

Lets say that we want base menu and menu just for users in `admin` role.

```
menu:

	default:
		items: []

	admin:
		allow:
			roles: [admin]
		items: []
```

Problem with this is that it will create more services with same class, so Nette autowiring will be disabled for all
menu services.

Solution is to create custom control and factory classes.

```
namespace App\Components\AdminMenu;

use DK;

class MenuControl extends DK\Menu\UI\Control {}

interface IMenuControlFactory
{


	/**
	 * @return \App\Components\AdminMenu\MenuControl
	 */
	public function create();

}
```

now we have to update our menu configuration

```
menu:

	admin:
		controlClass: App\Components\AdminMenu\MenuControl
		controlInterface: App\Components\AdminMenu\IMenuControlFactory
```

next step is to inject your custom control factory into BasePresenter.

## Dynamic links

We already have got links to adding books, but now we want links in breadcrumb with editing books just like in example
below:

Books / Harry Potter / Edit

```
menu:

	default:

		items:
			books:
				title: Books
				target: Book:default
```

There you can see the third way of writing links. Title is now in own option because we will need to access `books`
link for adding another links into it.

First, we will add just one small helper method to our BasePresenter.

```
class BasePresenter extends Nette\Application\UI\Presenter
{


	/** @var \DK\Menu\UI\IControlFactory @inject */
	public $menuFactory;


	/**
	 * @return \DK\Menu\UI\Control
	 */
	protected function createComponentMenu()
	{
		return $this->menuFactory->create();
	}


	/**
	 * Basically just helper for IDE because of @return annotation
	 *
	 * @return \DK\Menu\Menu
	 */
	protected function getMenu()
	{
		return $this['menu']->getMenu();
	}

}
```

BookPresenter:

```
class BookPresenter extends BasePresenter
{

	public function actionEdit($id)
	{
		$book = getBookEntitySomehow($id);

		$this->getMenu()->getItem('books')
			->addItem($book->title, 'Book:detail', array('id' => $id))->setVisual(false)	// do not show this link in menu
			->addItem('Edit', 'Book:edit', array('id' => $id));
	}

}
```

## Changelog

* 1.0.4
	+ Added `hasIcon` and `hasCounter` methods
	+ Support for "absolute" targets [#6](https://github.com/sakren/nette-menu/issues/6)

* 1.0.3
	+ Added shortcut methods for working with item's data [#5](https://github.com/sakren/nette-menu/issues/5)

* 1.0.2
	+ Support for "dynamic" modules in includes option [#4](https://github.com/sakren/nette-menu/issues/4)

* 1.0.1
	+ Include option can be array of targets [#1](https://github.com/sakren/nette-menu/issues/1)
	+ Added support for translatable titles [#2](https://github.com/sakren/nette-menu/issues/2)

* 1.0.0
	+ First version