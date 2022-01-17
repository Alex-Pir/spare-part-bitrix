<?php
namespace Citfact\Rest\Entity\Export;

use Bitrix\Main\Application;
use Bitrix\Main\DB\SqlQueryException;
use Bitrix\Main\Entity\DataManager;
use Exception;
use Polus\SpareParts\Entity\SparePartFileTable;
use Polus\SpareParts\Entity\SparePartTable;

/**
 * Класс для работы с таблицами
 */
class TableManager {

    /**
     * Создает таблицы
     */
    public static function install() {
        $connection = Application::getConnection();

        try {
            $connection->startTransaction();

            /** @var DataManager $class */
            foreach (static::getExportTableClasses() as $class) {
                if (!$connection->isTableExists($class::getTableName())) {
                    $entity = $class::getEntity();
                    $entity->createDbTable();
                }
            }

            $connection->commitTransaction();
        } catch (Exception $ex) {
            AddMessage2Log($ex->getMessage());

            try {
                $connection->rollbackTransaction();
            } catch (SqlQueryException $sqlEx) {
                AddMessage2Log($sqlEx->getMessage());
            }
        }

    }

    /**
     * Удаляет таблицы
     */
    public static function uninstall() {
        $connection = Application::getConnection();

        try {
            $connection->startTransaction();

            /** @var DataManager $class */
            foreach (static::getExportTableClasses() as $class) {
                if ($connection->isTableExists($class::getTableName())) {
                    $connection->dropTable($class::getTableName());
                }
            }

            $connection->commitTransaction();
        } catch (Exception $ex) {
            AddMessage2Log($ex->getMessage());

            try {
                $connection->rollbackTransaction();
            } catch (SqlQueryException $sqlEx) {
                AddMessage2Log($sqlEx->getMessage());
            }
        }
    }

    /**
     * Возвращает массив с ORM-классами выгрузок
     *
     * @return string[]
     */
    protected static function getExportTableClasses(): array {
        return [
            SparePartFileTable::class,
            SparePartTable::class
        ];
    }

}