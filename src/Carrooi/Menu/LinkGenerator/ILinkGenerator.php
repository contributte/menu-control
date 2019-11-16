<?php

declare(strict_types=1);

namespace Carrooi\Menu\LinkGenerator;

use Carrooi\Menu\IMenuItem;

/**
 * @author David Kudera <kudera.d@gmail.com>
 */
interface ILinkGenerator
{

	public function link(IMenuItem $item): string;

}
