<?php

declare(strict_types=1);

namespace Carrooi\Menu\Loaders;

use Carrooi\Menu\IMenu;

interface IMenuLoader
{

	public function load(IMenu $menu): void;

}
