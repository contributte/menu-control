<?php

declare(strict_types=1);

namespace Contributte\MenuControlTests\Cases\LinkGenerator;

use Contributte\MenuControl\LinkGenerator\NetteLinkGenerator;
use Contributte\MenuControlTests\AbstractTestCase;
use Mockery\MockInterface;
use Tester\Assert;

require_once __DIR__. '/../../bootstrap.php';

final class NetteLinkGeneratorTest extends AbstractTestCase
{

	public function testLink_action(): void
	{
		$request = $this->createMockHttpRequest();
		$netteLinkGenerator = $this->createMockNetteLinkGenerator(function (MockInterface $netteLinkGenerator): void {
			$netteLinkGenerator->shouldReceive('link')->andReturn('/');
		});

		$item = $this->createMockMenuItem(function (MockInterface $item): void {
			$item->shouldReceive('getActionTarget')->andReturn('Home:default');
			$item->shouldReceive('getActionParameters')->andReturn([]);
		});

		$linkGenerator = new NetteLinkGenerator($request, $netteLinkGenerator);

		Assert::same('/', $linkGenerator->link($item));
	}

	public function testAbsoluteLink_action(): void
	{
		$request = $this->createMockHttpRequest(function (MockInterface $request): void {
			$request->shouldReceive('getUrl')->andReturn(
				$this->createMockHttpUrl(function (MockInterface $url): void {
					$url->shouldReceive('getScheme')->andReturn('https');
					$url->shouldReceive('getHost')->andReturn('localhost');
					$url->shouldReceive('getPort')->andReturn(80);
				})
			);
		});
		$netteLinkGenerator = $this->createMockNetteLinkGenerator(function (MockInterface $netteLinkGenerator): void {
			$netteLinkGenerator->shouldReceive('link')->andReturn('/');
		});

		$item = $this->createMockMenuItem(function (MockInterface $item): void {
			$item->shouldReceive('getActionTarget')->andReturn('Home:default');
			$item->shouldReceive('getActionParameters')->andReturn([]);
		});

		$linkGenerator = new NetteLinkGenerator($request, $netteLinkGenerator);

		Assert::same('https://localhost/', $linkGenerator->absoluteLink($item));
	}

	public function testLink_link(): void
	{
		$request = $this->createMockHttpRequest();
		$netteLinkGenerator = $this->createMockNetteLinkGenerator(function (MockInterface $netteLinkGenerator): void {
			$netteLinkGenerator->shouldReceive('link')->andReturn('/');
		});

		$item = $this->createMockMenuItem(function (MockInterface $item): void {
			$item->shouldReceive('getActionTarget')->andReturn(null);
			$item->shouldReceive('getLink')->andReturn('/');
		});

		$linkGenerator = new NetteLinkGenerator($request, $netteLinkGenerator);

		Assert::same('/', $linkGenerator->link($item));
	}

	public function testLink_empty(): void
	{
		$request = $this->createMockHttpRequest();
		$netteLinkGenerator = $this->createMockNetteLinkGenerator();

		$item = $this->createMockMenuItem(function (MockInterface $item): void {
			$item->shouldReceive('getActionTarget')->andReturn(null);
			$item->shouldReceive('getLink')->andReturn(null);
		});

		$linkGenerator = new NetteLinkGenerator($request, $netteLinkGenerator);

		Assert::same('#', $linkGenerator->link($item));
	}

}

(new NetteLinkGeneratorTest)->run();
