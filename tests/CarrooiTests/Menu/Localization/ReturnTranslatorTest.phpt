<?php

declare(strict_types=1);

namespace CarrooiTests\Menu\Localization;

use Carrooi\Menu\Localization\ReturnTranslator;
use CarrooiTests\TestCase;
use Tester\Assert;

require_once __DIR__. '/../../../bootstrap.php';

/**
 * @testCase
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
