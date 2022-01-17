<?php
/** @var $arResult */

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

$arResult = array_merge(
    $arResult,
    [
        "componentName" => $this->getComponent()->getName(),
        "methodName" => "getSparePartInfo",
        "signedParameters" => $this->getComponent()->getSignedParameters(),
        "sessid" => bitrix_sessid()
    ]
);
