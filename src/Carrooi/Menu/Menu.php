<?php

declare(strict_types=1);

namespace Carrooi\Menu;

use Carrooi\Menu\LinkGenerator\ILinkGenerator;
use Carrooi\Menu\Loaders\IMenuLoader;
use Carrooi\Menu\Security\IAuthorizator;
use Nette\Application\Application;
use Nette\Application\LinkGenerator;
use Nette\Http\Request;
use Nette\Localization\ITranslator;

/**
 * @author David Kudera <kudera.d@gmail.com>
 */
final class Menu extends AbstractMenuItemsContainer implements IMenu
{


	/** @var \Carrooi\Menu\Loaders\IMenuLoader */
	private $loader;

	/** @var string */
	private $name;

	/** @var string[] */
	private $templates = [
		'menu' => null,
		'breadcrumbs' => null,
		'sitemap' => null,
	];


	public function __construct(ILinkGenerator $linkGenerator, ITranslator $translator, IAuthorizator $authorizator, LinkGenerator $nativeLinkGenerator, Request $httpRequest, IMenuItemFactory $menuItemFactory, IMenuLoader $loader, string $name, string $menuTemplate, string $breadcrumbsTemplate, string $sitemapTemplate)
	{
		parent::__construct($linkGenerator, $translator, $authorizator, $nativeLinkGenerator, $httpRequest, $menuItemFactory);

		$this->loader = $loader;
		$this->name = $name;
		$this->templates['menu'] = $menuTemplate;
		$this->templates['breadcrumbs'] = $breadcrumbsTemplate;
		$this->templates['sitemap'] = $sitemapTemplate;
	}


	public function init(): void
	{
		$this->loader->load($this);
	}


	public function getName(): string
	{
		return $this->name;
	}


	public function getMenuTemplate(): string
	{
		return $this->templates['menu'];
	}


	public function getBreadcrumbsTemplate(): string
	{
		return $this->templates['breadcrumbs'];
	}


	public function getSitemapTemplate(): string
	{
		return $this->templates['sitemap'];
	}


	public function getPath(): array
	{
		$path = [];
		$parent = $this;

		while ($parent) {
			$item = $parent->findActiveItem();

			if (!$item) {
				break;
			}

			$parent = $path[] = $item;
		}

		return $path;
	}

}
