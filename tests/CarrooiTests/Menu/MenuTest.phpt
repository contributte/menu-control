<?php

declare(strict_types=1);

namespace CarrooiTests\Menu;

use Carrooi\Menu\Menu;
use CarrooiTests\TestCase;
use Mockery\MockInterface;
use Tester\Assert;
use Tester\Environment;

require_once __DIR__. '/../../bootstrap.php';

/**
 * @testCase
 */
final class MenuTest extends TestCase
{

	public function testInit(): void
	{
		Environment::$checkAssertions = false;

		$linkGenerator = $this->createMockLinkGenerator();
		$translator = $this->createMockTranslator();
		$authorizator = $this->createMockAuthorizator();
		$request = $this->createMockHttpRequest();
		$itemFactory = $this->createMockMenuItemFactory();

		$loader = $this->createMockMenuLoader(function(MockInterface $loader) {
			$loader->shouldReceive('load');
		});

		$menu = new Menu($linkGenerator, $translator, $authorizator, $request, $itemFactory, $loader, 'menu', '', '', '');
		$menu->init();
	}


	public function testGetName(): void
	{
		$linkGenerator = $this->createMockLinkGenerator();
		$translator = $this->createMockTranslator();
		$authorizator = $this->createMockAuthorizator();
		$request = $this->createMockHttpRequest();
		$itemFactory = $this->createMockMenuItemFactory();
		$loader = $this->createMockMenuLoader();

		$menu = new Menu($linkGenerator, $translator, $authorizator, $request, $itemFactory, $loader, 'menu', '', '', '');

		Assert::same('menu', $menu->getName());
	}


	public function testTemplates(): void
	{
		$linkGenerator = $this->createMockLinkGenerator();
		$translator = $this->createMockTranslator();
		$authorizator = $this->createMockAuthorizator();
		$request = $this->createMockHttpRequest();
		$itemFactory = $this->createMockMenuItemFactory();
		$loader = $this->createMockMenuLoader();

		$menu = new Menu(
			$linkGenerator,
			$translator,
			$authorizator,
			$request,
			$itemFactory,
			$loader,
			'menu',
			'menu-template.latte',
			'breadcrumbs-template.latte',
			'sitemap-template.latte'
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
		$request = $this->createMockHttpRequest();
		$loader = $this->createMockMenuLoader();

		$itemC = $this->createMockMenuItem(function(MockInterface $itemC) {
			$itemC->shouldReceive('isAllowed')->andReturn(true);
			$itemC->shouldReceive('isActive')->andReturn(true);
			$itemC->shouldReceive('findActiveItem')->andReturn(null);
		});

		$itemB = $this->createMockMenuItem(function(MockInterface $itemB) use ($itemC) {
			$itemB->shouldReceive('isAllowed')->andReturn(true);
			$itemB->shouldReceive('isActive')->andReturn(true);
			$itemB->shouldReceive('findActiveItem')->andReturn($itemC);
		});

		$itemA = $this->createMockMenuItem(function(MockInterface $itemA) use ($itemB) {
			$itemA->shouldReceive('isAllowed')->andReturn(true);
			$itemA->shouldReceive('isActive')->andReturn(true);
			$itemA->shouldReceive('findActiveItem')->andReturn($itemB);
		});

		$itemFactory = $this->createMockMenuItemFactory(function(MockInterface $itemFactory) use ($itemA) {
			$itemFactory->shouldReceive('create')->andReturn($itemA);
		});

		$menu = new Menu($linkGenerator, $translator, $authorizator, $request, $itemFactory, $loader, 'menu', '', '', '');
		$menu->addItem('a','ItemA');

		Assert::equal([
			$itemA,
			$itemB,
			$itemC,
		], $menu->getPath());
	}

}

(new MenuTest)->run();
