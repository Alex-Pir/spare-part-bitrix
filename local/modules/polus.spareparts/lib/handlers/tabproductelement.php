<?php
namespace Polus\Handlers;

use Bitrix\Main\EventManager;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\UI\Extension;
use Bitrix\Main\Web\Json;
use Citfact\Rest\Traits\HasModuleOption;
use Polus\SpareParts\Constants;
use Polus\SpareParts\Options;
use Polus\SpareParts\Tools\ElementHelper;
use Polus\SpareParts\Traits\HasModules;

Loc::loadMessages(__FILE__);

/**
 * Таб с запасными частями
 */
class TabProductElement {
    use HasModuleOption;

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

    /**
     * Возвращает массив с описанием таба для инфоблока
     *
     * @param $elementInfo
     * @return array[]|null
     */
    public function getTabList($elementInfo): ?array {

        $productIblockId = static::getModuleOption(Options::OPTION_PRODUCTS_IBLOCK_ID, false);

        if (!$productIblockId) {
            return null;
        }

        $addTab = $elementInfo["ID"] > 0
            && $elementInfo["IBLOCK"]["ID"] == $productIblockId;

        return $addTab ? [
            [
                "DIV"   => "spare_parts_some_tab",
                "SORT"  => PHP_INT_MAX,
                "TAB"   => Loc::getMessage("POLUS_TAB_PRODUCT_ELEMENT_TAB_NAME"),
                "TITLE" => Loc::getMessage("POLUS_TAB_PRODUCT_ELEMENT_TITLE"),
            ],
        ] : null;
    }

    public function showTabContent($div, $elementInfo, $formData) {
        Extension::load(["polus.vue.spare-parts"]);

        $sparePartsIblockId = static::getModuleOption(Options::OPTION_SPARE_PART_IBLOCK_ID, false);

        if (!$sparePartsIblockId) {
            echo '';
            return;
        }

        $file = ElementHelper::getFile($elementInfo["IBLOCK"]["ID"], $elementInfo["ID"]);
        $points = ElementHelper::getPoints($sparePartsIblockId, $elementInfo["ID"]);

        ob_start();
        ?>
        <div id="spare_parts">
            <parts :picture="picture" :iblock-id="iblockId" :element-id="elementId" :saved-points="savedPoints"/>
        </div>
        <script type="text/javascript">
            BX.ready(function () {
                var spareParts = BX.Vue.create({
                    el: "#spare_parts",
                    data() {
                        return {
                            picture: <?= Json::encode($file) ?>,
                            iblockId: <?= (string)$sparePartsIblockId ?>,
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