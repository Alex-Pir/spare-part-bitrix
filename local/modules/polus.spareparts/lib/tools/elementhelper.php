<?php
namespace Polus\SpareParts\Tools;

use Bitrix\Iblock\Iblock;
use Bitrix\Main\Entity\ReferenceField;
use CFile;
use CIBlock;
use Polus\SpareParts\Entity\SparePartFileTable;
use Polus\SpareParts\Entity\SparePartTable;
use Polus\SpareParts\Traits\HasModules;

/**
 * Класс-помощник для работы с данными о запасных частях
 */
class ElementHelper {
    use HasModules;

    /** @var string Reference-field */
    const SPARE_PART_REF_FIELD = "SPARE_PART_INFO";

    /**
     * Возвращает схему запасных частей
     *
     * @param int $iblockId
     * @param int $elementId
     * @return array
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public static function getFile(int $iblockId, int $elementId): array {
        $fileId = SparePartFileTable::getRow([
            "filter" => [
                SparePartFileTable::IBLOCK_ID => $iblockId,
                SparePartFileTable::ELEMENT_ID => $elementId
            ]
        ])["FILE_ID"];

        if (!$fileId) {
            return ["src" => '', "id" => 0];
        }

        return ["src" => CFile::GetFileArray($fileId)["SRC"], "id" => $fileId];
    }

    /**
     * Возвращает набор точек для схемы запасных частей
     *
     * @param int $iblockId
     * @param int $elementId
     * @return array
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public static function getPoints(int $iblockId, int $elementId): array {
        $points = [];

        $parameters = [
            "filter" => [
                "=" . SparePartTable::IBLOCK_ID => $iblockId,
                "=" . SparePartTable::ELEMENT_ID => $elementId
            ],
            "select" => SparePartTable::getSelectRows(),
        ];

        $pointsFromDB = SparePartTable::getList($parameters)->fetchAll();

        foreach ($pointsFromDB as $point) {
            $resultPoint = [
                "id" => $point[SparePartTable::INDEX],
                "x" => $point[SparePartTable::X],
                "y" => $point[SparePartTable::Y],
                "value" => $point[SparePartTable::SPARE_PART_ID],
                "error" => false
            ];

            $points[] = $resultPoint;
        }

        return $points;
    }

    /**
     * Возвращает данные по элементу информационного блока
     *
     * @param int $iblockId
     * @param int $elementId
     * @return array
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\LoaderException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public static function getIblockElementData(int $iblockId, int $elementId): array {
        static::includeIblockModule();

        $iblockClass = Iblock::wakeUp($iblockId)
            ->getEntityDataClass();

        $element = $iblockClass::getRow([
            "filter" => ["=ID" => $elementId],
            "select" => [
                "NAME",
                "PREVIEW_TEXT",
                "PREVIEW_PICTURE",
                "CODE",
                "IBLOCK_SECTION_ID",
                "DETAIL_URL" => "IBLOCK.DETAIL_PAGE_URL"
            ]
        ]);

        if ($element["PREVIEW_PICTURE"]) {
            $pictureSrc = CFile::GetFileArray($element["PREVIEW_PICTURE"])["SRC"];
        }

        return [
            "id" => $elementId,
            "name" => $element["NAME"],
            "text" => $element["PREVIEW_TEXT"],
            "url" => CIBlock::ReplaceDetailUrl(
                $element["DETAIL_URL"],
                $element,
                true,
                "E"
            ),
            "picture" => $pictureSrc ?? false
        ];
    }
}
