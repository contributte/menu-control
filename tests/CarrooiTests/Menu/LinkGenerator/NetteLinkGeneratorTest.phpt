<?php

declare(strict_types=1);

namespace CarrooiTests\Menu\LinkGenerator;

use Carrooi\Menu\LinkGenerator\NetteLinkGenerator;
use CarrooiTests\TestCase;
use Mockery\MockInterface;
use Tester\Assert;

require_once __DIR__. '/../../../bootstrap.php';

/**
 * @author David Kudera <kudera.d@gmail.com>
 */
final class NetteLinkGeneratorTest extends TestCase
{


	public function testLink_action(): void
	{
		$application = $this->createMockApplication(function(MockInterface $application) {
			$application->shouldReceive('getPresenter')->andReturn(
				$this->createMockPresenter(function(MockInterface $presenter) {
					$presenter->shouldReceive('link')->andReturn('/');
				})
			);
		});

		$item = $this->createMockMenuItem(function(MockInterface $item) {
			$item->shouldReceive('getAction')->andReturn(':Home:default');
			$item->shouldReceive('getActionParameters')->andReturn([]);
		});

		$linkGenerator = new NetteLinkGenerator($application);

		Assert::same('/', $linkGenerator->link($item));
	}


	public function testLink_link(): void
	{
		$application = $this->createMockApplication();

		$item = $this->createMockMenuItem(function(MockInterface $item) {
			$item->shouldReceive('getAction')->andReturn(null);
			$item->shouldReceive('getLink')->andReturn('/');
		});

		$linkGenerator = new NetteLinkGenerator($application);

		Assert::same('/', $linkGenerator->link($item));
	}


	public function testLink(): void
	{
		$application = $this->createMockApplication();

		$item = $this->createMockMenuItem(function(MockInterface $item) {
			$item->shouldReceive('getAction')->andReturn(null);
			$item->shouldReceive('getLink')->andReturn(null);
		});

		$linkGenerator = new NetteLinkGenerator($application);

		Assert::same('#', $linkGenerator->link($item));
	}

}

(new NetteLinkGeneratorTest)->run();
