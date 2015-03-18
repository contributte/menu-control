<?php

namespace DKTests\Presenters;

use Nette\Application\UI\Presenter;
use Nette\DI\Container;

/**
 *
 * @author David Kudera
 */
class BasePresenter extends Presenter
{


	/** @var \DK\Menu\UI\IControlFactory */
	private $menuFactory;


	/**
	 * @param \Nette\DI\Container $context
	 */
	public function __construct(Container $context)
	{
		parent::__construct();

		$this->menuFactory = $context->getByType('DK\Menu\UI\IControlFactory');		// just for disabling warning about more than one services of same type
	}


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
