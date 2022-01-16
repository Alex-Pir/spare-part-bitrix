<?php

use Bitrix\Main\Localization\Loc;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

Loc::loadMessages(__FILE__);

$arComponentDescription = array(
    "NAME" => Loc::getMessage("POLUS_SPARE_PART_COMPONENT_NAME"),
    "DESCRIPTION" => Loc::getMessage("POLUS_SPARE_PART_COMPONENT_DESCRIPTION"),
    "CACHE_PATH" => "Y",
    "PATH" => array(
        "ID" => "polus",
        "NAME" => Loc::getMessage("POLUS_SPARE_PART_COMPONENT_PATH")
    ),
);
