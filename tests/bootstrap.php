<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

require_once __DIR__. '/CarrooiTests/TestCase.php';

Tester\Environment::setup();
Tester\Environment::bypassFinals();
date_default_timezone_set('Europe/Prague');

define('TEMP_DIR', __DIR__. '/tmp/'. getmypid());
Tester\Helpers::purge(TEMP_DIR);
