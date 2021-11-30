<?php
namespace Polus\SpareParts\Entity;

use Bitrix\Main\Entity\DataManager;
use Bitrix\Main\Entity\IntegerField;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

class SparePartTable extends DataManager {

    /** @var string поля таблицы */
    const ID = "ID";
    const IBLOCK_ID = "IBLOCK_ID";
    const ELEMENT_ID = "ELEMENT_ID";
    const INDEX = "SPARE_PART_INDEX";
    const X = "COORDS_X";
    const Y = "COORDS_Y";
    const SPARE_PART_ID = "SPARE_PART_ID";

    public static function getTableName() {
        return "polus_spare_parts";
    }

    public static function getMap() {
        return [
            new IntegerField("ID", [
                "primary" => true,
                "autocomplete" => true,
                "title" => Loc::getMessage("POLUS_SPARE_PARTS_FIELD_ID")
            ]),
            new IntegerField("IBLOCK_ID", [
                "required" => true,
                "title" => Loc::getMessage("POLUS_SPARE_PARTS_FIELD_IBLOCK_ID")
            ]),
            new IntegerField("ELEMENT_ID", [
                "required" => true,
                "title" => Loc::getMessage("POLUS_SPARE_PARTS_FIELD_ELEMENT_ID")
            ]),
            new IntegerField("SPARE_PART_INDEX", [
                "required" => true,
                "title" => Loc::getMessage("POLUS_SPARE_PARTS_FIELD_INDEX")
            ]),
            new IntegerField("COORDS_X", [
                "required" => true,
                "title" => Loc::getMessage("POLUS_SPARE_PARTS_FIELD_COORDS_X")
            ]),
            new IntegerField("COORDS_Y", [
                "required" => true,
                "title" => Loc::getMessage("POLUS_SPARE_PARTS_FIELD_COORDS_Y")
            ]),
            new IntegerField("SPARE_PART_ID", [
                "required" => true,
                "title" => Loc::getMessage("POLUS_SPARE_PARTS_FIELD_SPARE_PART_ID")
            ]),
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
            self::INDEX,
            self::X,
            self::Y,
            self::SPARE_PART_ID
        ];
    }
}