<?php

declare(strict_types=1);

namespace Contributte\MenuControlTests\Cases;

use Contributte\MenuControl\IMenuItem;
use Contributte\MenuControl\MenuItemFactory;
use Contributte\MenuControlTests\AbstractTestCase;
use Tester\Assert;

require_once __DIR__. '/../bootstrap.php';

/**
 * @testCase
 */
final class MenuItemFactoryTest extends AbstractTestCase
{

	public function testCreate(): void
	{
		$menu = $this->createMockMenu();
		$linkGenerator = $this->createMockLinkGenerator();
		$translator = $this->createMockTranslator();
		$authorizator = $this->createMockAuthorizator();
		$itemFactory = $this->createMockMenuItemFactory();

		$factory = new MenuItemFactory;
		$item = $factory->create($menu, $linkGenerator, $translator, $authorizator, $itemFactory, 'item');

		Assert::type(IMenuItem::class, $item);
	}

}

(new MenuItemFactoryTest)->run();
