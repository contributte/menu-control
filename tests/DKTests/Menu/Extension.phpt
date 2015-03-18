<?php

/**
 * Test: DK\Menu\DI\Extension
 *
 * @testCase DKTests\Menu\ExtensionTest
 * @author David Kudera
 */

namespace DKTests\Menu;

use Tester\TestCase;
use Tester\Assert;
use Nette\Configurator;
use DK\Menu\DI\Extension;
use DK\Menu\Menu;
use DK\Menu\UI\Control;
use DK\Menu\UI\IControlFactory;


require_once __DIR__. '/../bootstrap.php';


/**
 *
 * @author David Kudera
 */
class ExtensionTest extends TestCase
{


	/**
	 * @param string $configFile
	 * @return \Nette\DI\Container
	 */
	private function createContainer($configFile)
	{
		$config = new Configurator;
		$config->setTempDirectory(TEMP_DIR);
		$config->addParameters(array(
			'container' => array('class' => 'SystemContainer_'. md5($configFile)),
			'appDir' => realpath(__DIR__. '/../app'),
		));
		$config->addConfig(__DIR__. '/../app/config/config.neon');
		$config->addConfig(__DIR__. '/../app/config/'. $configFile. '.neon');
		Extension::register($config);

		return $config->createContainer();
	}


	public function testFunctionality()
	{
		$di = $this->createContainer('menu');

		$menu = $di->getByType('DK\Menu\Menu');		/** @var $menu \DK\Menu\Menu */
		Assert::true($menu instanceof Menu);

		$homepage = $menu->getItem('0');
		Assert::same('Home', $homepage->getTitle());
		Assert::same('Homepage:default', $homepage->getTarget());
		Assert::true($homepage->isAllowed());

		$settings = $menu->getItem('3');
		Assert::same('Settings', $settings->getTitle());
		Assert::same('Settings:default', $settings->getTarget());
		Assert::false($settings->isAllowed());

		$about = $menu->getItem('about');
		Assert::same('About us', $about->getTitle());
		Assert::same('About:default', $about->getTarget());

		$controlFactory = $di->getByType('DK\Menu\UI\IControlFactory');		/** @var $controlFactory \DK\Menu\UI\IControlFactory */
		Assert::true($controlFactory instanceof IControlFactory);
		Assert::true($controlFactory->create() instanceof Control);
	}


	public function testChangeDefaultAllow()
	{
		$di = $this->createContainer('defaultAllow');

		$menu = $di->getByType('DK\Menu\Menu');		/** @var $menu \DK\Menu\Menu */
		Assert::true($menu instanceof Menu);

		$homepage = $menu->getItem('0');
		Assert::same('Home', $homepage->getTitle());
		Assert::same('Homepage:default', $homepage->getTarget());
		Assert::false($homepage->isAllowed());
	}


	public function testCustomControl()
	{
		$di = $this->createContainer('customControl');

		$menu = $di->getByType('DK\Menu\Menu');		/** @var $menu \DK\Menu\Menu */
		Assert::true($menu instanceof Menu);

		$controlFactory = $di->getByType('DKTests\Menu\ICustomControlFactory');		/** @var $controlFactory \DKTests\Menu\ICustomControlFactory */
		Assert::true($controlFactory instanceof ICustomControlFactory);
		Assert::true($controlFactory->create() instanceof CustomControl);
	}


	public function testMoreMenus()
	{
		$di = $this->createContainer('more');

		$controlFactory = $di->getByType('DKTests\Menu\IOtherCustomControlFactory');		/** @var $controlFactory \DKTests\Menu\ICustomControlFactory */
		Assert::true($controlFactory instanceof IOtherCustomControlFactory);
		Assert::true($controlFactory->create() instanceof OtherCustomControl);
	}

}


class CustomControl extends Control {}

interface ICustomControlFactory extends IControlFactory
{


	/**
	 * @return \DKTests\Menu\CustomControl
	 */
	public function create();

}


class OtherCustomControl extends Control {}

interface IOtherCustomControlFactory extends IControlFactory
{


	/**
	 * @return \DKTests\Menu\OtherCustomControl
	 */
	public function create();

}


\run(new ExtensionTest);
