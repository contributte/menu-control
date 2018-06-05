<?php

declare(strict_types=1);

namespace Carrooi\Menu\LinkGenerator;

use Carrooi\Menu\IMenuItem;
use Nette\Application\LinkGenerator;

/**
 * @author David Kudera <kudera.d@gmail.com>
 */
final class NetteLinkGenerator implements ILinkGenerator
{

	/** @var LinkGenerator  */
	private $nativeLinkGenerator;



	public function __construct(LinkGenerator $nativeLinkGenerator)
	{
		$this->nativeLinkGenerator = $nativeLinkGenerator;
	}


	public function link(IMenuItem $item): string
	{
		if (($action = $item->getAction()) !== null) {
			return $this->nativeLinkGenerator->link($action, $item->getActionParameters());

		} elseif (($link = $item->getLink()) !== null) {
			return $link;
		}

		return '#';
	}

}
