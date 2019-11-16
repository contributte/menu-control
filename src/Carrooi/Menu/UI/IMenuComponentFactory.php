<?php

declare(strict_types=1);

namespace Carrooi\Menu\UI;

interface IMenuComponentFactory
{

	public function create(string $name): MenuComponent;

}
