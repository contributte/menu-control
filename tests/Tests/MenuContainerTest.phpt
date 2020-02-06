<?php

declare(strict_types=1);

namespace Tests;

use Contributte\MenuControl\MenuContainer;
use Mockery\MockInterface;
use Tester\Assert;

require_once __DIR__. '/../bootstrap.php';

/**
 * @testCase
 */
final class MenuContainerTest extends AbstractTestCase
{

	public function testMenu(): void
	{
		$menu = $this->createMockMenu(function(MockInterface $menu) {
			$menu->shouldReceive('getName')->andReturn('default');
		});

		$container = new MenuContainer;
		$container->addMenu($menu);

		Assert::same($menu, $container->getMenu('default'));
	}

}

(new MenuContainerTest)->run();
