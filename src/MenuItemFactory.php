<?php

declare(strict_types=1);

namespace Contributte\MenuControl;

use Contributte\MenuControl\LinkGenerator\ILinkGenerator;
use Contributte\MenuControl\Security\IAuthorizator;
use Nette\Localization\Translator;

final class MenuItemFactory implements IMenuItemFactory
{

	public function create(
		IMenu $menu,
		ILinkGenerator $linkGenerator,
		Translator $translator,
		IAuthorizator $authorizator,
		IMenuItemFactory $menuItemFactory,
		string $title
	): IMenuItem {
		return new MenuItem($menu, $linkGenerator, $translator, $authorizator, $menuItemFactory, $title);
	}

}
