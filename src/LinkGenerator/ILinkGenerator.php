<?php declare(strict_types = 1);

namespace Contributte\MenuControl\LinkGenerator;

use Contributte\MenuControl\IMenuItem;

interface ILinkGenerator
{

	public function link(IMenuItem $item): string;

	public function absoluteLink(IMenuItem $item): string;

}
