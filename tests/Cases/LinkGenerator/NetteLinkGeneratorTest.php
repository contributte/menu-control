<?php declare(strict_types = 1);

namespace Tests\Cases\LinkGenerator;

use Contributte\MenuControl\LinkGenerator\NetteLinkGenerator;
use Mockery\MockInterface;
use Nette\Application\UI\InvalidLinkException;
use Tester\Assert;
use Tests\Toolkit\AbstractTestCase;

require_once __DIR__ . '/../../bootstrap.php';

final class NetteLinkGeneratorTest extends AbstractTestCase
{

	public function testLinkAction(): void
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

	public function testAbsoluteLinkAction(): void
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

	public function testLinkLink(): void
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

	public function testLinkActionWithInvalidGeneratedLinkFallsBackToItemLink(): void
	{
		$request = $this->createMockHttpRequest();
		$netteLinkGenerator = $this->createMockNetteLinkGenerator(function (MockInterface $netteLinkGenerator): void {
			$netteLinkGenerator->shouldReceive('link')->andThrow(new InvalidLinkException());
		});

		$item = $this->createMockMenuItem(function (MockInterface $item): void {
			$item->shouldReceive('getActionTarget')->andReturn('Home:default');
			$item->shouldReceive('getActionParameters')->andReturn([]);
			$item->shouldReceive('getLink')->andReturn('/fallback');
		});

		$linkGenerator = new NetteLinkGenerator($request, $netteLinkGenerator);

		Assert::same('/fallback', $linkGenerator->link($item));
	}

	public function testLinkEmpty(): void
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

(new NetteLinkGeneratorTest())->run();
