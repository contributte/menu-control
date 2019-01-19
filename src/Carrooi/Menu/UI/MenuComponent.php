<?php

declare(strict_types=1);

namespace Carrooi\Menu\UI;

use Carrooi\Menu\IMenu;
use Carrooi\Menu\MenuContainer;
use Nette\Application\UI\Control;
use Nette\Application\UI\Presenter;

/**
 * @author David Kudera <kudera.d@gmail.com>
 */
final class MenuComponent extends Control
{


	/** @var \Carrooi\Menu\MenuContainer */
	private $container;

	/** @var string */
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
		$this->template->setFile($menuTemplate);
		$this->template->menu = $menu;

		$this->template->render();
	}

}
