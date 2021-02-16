<?php

declare(strict_types=1);

namespace Contributte\MenuControl;

use Contributte\MenuControl\LinkGenerator\ILinkGenerator;
use Contributte\MenuControl\Security\IAuthorizator;
use Nette\Http\IRequest;
use Nette\Localization\ITranslator;

interface IMenuItemFactory
{

	public function create(
		IMenu $menu,
		ILinkGenerator $linkGenerator,
		ITranslator $translator,
		IAuthorizator $authorizator,
		IRequest $httpRequest,
		IMenuItemFactory $menuItemFactory,
		string $title
	): IMenuItem;

}
