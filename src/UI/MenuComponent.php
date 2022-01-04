<?php declare(strict_types = 1);

namespace Contributte\MenuControl\UI;

use Contributte\MenuControl\IMenu;
use Nette\Application\UI\Control;
use Nette\Application\UI\Presenter;

final class MenuComponent extends Control
{

	/** @var IMenu */
	private $menu;

	public function __construct(IMenu $menu)
	{
		$this->menu = $menu;

		$this->monitor(Presenter::class, function (Presenter $presenter): void {
			$this->menu->setActivePresenter($presenter);
		});
	}

	public function render(): void
	{
		$this->renderType($this->menu->getMenuTemplate());
	}

	public function renderBreadcrumbs(): void
	{
		$this->renderType($this->menu->getBreadcrumbsTemplate());
	}

	public function renderSitemap(): void
	{
		$this->renderType($this->menu->getSitemapTemplate());
	}

	private function renderType(string $menuTemplate): void
	{
		$template = $this->getTemplate();
		$template->setFile($menuTemplate);
		$template->menu = $this->menu;

		$template->render();
	}

}
