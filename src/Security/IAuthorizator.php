<?php

declare(strict_types=1);

namespace Carrooi\Menu\Security;

use Carrooi\Menu\IMenuItem;

interface IAuthorizator
{

	public function isMenuItemAllowed(IMenuItem $item): bool;

}
