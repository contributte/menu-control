<?php

declare(strict_types=1);

namespace CarrooiTests\Menu;

use Carrooi\Menu\MenuContainer;
use CarrooiTests\TestCase;
use Mockery\MockInterface;
use Tester\Assert;

require_once __DIR__. '/../../bootstrap.php';

/**
 * @testCase
 *
 * @author David Kudera <kudera.d@gmail.com>
 */
final class MenuContainerTest extends TestCase
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
