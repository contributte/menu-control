<?php

declare(strict_types=1);

namespace Carrooi\Menu\Loaders;

use Carrooi\Menu\IMenu;

/**
 * @author David Kudera <kudera.d@gmail.com>
 */
interface IMenuLoader
{


	public function load(IMenu $menu): void;

}
