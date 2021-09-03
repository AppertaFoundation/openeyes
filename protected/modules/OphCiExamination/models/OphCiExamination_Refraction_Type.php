<?php
/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

namespace OEModule\OphCiExamination\models;

/**
 * This is the model class for table "ophciexamination_refraction_type".
 *
 * @property int $id
 * @property string $name
 */
class OphCiExamination_Refraction_Type extends \BaseActiveRecordVersioned
{
    /**
     * Returns the static model of the specified AR class.
     *
     * @return OphCiExamination_Instrument the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'ophciexamination_refraction_type';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        return [
            ['id, name, active, display_order, prority', 'safe'],
            ['name, display_order, priority', 'required'],
            ['priority', 'unique'],
            ['id, name, active, display_order', 'safe', 'on' => 'search'],
        ];
    }

    /**
     * Use standard Lookup behaviour
     *
     * @return array
     */
    public function behaviors()
    {
        return [
            'LookupTable' => \LookupTable::class,
        ];
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     *
     * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search()
    {
        $criteria = new \CDbCriteria();
        $criteria->compare('id', $this->id, true);
        $criteria->compare('name', $this->name, true);

        return new \CActiveDataProvider(get_class($this), array(
                'criteria' => $criteria,
        ));
    }

    public function getOptions()
    {
        $options = $this->findAll(array('order' => 'display_order'));

        return \CHtml::listData($options, 'id', 'name') + array('' => 'Other');
    }

    public function __toString()
    {
        return $this->name;
    }
}
