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

use OE\factories\models\traits\HasFactory;

/**
 * This is the model class for table "ophciexamination_element_set_item".
 *
 * @property string $id
 * @property OphCiExamination_ElementSet $set
 * @property ElementType $element_type
 * @property int $default
 */
class OphCiExamination_ElementSetItem extends \BaseActiveRecordVersioned
{
    use HasFactory;

    /**
     * Returns the static model of the specified AR class.
     *
     * @return OphCiExamination_ElementSetItem the static model class
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
        return 'ophciexamination_element_set_item';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        return array(
                array('is_hidden, is_mandatory, display_order', 'safe'),
                array('id', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        return array(
                'set' => array(self::BELONGS_TO, 'OEModule\OphCiExamination\models\OphCiExamination_ElementSet', 'set_id'),
                'element_type' => array(self::BELONGS_TO, 'ElementType', 'element_type_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
                'id' => 'ID',
        );
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

        return new \CActiveDataProvider(get_class($this), array(
                'criteria' => $criteria,
        ));
    }
}
