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

use OE\factories\models\traits\HasFactory;

/**
 * Class WorklistPatientAttribute.
 *
 * @property int $worklist_patient_id
 * @property int $worklist_attribute_id
 * @property string $attribute_value
 * @property WorklistPatient $worklistpatient
 * @property Patient $patient
 * @property Worklist $worklist
 * @property WorklistAttribute $worklistattribute
 */
class WorklistPatientAttribute extends BaseActiveRecordVersioned
{
    use HasFactory;

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'worklist_patient_attribute';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('worklist_patient_id, worklist_attribute_id, attribute_value', 'required'),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, worklist_patient_id, worklist_attribute_id, attribute_value', 'safe', 'on' => 'search'),
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
            'worklistpatient' => array(self::BELONGS_TO, 'WorklistPatient', 'worklist_patient_id'),
            'patient' => array(self::HAS_ONE, 'Patient', array('patient_id' => 'id'), 'through' => 'worklistpatient'),
            'worklist' => array(self::HAS_ONE, 'Worklist', array('worklist_id' => 'id'), 'through' => 'worklistpatient'),
            'worklistattribute' => array(self::BELONGS_TO, 'WorklistAttribute', 'worklist_attribute_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'attribute_value' => 'Value',
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
}
