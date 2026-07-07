![](https://heatbadger.now.sh/github/readme/contributte/menu-control/)

<p align=center>
  <a href="https://github.com/contributte/menu-control/actions"><img src="https://badgen.net/github/checks/contributte/menu-control/master"></a>
  <a href="https://codecov.io/gh/contributte/menu-control"><img src="https://badgen.net/codecov/c/github/contributte/menu-control"></a>
  <a href="https://packagist.org/packages/contributte/menu-control"><img src="https://badgen.net/packagist/dm/contributte/menu-control"></a>
  <a href="https://packagist.org/packages/contributte/menu-control"><img src="https://badgen.net/packagist/v/contributte/menu-control"></a>
</p>
<p align=center>
  <a href="https://packagist.org/packages/contributte/menu-control"><img src="https://badgen.net/packagist/php/contributte/menu-control"></a>
  <a href="https://github.com/contributte/menu-control"><img src="https://badgen.net/github/license/contributte/menu-control"></a>
  <a href="https://bit.ly/ctteg"><img src="https://badgen.net/badge/support/gitter/cyan"></a>
  <a href="https://bit.ly/cttfo"><img src="https://badgen.net/badge/support/forum/yellow"></a>
  <a href="https://contributte.org/partners.html"><img src="https://badgen.net/badge/sponsor/donations/F96854"></a>
</p>

<p align=center>
Website 🚀 <a href="https://contributte.org">contributte.org</a> | Contact 👨🏻‍💻 <a href="https://f3l1x.io">f3l1x.io</a> | Twitter 🐦 <a href="https://twitter.com/contributte">@contributte</a>
</p>

Menu control for Nette applications with configurable menus, breadcrumbs, sitemaps, translations, authorization, and custom link generation.

## Versions

| State  | Version | Branch   | Nette  | PHP     |
|--------|---------|----------|--------|---------|
| dev    | `^3.3`  | `master` | `3.1+` | `>=8.1` |
| stable | `^3.2`  | `master` | `3.1+` | `>=8.1` |
| stable | `^2.2`  | `v2.2`   | `3.0`  | `>=7.1` |
| stable | `^2.1`  | `v2.1`   | `2.4`  | `>=7.1` |

## Contents

- [Installation](#installation)
- [Usage](#usage)
- [Templates](#templates)
- [Translations](#translations)
- [Custom data](#custom-data)
- [Authorization](#authorization)
- [Link generator](#link-generator)
- [Menu loader](#menu-loader)

## Installation

To install the latest version of `contributte/menu-control` use [Composer](https://getcomposer.org).

```bash
composer require contributte/menu-control
```

Register as Nette extension:

```neon
extensions:
	menu: Contributte\MenuControl\DI\MenuExtension

menu:
```

## Usage

You can write menu links as associated multi dimensional arrays. Because of this you are able to create any
structure of menus and submenus you may need.

```neon
menu:
	front:
		items:
			Home:
				action: Front:Home:
			Books:
				link: '#'
				items:
					All:
						action: Front:Books:all
					Featured:
						action: Front:Books:featured
	admin:
		items:
			Users:
				action: Admin:Users:

			Books:
				action: Admin:Books:
```

```php
<?php

namespace App;

use Contributte\MenuControl\UI\MenuComponentFactory;
use Contributte\MenuControl\UI\MenuComponent;
use Nette\Application\UI\Presenter;

final class BasePresenter extends Presenter
{

	/**
	 * @var MenuComponentFactory
	 */
	private $menuFactory;

	public function injectBasePresenter(MenuComponentFactory $menuFactory)
	{
		$this->menuFactory = $menuFactory;
	}

	protected function createComponentMenu(): MenuComponent
	{
		return $this->menuFactory->create('front');
	}

}
```

```latte
{control menu}              <!-- display menu -->
{control menu:breadcrumbs}  <!-- display breadcrumbs -->
{control menu:sitemap}      <!-- display sitemap -->
```

That structure in neon config will generate two menus:

**front:**

* Home (action: `Front:Home:`)
* Books (link: `#`)
	+ All (action: `Front:Books:all`)
	+ Featured (action: `Front:Books:featured`)

**admin:**

* Users (action: `Admin:Users:`)
* Books (action: `Admin:Books:`)

## Templates

This package includes 3 default templates (menu, breadcrumbs, sitemap). However only the default sitemap template
should be used in real project. The other two templates should only help you in the beginning with building your own
templates which will fit your website's look.

* [menu.latte](src/UI/templates/menu.latte)
* [breadcrumbs.latte](src/UI/templates/breadcrumbs.latte)
* [sitemap.latte](src/UI/templates/sitemap.latte)

Changing templates can be done in your menu configuration:

```neon
menu:
	front:
		templates:
			menu: %appDir%/path/to/custom/menu.latte
			breadcrumbs: %appDir%/path/to/custom/breadcrumbs.latte
			sitemap: %appDir%/path/to/custom/sitemap.latte
```

**As you can see, each menu can have different templates.**

### Visibility of items

It may be useful to hide some links in specific situations. For that we have the `visibility` option on items where
you can tell on which template the link should be visible.

```neon
menu:
	front:
		items:
			Home:
				action: Front:Home:
				visibility:
					menu: true
					breadcrumbs: false
					sitemap: true
```

### Mark active item via regex

Menu item can be labeled as active by a regular expression (or array of regular expressions) that is compared to the entire Presenter's name and action.
You can set your regular expression via `include` setting.

```neon
menu:
	front:
		items:
			Home:
				action: Front:Home:
				include: '^Front\:Home\:[a-zA-Z\:]+$' # mark as active for all actions of "Front:Home:" presenter
			Books:
				action: Front:Books:
				include: # mark as active for actions "Front:Books:default" and "Front:Books:edit"
					- '^Front\:Books\:default$'
					- '^Front\:Books\:edit$'
```

## Translations

When displaying title of link in some template, we always work with translated titles.

You have three options for translator:

* Do nothing: Original `ReturnTranslator` class will be used. This translator just returns the given text.
* Set translator manually: Provide your own implementation of `Nette\Localization\ITranslator`.
* Set translator to `true`: Menu extension will try to find your translator in DI container automatically.

```neon
services:
	- App\MyOwnFrontTranslator

menu:
	front:
		translator: @App\MyOwnFrontTranslator
	admin:
		translator: true
```

## Custom data

Every link can contain additional data which can be later used eg. in your custom latte templates.

```neon
menu:
	admin:
		items:

			Adminer:
				link: http://localhost:20000
				data:
					icon: fa fa-database
					attrs:
						target: _blank
```

```latte
<a href="{$item->getRealLink()}" n:attr="(expand) $link->getData('attrs')">
	<i class="{$item->getData('icon')}"></i> {$link->getRealTitle()}
</a>
```

## Authorization

Sometimes you may want to hide some links based on custom rules, that includes for example authorization from nette.

This menu package uses custom `IAuthorizator` interface which you can use to write your own authorizator implementations.

```php
<?php

namespace App;

use Contributte\MenuControl\IMenuItem;
use Contributte\MenuControl\Security\IAuthorizator;

final class FrontAuthorizator implements IAuthorizator
{

	public function isMenuItemAllowed(IMenuItem $item): bool
	{
		return isItemAllowed($item);
	}

}
```

```neon
services:
	- App\FrontAuthorizator

menu:
	front:
		authorizator: @App\FrontAuthorizator
```

## Link generator

When you want to display a link in your template it uses the `ILinkGenerator` interface to generate it from the data
in your menu config. If you want to change the default logic (which uses `link` method from nette) you can just implement
your custom link generator.

```php
<?php

namespace App;

use Contributte\MenuControl\IMenuItem;
use Contributte\MenuControl\LinkGenerator\ILinkGenerator;

final class FrontLinkGenerator implements ILinkGenerator
{


	public function link(IMenuItem $item): string
	{
		return generateLink($item);
	}

}
```

```neon
services:
	- App\FrontLinkGenerator

menu:
	front:
		linkGenerator: @App\FrontLinkGenerator
```

**You can also override link generator later for some subtree of links:**

```neon
menu:
	front:
		items:
			Home:
				action: Front:Home:
			Books:
				link: '#'
				linkGenerator: @App\BooksLinkGenerator
```

## Menu loader

If you want to build your menu maybe from database instead of neon config, you can do that by creating `IMenuLoader`
class.

See the default [DefaultMenuLoader](https://github.com/contributte/menu-control/blob/master/src/Loaders/DefaultMenuLoader.php) how it works.

## Development

See [how to contribute](https://contributte.org/contributing.html) to this package.

This package is currently maintained by these authors.

<a href="https://github.com/foxycode">
  <img width="80" height="80" src="https://avatars2.githubusercontent.com/u/1284781?v=3&s=80">
</a>

<a href="https://github.com/f3l1x">
  <img width="80" height="80" src="https://avatars2.githubusercontent.com/u/538058?v=3&s=80">
</a>

-----

Consider to [support](https://contributte.org/partners.html) **contributte** development team.
Also thank you for using this package.
