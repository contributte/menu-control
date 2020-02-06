<?php

declare(strict_types=1);

namespace Carrooi\Menu\LinkGenerator;

use Carrooi\Menu\IMenuItem;
use Nette\Application\LinkGenerator;

final class NetteLinkGenerator implements ILinkGenerator
{

	/**
	 * @var LinkGenerator
	 */
	private $linkGenerator;


	public function __construct(LinkGenerator $linkGenerator)
	{
		$this->linkGenerator = $linkGenerator;
	}


	public function link(IMenuItem $item): string
	{
		$action = $item->getAction();
		if ($action !== null) {
			return $this->linkGenerator->link($action, $item->getActionParameters());
		}

		$link = $item->getLink();
		if ($link !== null) {
			return $link;
		}

		return '#';
	}

}
