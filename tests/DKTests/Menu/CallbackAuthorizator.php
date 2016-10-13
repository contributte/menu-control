<?php

namespace DKTests\Menu;

use DK\Menu\Item;

/**
 *
 * @author David Kudera <kudera.d@gmail.com>
 */
class CallbackAuthorizator
{


	public $result = true;


	/**
	 * @param \DK\Menu\Item $item
	 * @return bool
	 */
	public function check(Item $item)
	{
		return $this->result;
	}

}
