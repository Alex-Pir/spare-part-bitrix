<?php

use Bitrix\Main\UI\Extension;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

Extension::load(["ui.vue", "polus.vue.init", "polus.vue.plugins.request"]);
