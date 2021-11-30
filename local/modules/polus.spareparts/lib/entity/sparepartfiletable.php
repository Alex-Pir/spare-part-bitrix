<?php
namespace Polus\SpareParts\Entity;

use Bitrix\Main\Entity\DataManager;
use Bitrix\Main\Entity\IntegerField;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

class SparePartFileTable extends DataManager {

    /** @var string поля таблицы */
    const ID = "ID";
    const IBLOCK_ID = "IBLOCK_ID";
    const ELEMENT_ID = "ELEMENT_ID";
    const FILE_ID = "FILE_ID";

    public static function getTableName() {
        return "polus_spare_parts_file";
    }

    public static function getMap() {
        return [
            new IntegerField("ID", [
                "primary" => true,
                "autocomplete" => true,
                "title" => Loc::getMessage("POLUS_SPARE_PARTS_FILE_FIELD_ID")
            ]),
            new IntegerField("IBLOCK_ID", [
                "required" => true,
                "title" => Loc::getMessage("POLUS_SPARE_PARTS_FILE_FIELD_IBLOCK_ID")
            ]),
            new IntegerField("ELEMENT_ID", [
                "required" => true,
                "title" => Loc::getMessage("POLUS_SPARE_PARTS_FILE_FIELD_ELEMENT_ID")
            ]),
            new IntegerField("FILE_ID", [
                "required" => true,
                "title" => Loc::getMessage("POLUS_SPARE_PARTS_FILE_FIELD_FILE_ID")
            ])
        ];
    }

    /**
     * Возвращает поля, необходимые для выборки
     *
     * @return string[]
     */
    public static function getSelectRows(): array {
        return [
            self::ID,
            self::FILE_ID
        ];
    }
}