<?php
/**
 * OpenEyes.
 *
 * (C) OpenEyes Foundation, 2020
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2020, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */


/**
 * The followings are the available columns in table 'ophciexamination_history_macro':
 *
 * @propety int $id
 * @property string $name
 * @propety string $body
 * @propety int $display_order
 * @propety bool $active
 */
namespace OEModule\OphCiExamination\models;

class HistoryMacro extends \BaseActiveRecordVersioned
{
    use \MappedReferenceData;

    /**
     * Gets all supported levels.
     * @return int a Bitwise value representing the supported mapping levels.
     */
    protected function getSupportedLevels(): int
    {
        return \ReferenceData::LEVEL_SUBSPECIALTY;
    }

    /**
     * Gets the name of the ID column representing the reference data in the mapping table.
     * @param int $level The level used for mapping.
     * @return string The name of the reference data ID column in the mapping table.
     */
    protected function mappingColumn(int $level): string
    {
        return 'history_macro_id';
    }

    /**
     * Returns the static model of the specified AR class.
     *
     * @param string $className
     * @return HistoryMacro the static model class
     */
    public static function model($class_name = null)
    {
        return parent::model($class_name);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'ophciexamination_history_macro';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        return [
            ['id, name, body, display_order, active', 'safe'],
            ['name, body, active', 'required'],
        ];
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        return [
            'subspecialties' => [self::MANY_MANY, 'Subspecialty', 'ophciexamination_history_macro_subspecialty(history_macro_id,subspecialty_id)'],
        ];
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return [
            'name' => 'Macro Name',
            'body' => 'Macro Body',
            'display_order' => 'Display Order',
            'active' => 'Active',
        ];
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     *
     * @return \CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search()
    {
        $criteria = new \CDbCriteria();

        $criteria->compare('id', $this->id, true);
        $criteria->compare('name', $this->name, true);
        $criteria->compare('body', $this->body, true);
        $criteria->compare('display_order', $this->display_order, true);
        $criteria->compare('active', $this->active, true);

        return new \CActiveDataProvider(get_class($this), [
            'criteria' => $criteria,
        ]);
    }
}
