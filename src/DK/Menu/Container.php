<?php

namespace DK\Menu;

use Nette\ComponentModel\Container as BaseContainer;
use Nette\ComponentModel\IContainer;
use Nette\ComponentModel\IComponent;

/**
 *
 * @author David Kudera
 */
abstract class Container extends BaseContainer
{


	/**
	 * @return \DK\Menu\Menu
	 */
	public abstract function getMenu();


	/**
	 * @param \Nette\ComponentModel\IContainer $parent
	 * @throws \DK\Menu\InvalidArgumentException
	 */
	public function validateParent(IContainer $parent)
	{
		if (!$parent instanceof Container) {
			throw new InvalidArgumentException('Parent must be an instance of DK\Menu\Container, '. get_class($parent). ' given.');
		}
	}


	/**
	 * @param \Nette\ComponentModel\IComponent $child
	 * @throws \DK\Menu\InvalidArgumentException
	 */
	public function validateChildComponent(IComponent $child)
	{
		if (!$child instanceof Item) {
			throw new InvalidArgumentException('Child must be an instance of DK\Menu\Item, '. get_class($child). 'given.');
		}
	}


	/**
	 * @return bool
	 */
	public function hasItems()
	{
		return iterator_count($this->getComponents()) > 0;
	}


	/**
	 * @return \ArrayIterator|\DK\Menu\Item[]
	 */
	public function getItems()
	{
		return $this->getComponents();
	}


	/**
	 * @param string $name
	 * @return \DK\Menu\Item
	 */
	public function getItem($name)
	{
		return $this->getComponent($name, false);
	}


	/**
	 * @param string $title
	 * @param string $target
	 * @param array $parameters
	 * @param string $name
	 * @return \DK\Menu\Item
	 */
	public function addItem($title, $target, array $parameters = array(), $name = null)
	{
		$item = new Item($this->getMenu(), $title, $target, $parameters);

		if ($name === null) {
			$name = iterator_count($this->getItems());
		}

		$this->addComponent($item, $name);

		return $item;
	}


	/**
	 * @return bool
	 */
	public function hasVisualItems()
	{
		if (!$this->hasItems()) {
			return false;
		}

		foreach ($this->getItems() as $item) {
			if ($item->isVisual() && $item->isAllowed()) {
				return true;
			}
		}

		return false;
	}


	/**
	 * @return \DK\Menu\Item
	 */
	public function getCurrentItem()
	{
		if (!$this->hasItems()) {
			return null;
		}

		foreach ($this->getItems() as $item) {
			if ($item->isAllowed() && $item->isActive()) {
				return $item;
			}
		}

		return null;
	}

}
