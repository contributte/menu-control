<?php declare(strict_types = 1);

namespace Contributte\MenuControl\Loaders;

use Contributte\MenuControl\IMenu;

interface IMenuLoader
{

	public function load(IMenu $menu): void;

}
