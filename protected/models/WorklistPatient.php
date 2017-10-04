<?php

/**
 * OpenEyes.
 *
 * (C) OpenEyes Foundation, 2016
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2016, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

/**
 * Class WorklistPatient.
 *
 * @property int $patient_id
 * @property int $worklist_id
 * @property datetime $when
 * @property Patient $patient
 * @property Worklist $worklist
 * @property WorklistPatientAttribute[] $worklist_attributes
 */
class WorklistPatient extends BaseActiveRecordVersioned
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'worklist_patient';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('patient_id, worklist_id', 'required'),
            array('when', 'OEDatetimeValidator', 'allowEmpty' => true),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, patient_id, worklist_id', 'safe', 'on' => 'search'),
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
            'patient' => array(self::BELONGS_TO, 'Patient', 'patient_id'),
            'worklist' => array(self::BELONGS_TO, 'Worklist', 'worklist_id'),
            'worklist_attributes' => array(self::HAS_MANY, 'WorklistPatientAttribute', 'worklist_patient_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'when' => 'When',
            'patient' => 'Patient',
            'Worklist' => 'Worklist',
        );
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     *
     * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search()
    {
        // Warning: Please modify the following code to remove attributes that
        // should not be searched.

        $criteria = new CDbCriteria();

        $criteria->compare('id', $this->id, true);
        $criteria->compare('worklist_id', $this->worklist_id, true);
        $criteria->compare('patient_id', $this->patient_id, true);

        // TODO: proper support for date/time "when" search

        return new CActiveDataProvider(get_class($this), array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Only set when for scheduled worklist entries.
     */
    public function afterValidate()
    {
        if ($this->worklist->scheduled) {
            if (empty($this->when)) {
                $this->addError('when', $this->getAttributeLabel('when').' is required when the Worklist is scheduled.');
            }
        } else {
            if (!empty($this->when)) {
                $this->addError('when', $this->getAttributeLabel('when').' cannot be set when the Worklist not scheduled.');
            }
        }

        parent::afterValidate();
    }

    public function getScheduledTime()
    {
        if ($this->when) {
            if ($this->when instanceof DateTime) {
                return $this->when->format('H:i');
            } else {
                return DateTime::createFromFormat('Y-m-d H:i:s', $this->when)->format('H:i');
            }
        }
    }

    public function getWorklistAttributeValue(WorklistAttribute $attr)
    {
        foreach ($this->worklist_attributes as $wa) {
            if ($wa->worklist_attribute_id == $attr->id) {
                return $wa->attribute_value;
            }
        }
    }

    /**
     * Get the current worklist patient attributes indexed by the worklist mapping Ids.
     *
     * @return array
     */
    public function getCurrentAttributesById()
    {
        $res = array();
        foreach ($this->worklist_attributes as $wa) {
            $res[$wa->worklist_attribute_id] = $wa;
        }

        return $res;
    }
}
