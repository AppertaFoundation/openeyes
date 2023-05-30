<?php
/**
 * (C) OpenEyes Foundation, 2023
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2023, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

namespace OE\factories\models;

use OE\factories\ModelFactory;

class SettingMetadataFactory extends ModelFactory
{
    public function definition(): array
    {
        return [
            'field_type_id' => \SettingFieldType::factory()->useExisting(),
            'key' => $this->faker->word(),
            'name' => $this->faker->word(),
            'description' => '',
            'group_id' => \SettingGroup::factory()->useExisting()
        ];
    }

    /**
     * @return SettingMetadataFactory
     */
    public function forInstallationAsLowestLevel(): self
    {
        return $this->state([
            'lowest_setting_level' => 'INSTALLATION'
        ]);
    }

    /**
     * @return SettingMetadataFactory
     */
    public function forInstitutionAsLowestLevel(): self
    {
        return $this->state([
            'lowest_setting_level' => 'INSTITUTION'
        ]);
    }

    /**
     * @param $key string
     * @return SettingMetadataFactory
     */
    public function forKey(string $key): self
    {
        return $this->state([
            'key' => $key
        ]);
    }

    /**
     * @param $name string
     * @return SettingMetadataFactory
     */
    public function forName(string $name): self
    {
        return $this->state([
            'name' => $name
        ]);
    }

    /**
     * @param $name string
     * @return SettingMetadataFactory
     */
    public function forDescription(string $description): self
    {
        return $this->state([
            'description' => $description
        ]);
    }

    /**
     * @param $default_value boolean|null
     * @return SettingMetadataFactory
     */
    public function forCheckbox($default_value = null): self
    {
        return $this->state([
            'field_type_id' => \SettingFieldType::model()->find('name = "Checkbox"'),
            'default_value' => $default_value,
            'data' => serialize(['0' => 'Off', '1' => 'On'])
        ]);
    }

    /**
     * @param $data array
     * @param $default_value string|int|null
     * @return SettingMetadataFactory
     */
    public function forDropdownList($data = [], $default_value = null): self
    {
        return $this->state([
            'field_type_id' => \SettingFieldType::model()->find('name = "Dropdown list"'),
            'default_value' => $default_value,
            'data' => serialize($data)
        ]);
    }

    /**
     * @param $data array
     * @param $default_value string|int|null
     * @return SettingMetadataFactory
     */
    public function forRadioButtons($options = [], $default_value = null): self
    {
        return $this->state([
            'field_type_id' => \SettingFieldType::model()->find('name = "Radio buttons"'),
            'default_value' => $default_value,
            'data' => serialize($options)
        ]);
    }

    /**
     * @param $default_value string|null
     * @return SettingMetadataFactory
     */
    public function forTextField($default_value = null): self
    {
        return $this->state([
            'field_type_id' => \SettingFieldType::model()->find('name = "Text Field"'),
            'default_value' => $default_value,
        ]);
    }

    /**
     * @param $default_value string|null
     * @return SettingMetadataFactory
     */
    public function forTextArea($default_value = null): self
    {
        return $this->state([
            'field_type_id' => \SettingFieldType::model()->find('name = "Textarea"'),
            'default_value' => $default_value,
        ]);
    }

    /**
     * @param $default_value string|null
     * @return SettingMetadataFactory
     */
    public function forHTML($default_value = null): self
    {
        return $this->state([
            'field_type_id' => \SettingFieldType::model()->find('name = "HTML"'),
            'default_value' => $default_value,
        ]);
    }
}
