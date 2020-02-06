<?php

declare(strict_types=1);

namespace Contributte\MenuControl\UI;

interface IMenuComponentFactory
{

	public function create(string $name): MenuComponent;

}
