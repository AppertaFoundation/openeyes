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
 * This is the model class for table "ophtrconsent_patient_attorney_deputy_contact".
 *
 * The followings are the available columns in table 'ophtrconsent_patient_attorney_deputy_contact':
 *
 * @property int $id
 * @property int $patient_id
 * @property int $contact_id
 * @property String $comment
 *
 * The followings are the available model relations:
 * @property Patient $patient
 * @property Contact $contact
 */
class PatientAttorneyDeputyContact extends BaseActiveRecordVersioned
{
    public static $PRESENT = 1;
    public static $NOT_PRESENT = 2;

    public static $YES = 1;
    public static $NO = 2;

    /**
     * Returns the static model of the specified AR class.
     *
     * @return PatientAttorneyDeputyContact the static model class
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
        return 'ophtrconsent_patient_attorney_deputy_contact';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id', 'safe', 'on' => 'search'),
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
            'location' => array(self::BELONGS_TO, 'ContactLocation', 'location_id'),
            'contact' => array(self::BELONGS_TO, 'Contact', 'contact_id'),
            'authorisedDecision' => array(self::BELONGS_TO, 'OphTrConsent_Authorised_Decision', 'authorised_decision_id'),
            'consideredDecision' => array(self::BELONGS_TO, 'OphTrConsent_Considered_Decision', 'considered_decision_id'),
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
            'contact_id' => 'Contact',
            'authorised_decision_id' => 'Authorised Decision',
            'considered_decision_id' => 'Considered Decision'
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
        $criteria->compare('created_user_id', $this->created_user_id, true);

        return new CActiveDataProvider(get_class($this), array(
            'criteria' => $criteria,
        ));
    }

    public function getAddress()
    {
        if ($this->site) {
            return $this->site;
        }

        if ($this->institution) {
            return $this->institution->address ? $this->institution->address : false;
        }

        return $this->contact->address;
    }

    public function getLocationText()
    {
        if ($this->location) {
            return $this->location;
        }
        if ($this->contact->address) {
            return $this->contact->address->address1;
        }
    }

    /**
     * @return string
     */
    public function getAuthorisedDecision()
    {
        return $this->authorisedDecision ? $this->authorisedDecision->name : '';
    }

    /**
     * @return string
     */
    public function getConsideredDecision()
    {
        return $this->consideredDecision ? $this->consideredDecision->name : '';
    }
}
