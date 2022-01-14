<?php

/**
 * @var CUser $USER
 * @var CMain $APPLICATION
 */

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Polus\SpareParts\Options;
use Polus\SpareParts\Settings\ModuleSettings;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

Loc::loadMessages(__FILE__);

$moduleId = "polus.spareparts";

if (!defined('ADMIN_MODULE_NAME') || ADMIN_MODULE_NAME !== $moduleId) {
    define("ADMIN_MODULE_NAME", $moduleId);
}

if (!$USER->IsAdmin()) {
    $APPLICATION->AuthForm(Loc::getMessage("ACCESS_DENIED"));
    return false;
}

try {

    if (!Loader::includeModule($moduleId)) {
        ShowError(Loc::getMessage("POLUS_SPARE_PARTS_OPTION_E_MODULE_NOT_INSTALL"));
    }

    $moduleSettings = ModuleSettings::getInstance();
    $moduleSettings->setModuleId($moduleId);

    $options = Options::getInstance();

    foreach ($options->getTabs() as $tab) {
        $moduleSettings->addTab($tab);
    }

    $moduleSettings->viewSettingsPage();

} catch (Exception $ex) {
    ShowError($ex->getMessage());
}