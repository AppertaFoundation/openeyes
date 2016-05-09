<?php

/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2016
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2016, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

/**
 * Class Worklist
 *
 * The followings are the available columns in table:
 * @property integer $id
 * @property string $name
 * @property boolean $scheduled
 *
 * @property WorklistAttribute[] $mapping_attributes
 */
class Worklist extends BaseActiveRecordVersionedSoftDelete
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'worklist';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('name', 'required'),
            array('name', 'length', 'max'=>100),
            array('description', 'length', 'max' => 1000),
            array('start', 'OEDateValidator'),
            array('end', 'OEDateValidator'),
            array('start', 'OEDateCompareValidator', 'compareAttribute' => 'end', 'allowEmpty' => true,
                'operator' => '<=', 'message' => '{attribute} must be on or before {compareAttribute}'),
            array('scheduled', 'boolean', 'allowEmpty' => false),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, name, start, end, description, scheduled', 'safe', 'on'=>'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'mapping_attributes' => array(self::HAS_MANY, 'WorklistAttribute', 'worklist_id'),
            'worklist_patients' => array(self::HAS_MANY, 'WorklistPatient', 'worklist_id')
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'start' => 'Start Date',
            'end' => 'End Date',
        );
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search()
    {
        // Warning: Please modify the following code to remove attributes that
        // should not be searched.

        $criteria=new CDbCriteria;

        $criteria->compare('id',$this->id,true);
        $criteria->compare('name',$this->name,true);
        $criteria->compare('description',$this->description,true);
        $criteria->compare('scheduled', $this->scheduled, true);

        // TODO: proper support for date/time search

        return new CActiveDataProvider(get_class($this), array(
            'criteria'=>$criteria,
        ));
    }

}