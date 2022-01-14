<?php
namespace Polus\SpareParts\Settings\Fields;

/**
 * Выпадающий список с возможностью множественного выбора
 *
 * Class MultiSelectboxField
 * @package Polus\SpareParts\Settings\Fields
 */
class MultiSelectboxField extends SelectboxField {
    /**
     * Получение атрибутов поля
     *
     * @return array
     */
    protected function getAttributes(): array {
        return [self::MULTI_SELECTBOX_FIELD, $this->items];
    }
}