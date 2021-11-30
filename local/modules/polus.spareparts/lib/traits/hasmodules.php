<?php
namespace Polus\SpareParts\Traits;

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Loader;
use Bitrix\Main\LoaderException;

Loc::loadMessages(__FILE__);

/**
 * Трейт предназначен для подключения необходимых модулей
 *
 * Trait HasIblockModule
 * @package Polus\SpareParts\Traits
 */
trait HasModules {

    /**
     * Подключение модуля Информационные блоки
     *
     * @return bool
     * @throws LoaderException
     */
    public static function includeIblockModule(): bool {
        if (!Loader::includeModule("iblock")) {
            throw new LoaderException(Loc::getMessage("POLUS_SPARE_PARTS_IBLOCK_MODULE_NOT_INSTALLED"));
        }

        return true;
    }

    /**
     * Подключение модуля Торговый каталог
     *
     * @return bool
     * @throws LoaderException
     */
    public static function includeCatalogModule(): bool {
        if (!Loader::includeModule("catalog")) {
            throw new LoaderException(Loc::getMessage("POLUS_SPARE_PARTS_CATALOG_MODULE_NOT_INSTALLED"));
        }

        return true;
    }

    /**
     * Подключение модуля Highload-блоки
     *
     * @return bool
     * @throws LoaderException
     */
    public static function includeHLModule(): bool {
        if (!Loader::includeModule("highloadblock")) {
            throw new LoaderException(Loc::getMessage("POLUS_SPARE_PARTS_HL_MODULE_NOT_INSTALLED"));
        }

        return true;
    }

    /**
     * Подключение модуля Интернет-магазин
     *
     * @return bool
     * @throws LoaderException
     */
    public static function includeSaleModule(): bool {
        if (!Loader::includeModule("sale")) {
            throw new LoaderException(Loc::getMessage("POLUS_SPARE_PARTS_SALE_MODULE_NOT_INSTALLED"));
        }

        return true;
    }
}