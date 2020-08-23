<?php

declare(strict_types=1);

namespace Contributte\MenuControl\Security;

use Contributte\MenuControl\IMenuItem;

final class OptimisticAuthorizator implements IAuthorizator
{

	public function isMenuItemAllowed(IMenuItem $item): bool
	{
		return true;
	}

}
