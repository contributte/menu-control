<?php

declare(strict_types=1);

namespace Contributte\MenuControl;

use Contributte\MenuControl\LinkGenerator\ILinkGenerator;
use Contributte\MenuControl\Security\IAuthorizator;
use Nette\Http\Request;
use Nette\Localization\ITranslator;

final class MenuItemFactory implements IMenuItemFactory
{

	public function create(
		IMenu $menu,
		ILinkGenerator $linkGenerator,
		ITranslator $translator,
		IAuthorizator $authorizator,
		Request $httpRequest,
		IMenuItemFactory $menuItemFactory,
		string $title
	): IMenuItem {
		return new MenuItem($menu, $linkGenerator, $translator, $authorizator, $httpRequest, $menuItemFactory, $title);
	}

}
