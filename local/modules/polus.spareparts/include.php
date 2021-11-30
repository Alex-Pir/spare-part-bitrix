<?php

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
	die();
}

// citrus.restsale
define('CITRUS_SITE_REST_SALE_MODULE', basename(__DIR__));
// вместо __DIR__ проверяем путь с помощью getLocalPath чтобы избежать проблем с символическими ссылками
define('CITRUS_SITE_REST_SALE_BX_ROOT', strpos(getLocalPath('modules/' . CITRUS_SITE_REST_SALE_MODULE), '/local') === 0 ? '/local' : BX_ROOT);
define('CITRUS_SITE_REST_SALE_MODULE_DIR', CITRUS_SITE_REST_SALE_BX_ROOT . '/modules/' . CITRUS_SITE_REST_SALE_MODULE);