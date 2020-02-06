<?php

declare(strict_types=1);

namespace Contributte\MenuControl;

use Contributte\MenuControl\LinkGenerator\ILinkGenerator;
use Contributte\MenuControl\Security\IAuthorizator;
use Nette\Http\Request;
use Nette\Localization\ITranslator;

interface IMenuItemFactory
{

	public function create(
		IMenu $menu,
		ILinkGenerator $linkGenerator,
		ITranslator $translator,
		IAuthorizator $authorizator,
		Request $httpRequest,
		IMenuItemFactory $menuItemFactory,
		string $title
	): IMenuItem;

}
