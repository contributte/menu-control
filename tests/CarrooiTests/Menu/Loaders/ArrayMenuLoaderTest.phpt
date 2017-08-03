<?php

declare(strict_types=1);

namespace CarrooiTests\Menu\Loaders;

use Carrooi\Menu\Loaders\ArrayMenuLoader;
use CarrooiTests\TestCase;
use Mockery\MockInterface;
use Tester\Environment;

require_once __DIR__. '/../../../bootstrap.php';

/**
 * @author David Kudera <kudera.d@gmail.com>
 */
final class ArrayMenuLoaderTest extends TestCase
{


	public function testLoad_simple(): void
	{
		Environment::$checkAssertions = false;

		$config = [
			'home' => [
				'title' => 'Home',
				'action' => ':Home:default',
			],
		];

		$menu = $this->createMockMenu(function(MockInterface $menu) {
			$menu->shouldReceive('addItem')->withArgs(['home', 'Home', \Mockery::type('callable')]);
		});

		$loader = new ArrayMenuLoader($config);
		$loader->load($menu);
	}

}

(new ArrayMenuLoaderTest)->run();
