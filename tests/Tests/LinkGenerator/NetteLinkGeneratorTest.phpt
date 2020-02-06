<?php

declare(strict_types=1);

namespace Tests\LinkGenerator;

use Contributte\MenuControl\LinkGenerator\NetteLinkGenerator;
use Mockery\MockInterface;
use Tester\Assert;
use Tests\AbstractTestCase;

require_once __DIR__. '/../../bootstrap.php';

final class NetteLinkGeneratorTest extends AbstractTestCase
{

	public function testLink_action(): void
	{
		$netteLinkGenerator = $this->createMockNetteLinkGenerator(function(MockInterface $netteLinkGenerator) {
			$netteLinkGenerator->shouldReceive('link')->andReturn('/');
		});

		$item = $this->createMockMenuItem(function(MockInterface $item) {
			$item->shouldReceive('getAction')->andReturn(':Home:default');
			$item->shouldReceive('getActionParameters')->andReturn([]);
		});

		$linkGenerator = new NetteLinkGenerator($netteLinkGenerator);

		Assert::same('/', $linkGenerator->link($item));
	}


	public function testLink_link(): void
	{
		$netteLinkGenerator = $this->createMockNetteLinkGenerator(function(MockInterface $netteLinkGenerator) {
			$netteLinkGenerator->shouldReceive('link')->andReturn('/');
		});

		$item = $this->createMockMenuItem(function(MockInterface $item) {
			$item->shouldReceive('getAction')->andReturn(null);
			$item->shouldReceive('getLink')->andReturn('/');
		});

		$linkGenerator = new NetteLinkGenerator($netteLinkGenerator);

		Assert::same('/', $linkGenerator->link($item));
	}


	public function testLink(): void
	{
		$netteLinkGenerator = $this->createMockNetteLinkGenerator(function(MockInterface $netteLinkGenerator) {
			$netteLinkGenerator->shouldReceive('link')->andReturn('/');
		});

		$item = $this->createMockMenuItem(function(MockInterface $item) {
			$item->shouldReceive('getAction')->andReturn(null);
			$item->shouldReceive('getLink')->andReturn(null);
		});

		$linkGenerator = new NetteLinkGenerator($netteLinkGenerator);

		Assert::same('#', $linkGenerator->link($item));
	}

}

(new NetteLinkGeneratorTest)->run();
