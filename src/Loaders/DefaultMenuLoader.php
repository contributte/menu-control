<?php declare(strict_types = 1);

namespace Contributte\MenuControl\Loaders;

use Contributte\MenuControl\Config\MenuItemAction;
use Contributte\MenuControl\IMenu;
use Contributte\MenuControl\IMenuItem;
use Contributte\MenuControl\IMenuItemsContainer;
use Nette\Utils\Validators;
use stdClass;

final class DefaultMenuLoader implements IMenuLoader
{

	/** @var array<string, stdClass> */
	private array $items;

	/**
	 * @param array<string, stdClass> $items
	 */
	public function __construct(array $items)
	{
		$this->items = $items;
	}

	public function load(IMenu $menu): void
	{
		$this->processItems($menu, $this->items);
	}

	/**
	 * @param array<string, stdClass> $items
	 */
	private function processItems(IMenuItemsContainer $parent, array $items): void
	{
		foreach ($items as $name => $item) {
			$this->processItem($parent, $name, $item);
		}
	}

	private function processItem(IMenuItemsContainer $parent, string $name, stdClass $config): void
	{
		$parent->addItem($name, $config->title, function (IMenuItem $item) use ($config): void {
			$item->setData($config->data);

			$item->setMenuVisibility($config->visibility->menu);
			$item->setBreadcrumbsVisibility($config->visibility->breadcrumbs);
			$item->setSitemapVisibility($config->visibility->sitemap);

			if ($config->action !== null) {
				if (is_array($config->action)) {
					$item->setAction(MenuItemAction::fromArray($config->action)); // @phpstan-ignore-line
				} else {
					$item->setAction(MenuItemAction::fromString($config->action));
				}
			}

			if ($config->link !== null) {
				$item->setLink($config->link);
			}

			if ($config->include !== null) {
				$include = array_map(function ($include) {
					Validators::assert($include, 'string');

					return $include;
				}, is_array($config->include) ? $config->include : [$config->include]);
				$item->setInclude($include);
			}

			$this->processItems($item, $config->items);
		});
	}

}
