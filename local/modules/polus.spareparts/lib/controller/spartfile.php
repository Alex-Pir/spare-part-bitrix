<?php
namespace Polus\SpareParts\Controller;

use Bitrix\Main\Engine\Controller;
use Bitrix\Main\Engine\ActionFilter;
use Bitrix\Main\Error;
use Bitrix\Main\IO;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ORM\Data\AddResult;
use CFile;
use CTempFile;
use CUtil;
use Exception;
use Polus\SpareParts\Constants;
use Polus\SpareParts\Entity\SparePartFileTable;
use Polus\SpareParts\Entity\SparePartTable;
use Polus\SpareParts\Options;
use Polus\SpareParts\Traits\HasModuleOptions;

Loc::loadMessages(__FILE__);

/**
 * Класс-контроллер для работы с файлом-схемой запасных частей
 */
class SPartFile extends Controller {
    use HasModuleOptions;

    /**
     * Установка необходимых фильтров для запросов
     *
     * @return \array[][]
     */
    public function configureActions(): array {

        $prefilters = [
            new ActionFilter\Authentication(),
            new ActionFilter\HttpMethod(
                [ActionFilter\HttpMethod::METHOD_POST]
            ),
            new ActionFilter\Csrf()
        ];

        return [
            "saveFile" => [
                "prefilters" => $prefilters
            ],
            "removeFile" => [
                "prefilters" => $prefilters
            ]
        ];
    }

    /**
     * Сохранение файла в таблицу
     *
     * @param int $iblockId
     * @param int $elementId
     * @return array|false
     * @throws Exception
     */
    public function saveFileAction(int $iblockId, int $elementId) {
        try {
            if ($iblockId <= 0 || $elementId <= 0) {
                throw new Exception(Loc::getMessage("SPARE_PARTS_FILE_IDS_NOT_FOUND"));
            }

            $fileInfo = $this->request->getFile("file");

            if (!$fileInfo) {
                throw new Exception(Loc::getMessage("SPARE_PARTS_FILE_NOT_FOUND"));
            }

            $filePath = $this->getFileInfo($fileInfo);

            $fileAddResult = $this->saveFileToDb($filePath, $iblockId, $elementId);

            if (!$fileAddResult->isSuccess()) {
                throw new Exception(Loc::getMessage("SPARE_PARTS_FILE_ADD_ERROR"));
            }

            $fileId = $fileAddResult->getData()["FILE_ID"];
            $file = CFile::GetFileArray($fileId);

            return ["src" => $file["SRC"], "id" => $fileId];
        } catch (Exception $ex) {
            $this->addError(new Error($ex->getMessage()));
        }

        return false;
    }

    /**
     * Удаляет файл и связанные с ним элементы запчастей
     *
     * @param int $fileId
     * @return bool
     */
    public function removeFileAction(int $fileId): bool {
        try {
            if ($fileId <= 0) {
                throw new Exception(Loc::getMessage("SPARE_PARTS_FILE_ID_NOT_FOUND"));
            }

            $sparePartsIblockId = static::getModuleOption(Options::OPTION_SPARE_PART_IBLOCK_ID, false);

            if (!$sparePartsIblockId) {
                throw new Exception(Loc::getMessage("SPARE_PARTS_FILE_IDS_NOT_FOUND"));
            }

            CFile::Delete($fileId);

            $element = SparePartFileTable::getRow([
                "filter" => ["=FILE_ID" => $fileId],
                "select" => ["ID", "IBLOCK_ID", "ELEMENT_ID"]
            ]);

            if ($element) {
                $removeResult = SparePartFileTable::delete($element["ID"]);
                if (!$removeResult->isSuccess()) {
                    throw new Exception();
                }

                $this->removeElements($sparePartsIblockId, $element["ELEMENT_ID"]);
            }

            return true;
        } catch (Exception $ex) {
            $this->addError(new Error($ex->getMessage()));
        }

        return false;

    }

    /**
     * Сохраняет файл и привязывает его к элементу в БД
     *
     * @param string $filePath
     * @param int $iblockId
     * @param int $elementId
     * @return AddResult
     * @throws Exception
     */
    protected function saveFileToDb(string $filePath, int $iblockId, int $elementId): AddResult {
        $file = CFile::MakeFileArray($filePath);
        $file["MODULE_ID"] = Constants::MODULE_ID;
        $fileId = CFIle::SaveFile($file, Constants::MODULE_ID);

        if (!$fileId) {
            throw new Exception(Loc::getMessage("SPARE_PARTS_FILE_ID_NOT_FOUND"));
        }

        return SparePartFileTable::add([
            "IBLOCK_ID" => $iblockId,
            "ELEMENT_ID" => $elementId,
            "FILE_ID" => $fileId
        ]);
    }

    /**
     * Сохраняет файл во временное хранилище и возвращает информацию о нем
     *
     * @param array $fileInfo
     * @return string
     */
    protected function getFileInfo(array $fileInfo): string {
        if (!isset($fileInfo["tmp_name"])) {
            return false;
        }

        $info = pathinfo($fileInfo["name"]);
        $fileInfo["name"] = implode(".", [CUtil::translit($info["filename"], "ru"), $info["extension"]]);

        $tmpName = CTempFile::GetDirectoryName(1);
        $dir = new IO\Directory($tmpName);
        $dir->create();

        if (!$dir->isExists()) {
            return false;
        }

        $newFileName = implode('', [$tmpName, $fileInfo["name"]]);

        if (!move_uploaded_file($fileInfo["tmp_name"], $newFileName)) {
            return false;
        }

        return str_replace($_SERVER["DOCUMENT_ROOT"], "", $newFileName);
    }

    /**
     * Удаляет элементы из базы
     *
     * @param int $iblockId
     * @param int $elementId
     * @return bool
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public function removeElements(int $iblockId, int $elementId): bool {
        $elements = SparePartTable::getList([
            "filter" => [
                "=IBLOCK_ID" => $iblockId,
                "=ELEMENT_ID" => $elementId
            ],
            "select" => ["ID"]
        ])->fetchAll();

        foreach ($elements as $element) {
            SparePartTable::delete($element["ID"]);
        }

        return true;
    }
}
