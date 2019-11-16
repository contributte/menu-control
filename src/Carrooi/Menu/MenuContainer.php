<?php

declare(strict_types=1);

namespace Carrooi\Menu;

/**
 * @author David Kudera <kudera.d@gmail.com>
 */
final class MenuContainer
{

	/**
	 * @var IMenu[]
	 */
	private $menus = [];


	public function getMenu(string $name): IMenu
	{
		return $this->menus[$name];
	}


	public function addMenu(IMenu $menu): void
	{
		$this->menus[$menu->getName()] = $menu;
	}

}
