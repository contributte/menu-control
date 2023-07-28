<?php declare(strict_types = 1);

namespace Tests\Cases\DI;

use Contributte\MenuControl\MenuContainer;
use Contributte\MenuControl\UI\MenuComponent;
use Contributte\MenuControl\UI\MenuComponentFactory;
use Nette\Application\Request as ApplicationRequest;
use Nette\Application\Responses\TextResponse;
use Nette\Bootstrap\Configurator;
use Nette\DI\Container;
use Nette\Http\Request as HttpRequest;
use Nette\Http\UrlScript;
use Tester\Assert;
use Tests\Fixtures\Presenters\HomepagePresenter;
use Tests\Toolkit\AbstractTestCase;

require_once __DIR__ . '/../../bootstrap.php';

/**
 * @testCase
 */
final class MenuExtensionTest extends AbstractTestCase
{

	public function testDI(): void
	{
		$dic = $this->createContainer();

		Assert::type(MenuContainer::class, $dic->getService('menu.container'));
		Assert::type(MenuComponentFactory::class, $dic->getService('menu.component.factory'));
		Assert::type(MenuComponent::class, $dic->getService('menu.component.factory')->create('default'));
	}

	public function testDataItems(): void
	{
		$dic = $this->createContainer();

		$container = $dic->getService('menu.container');
		/** @var \Contributte\MenuControl\IMenu $menu */
		$menu = $container->getMenu('default');

		$item = $menu->getItem('Homepage');

		Assert::type('bool', $item->getDataItem('bool'));
		Assert::type('string', $item->getDataItem('icon'));
		Assert::type('array', $item->getDataItem('structured'));
	}

	public function testRender(): void
	{
		$dic = $this->createContainer();
		$httpRequest = new HttpRequest(new UrlScript('http://localhost/'));
		$dic->addService('http.request', $httpRequest);

		$request = new ApplicationRequest('Homepage', 'default');

		$presenter = new HomepagePresenter();
		$dic->callInjects($presenter);

		/** @var TextResponse $response */
		$response = $presenter->run($request);

		ob_start();
		$response->getSource()->render();
		$output = ob_get_clean();

		//file_put_contents(__DIR__ . '/output.html', $output);
		Assert::same(file_get_contents(__DIR__ . '/output.html'), $output);
	}

	private function createContainer(): Container
	{
		$config = new Configurator();
		$config->setTempDirectory(TEMP_DIR);
		$config->addConfig(__DIR__ . '/config.neon');

		return $config->createContainer();
	}

}

(new MenuExtensionTest())->run();
