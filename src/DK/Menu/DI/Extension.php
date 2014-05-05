<?php

namespace DK\Menu\DI;

use Nette\DI\CompilerExtension;
use Nette\DI\Config\Helpers;
use Nette\DI\Compiler;
use Nette\Configurator;
use Nette\Application\Application;
use Nette\Security\User;
use DK\Menu\Menu;
use DK\Menu\Container;

/**
 *
 * @author David Kudera
 */
class Extension extends CompilerExtension
{


	/** @var array  */
	private $defaults = array(
		'default' => array(),
	);

	/** @var array  */
	private $menuDefaults = array(
		'controlClass' => 'DK\Menu\UI\Control',
		'controlInterface' => 'DK\Menu\UI\IControlFactory',
		'template' => array(
			'menu' => null,				// see constructor
			'breadcrumb' => null,
		),
		'allow' => array(
			'loggedIn' => null,
			'roles' => array(),
			'module' => null,
			'parameters' => array(),
		),
		'items' => array(),
	);

	/** @var array  */
	private $itemDefaults = array(
		'name' => null,
		'title' => null,
		'target' => null,
		'parameters' => array(),
		'include' => null,
		'visual' => true,
		'allow' => null,		// loaded from defaults in neon config
		'items' => array(),
	);


	public function __construct()
	{
		$this->menuDefaults['template']['menu'] = __DIR__. '/../UI/menu.latte';
		$this->menuDefaults['template']['breadcrumb'] = __DIR__. '/../UI/breadcrumb.latte';
	}


	public function loadConfiguration()
	{
		$config = $this->getConfig($this->defaults);
		$builder = $this->getContainerBuilder();

		$autowired = count($config) === 1;

		foreach ($config as $name => $data) {
			$data = Helpers::merge($data, $builder->expand($this->menuDefaults));

			$this->itemDefaults['allow'] = $data['allow'];

			$data['items'] = $this->parseItems($data['items']);

			$builder->addDefinition($this->prefix($name))
				->setClass('DK\Menu\Menu')
				->setFactory('DK\Menu\DI\Extension::createMenu', array($name, $data['items']))
				->setAutowired($autowired);

			$builder->addDefinition($this->prefix($name. 'Control'))
				->setClass($data['controlClass'], array('@'. $this->prefix($name)))
				->addSetup('setMenuTemplate', array($data['template']['menu']))
				->addSetup('setBreadcrumbTemplate', array($data['template']['breadcrumb']))
				->setImplement($data['controlInterface']);
		}
	}


	/**
	 * @param array $items
	 * @return array
	 */
	public function parseItems(array $items)
	{
		$builder = $this->getContainerBuilder();
		$defaults = $this->itemDefaults;
		$_this = $this;

		array_walk($items, function(&$item, $name) use ($builder, $defaults, $_this) {
			if (is_string($item)) {
				$item = array(
					'title' => null,
					'target' => $item,
				);
			}

			$item['name'] = $name;

			$item = Helpers::merge($item, $builder->expand($defaults));

			if ($item['title'] === null) {
				$item['name'] = null;
				$item['title'] = $name;
			}

			if (count($item['items']) > 0) {
				$item['items'] = $_this->parseItems($item['items']);
			}
		});

		return $items;
	}


	/**
	 * @param string $name
	 * @param array $items
	 * @param \Nette\Application\Application $application
	 * @param \Nette\Security\User $user
	 * @return \DK\Menu\Menu
	 */
	public static function createMenu($name, array $items, Application $application, User $user)
	{
		$menu = new Menu($user, $name);
		$menu->init($application);

		self::addItemsToParent($menu, $items);

		return $menu;
	}


	/**
	 * @param \DK\Menu\Container $parent
	 * @param array $items
	 */
	private static function addItemsToParent(Container $parent, array $items)
	{
		foreach ($items as $data) {
			$item = $parent->addItem($data['title'], $data['target'], $data['parameters'], $data['name']);

			if ($data['include'] !== null) {
				$item->setInclude($data['include']);
			}

			if ($data['visual'] !== null) {
				$item->setVisual($data['visual']);
			}

			if ($data['allow']['loggedIn'] !== null) {
				$item->setAllowedForLoggedIn($data['allow']['loggedIn']);
			}

			if (count($data['allow']['roles']) > 0) {
				$item->setAllowedForRoles($data['allow']['roles']);
			}

			if ($data['allow']['module'] !== null) {
				$item->setAllowedForModule($data['allow']['module']);
			}

			if (count($data['allow']['parameters']) > 0) {
				$item->setAllowedForParameters($data['allow']['parameters']);
			}

			if (count($data['items']) > 0) {
				self::addItemsToParent($item, $data['items']);
			}
		}
	}


	/**
	 * @param \Nette\Configurator $configurator
	 */
	public static function register(Configurator $configurator)
	{
		$configurator->onCompile[] = function($config, Compiler $compiler) {
			$compiler->addExtension('menu', new Extension);
		};
	}

} 