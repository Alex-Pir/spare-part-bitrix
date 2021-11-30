<?php
namespace Polus\SpareParts\Controller;

use Bitrix\Main\Application;
use Bitrix\Main\Engine\Controller;
use Bitrix\Main\Error;
use Bitrix\Main\Web\Json;
use Exception;
use Polus\SpareParts\Entity\SparePartTable;

class Iblock extends Controller {

    public function saveSparePartAction($parameters) {
        if (!$parameters) {
            throw new Exception();
        }

        $parameters = Json::decode($parameters);

        if (!$parameters
            || !$parameters["iblock_id"]
            || !$parameters["element_id"]
            || !$parameters["items"]) {
            throw new Exception();
        }

        $items = Json::decode($parameters["items"]);

        $elements = $this->getElements($parameters["iblock_id"], $parameters["element_id"]);

        $tableElementsDiff = array_diff_key($elements, $items);
        $itemElementsDiff = array_diff_key($items, $elements);

        /**
         * оставляем в $items только старые элементы
         */
        foreach ($itemElementsDiff as $key => $item) {
            unset($items[$key]);
        }

        return $this->saveToDB($parameters["iblock_id"], $parameters["element_id"], $tableElementsDiff, $items, $itemElementsDiff);
    }

    /**
     * Возвращает элементы из таблицы запасных частей
     *
     * @param int $iblockId
     * @param int $elementId
     * @return array
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    protected function getElements(int $iblockId, int $elementId): array {
        $parts = SparePartTable::getList([
            "filter" => ["=IBLOCK_ID" => $iblockId, "=ELEMENT_ID" => $elementId],
            "select" => SparePartTable::getSelectRows()
        ])->fetchAll();

        $result = [];

        foreach ($parts as $part) {
            $result[$part[SparePartTable::INDEX]] = [
                "id" => $part[SparePartTable::INDEX],
                "x" => $part[SparePartTable::X],
                "y" => $part[SparePartTable::Y],
                "value" => $part[SparePartTable::SPARE_PART_ID],
                "error" => false
            ];
        }

        return $result;
    }

    /**
     * Сохраняет изменения в базу
     *
     * @param int $iblockId
     * @param int $elementId
     * @param array $deleteElements
     * @param array $updateElements
     * @param array $saveElements
     * @return bool
     * @throws \Bitrix\Main\DB\SqlQueryException
     */
    protected function saveToDB(int $iblockId, int $elementId, array $deleteElements, array $updateElements, array $saveElements): bool {
        $connection = Application::getConnection();
        try {

            $spareParts = SparePartTable::getList([
                "filter" => [
                    "=" . SparePartTable::IBLOCK_ID => $iblockId,
                    "=" . SparePartTable::ELEMENT_ID => $elementId,
                    SparePartTable::INDEX => array_merge(array_keys($deleteElements), array_keys($updateElements))
                ],
                "select" => [SparePartTable::INDEX, SparePartTable::ID]
            ])->fetchAll();

            foreach ($spareParts as $element) {
                if (isset($deleteElements[$element[SparePartTable::INDEX]])) {
                    $deleteElements[$element[SparePartTable::INDEX]]["ID"] = $element[SparePartTable::ID];
                }

                if (isset($updateElements[$element[SparePartTable::INDEX]])) {
                    $updateElements[$element[SparePartTable::INDEX]]["ID"] = $element[SparePartTable::ID];
                }
            }

            $connection->startTransaction();

            foreach ($deleteElements as $deleteElement) {
                SparePartTable::delete($deleteElement["ID"]);
            }

            foreach ($updateElements as $updateElement) {
                $updateElementId = $updateElement["ID"];
                unset($updateElement["ID"]);
                SparePartTable::update($updateElementId, $this->prepareDataToDB($updateElement));
            }

            foreach ($saveElements as $saveElement) {
                SparePartTable::add(
                    $this->prepareDataToDB(
                        $saveElement,
                        [
                            SparePartTable::IBLOCK_ID => $iblockId,
                            SparePartTable::ELEMENT_ID => $elementId
                        ]
                    )
                );
            }

            $connection->commitTransaction();

            return true;
        } catch (Exception $ex) {
            AddMessage2Log($ex->getMessage());
            $this->addError(new Error($ex->getMessage()));
            $connection->rollbackTransaction();
        }

        return false;
    }

    /**
     * Возвращает массив с ключами, необходимыми для базы данных
     *
     * @param array $data
     * @param array $addKeys
     * @return array
     * @throws Exception
     */
    protected function prepareDataToDB(array $data, array $addKeys = []): array {
        if (empty($data)) {
            throw new Exception();
        }

        $result = [
            SparePartTable::INDEX => $data["id"],
            SparePartTable::SPARE_PART_ID => $data["value"],
            SparePartTable::X => $data["x"],
            SparePartTable::Y => $data["y"]
        ];

        if ([SparePartTable::IBLOCK_ID, SparePartTable::ELEMENT_ID] == array_keys($addKeys)) {
            $result[SparePartTable::IBLOCK_ID] = $addKeys[SparePartTable::IBLOCK_ID];
            $result[SparePartTable::ELEMENT_ID] = $addKeys[SparePartTable::ELEMENT_ID];
        }

        return $result;
    }
}