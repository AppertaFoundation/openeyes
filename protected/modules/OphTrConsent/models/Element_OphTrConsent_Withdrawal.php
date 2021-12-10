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
 * This is the model class for table "et_ophtrconsent_withdrawal".
 *
 * The followings are the available columns in table 'et_ophtrconsent_withdrawal':
 * @property integer $id
 * @property string $event_id
 * @property integer $withdrawn
 * @property string $withdrawal_reason
 * @property integer $signature_id
 * @property string $last_modified_user_id
 * @property string $last_modified_date
 * @property string $created_user_id
 * @property string $created_date
 * @property integer $contact_type_id
 * @property string $contact_user_id
 * @property string $first_name
 * @property string $last_name
 * @property string $email
 * @property string $phone_number
 * @property string $mobile_number
 * @property string $address_line1
 * @property string $address_line2
 * @property string $city
 * @property string $country_id
 * @property string $postcode
 * @property integer $consent_patient_relationship_id
 * @property string $other_relationship
 * @property string $comment
 *
 * The followings are the available model relations:
 * @property User $createdUser
 * @property User $lastModifiedUser
 * @property Event $event
 * @property Country $country
 * @property OphTrConsent_PatientRelationship $consentPatientRelationship
 * @property OphTrConsent_Signature $signature
 * @property User $contactUser
 */

use OEModule\OphTrConsent\models\RequiresSignature;

class Element_OphTrConsent_Withdrawal extends BaseEventTypeElement implements RequiresSignature
{
    public const TYPE_PATIENT_AGREEMENT_ID = 4;

    public const PATIENT_CONTACTS_TYPE = 1;
    public const OPENEYES_USERS_TYPE = 2;
    public const PATIENT_TYPE = 3;

    private const SIGNATURE_TYPES = [
        self::PATIENT_CONTACTS_TYPE => BaseSignature::TYPE_PATIENT,
        self::OPENEYES_USERS_TYPE => BaseSignature::TYPE_OTHER_USER,
        self::PATIENT_TYPE => BaseSignature::TYPE_PATIENT
    ];

    public function getElementTypeName()
    {
        return "Withdrawal of consent";
    }

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'et_ophtrconsent_withdrawal';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('withdrawn, signature_id, contact_type_id, consent_patient_relationship_id', 'numerical', 'integerOnly' => true),
            array('event_id, last_modified_user_id, created_user_id, contact_user_id, country_id', 'length', 'max' => 10),
            array('withdrawal_reason', 'length', 'max' => 4096),
            array('first_name, last_name, other_relationship', 'length', 'max' => 200),
            array('email, address_line1, address_line2', 'length', 'max' => 255),
            array('phone_number, mobile_number', 'length', 'max' => 50),
            array('city', 'length', 'max' => 100),
            array('postcode', 'length', 'max' => 20),
            array('last_modified_date, created_date, comment', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, event_id, withdrawn, withdrawal_reason, signature_id, last_modified_user_id, last_modified_date, created_user_id, created_date, contact_type_id, contact_user_id, first_name, last_name, email, phone_number, mobile_number, address_line1, address_line2, city, country_id, postcode, consent_patient_relationship_id, other_relationship, comment', 'safe', 'on' => 'search'),
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
            'element_type' => array(self::HAS_ONE, 'ElementType', 'id', 'on' => "element_type.class_name='" . get_class($this) . "'"),
            'eventType' => array(self::BELONGS_TO, 'EventType', 'event_type_id'),
            'event' => array(self::BELONGS_TO, 'Event', 'event_id'),
            'user' => array(self::BELONGS_TO, 'User', 'created_user_id'),
            'usermodified' => array(self::BELONGS_TO, 'User', 'last_modified_user_id'),
            'signature' => array(self::BELONGS_TO, 'OphTrConsent_Signature', 'signature_id'),
            'country' => array(self::BELONGS_TO, 'Country', 'country_id'),
            'consentPatientRelationship' => array(self::BELONGS_TO, 'OphTrConsent_PatientRelationship', 'consent_patient_relationship_id'),
            'contactUser' => array(self::BELONGS_TO, 'User', 'contact_user_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'event_id' => 'Event',
            'withdrawn' => 'Patient has withdrawn consent',
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

        $criteria = new \CDbCriteria;

        $criteria->compare('id', $this->id);
        $criteria->compare('event_id', $this->event_id, true);
        $criteria->compare('withdrawn', $this->withdrawn);
        $criteria->compare('withdrawal_reason', $this->withdrawal_reason, true);
        $criteria->compare('signature_id', $this->signature_id);
        $criteria->compare('last_modified_user_id', $this->last_modified_user_id, true);
        $criteria->compare('last_modified_date', $this->last_modified_date, true);
        $criteria->compare('created_user_id', $this->created_user_id, true);
        $criteria->compare('created_date', $this->created_date, true);
        $criteria->compare('contact_type_id', $this->contact_type_id);
        $criteria->compare('contact_user_id', $this->contact_user_id, true);
        $criteria->compare('first_name', $this->first_name, true);
        $criteria->compare('last_name', $this->last_name, true);
        $criteria->compare('email', $this->email, true);
        $criteria->compare('phone_number', $this->phone_number, true);
        $criteria->compare('mobile_number', $this->mobile_number, true);
        $criteria->compare('address_line1', $this->address_line1, true);
        $criteria->compare('address_line2', $this->address_line2, true);
        $criteria->compare('city', $this->city, true);
        $criteria->compare('postcode', $this->postcode, true);
        $criteria->compare('consent_patient_relationship_id', $this->consent_patient_relationship_id);
        $criteria->compare('other_relationship', $this->other_relationship, true);
        $criteria->compare('comment', $this->comment, true);

        return new \CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return Element_OphTrConsent_Withdrawal the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function isUnableToConsent()
    {
        return $this->event->getElementByClass(\Element_OphTrConsent_Type::class)->isUnableToConsent();
    }

    public function getFullName()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function getRequiredSignatures(): array
    {
        $result = [];

        if ($this->signature_id) {
            $result[] = OphTrConsent_Signature::model()->findByPk($this->signature_id);
        } else {
            $type = self::SIGNATURE_TYPES[$this->contact_type_id] ?? 1; // default
            $sig = new OphTrConsent_Signature();
            $sig->setAttributes([
                "type" => $type,
                "signatory_role" => "Withdrawn by",
                "signatory_name" => $this->getFullName(),
                "initiator_row_id" => $this->id,
            ]);
            $sig->user_id = Yii::app()->session['user']->id;
            $result[] = $sig;
        }

        return $result;
    }

    public function afterSignedCallback(int $row_id, int $signature_id): void
    {
        $this->signature_id = $signature_id;
        if (!$this->save(false, ["signature_id"])) {
            throw new Exception('Unable to save withdrawal: ' . print_r($this->getErrors(), true));
        };
    }

    /**
     * Retrieves a list of Relationships.
     *
     * @return array
     * @throws Exception
     */
    public function getRelationshipItemSet()
    {
        $relationships = \OphTrConsent_PatientRelationship::model()->findAll();
        foreach ($relationships as $i => $relationship) {
            $relationship_items[$i] = [
                'label' => $relationship->name,
                'item_id' => $relationship->id
            ];
            if (strtolower($relationship->name) === 'other') {
                $relationship_items[$i]['js_action'] = 'setOtherRelationship';
            }
            if (strtolower($relationship->name) === 'health professional') {
                $relationship_items[$i]['js_special_attr'] = 'HP';
            }
        }
        return $relationship_items;
    }

    public function getContactTypeItemSet()
    {
        return $contact_type_items = [
            [
                'search_url' => Yii::app()->createUrl('/OphTrConsent/contact') . '/AllPatientContacts',
                'js_action' => 'patientContactSelected',
                'label' => 'Patient contacts',
                'contact_type_id' => self::PATIENT_CONTACTS_TYPE,
                'id' => 'adder_dialog_patient_contact_button',
            ],
            [
                'search_url' => Yii::app()->createUrl('/OphTrConsent/contact') . '/OpeneyesContactsWithUser',
                'js_action' => 'openeyesUserSelected',
                'label' => 'Openeyes users',
                'contact_type_id' => self::OPENEYES_USERS_TYPE,
                'id' => 'adder_dialog_openeyes_users_contact_button'
            ]
        ];
    }

    /**
     * Retrieves an item sets.
     *
     * @return array
     */
    public function getContactItemSets()
    {
        $itemSets = array(
            [
                'items' => $this->getContactTypeItemSet(),
                'id' => 'contact_adder_type',
                'header' => 'Contact type',
                'multiSelect' => false
            ],
            [
                'items' => $this->getRelationshipItemSet(),
                'id' => 'contact_adder_relationship',
                'header' => 'Relationship',
                'multiSelect' => false
            ],
        );
        return $itemSets;
    }
}
