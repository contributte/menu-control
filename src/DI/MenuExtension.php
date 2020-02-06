<?php

declare(strict_types=1);

namespace Contributte\MenuControl\DI;

use Contributte\MenuControl\LinkGenerator\NetteLinkGenerator;
use Contributte\MenuControl\Loaders\ArrayMenuLoader;
use Contributte\MenuControl\Localization\ReturnTranslator;
use Contributte\MenuControl\Menu;
use Contributte\MenuControl\MenuContainer;
use Contributte\MenuControl\MenuItemFactory;
use Contributte\MenuControl\Security\OptimisticAuthorizator;
use Contributte\MenuControl\UI\IMenuComponentFactory;
use Contributte\MenuControl\UI\MenuComponent;
use Nette\DI\CompilerExtension;
use Nette\DI\ContainerBuilder;
use Nette\DI\Definitions\ServiceDefinition;
use Nette\Http;
use Nette\Localization\ITranslator;
use Nette\Utils\Strings;

final class MenuExtension extends CompilerExtension
{

	/**
	 * @var array
	 */
	private $menuDefaults = [
		'authorizator' => OptimisticAuthorizator::class,
		'translator' => ReturnTranslator::class,
		'loader' => ArrayMenuLoader::class,
		'linkGenerator' => NetteLinkGenerator::class,
		'items' => [],
		'templates' => [
			'menu' => __DIR__. '/../UI/templates/menu.latte',
			'breadcrumbs' => __DIR__. '/../UI/templates/breadcrumbs.latte',
			'sitemap' => __DIR__. '/../UI/templates/sitemap.latte',
		],
	];

	/**
	 * @var array
	 */
	private $itemDefaults = [
		'linkGenerator' => null,
		'title' => null,
		'action' => null,
		'link' => null,
		'include' => null,
		'data' => [],
		'items' => [],
		'visibility' => [
			'menu' => true,
			'breadcrumbs' => true,
			'sitemap' => true,
		],
	];


	public function loadConfiguration(): void
	{
		$config = $this->config;
		$builder = $this->getContainerBuilder();

		$container = $builder->addDefinition($this->prefix('container'))
			->setType(MenuContainer::class);

		$builder->addFactoryDefinition($this->prefix('component.menu'))
			->setImplement(IMenuComponentFactory::class)
			->getResultDefinition()
				->setType(MenuComponent::class);

		foreach ($config as $menuName => $menu) {
			$container->addSetup('addMenu', [
				$this->loadMenuConfiguration($builder, $menuName, $menu),
			]);
		}
	}


	private function loadMenuConfiguration(ContainerBuilder $builder, string $menuName, array $config): ServiceDefinition
	{
		$config = $this->validateConfig($this->menuDefaults, $config);

		$translator = $config['translator'];
		$authorizator = $config['authorizator'];
		$loader = $config['loader'];
		$linkGenerator = $config['linkGenerator'];

		if ($config['translator'] === true) {
			$translator = $builder->getDefinitionByType(ITranslator::class);

		} elseif (!Strings::startsWith($config['translator'], '@')) {
			$translator = $builder->addDefinition($this->prefix('menu.'. $menuName. '.translator'))
				->setType($config['translator'])
				->setAutowired(false);
		}

		if (!Strings::startsWith($config['authorizator'], '@')) {
			$authorizator = $builder->addDefinition($this->prefix('menu.'. $menuName. '.authorizator'))
				->setType($config['authorizator'])
				->setAutowired(false);
		}

		if (!Strings::startsWith($config['loader'], '@')) {
			$loader = $builder->addDefinition($this->prefix('menu.'. $menuName. '.loader'))
				->setType($config['loader'])
				->setAutowired(false);
		}

		if (!Strings::startsWith($config['linkGenerator'], '@')) {
			$linkGenerator = $builder->addDefinition($this->prefix('menu.'. $menuName. '.linkGenerator'))
				->setType($config['linkGenerator'])
				->setAutowired(false);
		}

		if ($loader->getType() === ArrayMenuLoader::class) {
			$loader->setArguments([$this->normalizeMenuItems($config['items'])]);
		}

		$itemFactory = $builder->addDefinition($this->prefix('menu.'. $menuName. '.factory'))
			->setType(MenuItemFactory::class);

		return $builder->addDefinition($this->prefix('menu.' . $menuName))
			->setType(Menu::class)
			->setArguments([
				$linkGenerator,
				$translator,
				$authorizator,
				'@'. Http\IRequest::class,
				$itemFactory,
				$loader,
				$menuName,
				$config['templates']['menu'],
				$config['templates']['breadcrumbs'],
				$config['templates']['sitemap'],
			])
			->addSetup('init')
			->setAutowired(false);
	}


	private function normalizeMenuItems(array $items): array
	{
		array_walk($items, function(array &$item, string $key) {
			$item = $this->validateConfig($this->itemDefaults, $item);

			if ($item['title'] === null) {
				$item['title'] = $key;
			}

			$item['items'] = $this->normalizeMenuItems($item['items']);
		});

		return $items;
	}

}
