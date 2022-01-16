<?php
namespace Polus\SpareParts\Handlers;

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\UI\Extension;
use Bitrix\Main\Web\Json;
use Polus\SpareParts\Options;
use Polus\SpareParts\Tools\ElementHelper;
use Polus\SpareParts\Traits\HasModuleOptions;

Loc::loadMessages(__FILE__);

/**
 * Таб с запасными частями
 */
class TabProductElement {
    use HasModuleOptions;

    public static function initSparePartsTab(): array {
        $tabSet = new TabProductElement();

        return [
            "TABSET"  => $tabSet->getTabName(),
            "Check"   => [$tabSet, "check"],
            "Action"  => [$tabSet, "action"],
            "GetTabs" => [$tabSet, "getTabList"],
            "ShowTab" => [$tabSet, "showTabContent"]
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

    /**
     * Содержимое таба с запасными частями
     *
     * @param $div
     * @param $elementInfo
     * @param $formData
     * @return void
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\LoaderException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public function showTabContent($div, $elementInfo, $formData) {
        Extension::load(["polus.vue.spare-parts"]);

        $sparePartsIblockId = static::getModuleOption(Options::OPTION_SPARE_PART_IBLOCK_ID, false);
        $sproductsIblockId = static::getModuleOption(Options::OPTION_PRODUCTS_IBLOCK_ID, false);

        if (!$sparePartsIblockId || !$sproductsIblockId) {
            echo '';
            return;
        }

        $file = ElementHelper::getFile($elementInfo["IBLOCK"]["ID"], $elementInfo["ID"]);
        $points = ElementHelper::getPoints($sparePartsIblockId, $elementInfo["ID"]);

        ob_start();
        ?>
        <div id="spare_parts">
            <parts :picture="picture" :spare-parts-iblock-id="sparePartsIblockId" :product-iblock-id="productsIblockId" :element-id="elementId" :saved-points="savedPoints"/>
        </div>
        <script type="text/javascript">
            BX.ready(function () {
                var spareParts = BX.Vue.create({
                    el: "#spare_parts",
                    data() {
                        return {
                            picture: <?= Json::encode($file) ?>,
                            sparePartsIblockId: <?= (string)$sparePartsIblockId ?>,
                            productsIblockId: <?= (string)$sproductsIblockId ?>,
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

    /**
     * Проверка доступности
     *
     * @return bool
     */
    public function check() {
        return true;
    }

    /**
     * Действия
     *
     * @param $params
     * @return bool
     */
    public function action($params) {
        return true;
    }

    /**
     * идентификатор таба
     *
     * @return string
     */
    protected function getTabName(): string {
        return "spare_parts_tab";
    }
}
