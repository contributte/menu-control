<?php declare(strict_types = 1);

namespace Contributte\MenuControl\DI;

use Contributte\MenuControl\Config\MenuVisibility;
use Contributte\MenuControl\Config\TemplatePaths;
use Contributte\MenuControl\LinkGenerator\NetteLinkGenerator;
use Contributte\MenuControl\Loaders\DefaultMenuLoader;
use Contributte\MenuControl\Localization\ReturnTranslator;
use Contributte\MenuControl\Menu;
use Contributte\MenuControl\MenuContainer;
use Contributte\MenuControl\MenuItemFactory;
use Contributte\MenuControl\Security\OptimisticAuthorizator;
use Contributte\MenuControl\UI\MenuComponentFactory;
use Nette\DI\CompilerExtension;
use Nette\DI\ContainerBuilder;
use Nette\DI\Definitions\ServiceDefinition;
use Nette\Localization\Translator;
use Nette\Schema\Expect;
use Nette\Schema\Processor;
use Nette\Schema\Schema;
use stdClass;

final class MenuExtension extends CompilerExtension
{

	public function getConfigSchema(): Schema
	{
		return Expect::arrayOf(Expect::structure([
			'authorizator' => Expect::string(OptimisticAuthorizator::class),
			'translator' => Expect::type('string|bool')->default(ReturnTranslator::class),
			'loader' => Expect::string(DefaultMenuLoader::class),
			'linkGenerator' => Expect::string(NetteLinkGenerator::class),
			'templates' => Expect::from(new TemplatePaths()),
			'items' => Expect::array()->required(),
		]));
	}

	public function getItemSchema(): Schema
	{
		return Expect::structure([
			'title' => Expect::string(),
			'action' => Expect::type('string|array'),
			'link' => Expect::string(),
			'include' => Expect::type('string|array'),
			'data' => Expect::arrayOf('mixed', 'string'),
			'items' => Expect::array(),
			'visibility' => Expect::from(new MenuVisibility()),
		]);
	}

	public function loadConfiguration(): void
	{
		/** @var array<string, stdClass> $config */
		$config = $this->getConfig();
		$builder = $this->getContainerBuilder();
		$processor = new Processor();

		$container = $builder->addDefinition($this->prefix('container'))
			->setType(MenuContainer::class);

		$builder->addDefinition($this->prefix('component.factory'))
			->setType(MenuComponentFactory::class);

		foreach ($config as $menuName => $menu) {
			$container->addSetup('addMenu', [
				$this->loadMenuConfiguration($builder, $processor, $menuName, $menu),
			]);
		}
	}

	private function loadMenuConfiguration(
		ContainerBuilder $builder,
		Processor $processor,
		string $menuName,
		stdClass $config
	): ServiceDefinition
	{
		$translator = $config->translator;
		$authorizator = $config->authorizator;
		$loader = $config->loader;
		$linkGenerator = $config->linkGenerator;

		if ($config->translator === true) {
			$translator = $builder->getDefinitionByType(Translator::class);

		} elseif (!str_starts_with($config->translator, '@')) {
			$translator = $builder->addDefinition($this->prefix('menu.' . $menuName . '.translator'))
				->setType($config->translator)
				->setAutowired(false);
		}

		if (!str_starts_with($config->authorizator, '@')) {
			$authorizator = $builder->addDefinition($this->prefix('menu.' . $menuName . '.authorizator'))
				->setType($config->authorizator)
				->setAutowired(false);
		}

		if (!str_starts_with($config->loader, '@')) {
			$loader = $builder->addDefinition($this->prefix('menu.' . $menuName . '.loader'))
				->setType($config->loader)
				->setAutowired(false);
		}

		if (!str_starts_with($config->linkGenerator, '@')) {
			$linkGenerator = $builder->addDefinition($this->prefix('menu.' . $menuName . '.linkGenerator'))
				->setType($config->linkGenerator)
				->setAutowired(false);
		}

		if ($loader->getType() === DefaultMenuLoader::class) {
			$loader->setArguments([$this->normalizeMenuItems($processor, $config->items)]);
		}

		$itemFactory = $builder->addDefinition($this->prefix('menu.' . $menuName . '.factory'))
			->setType(MenuItemFactory::class);

		return $builder->addDefinition($this->prefix('menu.' . $menuName))
			->setType(Menu::class)
			->setArguments([
				$linkGenerator,
				$translator,
				$authorizator,
				$itemFactory,
				$loader,
				$menuName,
				$config->templates,
			])
			->addSetup('init')
			->setAutowired(false);
	}

	/**
	 * @param array<mixed> $items
	 * @return array<mixed>
	 */
	private function normalizeMenuItems(Processor $processor, array $items): array
	{
		array_walk(
			$items,
			// @phpcs:ignore SlevomatCodingStandard.PHP.DisallowReference.DisallowedPassingByReference
			function (&$item, string $key) use ($processor): void {
				$item = $processor->process($this->getItemSchema(), $item);
				assert($item instanceof stdClass);

				if ($item->title === null) {
					$item->title = $key;
				}

				$item->items = $this->normalizeMenuItems($processor, $item->items);
			}
		);

		return $items;
	}

}
