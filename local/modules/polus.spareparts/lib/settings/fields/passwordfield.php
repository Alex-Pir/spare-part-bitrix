<?php
namespace Polus\SpareParts\Settings\Fields;

/**
 * Поле для пароля
 *
 * Class PasswordField
 * @package Polus\SpareParts\Settings\Fields
 */
class PasswordField extends StringField
{
    /**
     * Получение атрибутов поля
     *
     * @return array
     */
    protected function getAttributes(): array {
        return [self::PASSWORD_FIELD, $this->size];
    }
}