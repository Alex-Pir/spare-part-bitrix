<?php

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
	die();
}

use Bitrix\Main;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

/**
 * Class polus_spareparts
 *
 * Класс описывющий модуль, используется для установки модуля
 */
class polus_spareparts extends CModule
{

	/**
	 * @var string код модуля
	 */
	public $MODULE_ID = "polus.spareparts";

	/**
	 * module constructor.
	 */
	public function __construct()
	{
		$this->MODULE_NAME = Loc::getMessage("CITRUS_REST_SALE_F_NAME");
		$this->MODULE_DESCRIPTION = Loc::getMessage("CITRUS_REST_SALE_F_DESCRIPTION");
		$this->PARTNER_NAME = Loc::getMessage("CITRUS_REST_SALE_F_COMPANY_NAME");
		$this->PARTNER_URI = Loc::getMessage('CITRUS_REST_SALE_F_COMPANY_URL');

		$this->loadVersion();
	}

	/**
	 * Установка модуля
	 */
	public function doInstall()
	{
		global $APPLICATION;

		try {
			Main\ModuleManager::registerModule($this->MODULE_ID);
			Main\Loader::includeModule($this->MODULE_ID);

			$this->installDb();
			$this->installEvents();
			$this->installFiles();
		} catch (Exception $ex) {
			Main\ModuleManager::unRegisterModule($this->MODULE_ID);
			$APPLICATION->ThrowException($ex->getMessage());
		}

		$APPLICATION->IncludeAdminFile(
			Loc::getMessage(
				"CITRUS_REST_SALE_F_INSTALL_TITLE",
				array("#MODULE#" => $this->MODULE_NAME, "#MODULE_ID#" => $this->MODULE_ID)
			),
			__DIR__ . "/step1.php"
		);
	}

	/**
	 * Удаление моудля и всех его составляющих
	 */
	public function doUninstall()
	{
		global $APPLICATION;

		try {
			Main\Loader::includeModule($this->MODULE_ID);

			$this->unInstallDB();
			$this->uninstallEvents();
			$this->uninstallFiles();

			Main\ModuleManager::unRegisterModule($this->MODULE_ID);
		} catch (Exception $ex) {
			$APPLICATION->ThrowException($ex->getMessage());
		}

		$APPLICATION->IncludeAdminFile(
			Loc::getMessage(
				"CITRUS_REST_SALE_F_INSTALL_TITLE",
				array("#MODULE#" => $this->MODULE_NAME)
			),
			__DIR__ . "/uninstall.php"
		);
	}

    /**
     * Установить данные по версии модуля
     */
    protected function loadVersion()
    {
        $arModuleVersion = array(
            "VERSION" => "1.0.0",
            "VERSION_DATE" => DateTime::createFromFormat('Y-m-d', time()),
        );

        @include __DIR__ . '/version.php';

        if (is_array($arModuleVersion) && array_key_exists('VERSION', $arModuleVersion)) {
            $this->MODULE_VERSION = $arModuleVersion['VERSION'];
            $this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];
        }
    }
}
