<?php

/** @var array $arResult */

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Web\Json;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

Loc::loadMessages(__FILE__);
?>

<div id="app" class="parts-container">
    <div class="container-image">
        <div class="container-image__block">
            <img :src="file.src" :title="file.title" :alt="file.alt"/>
        </div>
        <div
                v-for="element in points"
                :key="element.id"
                class="container-point"
                :class="{'active': currentSparePartId == element.value}"
                :style="getStyle(element.x, element.y)"
                v-html="element.id"
                @click="choosePoint(element.value)"
        >
        </div>
    </div>
    <transition name="fade">
        <div class="parts-container__points" v-if="currentElementShow">
            <img v-if="currentElement.picture" :src="currentElement.picture" :title="currentElement.name" :alt="currentElement.name"/>
            <h3 v-if="currentElement.name" v-html="currentElement.name"></h3>
            <div v-if="currentElement.text" v-html="currentElement.text"></div>
            <a v-if="currentElement.url" :href="currentElement.url"><?= Loc::getMessage("SPARE_PART_TPL_BTN_ELEMENT_DETAIL_NAME") ?></a>
        </div>
    </transition>
</div>
<script>
    (new BX.Polus.Init.VueInit(
        BX.Polus.Components.SpareParts,
        '#app',
        <?= Json::encode($arResult) ?>
    )).init();

</script>