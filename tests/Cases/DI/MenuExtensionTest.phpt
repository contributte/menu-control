<?php

declare(strict_types=1);

namespace Contributte\MenuControlTests\Cases\DI;

use Contributte\MenuControl\MenuContainer;
use Contributte\MenuControl\UI\IMenuComponentFactory;
use Contributte\MenuControl\UI\MenuComponent;
use Contributte\MenuControlTests\AbstractTestCase;
use Contributte\MenuControlTests\Presenters\HomepagePresenter;
use Nette\Application\Request;
use Nette\Application\Responses\TextResponse;
use Nette\Bootstrap\Configurator;
use Nette\DI\Container;
use Tester\Assert;

require_once __DIR__. '/../../bootstrap.php';

/**
 * @testCase
 */
final class MenuExtensionTest extends AbstractTestCase
{

	public function testDI(): void
	{
		$dic = $this->createContainer();

		Assert::type(MenuContainer::class, $dic->getService('menu.container'));
		Assert::type(IMenuComponentFactory::class, $dic->getService('menu.component.menu'));
		Assert::type(MenuComponent::class, $dic->getService('menu.component.menu')->create('default'));
	}

	public function testRender(): void
	{
		$dic = $this->createContainer();
		$request = new Request('Homepage', 'default');

		$presenter = new HomepagePresenter;
		$dic->callInjects($presenter);

		/** @var TextResponse $response */
		$response = $presenter->run($request);

		ob_start();
		$response->getSource()->render();
		$output = ob_get_clean();

		Assert::same(file_get_contents(__DIR__ . '/output.html'), $output);
	}

	private function createContainer(): Container
	{
		$config = new Configurator;
		$config->setTempDirectory(TEMP_DIR);
		$config->addConfig(__DIR__ . '/config.neon');

		return $config->createContainer();
	}

}

(new MenuExtensionTest)->run();
