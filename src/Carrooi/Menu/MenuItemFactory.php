<?php

declare(strict_types=1);

namespace Carrooi\Menu;

use Carrooi\Menu\LinkGenerator\ILinkGenerator;
use Carrooi\Menu\Security\IAuthorizator;
use Nette\Application\Application;
use Nette\Http\Request;
use Nette\Localization\ITranslator;

/**
 * @author David Kudera <kudera.d@gmail.com>
 */
final class MenuItemFactory implements IMenuItemFactory
{


	public function create(ILinkGenerator $linkGenerator, ITranslator $translator, IAuthorizator $authorizator, Application $application, Request $httpRequest, IMenuItemFactory $menuItemFactory, string $title): IMenuItem
	{
		return new MenuItem($linkGenerator, $translator, $authorizator, $application, $httpRequest, $menuItemFactory, $title);
	}

}
