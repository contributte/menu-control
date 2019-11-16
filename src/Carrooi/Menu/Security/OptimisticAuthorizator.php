<?php

declare(strict_types=1);

namespace Carrooi\Menu\Security;

use Carrooi\Menu\IMenuItem;

final class OptimisticAuthorizator implements IAuthorizator
{

	public function isMenuItemAllowed(IMenuItem $item): bool
	{
		return true;
	}

}
