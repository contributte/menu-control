<?php

namespace DK\Menu\UI;

use Nette\Application\UI\Control as BaseControl;
use DK\Menu\Menu;

/**
 *
 * @author David Kudera
 */
class Control extends BaseControl
{


	/** @var \DK\Menu\Menu  */
	private $menu;

	/** @var string  */
	private $menuTemplate;

	/** @var string  */
	private $breadcrumbTemplate;


	/**
	 * @param \DK\Menu\Menu $menu
	 */
	public function __construct(Menu $menu)
	{
		parent::__construct();

		$this->menu = $menu;

		$this->menuTemplate = __DIR__. '/menu.latte';
		$this->breadcrumbTemplate = __DIR__. '/breadcrumb.latte';
	}


	/**
	 * @return \DK\Menu\Menu
	 */
	public function getMenu()
	{
		return $this->menu;
	}


	/**
	 * @return string
	 */
	public function getMenuTemplate()
	{
		return $this->menuTemplate;
	}


	/**
	 * @param string $path
	 * @return \DK\Menu\UI\Control
	 */
	public function setMenuTemplate($path)
	{
		$this->menuTemplate = $path;
		return $this;
	}


	/**
	 * @return string
	 */
	public function getBreadcrumbTemplate()
	{
		return $this->breadcrumbTemplate;
	}


	/**
	 * @param string $path
	 * @return \DK\Menu\UI\Control
	 */
	public function setBreadcrumbTemplate($path)
	{
		$this->breadcrumbTemplate = $path;
		return $this;
	}


	public function render()
	{
		$this->template->setFile($this->getMenuTemplate());
		$this->template->menu = $this->menu;
		$this->template->render();
	}


	public function renderBreadcrumb()
	{
		$this->template->setFile($this->getBreadcrumbTemplate());
		$this->template->menu = $this->menu;
		$this->template->render();
	}

} 