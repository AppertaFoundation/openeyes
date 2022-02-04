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

?>

<?php
/**
 * This is the model class for table "genetics_patient_pedigree".
 *
 * The followings are the available columns in table 'genetics_patient_pedigree':
 * @property string $id
 * @property integer $patient_id
 * @property string $pedigree_id
 * @property string $last_modified_date
 * @property string $last_modified_user_id
 * @property string $created_user_id
 * @property string $created_date
 * @property string $status_id
 *
 * The followings are the available model relations:
 * @property User $createdUser
 * @property User $lastModifiedUser
 * @property GeneticsPatient $patient
 * @property Pedigree $pedigree
 * @property PedigreeStatus $status
 */
class GeneticsPatientPedigree extends CActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'genetics_patient_pedigree';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('patient_id', 'numerical', 'integerOnly' => true),
            array('pedigree_id, last_modified_user_id, created_user_id, status_id', 'length', 'max' => 10),
            array('pedigree_id,patient_id,status_id, last_modified_date, created_date', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, patient_id, pedigree_id, last_modified_date, last_modified_user_id, created_user_id, created_date, status_id', 'safe', 'on' => 'search'),
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
            'createdUser' => array(self::BELONGS_TO, 'User', 'created_user_id'),
            'lastModifiedUser' => array(self::BELONGS_TO, 'User', 'last_modified_user_id'),
            'patient' => array(self::BELONGS_TO, 'GeneticsPatient', 'patient_id',
                'condition' => 'patient.deleted=0'),
            'pedigree' => array(self::BELONGS_TO, 'Pedigree', 'pedigree_id'),
            'status' => array(self::BELONGS_TO, 'PedigreeStatus', 'status_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'patient_id' => 'Patient',
            'pedigree_id' => 'Pedigree',
            'last_modified_date' => 'Last Modified Date',
            'last_modified_user_id' => 'Last Modified User',
            'created_user_id' => 'Created User',
            'created_date' => 'Created Date',
            'status_id' => 'Status',
        );
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     *
     * Typical usecase:
     * - Initialize the model fields with values from filter form.
     * - Execute this method to get CActiveDataProvider instance which will filter
     * models according to data in model fields.
     * - Pass data provider to CGridView, CListView or any similar widget.
     *
     * @return CActiveDataProvider the data provider that can return the models
     * based on the search/filter conditions.
     */
    public function search()
    {
        // @todo Please modify the following code to remove attributes that should not be searched.

        $criteria = new CDbCriteria();
        $criteria->compare('id', $this->id, true);
        $criteria->compare('patient_id', $this->patient_id);
        $criteria->compare('pedigree_id', $this->pedigree_id, true);
        $criteria->compare('last_modified_date', $this->last_modified_date, true);
        $criteria->compare('last_modified_user_id', $this->last_modified_user_id, true);
        $criteria->compare('created_user_id', $this->created_user_id, true);
        $criteria->compare('created_date', $this->created_date, true);
        $criteria->compare('status_id', $this->status_id, true);
        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return GeneticsPatientPedigree the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }
}
