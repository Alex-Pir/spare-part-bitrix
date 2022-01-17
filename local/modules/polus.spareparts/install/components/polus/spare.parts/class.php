<?php

use Bitrix\Main\Config\Option;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main;
use Bitrix\Main\Engine\ActionFilter;
use Polus\SpareParts\Constants;
use Polus\SpareParts\Options;
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

    public function configureActions(): array {
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
            "ELEMENT_ID",
            "SPARE_PARTS_IBLOCK_ID"
        ];
    }

    /**
     * Подготовка параметров компонента
     *
     * @param $arParams
     * @return array
     * @throws Main\ArgumentNullException
     * @throws Main\ArgumentOutOfRangeException
     * @throws Main\LoaderException
     * @throws Main\SystemException
     */
    public function onPrepareComponentParams($arParams): array {
        $this->includeModule();

        $arParams["SPARE_PARTS_IBLOCK_ID"] = Option::get(static::SPARE_PARTS_MODULE, Options::OPTION_SPARE_PART_IBLOCK_ID, false);
        $arParams["IBLOCK_ID"] = Option::get(static::SPARE_PARTS_MODULE, Options::OPTION_PRODUCTS_IBLOCK_ID, false);

        if (!$arParams["SPARE_PARTS_IBLOCK_ID"] || !$arParams["IBLOCK_ID"]) {
            throw new Main\SystemException(Loc::getMessage("SPARE_PART_CLASS_IBLOCK_ID_NOT_FOUND"));
        }

        if (!$arParams["ELEMENT_ID"]) {
            throw new Main\SystemException(Loc::getMessage("SPARE_PART_CLASS_ELEMENT_ID_NOT_FOUND"));
        }

        if (!isset($arParams["CACHE_TIME"])) {
            $arParams["CACHE_TIME"] = Constants::CACHE_TIME_LONG;
        }

        return $arParams;
    }

    /**
     * Выполнение компонента
     *
     * @return void
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
                        $this->arParams["SPARE_PARTS_IBLOCK_ID"],
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

    /**
     * Возвращает информацию по выбранной запасной части
     *
     * @param int $elementId
     * @return array
     */
    public function getSparePartInfoAction(int $elementId): array {
        try {
            return ElementHelper::getIblockElementData($this->arParams["SPARE_PARTS_IBLOCK_ID"], $elementId);
        } catch (Exception $ex) {
            AddMessage2Log($ex->getMessage());
        }

        return [];
    }
}
