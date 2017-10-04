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

/**
 * This is the model class for table "patient_allergy_assignment".
 *
 * The followings are the available columns in table 'patient_allergy_assignment':
 *
 * Note as of v2.0 this is a model on a view, and is not for direct editing
 *
 * @property int $id
 * @property int $patient_id
 * @property int $allergy_id
 * @property string $comments
 */
class PatientAllergyAssignment extends BaseEventTypeElement
{
    /**
     * Returns the static model of the specified AR class.
     *
     * @return PatientAllergyAssignment the static model class
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
        return 'patient_allergy_assignment';
    }

    /**
     * Set for view as no PK defined in view
     * @return string
     */
    public function primaryKey()
    {
        return 'id';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('patient_id, allergy_id', 'required'),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, name', 'safe', 'on' => 'search'),
            array('comments', 'default', 'setOnEmpty' => true, 'value' => null),
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
            'allergy' => array(self::BELONGS_TO, 'Allergy', 'allergy_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
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
        $criteria->compare('patient_id', $this->patient_id, true);
        $criteria->compare('allergy_id', $this->allergy_id, true);

        return new CActiveDataProvider(get_class($this), array(
            'criteria' => $criteria,
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->allergy->name == 'Other' ? $this->other : $this->allergy->name;
    }

    /**
     * List of allergies.
     */
    public function allergyList($patient_id)
    {
        $patient = Patient::model()->findByPk((int) $patient_id);

        $allergy_ids = array();
        foreach ($patient->allergies as $allergy) {
            if ($allergy->name != 'Other') {
                $allergy_ids[] = $allergy->id;
            }
        }
        $criteria = new CDbCriteria();
        !empty($allergy_ids) && $criteria->addNotInCondition('id', $allergy_ids);
        $criteria->order = 'display_order';

        return Allergy::model()->active()->findAll($criteria);
    }
}
