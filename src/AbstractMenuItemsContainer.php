<?php

declare(strict_types=1);

namespace Carrooi\Menu;

use Carrooi\Menu\LinkGenerator\ILinkGenerator;
use Carrooi\Menu\Security\IAuthorizator;
use Nette\Http\Request;
use Nette\Localization\ITranslator;

/**
 * @author David Kudera <kudera.d@gmail.com>
 */
abstract class AbstractMenuItemsContainer implements IMenuItemsContainer
{


	/** @var \Carrooi\Menu\IMenu */
	protected $menu;

	/** @var \Carrooi\Menu\LinkGenerator\ILinkGenerator */
	protected $linkGenerator;

	/** @var \Nette\Localization\ITranslator */
	protected $translator;

	/** @var \Carrooi\Menu\Security\IAuthorizator */
	protected $authorizator;

	/** @var \Nette\Http\Request */
	protected $httpRequest;

	/** @var \Carrooi\Menu\IMenuItemFactory */
	protected $menuItemFactory;

	/** @var \Carrooi\Menu\IMenuItem[] */
	private $items = [];


	public function __construct(IMenu $menu, ILinkGenerator $linkGenerator, ITranslator $translator, IAuthorizator $authorizator, Request $httpRequest, IMenuItemFactory $menuItemFactory)
	{
		$this->menu = $menu;
		$this->linkGenerator = $linkGenerator;
		$this->translator = $translator;
		$this->authorizator = $authorizator;
		$this->httpRequest = $httpRequest;
		$this->menuItemFactory = $menuItemFactory;
	}


	public function setLinkGenerator(ILinkGenerator $linkGenerator): void
	{
		$this->linkGenerator = $linkGenerator;
	}


	/**
	 * @return \Carrooi\Menu\IMenuItem[]
	 */
	public function getItems(): array
	{
		return $this->items;
	}


	public function getItem(string $name): IMenuItem
	{
		$path = explode('-', $name);

		if (count($path) === 1) {
			return $this->getItems()[$name];
		}

		$current = $this->getItem(array_shift($path));

		while (count($path) > 0) {
			$current = $current->getItem(array_shift($path));
		}

		return $current;
	}


	public function addItem(string $name, string $title, callable $fn = null): void
	{
		$this->items[$name] = $item = $this->menuItemFactory->create($this->menu, $this->linkGenerator, $this->translator, $this->authorizator, $this->httpRequest, $this->menuItemFactory, $title);

		if ($fn) {
			$fn($item);
		}
	}


	public function findActiveItem(): ?IMenuItem
	{
		foreach ($this->getItems() as $item) {
			if ($item->isAllowed() && $item->isActive()) {
				return $item;
			}
		}

		return null;
	}


	public function hasVisibleItemsOnMenu(): bool
	{
		return $this->hasVisibleItemsOn('menu');
	}


	public function getVisibleItemsOnMenu(): array
	{
		return $this->getVisibleItemsOn('menu');
	}


	public function hasVisibleItemsOnBreadcrumbs(): bool
	{
		return $this->hasVisibleItemsOn('breadcrumbs');
	}


	public function getVisibleItemsOnBreadcrumbs(): array
	{
		return $this->getVisibleItemsOn('breadcrumbs');
	}


	public function hasVisibleItemsOnSitemap(): bool
	{
		return $this->hasVisibleItemsOn('sitemap');
	}


	public function getVisibleItemsOnSitemap(): array
	{
		return $this->getVisibleItemsOn('sitemap');
	}


	private function hasVisibleItemsOn(string $type): bool
	{
		return count($this->getVisibleItemsOn($type)) > 0;
	}


	/**
	 * @param string $type
	 * @return \Carrooi\Menu\AbstractMenuItemsContainer[]
	 */
	private function getVisibleItemsOn(string $type): array
	{
		return array_filter($this->getItems(), function(IMenuItem $item) use ($type) {
			switch ($type) {
				case 'menu': return $item->isVisibleOnMenu();
				case 'breadcrumbs': return $item->isVisibleOnBreadcrumbs();
				case 'sitemap': return $item->isVisibleOnSitemap();
				default: return false;
			}
		});
	}

}
