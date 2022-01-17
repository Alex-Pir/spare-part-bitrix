<?php

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
	die();
}

use Bitrix\Main;
use Bitrix\Main\Localization\Loc;
use Citfact\Rest\Entity\Export\TableManager;

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
		$this->MODULE_NAME = Loc::getMessage("POLUS_SPARE_PARTS_F_NAME");
		$this->MODULE_DESCRIPTION = Loc::getMessage("POLUS_SPARE_PARTS_F_DESCRIPTION");
		$this->PARTNER_NAME = Loc::getMessage("POLUS_SPARE_PARTS_F_COMPANY_NAME");
		$this->PARTNER_URI = Loc::getMessage('POLUS_SPARE_PARTS_F_COMPANY_URL');

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
				"POLUS_SPARE_PARTS_F_INSTALL_TITLE",
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
				"POLUS_SPARE_PARTS_F_INSTALL_TITLE",
				array("#MODULE#" => $this->MODULE_NAME)
			),
			__DIR__ . "/uninstall.php"
		);
	}

    /**
     * Создание таблиц
     *
     * @return void
     */
    public function installDb() {
        TableManager::install();
    }

    /**
     * Удаление таблиц
     *
     * @return void
     */
    public function unInstallDB() {
        TableManager::uninstall();
    }

    /**
     * Добалвение (регистрация) обработчиков
     *
     * @return void
     */
    public function installEvents() {
        $eventManager = Main\EventManager::getInstance();

        /**
         * Добавляет таб в инфоблок
         */
        $eventManager->registerEventHandler(
            "main",
            "OnAdminIBlockElementEdit",
            $this->MODULE_ID,
            "Polus\\SpareParts\\Handlers\\TabProductElement", "initSparePartsTab"
        );
    }

    /**
     * Удаление всех зависимостей между модулями (события)
     *
     * @return void
     */
    public function uninstallEvents() {
        $eventManager = Main\EventManager::getInstance();

        $eventManager->unRegisterEventHandler(
            "main",
            "OnAdminIBlockElementEdit",
            $this->MODULE_ID,
            "Polus\\SpareParts\\Handlers\\TabProductElement", "initSparePartsTab"
        );
    }

    /**
     * Копирование файлов модуля
     *
     * @return void
     */
    public function installFiles() {
        CopyDirFiles(__DIR__ . "/components", Main\Application::getDocumentRoot() . POLUS_SPARE_PARTS_BX_ROOT . "/components/polus/", true, true);
        CopyDirFiles(__DIR__ . "/js", Main\Application::getDocumentRoot() . POLUS_SPARE_PARTS_BX_ROOT . "/js/", true, true);
    }

    /**
     * Удаление файлов модуля
     *
     * @return void
     */
    public function unInstallFiles() {
        DeleteDirFilesEx(POLUS_SPARE_PARTS_BX_ROOT . "/components/polus/");
        DeleteDirFilesEx(POLUS_SPARE_PARTS_BX_ROOT . "/js/polus/");
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
