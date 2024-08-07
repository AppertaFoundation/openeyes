<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2021
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2021, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

/**
 * This is the model class for table "ophtrconsent_additional_risk_subspecialty_assignment".
 *
 * The followings are the available columns in table:
 *
 * @property integer $id
 * @property integer $subspecialty_id
 * @property integer $additiona_risk_id
 *
 * The followings are the available model relations:
 * @property subspecialty $subspecialty
 * @property OphTrConsent_AdditionalRisk $additional_risk
 * @property User $user
 * @property User $usermodified
 */

class OphTrConsent_AdditionalRiskSubspecialtyAssignment extends BaseActiveRecordVersioned
{
    /**
     * Returns the static model of the specified AR class.
     *
     * @return OphTrConsent_AdditionalRiskSubspecialtyAssignment|BaseActiveRecord the static model class
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
        return 'ophtrconsent_additional_risk_subspecialty_assignment';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('subspecialty_id, additional_risk_id', 'safe'),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
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
            'additional_risk' => array(self::BELONGS_TO, 'OphTrConsent_AdditionalRisk', 'additional_risk_id'),
            'subspecialty' => array(self::BELONGS_TO, 'subspecialty', 'subspecialty_id'),
            'user' => array(self::BELONGS_TO, 'User', 'created_user_id'),
            'usermodified' => array(self::BELONGS_TO, 'User', 'last_modified_user_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array();
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
        $criteria->compare('name', $this->name, true);
        $criteria->compare('active', $this->active, true);

        return new CActiveDataProvider(get_class($this), array(
            'criteria' => $criteria,
        ));
    }

    public function behaviors()
    {
        return array(
            'LookupTable' => 'LookupTable',
        );
    }
}
