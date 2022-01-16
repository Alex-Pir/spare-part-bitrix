<?php
namespace Polus\SpareParts;

use Bitrix\Iblock\IblockTable;
use Bitrix\Main\Localization\Loc;
use Exception;
use Polus\SpareParts\Settings\Fields\NoteField;
use Polus\SpareParts\Settings\Fields\SelectboxField;
use Polus\SpareParts\Settings\Tab;
use Polus\SpareParts\Traits\HasModules;

Loc::loadMessages(__FILE__);

/**
 * Класс для работы с настройками модуля
 */
class Options {
    use HasModules;

    /** @var string информационный блок товаров */
    const OPTION_PRODUCTS_IBLOCK_ID = "sp_product_iblock_id";

    /** @var string информационный блок запасных частей */
    const OPTION_SPARE_PART_IBLOCK_ID = "sp_spare_part_iblock_id";

    /** @var int время кеширования */
    const CACHE_TIME = 36000000;

    /** @var null экземпляр класса */
    private static $instance = null;

    /** доступ к коснтруктору закрыт */
    private function __construct(){}

    /**
     * Создание экземпляра класса
     *
     * @return Options
     */
    public static function getInstance(): Options {
        if (is_null(static::$instance)) {
            static::$instance = new Options();
        }

        return static::$instance;
    }

    public function getTabs(): array {
        return [
            $this->getMainTab()
        ];
    }

    /**
     * Возвращает таб с основными настройками модуля
     *
     * @return Tab
     */
    protected function getMainTab(): Tab {
        $tab = new Tab(
            "edit1",
            Loc::getMessage("POLUS_SPARE_PARTS_MAIN_TAB_NAME"),
            Loc::getMessage("POLUS_SPARE_PARTS_MAIN_TAB_TITLE")
        );

        $tab->addField(new NoteField(Loc::getMessage("POLUS_SPARE_PARTS_WARNING")));
        $tab->addField(
            (new SelectboxField(static::OPTION_PRODUCTS_IBLOCK_ID, Loc::getMessage("POLUS_SPARE_PARTS_PRODUCTS_IBLOCK")))
                ->setItems($this->getIblockSelectList())
        );
        $tab->addField(
            (new SelectboxField(static::OPTION_SPARE_PART_IBLOCK_ID, Loc::getMessage("POLUS_SPARE_PARTS_IBLOCK")))
                ->setItems($this->getIblockSelectList())
        );

        return $tab;
    }

    /**
     * Получение списка информационных блоков
     *
     * @return array
     */
    protected function getIblockSelectList(): array {
        $result = [];

        try {
            static::includeIblockModule();

            $iblocks = IblockTable::getList([
                "select" => ["ID", "NAME"],
                "cache" => ["ttl" => self::CACHE_TIME]
            ])->fetchAll();

            $result = $this->prepareOptionList($iblocks, ["ID" => "ID", "NAME" => "NAME"], true);
        } catch(Exception $ex) {
            AddMessage2Log($ex->getMessage());
        }

        return $result;
    }

    /**
     * Обработка выборки значений из базы для настроек модуля
     *
     * @param array $variants
     * @param array $select
     * @param bool $allowEmpty
     * @return array
     */
    protected function prepareOptionList(array $variants, array $select, bool $allowEmpty = false): array {
        $result = [];

        if ($allowEmpty) {
            $result[0] = "";
        }

        if (!isset($select["ID"]) || !isset($select["NAME"])) {
            return $result;
        }

        $id = $select["ID"];
        $name = $select["NAME"];

        foreach ($variants as $variant) {
            if (!isset($variant[$id]) || !isset($variant[$name])) {
                continue;
            }

            $result[$variant[$id]] = "[{$variant[$id]}]{$variant[$name]}";
        }

        return $result;
    }
}
