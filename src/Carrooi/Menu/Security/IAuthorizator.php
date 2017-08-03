<?php

declare(strict_types=1);

namespace Carrooi\Menu\Security;

use Carrooi\Menu\IMenuItem;

/**
 * @author David Kudera <kudera.d@gmail.com>
 */
interface IAuthorizator
{


	public function isMenuItemAllowed(IMenuItem $item): bool;

}
