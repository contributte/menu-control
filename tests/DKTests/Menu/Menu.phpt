<?php

/**
 * Test: DK\Menu\Menu
 *
 * @testCase DKTests\Menu\MenuTest
 * @author David Kudera
 */

namespace DKTests\Menu;


require_once __DIR__. '/../bootstrap.php';


if (!interface_exists('Nette\Application\UI\ITemplate')) {
	class_alias('Nette\Templating\ITemplate', 'Nette\Application\UI\ITemplate');
}


use Tester\Assert;
use Tester\TestCase;
use Tester\DomQuery;
use Nette\DI\Container;
use Nette\Configurator;
use Nette\Application\UI\ITemplate;
use Nette\Application\Request;
use Nette\Application\Responses\TextResponse;
use Nette\Application\UI\Presenter;
use Nette\Security\IAuthenticator;
use Nette\Security\Identity;
use Nette\Localization\ITranslator;
use DK\Menu\DI\Extension;
use DK\Menu\Item;


/**
 *
 * @author David Kudera
 */
class MenuTest extends TestCase
{


	/** @var \Nette\DI\Container */
	private $container;


	public function tearDown()
	{
		$user = $this->container->getByType('Nette\Security\User');		/** @var $user \Nette\Security\User */
		if ($user->isLoggedIn()) {
			$user->logout();
		}
	}


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

		$this->container = $config->createContainer();

		$user = $this->container->getByType('Nette\Security\User');		/** @var $user \Nette\Security\User */
		$user->setAuthenticator(new Authenticator);

		return $this->container;
	}


	/**
	 * @param string $name
	 * @param \Nette\DI\Container $container
	 * @return \DKTests\Presenters\BasePresenter
	 */
	private function createPresenter(Container $container, $name)
	{
		$presenterFactory = $container->getByType('Nette\Application\IPresenterFactory');
		$presenter = $presenterFactory->createPresenter($name);		/** @var $presenter \DKTests\Presenters\BasePresenter */

		$presenter->autoCanonicalize = false;
		$presenter->invalidLinkMode = Presenter::INVALID_LINK_EXCEPTION;

		return $presenter;
	}


	/**
	 * @param string $name
	 * @param string $action
	 * @param array $parameters
	 * @return \Nette\Application\Request
	 */
	private function createRequest($name, $action, $parameters = array())
	{
		$parameters['action'] = $action;
		return new Request($name, 'GET', $parameters);
	}


	public function testControl()
	{
		$container = $this->createContainer('menu');
		$presenter = $this->createPresenter($container, 'Homepage');
		$request = $this->createRequest('Homepage', 'default');

		$response = $presenter->run($request);		/** @var $response \Nette\Application\Responses\TextResponse */

		Assert::true($response instanceof TextResponse);
		Assert::true($response->getSource() instanceof ITemplate);

		$html = (string) $response->getSource();
		$dom = DomQuery::fromHtml($html);

		Assert::same(1, count($dom->find('#breadcrumb a')));

		Assert::true($dom->has('#menu ul'));
		Assert::same(7, count($dom->find('#menu ul li')));
	}


	public function testCurrent()
	{
		$container = $this->createContainer('menu');
		$presenter = $this->createPresenter($container, 'Homepage');
		$request = $this->createRequest('Homepage', 'default');

		$presenter->run($request);

		$menu = $presenter->getMenu();
		$current = $menu->getLastCurrentItem();

		Assert::same('Home', $current->getTitle());
	}


	public function testCurrentNested()
	{
		$container = $this->createContainer('menu');
		$presenter = $this->createPresenter($container, 'Book');
		$request = $this->createRequest('Book', 'authorsPopular');

		$presenter->run($request);

		$menu = $presenter->getMenu();
		$current = $menu->getLastCurrentItem();

		Assert::same('Popular authors', $current->getTitle());
		Assert::same('Book:authorsPopular', $current->getTarget());
	}


	public function testCurrentIncludedRegexp()
	{
		$container = $this->createContainer('menu');
		$presenter = $this->createPresenter($container, 'User');
		$request = $this->createRequest('User', 'login');

		$presenter->run($request);

		$menu = $presenter->getMenu();
		$current = $menu->getLastCurrentItem();

		Assert::same('Users', $current->getTitle());
		Assert::same('User:default', $current->getTarget());
	}


	public function testCurrentIncludedArray()
	{
		$container = $this->createContainer('menu');
		$presenter = $this->createPresenter($container, 'About');
		$request = $this->createRequest('About', 'terms');

		$presenter->run($request);

		$menu = $presenter->getMenu();
		$current = $menu->getLastCurrentItem();

		Assert::same('About us', $current->getTitle());
		Assert::same('About:default', $current->getTarget());
	}


	public function testTranslator()
	{
		$container = $this->createContainer('translated');
		$menu = $container->getByType('DK\Menu\Menu');		/** @var $menu \DK\Menu\Menu */

		$item = $menu->getItem('home');

		Assert::same('menu.homepage', $item->getTitle());
		Assert::same('Homepage', $item->getTranslatedTitle());
	}


	public function testPath()
	{
		$container = $this->createContainer('menu');
		$presenter = $this->createPresenter($container, 'Book');
		$request = $this->createRequest('Book', 'anonymousAdd');

		$presenter->run($request);

		$menu = $presenter->getMenu();

		$path = array_map(function(Item $item) {
			return $item->getTitle();
		}, $menu->getPath());

		Assert::same(array('Books', 'Anonymous', 'Create'), $path);
	}


	public function testAppendItems()
	{
		$id = 5;

		$container = $this->createContainer('menu');
		$presenter = $this->createPresenter($container, 'Book');
		$request = $this->createRequest('Book', 'anonymousEdit', array('id' => $id));

		$presenter->run($request);

		$menu = $presenter->getMenu();

		// This should be in BookPresenter::actionAnonymousEdit method
		$menu->getItem('books-anon')
			->addItem('How to...', 'Book:anonymousDetail', array('id' => $id))->setVisual(false)
			->addItem('Edit', 'Book:anonymousEdit', array('id' => $id));

		$path = array_map(function(Item $item) {
			return $item->getTitle();
		}, $menu->getPath());

		Assert::same(array('Books', 'Anonymous', 'How to...', 'Edit'), $path);
	}


	public function testAuthLoggedIn()
	{
		$container = $this->createContainer('menu');
		$presenter = $this->createPresenter($container, 'Book');
		$request = $this->createRequest('Book', 'anonymousEdit');

		$presenter->run($request);

		$menu = $presenter->getMenu();
		$settings = $menu->getItem('3');

		Assert::false($settings->isAllowed());
		$settings->invalidate();

		$user = $container->getByType('Nette\Security\User');		/** @var $user \Nette\Security\User */
		$user->login('david');

		Assert::true($settings->isAllowed());
	}


	public function testAuthRoles()
	{
		$container = $this->createContainer('menu');
		$presenter = $this->createPresenter($container, 'Book');
		$request = $this->createRequest('Book', 'anonymousEdit');

		$presenter->run($request);

		$menu = $presenter->getMenu();
		$users = $menu->getItem('3-0');

		Assert::false($users->isAllowed());
		$users->invalidate();

		$user = $container->getByType('Nette\Security\User');		/** @var $user \Nette\Security\User */
		$user->login('john');

		Assert::true($users->isAllowed());
	}


	public function testAuthModule()
	{
		$container = $this->createContainer('menu');
		$presenter = $this->createPresenter($container, 'Books:Homepage');
		$request = $this->createRequest('Books:Homepage', 'default');

		$presenter->run($request);

		$menu = $presenter->getMenu();
		$books = $menu->getItem('3-1');

		Assert::true($books->isAllowed());
	}


	public function testAuthParameters()
	{
		$container = $this->createContainer('menu');
		$presenter = $this->createPresenter($container, 'Settings');
		$request = $this->createRequest('Settings', 'images', array('go' => 'you-can'));

		$presenter->run($request);

		$menu = $presenter->getMenu();
		$books = $menu->getItem('3-2');

		Assert::true($books->isAllowed());
	}

}


class Authenticator implements IAuthenticator
{


	/**
	 * @param array $credentials
	 * @return \Nette\Security\Identity
	 */
	public function authenticate(array $credentials)
	{
		list($user) = $credentials;

		$roles = array();

		if ($user === 'john') {
			$roles = array('normal', 'author', 'admin');
		}

		return new Identity($user, $roles);
	}

}


class Translator implements ITranslator
{


	/**
	 * @param string $message
	 * @param int|null $count
	 * @return string
	 */
	public function translate($message, $count = null)
	{
		if ($message === 'menu.homepage') {
			return 'Homepage';
		}

		return null;
	}

}


\run(new MenuTest);