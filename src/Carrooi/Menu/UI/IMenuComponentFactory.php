<?php

declare(strict_types=1);

namespace Carrooi\Menu\UI;

/**
 * @author David Kudera <kudera.d@gmail.com>
 */
interface IMenuComponentFactory
{


	public function create(string $name): MenuComponent;

}
