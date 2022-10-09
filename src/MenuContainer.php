<?php declare(strict_types = 1);

namespace Contributte\MenuControl;

final class MenuContainer
{

	/** @var IMenu[] */
	private array $menus = [];

	public function getMenu(string $name): IMenu
	{
		return $this->menus[$name];
	}

	public function addMenu(IMenu $menu): void
	{
		$this->menus[$menu->getName()] = $menu;
	}

}
