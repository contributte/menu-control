<?php declare(strict_types = 1);

namespace Contributte\MenuControl;

use Contributte\MenuControl\LinkGenerator\ILinkGenerator;
use Contributte\MenuControl\Security\IAuthorizator;
use Nette\Localization\Translator;

interface IMenuItemFactory
{

	public function create(
		IMenu $menu,
		ILinkGenerator $linkGenerator,
		Translator $translator,
		IAuthorizator $authorizator,
		IMenuItemFactory $menuItemFactory,
		string $title
	): IMenuItem;

}
