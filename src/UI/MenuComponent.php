<?php

declare(strict_types=1);

namespace Contributte\MenuControl\UI;

use Contributte\MenuControl\IMenu;
use Contributte\MenuControl\MenuContainer;
use Nette\Application\UI\Control;
use Nette\Application\UI\Presenter;

final class MenuComponent extends Control
{

	/**
	 * @var MenuContainer
	 */
	private $container;

	/**
	 * @var string
	 */
	private $menuName;

	public function __construct(MenuContainer $container, string $name)
	{
		$this->container = $container;
		$this->menuName = $name;

		$this->monitor(Presenter::class, function (Presenter $presenter): void {
			$menu = $this->container->getMenu($this->menuName);
			$menu->setActivePresenter($presenter);
		});
	}

	public function render(): void
	{
		$menu = $this->container->getMenu($this->menuName);
		$this->renderType($menu, $menu->getMenuTemplate());
	}

	public function renderBreadcrumbs(): void
	{
		$menu = $this->container->getMenu($this->menuName);
		$this->renderType($menu, $menu->getBreadcrumbsTemplate());
	}

	public function renderSitemap(): void
	{
		$menu = $this->container->getMenu($this->menuName);
		$this->renderType($menu, $menu->getSitemapTemplate());
	}

	public function renderType(IMenu $menu, string $menuTemplate): void
	{
		$template = $this->getTemplate();
		$template->setFile($menuTemplate);
		$template->menu = $menu;

		$template->render();
	}

}
