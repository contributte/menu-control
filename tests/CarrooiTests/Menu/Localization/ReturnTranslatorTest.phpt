<?php

declare(strict_types=1);

namespace CarrooiTests\Menu\Localization;

use Contributte\MenuControl\Localization\ReturnTranslator;
use CarrooiTests\TestCase;
use Tester\Assert;

require_once __DIR__. '/../../../bootstrap.php';

/**
 * @testCase
 *
 * @author David Kudera <kudera.d@gmail.com>
 */
final class ReturnTranslatorTest extends TestCase
{


	public function testTranslate(): void
	{
		$translator = new ReturnTranslator;

		Assert::same('message', $translator->translate('message'));
	}

}

(new ReturnTranslatorTest)->run();
