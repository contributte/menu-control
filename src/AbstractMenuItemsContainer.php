<?php declare(strict_types = 1);

namespace Contributte\MenuControl;

use Contributte\MenuControl\LinkGenerator\ILinkGenerator;
use Contributte\MenuControl\Security\IAuthorizator;
use Nette\Localization\Translator;

abstract class AbstractMenuItemsContainer implements IMenuItemsContainer
{

	protected IMenu $menu;

	protected ILinkGenerator $linkGenerator;

	protected Translator $translator;

	protected IAuthorizator $authorizator;

	protected IMenuItemFactory $menuItemFactory;

	/** @var IMenuItem[] */
	private array $items = [];

	public function __construct(
		IMenu $menu,
		ILinkGenerator $linkGenerator,
		Translator $translator,
		IAuthorizator $authorizator,
		IMenuItemFactory $menuItemFactory
	)
	{
		$this->menu = $menu;
		$this->linkGenerator = $linkGenerator;
		$this->translator = $translator;
		$this->authorizator = $authorizator;
		$this->menuItemFactory = $menuItemFactory;
	}

	/**
	 * @return IMenuItem[]
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

	public function addItem(string $name, string $title, ?callable $fn = null): void
	{
		$this->items[$name] = $item = $this->menuItemFactory->create(
			$this->menu,
			$this->linkGenerator,
			$this->translator,
			$this->authorizator,
			$this->menuItemFactory,
			$title
		);

		if ($fn !== null) {
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

	/**
	 * @return IMenuItem[]
	 */
	public function getVisibleItemsOnMenu(): array
	{
		return $this->getVisibleItemsOn('menu');
	}

	public function hasVisibleItemsOnBreadcrumbs(): bool
	{
		return $this->hasVisibleItemsOn('breadcrumbs');
	}

	/**
	 * @return IMenuItem[]
	 */
	public function getVisibleItemsOnBreadcrumbs(): array
	{
		return $this->getVisibleItemsOn('breadcrumbs');
	}

	public function hasVisibleItemsOnSitemap(): bool
	{
		return $this->hasVisibleItemsOn('sitemap');
	}

	/**
	 * @return IMenuItem[]
	 */
	public function getVisibleItemsOnSitemap(): array
	{
		return $this->getVisibleItemsOn('sitemap');
	}

	private function hasVisibleItemsOn(string $type): bool
	{
		return count($this->getVisibleItemsOn($type)) > 0;
	}

	/**
	 * @return IMenuItem[]
	 */
	private function getVisibleItemsOn(string $type): array
	{
		return array_filter($this->getItems(), function (IMenuItem $item) use ($type): bool {
			return match ($type) {
				'menu' => $item->isVisibleOnMenu(),
				'breadcrumbs' => $item->isVisibleOnBreadcrumbs(),
				'sitemap' => $item->isVisibleOnSitemap(),
				default => false,
			};
		});
	}

}
