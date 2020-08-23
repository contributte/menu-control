<?php

declare(strict_types=1);

namespace Contributte\MenuControlTests\Localization;

use Contributte\MenuControl\Localization\ReturnTranslator;
use Contributte\MenuControlTests\AbstractTestCase;
use Tester\Assert;

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
