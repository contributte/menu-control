<?php declare(strict_types = 1);

namespace Tests\Cases\Security;

use Contributte\MenuControl\Security\OptimisticAuthorizator;
use Tester\Assert;
use Tests\Toolkit\AbstractTestCase;

require_once __DIR__ . '/../../bootstrap.php';

/**
 * @testCase
 */
final class OptimisticAuthorizatorTest extends AbstractTestCase
{

	public function testIsMenuItemAllowed(): void
	{
		$item = $this->createMockMenuItem();
		$authorizator = new OptimisticAuthorizator();

		Assert::true($authorizator->isMenuItemAllowed($item));
	}

}

(new OptimisticAuthorizatorTest())->run();
