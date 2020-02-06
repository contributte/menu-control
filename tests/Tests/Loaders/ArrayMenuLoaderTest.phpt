<?php

declare(strict_types=1);

namespace Tests\Loaders;

use Contributte\MenuControl\Loaders\ArrayMenuLoader;
use Mockery\MockInterface;
use Tester\Environment;
use Tests\AbstractTestCase;

require_once __DIR__. '/../../bootstrap.php';

final class ArrayMenuLoaderTest extends AbstractTestCase
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
