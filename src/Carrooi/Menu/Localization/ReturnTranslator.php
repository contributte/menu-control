<?php

declare(strict_types=1);

namespace Carrooi\Menu\Localization;

use Nette\Localization\ITranslator;

/**
 * @author David Kudera <kudera.d@gmail.com>
 */
final class ReturnTranslator implements ITranslator
{

	public function translate($message, ...$parameters): string
	{
		return $message;
	}

}
