<?php

declare(strict_types=1);

namespace Tests\Localization;

use Contributte\MenuControl\Localization\ReturnTranslator;
use Tester\Assert;
use Tests\AbstractTestCase;

require_once __DIR__. '/../../bootstrap.php';

/**
 * @testCase
 */
final class ReturnTranslatorTest extends AbstractTestCase
{

	public function testTranslate(): void
	{
		$translator = new ReturnTranslator;

		Assert::same('message', $translator->translate('message'));
	}

}

(new ReturnTranslatorTest)->run();
