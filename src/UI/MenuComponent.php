<?php

declare(strict_types=1);

namespace Contributte\MenuControl\UI;

use Contributte\MenuControl\IMenu;
use Contributte\MenuControl\MenuContainer;
use Nette\Application\UI\Control;
use Nette\Application\UI\Presenter;

/**
 * @author David Kudera <kudera.d@gmail.com>
 */
final class MenuComponent extends Control
{


	/** @var MenuContainer */
	private $container;

	/** @var string */
	private $menuName;


	public function __construct(MenuContainer $container, string $name)
	{
		parent::__construct();

		$this->container = $container;
		$this->menuName = $name;
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


	/**
	 * This method will be called when the component (or component's parent)
	 * becomes attached to a monitored object. Do not call this method yourself.
	 * @param  \Nette\ComponentModel\IComponent
	 * @return void
	 */
	protected function attached($presenter)
	{
		if ($presenter instanceof Presenter) {
			$menu = $this->container->getMenu($this->menuName);
			$menu->setActivePresenter($presenter);
		}

		parent::attached($presenter);
	}

}
