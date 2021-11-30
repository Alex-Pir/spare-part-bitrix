<?php
namespace Polus\SpareParts\Tools;

use Bitrix\Iblock\Iblock;
use Bitrix\Iblock\IblockTable;
use Bitrix\Iblock\TypeTable;
use Bitrix\Main\ORM\Data\Result;
use Exception;
use Polus\SpareParts\Constants;
use Polus\SpareParts\Traits\HasModules;

class ParametersHelper {
    use HasModules;

    /**
     * Возвращает массив с данными по типам инфоблоков
     *
     * @return array
     */
    public function getIblockTypes(): array {
        $result = [];
        try {
            static::includeIblockModule();

            $iblockTypes = TypeTable::getList($this->getOrmParameters());

             while ($iblock = $iblockTypes->fetch()) {
                 $result[$iblock["ID"]] = sprintf("%s", $iblock["ID"]);
             }

        } catch (Exception $ex) {
            AddMessage2Log($ex->getMessage());
        }

        return $result;
    }

    /**
     * Возвращает массив с данными по инфоблокам
     *
     * @param string $iblockTypeId
     * @return array
     */
    public function getIblocks(string $iblockTypeId): array {
        $result = [];
        try {
            static::includeIblockModule();

            $parameters = $this->getOrmParameters();
            $parameters["filter"]["IBLOCK_TYPE_ID"] = $iblockTypeId;
            array_push($parameters["select"], "NAME");

            $iblocks = IblockTable::getList($parameters);

            while ($iblock = $iblocks->fetch()) {
                $result[$iblock["ID"]] = sprintf("[%d] - %s", $iblock["ID"], $iblock["NAME"]);
            }

        } catch (Exception $ex) {
            AddMessage2Log($ex->getMessage());
        }

        return $result;
    }

    /**
     * Возвращает параметры запроса к базе
     *
     * @return array
     */
    protected function getOrmParameters(): array {
        return [
            "select" => ["ID"],
            "cache" => ["ttl" => Constants::CACHE_TIME_LONG]
        ];
    }

    /**
     * Возвращает результат выборки из базы
     *
     * @param $dbResult
     * @return array
     */
    protected function prepareResult($dbResult): array {
        $result = [];
        while ($iblock = $dbResult->fetch()) {
            $result[$iblock["ID"]] = sprintf("[%d] - %s", $iblock["ID"], $iblock["NAME"]);
        }

        return $result;
    }
}