<?php declare(strict_types = 1);

namespace Contributte\MenuControl\UI;

use Contributte\MenuControl\MenuContainer;

final class MenuComponentFactory
{

	/** @var MenuContainer */
	private $menuContainer;

	public function __construct(MenuContainer $menuContainer)
	{
		$this->menuContainer = $menuContainer;
	}

	public function create(string $name): MenuComponent
	{
		return new MenuComponent($this->menuContainer->getMenu($name));
	}

}
