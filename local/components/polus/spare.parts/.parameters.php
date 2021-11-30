<?php

/** @var array $arCurrentValues */

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Polus\SpareParts\Constants;
use Polus\SpareParts\Tools\ParametersHelper;


if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

if (!Loader::includeModule("polus.spareparts")) {
    return;
}

Loc::loadMessages(__FILE__);

$parameters = new ParametersHelper();

$iblockTypes = $parameters->getIblockTypes();
$iblocks = [];

if ($arCurrentValues["IBLOCK_TYPE"]) {
    $iblocks = $parameters->getIblocks((string)$arCurrentValues["IBLOCK_TYPE"]);
}


$arComponentParameters = [
    "PARAMETERS" => [
        "IBLOCK_TYPE" => [
            "PARENT" => "BASE",
            "NAME" => Loc::getMessage("POLUS_SPARE_PARTS_COMPONENT_IBLOCK_TYPE"),
            "TYPE" => "LIST",
            "ADDITIONAL_VALUES" => "Y",
            "VALUES" => $iblockTypes,
            "REFRESH" => "Y",
        ],
        "IBLOCK_ID" => [
            "PARENT" => "BASE",
            "NAME" => Loc::getMessage("POLUS_SPARE_PARTS_COMPONENT_IBLOCK_ID"),
            "TYPE" => "LIST",
            "ADDITIONAL_VALUES" => "Y",
            "VALUES" => $iblocks,
            "REFRESH" => "Y",
        ],
        "FILE_TITLE" => [
            "PARENT" => "BASE",
            "NAME" => Loc::getMessage("POLUS_SPARE_PARTS_COMPONENT_FILE_TITLE"),
            "TYPE" => "STRING",
            "DEFAULT" => ""
        ],
        "FILE_ALT" => [
            "PARENT" => "BASE",
            "NAME" => Loc::getMessage("POLUS_SPARE_PARTS_COMPONENT_FILE_ALT"),
            "TYPE" => "STRING",
            "DEFAULT" => ""
        ],
        "CACHE_TIME" => ["DEFAULT" => Constants::CACHE_TIME_LONG]
    ]
];

if ($arCurrentValues["IBLOCK_ID"]) {
    $arComponentParameters["PARAMETERS"]["ELEMENT_ID"] = [
        "PARENT" => "BASE",
        "NAME" => Loc::getMessage("POLUS_SPARE_PARTS_COMPONENT_ELEMENT_ID"),
        "TYPE" => "STRING",
        "DEFAULT" => ""
    ];
}
