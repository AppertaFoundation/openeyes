<?php

/**
 * This is the model class for table "ophtrconsent_others_involved_decision_making_process_contact".
 *
 * The followings are the available columns in table 'ophtrconsent_others_involved_decision_making_process_contact':
 * @property integer $id
 * @property string $element_id
 * @property integer $contact_id
 * @property integer $contact_user_id
 * @property integer $contact_type_id
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
 * @property integer $consent_patient_contact_method_id
 * @property string $other_relationship
 * @property string $other_contact_method
 * @property string $comment
 * @property string $last_modified_user_id
 * @property string $last_modified_date
 * @property string $created_user_id
 * @property string $created_date
 * @property integer $contact_signature_id
 * @property integer $signature_required
 *
 * The followings are the available model relations:
 * @property OphtrconsentPatientContactMethod $consentPatientContactMethod
 * @property Country $country
 * @property OphtrconsentPatientRelationship $consentPatientRelationship
 * @property User $createdUser
 * @property User $lastModifiedUser
 * @property Contact $contact
 */

class Ophtrconsent_OthersInvolvedDecisionMakingProcessContact extends BaseActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'ophtrconsent_others_involved_decision_making_process_contact';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('element_id, contact_type_id, consent_patient_relationship_id, consent_patient_contact_method_id, signature_required', 'required'),
            array('consent_patient_relationship_id, contact_user_id, consent_patient_contact_method_id, contact_type_id', 'numerical', 'integerOnly'=>true),
            array('element_id, country_id, last_modified_user_id, created_user_id', 'length', 'max'=>10),
            array('first_name, last_name, other_relationship, other_contact_method', 'length', 'max'=>200),
            array('email, address_line1, address_line2', 'length', 'max'=>255),
            array('phone_number, mobile_number', 'length', 'max'=>50),
            array('city', 'length', 'max'=>100),
            array('postcode', 'length', 'max'=>20),
            array('comment, last_modified_date, created_date, contact_user_id, contact_signature_id', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, element_id, contact_type_id, contact_user_id, first_name, last_name, email, phone_number, mobile_number, address_line1, address_line2, city, country_id, postcode, consent_patient_relationship_id, consent_patient_contact_method_id, other_relationship, $other_contact_method, comment, last_modified_user_id, last_modified_date, created_user_id, created_date, contact_signature_id, signature_required', 'safe', 'on'=>'search'),
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
            'consentPatientContactMethod' => array(self::BELONGS_TO, 'OphTrConsent_PatientContactMethod', 'consent_patient_contact_method_id'),
            'contactUser' => array(self::BELONGS_TO, 'User', 'contact_user_id'),
            'country' => array(self::BELONGS_TO, 'Country', 'country_id'),
            'consentPatientRelationship' => array(self::BELONGS_TO, 'OphTrConsent_PatientRelationship', 'consent_patient_relationship_id'),
            'createdUser' => array(self::BELONGS_TO, 'User', 'created_user_id'),
            'lastModifiedUser' => array(self::BELONGS_TO, 'User', 'last_modified_user_id'),
            'contact' => array(self::BELONGS_TO, 'Contact', 'contact_id'),
            'signature' => array(self::BELONGS_TO, 'OphTrConsent_Signature', 'contact_signature_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'element_id' => 'Element',
            'contact_type_id' => 'Contact Type ID',
            'contact_user_id' => 'Contact User ID',
            'first_name' => 'First Name',
            'last_name' => 'Last Name',
            'email' => 'Email',
            'phone_number' => 'Phone Number',
            'mobile_number' => 'Mobile Number',
            'address_line1' => 'Address Line1',
            'address_line2' => 'Address Line2',
            'city' => 'City',
            'country_id' => 'Country',
            'postcode' => 'Postcode',
            'consent_patient_relationship_id' => 'Consent Patient Relationship',
            'consent_patient_contact_method_id' => 'Consent Patient Contact Method',
            'other_relationship' => 'Other Relationship',
            'other_contact_method' => 'Other Contact Method',
            'comment' => 'Comment',
            'last_modified_user_id' => 'Last Modified User',
            'last_modified_date' => 'Last Modified Date',
            'created_user_id' => 'Created User',
            'created_date' => 'Created Date',
            'contact_signature_id' => 'CS_ID',
            'signature_required' => 'Signature Required',
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

        $criteria=new CDbCriteria;

        $criteria->compare('id', $this->id);
        $criteria->compare('element_id', $this->element_id, true);
        $criteria->compare('contact_type_id', $this->contact_type_id, true);
        $criteria->compare('first_name', $this->first_name, true);
        $criteria->compare('last_name', $this->last_name, true);
        $criteria->compare('email', $this->email, true);
        $criteria->compare('phone_number', $this->phone_number, true);
        $criteria->compare('mobile_number', $this->mobile_number, true);
        $criteria->compare('address_line1', $this->address_line1, true);
        $criteria->compare('address_line2', $this->address_line2, true);
        $criteria->compare('city', $this->city, true);
        $criteria->compare('country_id', $this->country_id, true);
        $criteria->compare('postcode', $this->postcode, true);
        $criteria->compare('consent_patient_relationship_id', $this->consent_patient_relationship_id);
        $criteria->compare('consent_patient_contact_method_id', $this->consent_patient_contact_method_id);
        $criteria->compare('other_relationship', $this->other_relationship, true);
        $criteria->compare('other_contact_method', $this->other_contact_method, true);
        $criteria->compare('comment', $this->comment, true);
        $criteria->compare('last_modified_user_id', $this->last_modified_user_id, true);
        $criteria->compare('last_modified_date', $this->last_modified_date, true);
        $criteria->compare('created_user_id', $this->created_user_id, true);
        $criteria->compare('created_date', $this->created_date, true);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return Ophtrconsent_OthersInvolvedDecisionMakingProcessContact the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    /**
     * @return string Full name
     */
    public function getFullName()
    {
        return trim(implode(' ', array($this->first_name, $this->last_name)));
    }

    /**
     * @return string JSON
     */
    public function getJsonData()
    {
        $data = $this->attributes;
        $data['existing_id'] = $this->id;
        return json_encode($data);
    }

    /**
     * @return string html
     */
    public function getContactInfo()
    {
        $controller = \Yii::app()->getController();
        return $controller->renderPartial('_contact_info', ['contact' => $this], true);
    }

    /**
     * @return boolean
     */
    public function isOtherRelationship()
    {
        $relationship = \OphTrConsent_PatientRelationship::model()->find('LOWER(name)=LOWER(:name)', [":name"=>'Other']);
        if ($relationship) {
            if ($relationship->id === $this->consent_patient_relationship_id) {
                return true;
            }
        }
        return false;
    }

    /**
     * @return string
     */
    public function getRelationshipName()
    {
        if ($this->isOtherRelationship()) {
            return $this->other_relationship;
        } else {
            return $this->consentPatientRelationship->name;
        }
    }

    /**
     * @return boolean
     */
    public function isOtherContactMethod()
    {
        $contact_method = \OphTrConsent_PatientContactMethod::model()->find('LOWER(name)=LOWER(:name)', [":name"=>'other']);
        if ($contact_method) {
            if ((int)$contact_method->id === (int)$this->consent_patient_contact_method_id) {
                return true;
            }
        }
        return false;
    }

    /**
     * @return string
     */
    public function getContactMethodName()
    {
        if ($this->isOtherContactMethod()) {
            return $this->other_contact_method;
        } else {
            return $this->consentPatientContactMethod->name;
        }
    }

    public function getSignatureRequired()
    {
        return (int)$this->signature_required ?? (int)$this->consentPatientContactMethod->need_signature;
    }

    public function getSignatureRequiredFromContactMethodType()
    {
        return (int)$this->consentPatientContactMethod->need_signature;
    }

    public function getSignatureRequiredString()
    {
        $result = '';

        switch ((int)$this->getSignatureRequired()) {
            case OphTrConsent_PatientContactMethod::SIGNATURE_NOT_REQUIRED:
                $result = OphTrConsent_PatientContactMethod::getTypeLabel('SIGNATURE_NOT_REQUIRED');
                break;
            case OphTrConsent_PatientContactMethod::SIGNATURE_REQUIRED:
                $result = OphTrConsent_PatientContactMethod::getTypeLabel('SIGNATURE_REQUIRED');
                break;
            case OphTrConsent_PatientContactMethod::SIGNATURE_OPTIONAL:
            default:
                $result = OphTrConsent_PatientContactMethod::getTypeLabel('SIGNATURE_OPTIONAL');
                break;
        }
        return $result;
    }
}
