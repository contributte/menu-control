<?php

declare(strict_types=1);

namespace Carrooi\Menu\LinkGenerator;

use Carrooi\Menu\IMenuItem;

interface ILinkGenerator
{

	public function link(IMenuItem $item): string;

}
