<?php

declare(strict_types=1);

namespace CarrooiTests\Menu;

use CarrooiTests\TestCase;
use Mockery\MockInterface;
use Tester\Assert;

require_once __DIR__. '/../../bootstrap.php';

final class AbstractMenuItemsContainerTest extends TestCase
{

	public function testItems(): void
	{
		$menu = $this->createMockMenu();
		$linkGenerator = $this->createMockLinkGenerator();
		$translator = $this->createMockTranslator();
		$authorizator = $this->createMockAuthorizator();
		$request = $this->createMockHttpRequest();
		$item = $this->createMockMenuItem();

		$itemFactory = $this->createMockMenuItemFactory(function(MockInterface $itemFactory) use ($item) {
			$itemFactory->shouldReceive('create')->andReturn($item);
		});

		$container = $this->createPartialMockAbstractMenuItemsContainer(null, [$menu, $linkGenerator, $translator, $authorizator, $request, $itemFactory]);

		Assert::equal([], $container->getItems());

		$container->addItem('item', 'Item');

		Assert::equal([
			'item' => $item,
		], $container->getItems());
	}


	public function testGetItem_direct(): void
	{
		$menu = $this->createMockMenu();
		$linkGenerator = $this->createMockLinkGenerator();
		$translator = $this->createMockTranslator();
		$authorizator = $this->createMockAuthorizator();
		$request = $this->createMockHttpRequest();
		$itemFactory = $this->createMockMenuItemFactory();

		$item = $this->createMockMenuItem();

		$container = $this->createPartialMockAbstractMenuItemsContainer(function(MockInterface $container) use ($item) {
			$container->shouldReceive('getItems')->andReturn(['item' => $item]);
		}, [$menu, $linkGenerator, $translator, $authorizator, $request, $itemFactory]);

		Assert::same($item, $container->getItem('item'));
	}


	public function testGetItem(): void
	{
		$menu = $this->createMockMenu();
		$linkGenerator = $this->createMockLinkGenerator();
		$translator = $this->createMockTranslator();
		$authorizator = $this->createMockAuthorizator();
		$request = $this->createMockHttpRequest();
		$itemFactory = $this->createMockMenuItemFactory();

		$itemC = $this->createMockMenuItem();

		$itemB = $this->createMockMenuItem(function(MockInterface $itemA) use ($itemC) {
			$itemA->shouldReceive('getItem')->withArgs(['c'])->andReturn($itemC);
		});

		$itemA = $this->createMockMenuItem(function(MockInterface $itemA) use ($itemB) {
			$itemA->shouldReceive('getItem')->withArgs(['b'])->andReturn($itemB);
		});

		$container = $this->createPartialMockAbstractMenuItemsContainer(function(MockInterface $container) use ($itemA) {
			$container->shouldReceive('getItems')->andReturn(['a' => $itemA]);
		}, [$menu, $linkGenerator, $translator, $authorizator, $request, $itemFactory]);

		Assert::same($itemC, $container->getItem('a-b-c'));
	}


	public function testFindActiveItem(): void
	{
		$menu = $this->createMockMenu();
		$linkGenerator = $this->createMockLinkGenerator();
		$translator = $this->createMockTranslator();
		$authorizator = $this->createMockAuthorizator();
		$request = $this->createMockHttpRequest();
		$itemFactory = $this->createMockMenuItemFactory();

		$itemA = $this->createMockMenuItem(function(MockInterface $itemA) {
			$itemA->shouldReceive('isAllowed')->andReturn(false);
		});

		$itemB = $this->createMockMenuItem(function(MockInterface $itemB) {
			$itemB->shouldReceive('isAllowed')->andReturn(false);
		});

		$itemC = $this->createMockMenuItem(function(MockInterface $itemC) {
			$itemC->shouldReceive('isAllowed')->andReturn(true);
			$itemC->shouldReceive('isActive')->andReturn(true);
		});

		$container = $this->createPartialMockAbstractMenuItemsContainer(function(MockInterface $container) use ($itemA, $itemB, $itemC) {
			$container->shouldReceive('getItems')->andReturn([
				'a' => $itemA,
				'b' => $itemB,
				'c' => $itemC,
			]);
		}, [$menu, $linkGenerator, $translator, $authorizator, $request, $itemFactory]);

		Assert::same($itemC, $container->findActiveItem());
	}


	public function testVisibleItemsOn(): void
	{
		$menu = $this->createMockMenu();
		$linkGenerator = $this->createMockLinkGenerator();
		$translator = $this->createMockTranslator();
		$authorizator = $this->createMockAuthorizator();
		$request = $this->createMockHttpRequest();
		$itemFactory = $this->createMockMenuItemFactory();

		$itemA = $this->createMockMenuItem(function(MockInterface $itemA) {
			$itemA->shouldReceive('isVisibleOnMenu')->andReturn(false);
			$itemA->shouldReceive('isVisibleOnBreadcrumbs')->andReturn(false);
			$itemA->shouldReceive('isVisibleOnSitemap')->andReturn(false);
		});

		$itemB = $this->createMockMenuItem(function(MockInterface $itemB) {
			$itemB->shouldReceive('isVisibleOnMenu')->andReturn(true);
			$itemB->shouldReceive('isVisibleOnBreadcrumbs')->andReturn(true);
			$itemB->shouldReceive('isVisibleOnSitemap')->andReturn(true);
		});

		$container = $this->createPartialMockAbstractMenuItemsContainer(function(MockInterface $container) use ($itemA, $itemB) {
			$container->shouldReceive('getItems')->andReturn([
				'a' => $itemA,
				'b' => $itemB,
			]);
		}, [$menu, $linkGenerator, $translator, $authorizator, $request, $itemFactory]);

		Assert::true($container->hasVisibleItemsOnMenu());
		Assert::equal(['b' => $itemB], $container->getVisibleItemsOnMenu());

		Assert::true($container->hasVisibleItemsOnBreadcrumbs());
		Assert::equal(['b' => $itemB], $container->getVisibleItemsOnBreadcrumbs());

		Assert::true($container->hasVisibleItemsOnSitemap());
		Assert::equal(['b' => $itemB], $container->getVisibleItemsOnSitemap());
	}

}

(new AbstractMenuItemsContainerTest)->run();
