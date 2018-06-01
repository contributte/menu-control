<?php

declare(strict_types=1);

namespace Carrooi\Menu;

use Carrooi\Menu\LinkGenerator\ILinkGenerator;
use Carrooi\Menu\Security\IAuthorizator;
use Nette\Application\Application;
use Nette\Application\LinkGenerator;
use Nette\Http\Request;
use Nette\Localization\ITranslator;

/**
 * @author David Kudera <kudera.d@gmail.com>
 */
interface IMenuItemFactory
{


	public function create(ILinkGenerator $linkGenerator, ITranslator $translator, IAuthorizator $authorizator, LinkGenerator $nativeLinkGenerator, Request $httpRequest, IMenuItemFactory $menuItemFactory, string $title): IMenuItem;

}
