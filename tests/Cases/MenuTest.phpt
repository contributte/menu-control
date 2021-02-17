<?php

declare(strict_types=1);

namespace Contributte\MenuControlTests\Cases;

use Contributte\MenuControl\Menu;
use Contributte\MenuControlTests\AbstractTestCase;
use Mockery\MockInterface;
use Tester\Assert;
use Tester\Environment;

require_once __DIR__. '/../bootstrap.php';

/**
 * @testCase
 */
final class MenuTest extends AbstractTestCase
{

	public function testInit(): void
	{
		Environment::$checkAssertions = false;

		$linkGenerator = $this->createMockLinkGenerator();
		$translator = $this->createMockTranslator();
		$authorizator = $this->createMockAuthorizator();
		$itemFactory = $this->createMockMenuItemFactory();
		$templateConfig = $this->createMockTemplateConfig();

		$loader = $this->createMockMenuLoader(function (MockInterface $loader): void {
			$loader->shouldReceive('load');
		});

		$menu = new Menu(
			$linkGenerator,
			$translator,
			$authorizator,
			$itemFactory,
			$loader,
			'menu',
			$templateConfig
		);
		$menu->init();
	}

	public function testGetName(): void
	{
		$linkGenerator = $this->createMockLinkGenerator();
		$translator = $this->createMockTranslator();
		$authorizator = $this->createMockAuthorizator();
		$itemFactory = $this->createMockMenuItemFactory();
		$loader = $this->createMockMenuLoader();
		$templateConfig = $this->createMockTemplateConfig();

		$menu = new Menu(
			$linkGenerator,
			$translator,
			$authorizator,
			$itemFactory,
			$loader,
			'menu',
			$templateConfig
		);

		Assert::same('menu', $menu->getName());
	}

	public function testTemplates(): void
	{
		$linkGenerator = $this->createMockLinkGenerator();
		$translator = $this->createMockTranslator();
		$authorizator = $this->createMockAuthorizator();
		$itemFactory = $this->createMockMenuItemFactory();
		$loader = $this->createMockMenuLoader();
		$templateConfig = $this->createMockTemplateConfig();

		$menu = new Menu(
			$linkGenerator,
			$translator,
			$authorizator,
			$itemFactory,
			$loader,
			'menu',
			$templateConfig
		);

		Assert::same('menu-template.latte', $menu->getMenuTemplate());
		Assert::same('breadcrumbs-template.latte', $menu->getBreadcrumbsTemplate());
		Assert::same('sitemap-template.latte', $menu->getSitemapTemplate());
	}

	public function testGetPath(): void
	{
		$linkGenerator = $this->createMockLinkGenerator();
		$translator = $this->createMockTranslator();
		$authorizator = $this->createMockAuthorizator();
		$loader = $this->createMockMenuLoader();
		$templateConfig = $this->createMockTemplateConfig();

		$itemC = $this->createMockMenuItem(function (MockInterface $itemC): void {
			$itemC->shouldReceive('isAllowed')->andReturn(true);
			$itemC->shouldReceive('isActive')->andReturn(true);
			$itemC->shouldReceive('findActiveItem')->andReturn(null);
		});

		$itemB = $this->createMockMenuItem(function (MockInterface $itemB) use ($itemC): void {
			$itemB->shouldReceive('isAllowed')->andReturn(true);
			$itemB->shouldReceive('isActive')->andReturn(true);
			$itemB->shouldReceive('findActiveItem')->andReturn($itemC);
		});

		$itemA = $this->createMockMenuItem(function (MockInterface $itemA) use ($itemB): void {
			$itemA->shouldReceive('isAllowed')->andReturn(true);
			$itemA->shouldReceive('isActive')->andReturn(true);
			$itemA->shouldReceive('findActiveItem')->andReturn($itemB);
		});

		$itemFactory = $this->createMockMenuItemFactory(function (MockInterface $itemFactory) use ($itemA): void {
			$itemFactory->shouldReceive('create')->andReturn($itemA);
		});

		$menu = new Menu(
			$linkGenerator,
			$translator,
			$authorizator,
			$itemFactory,
			$loader,
			'menu',
			$templateConfig
		);
		$menu->addItem('a','ItemA');

		Assert::equal([
			$itemA,
			$itemB,
			$itemC,
		], $menu->getPath());
	}

}

(new MenuTest)->run();
