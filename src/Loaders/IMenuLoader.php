<?php

declare(strict_types=1);

namespace Contributte\MenuControl\Loaders;

use Contributte\MenuControl\IMenu;

/**
 * @author David Kudera <kudera.d@gmail.com>
 */
interface IMenuLoader
{


	public function load(IMenu $menu): void;

}
