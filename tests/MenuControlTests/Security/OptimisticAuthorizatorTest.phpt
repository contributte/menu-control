<?php

declare(strict_types=1);

namespace Contributte\MenuControlTests\Security;

use Contributte\MenuControl\Security\OptimisticAuthorizator;
use Contributte\MenuControlTests\AbstractTestCase;
use Tester\Assert;

require_once __DIR__. '/../../bootstrap.php';

/**
 * @testCase
 */
final class OptimisticAuthorizatorTest extends AbstractTestCase
{

	public function testIsMenuItemAllowed(): void
	{
		$item = $this->createMockMenuItem();
		$authorizator = new OptimisticAuthorizator;

		Assert::true($authorizator->isMenuItemAllowed($item));
	}

}

(new OptimisticAuthorizatorTest)->run();
