<?php
namespace Polus\SpareParts\Traits;

use Bitrix\Main\Config\Option;
use Exception;
use Polus\SpareParts\Constants;

/**
 * Трейт предназначен для получения параметра настроек модуля
 *
 * Trait HasModuleOption
 * @package Citfact\Rest\Traits
 */
trait HasModuleOptions {

    /**
     * Получение параметра настроек модуля
     *
     * @param string $code
     * @param $default
     * @return mixed
     */
    public static function getModuleOption(string $code, $default = null) {
        return static::getOtherModuleOption(Constants::MODULE_ID, $code, $default);
    }

    /**
     * Получение параметра настроек любого модуля
     *
     * @param string $moduleId
     * @param string $code
     * @param null $default
     * @return mixed
     */
    public static function getOtherModuleOption(string $moduleId, string $code, $default = null) {
        try {
            return Option::get($moduleId, $code, $default);
        } catch (Exception $ex) {
            AddMessage2Log($ex->getMessage());
            return $default;
        }
    }
}