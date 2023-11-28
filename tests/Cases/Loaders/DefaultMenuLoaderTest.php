<?php declare(strict_types = 1);

namespace Tests\Cases\Loaders;

use Contributte\MenuControl\Loaders\DefaultMenuLoader;
use Mockery;
use Mockery\MockInterface;
use Tester\Environment;
use Tests\Toolkit\AbstractTestCase;

require_once __DIR__ . '/../../bootstrap.php';

final class DefaultMenuLoaderTest extends AbstractTestCase
{

	public function testLoad_simple(): void
	{
		Environment::$checkAssertions = false;

		$config = [
			'home' => (object) [
				'title' => 'Home',
				'action' => 'Home:default',
			],
		];

		$menu = $this->createMockMenu(function (MockInterface $menu): void {
			$menu->shouldReceive('addItem')->withArgs(['home', 'Home', Mockery::type('callable')]);
		});

		$loader = new DefaultMenuLoader($config);
		$loader->load($menu);
	}

}

(new DefaultMenuLoaderTest())->run();
