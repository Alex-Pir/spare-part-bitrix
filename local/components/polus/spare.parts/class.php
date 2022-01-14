<?php

use Bitrix\Main\Localization\Loc;
use Bitrix\Main;
use Bitrix\Main\Engine\ActionFilter;
use Polus\SpareParts\Constants;
use Polus\SpareParts\Tools\ElementHelper;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

Loc::loadMessages(__FILE__);

/**
 * Компонент показывает схему запасных частей, привязанных к элементу инфоблока
 */
class PolusSparePartsComponent extends CBitrixComponent implements Main\Engine\Contract\Controllerable {

    /** @var string модуль запасных частей */
    const SPARE_PARTS_MODULE = "polus.spareparts";

    public function configureActions() {
        return [
            "getSparePartInfo" => [
                "prefilters" => [
                    new ActionFilter\HttpMethod(
                        [ActionFilter\HttpMethod::METHOD_POST]
                    ),
                    new ActionFilter\Csrf()
                ]
            ]
        ];
    }

    /**
     * Параметры, которые можно использовать при
     * REST-взаимодействии с компонентом
     *
     * @return string[]
     */
    protected function listKeysSignedParameters(): array {
        return [
            "IBLOCK_ID",
            "ELEMENT_ID"
        ];
    }

    /**
     * Подготовка параметров компонента
     *
     * @param $arParams
     * @return array
     * @throws Main\SystemException
     */
    public function onPrepareComponentParams($arParams): array {
        $this->includeModule();

        if (!$arParams["IBLOCK_ID"] || !$arParams["ELEMENT_ID"]) {
            throw new Main\SystemException(Loc::getMessage(""));
        }

        if (!isset($arParams["CACHE_TIME"])) {
            $arParams["CACHE_TIME"] = Constants::CACHE_TIME_LONG;
        }

        return $arParams;
    }

    /**
     * Выполнение компонента
     *
     * @return mixed|void|null
     * @throws Main\LoaderException
     */
    public function executeComponent() {
        $this->includeModule();

        try {
            if ($this->startResultCache()) {
                $this->arResult = [
                    "file" => array_merge(
                        ElementHelper::getFile($this->arParams["IBLOCK_ID"], $this->arParams["ELEMENT_ID"]),
                        [
                            "title" => $this->arParams["FILE_TITLE"],
                            "alt" => $this->arParams["FILE_ALT"]
                        ]
                    ),
                    "points" => ElementHelper::getPoints(
                        $this->arParams["IBLOCK_ID"],
                        $this->arParams["ELEMENT_ID"]
                    )
                ];

                $this->includeComponentTemplate();
            }
        } catch (Exception $ex) {
            AddMessage2Log($ex->getMessage());
            $this->abortResultCache();
            return;
        }
    }

    /**
     * Подключение модуля
     *
     * @return bool
     * @throws Main\LoaderException
     */
    protected function includeModule(): bool {
        if (!Main\Loader::includeModule(static::SPARE_PARTS_MODULE)) {
            throw new Main\LoaderException(Loc::getMessage("SPARE_PART_CLASS_MODULE_NOT_FOUND"));
        }

        return true;
    }

    public function getSparePartInfoAction(int $elementId): array {
        try {
            return ElementHelper::getIblockElementData($this->arParams["IBLOCK_ID"], $elementId);
        } catch (Exception $ex) {
            AddMessage2Log($ex->getMessage());
        }

        return [];
    }
}