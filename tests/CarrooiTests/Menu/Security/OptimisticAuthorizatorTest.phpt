<?php

declare(strict_types=1);

namespace CarrooiTests\Menu\Security;

use Carrooi\Menu\Security\OptimisticAuthorizator;
use CarrooiTests\TestCase;
use Tester\Assert;

require_once __DIR__. '/../../../bootstrap.php';

/**
 * @testCase
 *
 * @author David Kudera <kudera.d@gmail.com>
 */
final class OptimisticAuthorizatorTest extends TestCase
{


	public function testIsMenuItemAllowed(): void
	{
		$item = $this->createMockMenuItem();
		$authorizator = new OptimisticAuthorizator;

		Assert::true($authorizator->isMenuItemAllowed($item));
	}

}

(new OptimisticAuthorizatorTest)->run();
