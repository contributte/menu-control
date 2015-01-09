<?php

namespace DK\Menu\UI;

/**
 *
 * @author David Kudera
 */
interface IControlFactory
{


	/**
	 * @return \DK\Menu\UI\Control
	 */
	public function create();

}
