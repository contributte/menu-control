[![Build Status](https://img.shields.io/travis/Carrooi/Nette-Menu.svg?style=flat-square)](https://travis-ci.org/Carrooi/Nette-Menu)
[![Donate](https://img.shields.io/badge/donate-PayPal-brightgreen.svg?style=flat-square)](https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=HPH6AC9D5LGHY)

# Nette menu

Nette component for creating menus, breadcrumbs and sitemaps.

## Requirements

* PHP >=7.1
* Nette ^2.4 

## Installation

**Install package with composer:**

```
$ composer require carrooi/nette-menu
```

**Register as nette extension:**

```yaml
extensions:
  menu: Carrooi\Menu\DI\MenuExtension

menu:
```

## Basic usage

You can write menu links as associated multi dimensional arrays. Because of this you are able to create any
structure of menus and submenus you may need.

```yaml
menu:

  front:
    items:

      Home:
        action: :Front:Home:

      Books:
        link: '#'
        items:
        
          All:
            action: :Front:Books:all
            
          Featured:
            action: :Front:Books:featured
            
  admin:
    items:
    
      Users:
        action: :Admin:Users:
        
      Books:
        action: :Admin:Books:
```

```php
<?php

namespace App;

use Carrooi\Menu\UI\IMenuComponentFactory;
use Carrooi\Menu\UI\MenuComponent;
use Nette\Application\UI\Presenter;

final class BasePresenter extends Presenter
{
	
	
	private $menuFactory;
	
	
	public function injectBasePresenter(IMenuComponentFactory $menuFactory)
	{
		$this->menuFactory = $menuFactory;
	}
	
	
	protected function createComponentMenu(): MenuComponent
	{
		return $this->menuFactory->create('front');
	}
	
}
```

```html
{control menu}              <!-- display menu -->
{control menu:breadcrumbs}  <!-- display breadcrumbs -->
{control menu:sitemap}      <!-- display sitemap -->
```

That structure in neon config will generate two menus:

**front:**

* Home (action: `:Front:Home:`)
* Books (link: `#`)
	+ All (action: `:Front:Books:all`)
	+ Featured (action: `:Front:Books:featured`)
	
**admin:**

* Users (action: `:Admin:Users:`)
* Books (action: `:Admin:Books:`)

## Templates

This package includes 3 default templates (menu, breadcrumbs, sitemap). However only the default sitemap template 
should be used in real project. The other two templates should only help you in the beginning with building your own 
templates which will fit your's website look.

* [menu.latte](./src/Carrooi/Menu/UI/templates/menu.latte)
* [breadcrumbs.latte](./src/Carrooi/Menu/UI/templates/menu.latte)
* [sitemap.latte](./src/Carrooi/Menu/UI/templates/sitemap.latte)

Changing templates can be done in your menu configuration:

```yaml
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

```yaml
menu:

  front:
    items:
    
      Home:
        action: :Front:Home:
        visibility:
          menu: true
          breadcrumbs: false
          sitemap: true
```

## Translations

When displaying title of link in some template, we always work with translated titles.

You have three options for translator:

* Do nothing: Original `ReturnTranslator` class will be used. This translator just returns the given text.
* Set translator manually: Provide your own implementation of `Nette\Localization\ITranslator`.
* Set translator to `true`: Menu extension will try to find your translator in DI container automatically.

```yaml
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

```yaml
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

```html
<a href="{$item->getRealLink()" n:attr="(expand) $link->getData('attrs')">
	<i class="{$item->getData('icon')"></i> {$link->getRealTitle()}
</a>
```

## Authorization

Sometimes you may want to hide some links based on custom rules, that includes for example authorization from nette.

This menu package uses custom `IAuthorizator` interface which you can use to write your own authorizators.

```php
<?php

namespace App;

use Carrooi\Menu\IMenuItem;
use Carrooi\Menu\Security\IAuthorizator;

final class FrontAuthorizator implements IAuthorizator
{
	
	public function isMenuItemAllowed(IMenuItem $item): bool
	{
		return isItemAllowed($item);
	}
	
}
```

```yaml
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

use Carrooi\Menu\IMenuItem;
use Carrooi\Menu\LinkGenerator\ILinkGenerator;

final class FrontLinkGenerator implements ILinkGenerator
{


	public function link(IMenuItem $item): string
	{
		return generateLink($item);
	}
	
}
```

```yaml
services:

  - App\FrontLinkGenerator
  
menu:

  front:
    linkGenerator: @App\FrontLinkGenerator
```

**You can also override link generator later for some subtree of links:**

```yaml
menu:

  front:
    items:
    
      Home:
        action: :Front:Home:
        
      Books:
        link: '#'
        linkGenerator: @App\BooksLinkGenerator
```

## Menu loader

If you want to build your menu maybe from database instead of neon config, you can do that by creating `IMenuLoader`
class.

See the default [ArrayMenuLoader](./src/Carrooi/Menu/Loaders/ArrayMenuLoader.php) how it works.

## Changelog

* 2.1.0
	+ Fixed registration of more menus [#23](https://github.com/Carrooi/Nette-Menu/pull/23)
	+ Added shortcuts for working with data [#24](https://github.com/Carrooi/Nette-Menu/pull/24)

* 2.0.0
	+ Refactoring - complete rewrite

* 1.1.1
	+ Move under Carrooi organization

* 1.1.0
	+ Support for ACL permissions (thanks [whipsterCZ](https://github.com/whipsterCZ))
	+ Support for generating sitemap (thanks [whipsterCZ](https://github.com/whipsterCZ))

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
