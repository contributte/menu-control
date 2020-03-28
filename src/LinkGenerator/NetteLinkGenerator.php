<?php

declare(strict_types=1);

namespace Contributte\MenuControl\LinkGenerator;

use Contributte\MenuControl\IMenuItem;
use Nette\Application\LinkGenerator;

/**
 * @author David Kudera <kudera.d@gmail.com>
 */
final class NetteLinkGenerator implements ILinkGenerator
{


	/** @var \Nette\Application\LinkGenerator */
	private $linkGenerator;


	public function __construct(LinkGenerator $linkGenerator)
	{
		$this->linkGenerator = $linkGenerator;
	}


	public function link(IMenuItem $item): string
	{
		if (($action = $item->getAction()) !== null) {
			return $this->linkGenerator->link($action, $item->getActionParameters());

		} elseif (($link = $item->getLink()) !== null) {
			return $link;
		}

		return '#';
	}

}
