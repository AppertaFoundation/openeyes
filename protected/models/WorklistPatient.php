<?php

/**
 * OpenEyes.
 *
 * (C) OpenEyes Foundation, 2019
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

/**
 * Class WorklistPatient.
 *
 * @property int $patient_id
 * @property int $worklist_id
 * @property datetime|string $when
 * @property Patient $patient
 * @property Worklist $worklist
 * @property WorklistPatientAttribute[] $worklist_attributes
 * @property Pathway $pathway
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
            'order_assignments' => array(self::HAS_MANY, 'OphDrPGDPSD_Assignment', 'visit_id', 'on' => 'order_assignments.active = 1'),
            'pathway' => array(self::HAS_ONE, 'Pathway', 'worklist_patient_id'),
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

        return new CActiveDataProvider(
            get_class($this),
            array(
                'criteria' => $criteria,
            )
        );
    }

    /**
     * Only set when for scheduled worklist entries.
     */
    public function afterValidate()
    {
        if ($this->worklist->scheduled) {
            if (empty($this->when)) {
                $this->addError(
                    'when',
                    $this->getAttributeLabel('when') . ' is required when the Worklist is scheduled.'
                );
            }
        } elseif (!empty($this->when)) {
            $this->addError(
                'when',
                $this->getAttributeLabel('when') . ' cannot be set when the Worklist not scheduled.'
            );
        }

        parent::afterValidate();
    }

    public function getScheduledTime(): ?string
    {
        if ($this->when) {
            if ($this->when instanceof DateTime) {
                return $this->when->format('H:i');
            }

            return DateTime::createFromFormat('Y-m-d H:i:s', $this->when)->format('H:i');
        }
        return null;
    }

    public function getWorklistAttributeValue(WorklistAttribute $attr): ?string
    {
        foreach ($this->worklist_attributes as $wa) {
            if ($wa->worklist_attribute_id === $attr->id) {
                return $wa->attribute_value;
            }
        }
        return null;
    }

    /**
     * Get the current worklist patient attributes indexed by the worklist mapping Ids.
     *
     * @return array
     */
    public function getCurrentAttributesById(): array
    {
        $res = array();
        foreach ($this->worklist_attributes as $wa) {
            $res[$wa->worklist_attribute_id] = $wa;
        }

        return $res;
    }

    public function getWorklistPatientAttribute($attribute_name): ?WorklistPatientAttribute
    {
        if ($this->hasRelated('worklist_attributes')) {
            return $this->getWorklistPatientAttributeFromRelation($attribute_name);
        }

        return $this->getWorklistPatientAttributeFromQuery($attribute_name);
    }

    protected function getWorklistPatientAttributeFromRelation($attribute_name): ?WorklistPatientAttribute
    {
        $attributes = array_filter(
            $this->worklist_attributes,
            function ($attr) use ($attribute_name) {
                return $attr->worklistattribute->name == $attribute_name;
            }
        );

        return $attributes[0] ?? null;
    }

    protected function getWorklistPatientAttributeFromQuery($attribute_name): ?WorklistPatientAttribute
    {
        $criteria = new CDbCriteria();
        $criteria->join = " JOIN worklist_attribute wa ON wa.id = t.worklist_attribute_id";
        $criteria->addCondition('t.worklist_patient_id = :worklist_patient_id');
        $criteria->addCondition('LOWER(wa.name) = :attribute_name');
        $criteria->addCondition('wa.worklist_id = :worklist_id');
        $criteria->params[':attribute_name'] = strtolower($attribute_name);
        $criteria->params[':worklist_patient_id'] = $this->id;
        $criteria->params[':worklist_id'] = $this->worklist->id;
        return WorklistPatientAttribute::model()->find($criteria);
    }
}
