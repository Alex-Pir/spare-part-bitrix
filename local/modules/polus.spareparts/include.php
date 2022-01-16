<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
	die();
}

// polus.spareparts
define('POLUS_SPARE_PARTS_MODULE', basename(__DIR__));
// вместо __DIR__ проверяем путь с помощью getLocalPath чтобы избежать проблем с символическими ссылками
define('POLUS_SPARE_PARTS_BX_ROOT', strpos(getLocalPath('modules/' . POLUS_SPARE_PARTS_MODULE), '/local') === 0 ? '/local' : BX_ROOT);
define('POLUS_SPARE_PARTS_MODULE_DIR', POLUS_SPARE_PARTS_BX_ROOT . '/modules/' . POLUS_SPARE_PARTS_MODULE);
