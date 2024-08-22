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
 * This is the model class for table "secondary_diagnosis".
 *
 * The followings are the available columns in table 'secondary_diagnosis':
 *
 * @property int $id
 * @property int $disorder_id
 * @property int $eye_id
 * @property int $patient_id
 *
 * The followings are the available model relations:
 * @property Disorder $disorder
 * @property Eye $eye
 * @property Patient $patient
 */
class SecondaryDiagnosis extends BaseActiveRecordVersioned
{
    /**
     * Returns the static model of the specified AR class.
     *
     * @return SecondaryDiagnosis the static model class
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
        return 'secondary_diagnosis';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('disorder_id, patient_id', 'required'),
            array('disorder_id, eye_id, patient_id', 'safe'),
            array('date', 'OEFuzzyDateValidator'),
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
            'disorder' => array(self::BELONGS_TO, 'Disorder', 'disorder_id'),
            'eye' => array(self::BELONGS_TO, 'Eye', 'eye_id'),
            'patient' => array(self::BELONGS_TO, 'Patient', 'patient_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'disorder_id' => 'Disorder',
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

        return new CActiveDataProvider(get_class($this), array(
            'criteria' => $criteria,
        ));
    }

    public function behaviors()
    {
        return array(
            'OeDateFormat' => array(
                'class' => 'application.behaviors.OeDateFormat',
                'date_columns' => [],
                'fuzzy_date_field' => 'date',
            ),
        );
    }

    public function getDateText()
    {
        return Helper::formatFuzzyDate($this->date);
    }

    /**
     * @return string
     */
    public function getOphthalmicDescription() {
        return $this->eye ? $this->eye->adjective.'~'.$this->disorder->term . '~' . $this->getDateText() . '~' : '~'.$this->disorder->term . '~' . $this->getDateText() . '~';
    }

    /**
     * @return string
     */
    public function getSystemicDescription() {
        return ($this->eye ? $this->eye->adjective.' ' : '').$this->disorder->term;
    }
}
