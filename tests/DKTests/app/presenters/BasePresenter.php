<?php

namespace DKTests\Presenters;

use Nette\Application\UI\Presenter;

/**
 *
 * @author David Kudera
 */
class BasePresenter extends Presenter
{


	/** @var \DK\Menu\UI\IControlFactory @inject */
	public $menuFactory;


	/**
	 * @return \DK\Menu\UI\Control
	 */
	protected function createComponentMenu()
	{
		$control = $this->menuFactory->create();
		$control->getMenu()->setPresenter($this);		// do not call this on your own

		return $control;
	}


	/**
	 * @return \DK\Menu\Menu
	 */
	public function getMenu()
	{
		return $this['menu']->getMenu();
	}

} 