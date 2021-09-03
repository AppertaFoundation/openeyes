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
 * Class Synoptophore_Direction
 *
 * @package OEModule\OphCiExamination\models
 * @property string $name
 */
class Synoptophore_Direction extends \BaseActiveRecordVersioned
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'ophciexamination_synoptophore_direction';
    }

    /**
     * Use standard Lookup behaviour
     *
     * @return array
     */
    public function behaviors()
    {
        return array(
            'LookupTable' => \LookupTable::class,
        );
    }

    public function __toString()
    {
        return $this->name;
    }

    public function rules()
    {
        return [
            ['name, display_order', 'required'],
            ['name', 'length', 'max' => 31, 'min' => 2],
            ['active', 'boolean'],
            ['display_order', 'numerical', 'integerOnly' => true],
            ['id, name, active, display_order', 'safe', 'on' => 'search'],
        ];
    }
}
