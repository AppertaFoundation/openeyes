<?php

/**
 * (C) Apperta Foundation, 2020
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2020, Apperta Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

namespace OEModule\OphCiExamination\models;

/**
 * Class StrabismusManagement_Treatment
 *
 * @package OEModule\OphCiExamination\models
 * @property int $id
 * @property string $name
 * @property boolean $reason_required
 * @property boolean $column1_multiselect
 * @property boolean $column2_multiselect
 * @property StrabismusManagement_TreatmentOption[] $options
 */
class StrabismusManagement_Treatment extends \BaseActiveRecord
{
    public function tableName()
    {
        return 'ophciexamination_strabismusmanagement_treatment';
    }

    public function defaultScope()
    {
        $alias = $this->getTableAlias(true, false);
        return ['order' => "$alias.display_order asc"];
    }

    public function rules()
    {
        return [
            ['name, display_order, reason_required, column1_multiselect, column2_multiselect', 'safe'],
            ['name', 'required'],
            ['reason_required, column1_multiselect, column2_multiselect', 'boolean']
        ];
    }

    public function relations()
    {
        return [
            'options' => [self::HAS_MANY, StrabismusManagement_TreatmentOption::class, 'treatment_id']
        ];
    }

    public function beforeValidate()
    {
        foreach (['column1_multiselect', 'column2_multiselect', 'reason_required'] as $attribute) {
            if (is_null($this->$attribute)) {
                unset($this->$attribute);
            }
        }

        return parent::beforeValidate();
    }

    /**
     * Returns a list of options by zero-indexed column key
     * @return mixed|null
     */
    public function getOptionsByColumn()
    {
        return array_reduce(
            $this->options,
            function ($result, $option) {
                // zero index the columns
                $result[$option->column_number - 1] ??= [];
                $result[$option->column_number - 1][] = [
                    'id' => $option->id,
                    'value' => $option->name
                ];
                return $result;
            },
            []
        );
    }
}
