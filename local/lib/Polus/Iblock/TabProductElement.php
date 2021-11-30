<?php
namespace Polus\Iblock;

use Bitrix\Main\EventManager;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\UI\Extension;
use Bitrix\Main\Web\Json;
use CFile;
use Polus\SpareParts\Entity\SparePartFileTable;
use Polus\SpareParts\Entity\SparePartTable;
use Polus\SpareParts\Tools\ElementHelper;

Loc::loadMessages(__FILE__);

/**
 * Таб с запасными частями
 */
class TabProductElement {

    /** @var string идентификатор главного модуля */
    const MODULE_ID = "main";

    public static function addEventHandler() {
        $eventManager = EventManager::getInstance();

        $eventManager->addEventHandler(
            self::MODULE_ID,
            "OnAdminIBlockElementEdit",
            [static::class, "initSparePartsTab"]
        );
    }

    public static function initSparePartsTab(): array {
        $tabSet = new TabProductElement();

        return [
            "TABSET"  => $tabSet->getTabName(),
            "Check"   => [$tabSet, "check"], //callable для функции проверки
            "Action"  => [$tabSet, "action"], //callable для функции хз чего
            "GetTabs" => [$tabSet, "getTabList"], //callable для получения списка табов
            "ShowTab" => [$tabSet, "showTabContent"], //callable для вывода содержимого табов
        ];
    }

    public function getTabList($elementInfo): ?array {
        $addTab = $elementInfo['ID'] > 0
            && $elementInfo['IBLOCK']['ID'] == 1;

        return $addTab ? [
            [
                "DIV"   => 'maximaster_some_tab',
                "SORT"  => PHP_INT_MAX,
                "TAB"   => Loc::getMessage("POLUS_TAB_PRODUCT_ELEMENT_TAB_NAME"),
                "TITLE" => Loc::getMessage("POLUS_TAB_PRODUCT_ELEMENT_TITLE"),
            ],
        ] : null;
    }

    public function showTabContent($div, $elementInfo, $formData) {

        //TODO перенести обработчик в модуль, убрать подключение
        Loader::includeModule("polus.spareparts");

        Extension::load(["polus.vue.spare-parts"]);
        $file = ElementHelper::getFile($elementInfo["IBLOCK"]["ID"], $elementInfo["ID"]);
        $points = ElementHelper::getPoints($elementInfo["IBLOCK"]["ID"], $elementInfo["ID"]);

        ob_start();
        ?>
            <div id="spare_parts">
                <parts :picture="picture" :iblock-id="iblockId" :element-id="elementId" :saved-points="savedPoints"/>
            </div>
            <script type="text/javascript">
                BX.ready(function () {
                    var spareParts = BX.Vue.create({
                        el: '#spare_parts',
                        data() {
                            return {
                                picture: <?= Json::encode($file) ?>,
                                iblockId: <?= (string)$elementInfo["IBLOCK"]["ID"] ?>,
                                elementId: <?= (string)$elementInfo["ID"] ?>,
                                savedPoints: <?= Json::encode($points) ?>
                            }
                        }
                    });
                });
            </script>
        <?
        echo ob_get_clean();
    }

    public function check() {
        return true;
    }

    public function action($params) {
        return true;
    }

    protected function getTabName(): string {
        return "spare_parts_tab";
    }
}