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
 * The followings are the available columns in table '':.
 *
 * @property int $id
 * @property int $event_id
 * @property bool $use_nickname
 * @property string $date
 * @property string $address
 * @property string $introduction
 * @property string $re
 * @property string $body
 * @property string $footer
 * @property string $cc
 * @property bool $draft
 * @property bool $print
 * @property bool $locked
 * @property int $site_id
 * @property string $direct_line
 * @property string $fax
 * @property string $clinic_date
 * @property bool $print_all
 * @property int $letter_type_id
 * @property bool $is_signed_off
 * @property int $to_subspecialty_id
 * @property int $to_firm_id
 * @property bool $is_urgent
 * @property bool $is_same_condition
 * @property int $to_location_id
 * @property int $supersession_id
 *
 * The followings are the available model relations:
 * @property Event $event
 * @property DocumentInstance[] $document_instance
 * @property LetterEnclosure[] $enclosures
 * @property LetterType $letterType
 * @property LetterMacro $macro
 */
class ElementLetter extends BaseEventTypeElement implements Exportable
{
    private const NDR_URI = 'http://www.wales.nhs.uk/ndr';
    public $cc_targets = array();
    public $address_target = null;
    // track the original source address so when overridden for copies to cc addresses, we can still keep
    // the correct cc footer information
    public $source_address = null;
    public $lock_period_hours = 24;
    public $macro = null;

    /**
     * Returns the static model of the specified AR class.
     *
     * @param string $className
     * @return ElementLetter the static model class
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
        return 'et_ophcocorrespondence_letter';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array(
                'event_id, site_id, print, address, use_nickname, date, introduction, cc, re, body, footer, draft, direct_line, fax, clinic_date,' .
                'print_all, is_signed_off, to_subspecialty_id, to_firm_id, is_urgent, is_same_condition',
                'safe'
            ),
            array('to_location_id', 'internalReferralToLocationIdValidator'),
            array('to_subspecialty_id', 'internalReferralServiceValidator'),
            array('is_same_condition', 'internalReferralConditionValidator'),
            array('letter_type_id', 'letterTypeValidator'),
            array('date, introduction, body', 'requiredIfNotDraft'),
            array('use_nickname , site_id', 'required'),
            array('date', 'OEDateValidator'),
            array('clinic_date', 'OEDateValidatorNotFuture'),
            //array('is_signed_off', 'isSignedOffValidator'), // they do not want this at the moment - waiting for the demo/feedback
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, event_id, site_id, use_nickname, date, introduction, re, body, footer, draft, direct_line, letter_type_id, to_location_id', 'safe', 'on' => 'search'),
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
            'site' => array(self::BELONGS_TO, 'Site', 'site_id'),
            'enclosures' => array(self::HAS_MANY, 'LetterEnclosure', 'element_letter_id', 'order' => 'display_order'),
            'document_instance' => array(self::HAS_MANY, 'DocumentInstance', array( 'correspondence_event_id' => 'event_id')),
            'letterType' => array(self::BELONGS_TO, 'LetterType', 'letter_type_id'),
            'toSubspecialty' => array(self::BELONGS_TO, 'Subspecialty', 'to_subspecialty_id'),
            'toLocation' => array(self::BELONGS_TO, 'OphCoCorrespondence_InternalReferral_ToLocation', 'to_location_id'),
            'toFirm' => array(self::BELONGS_TO, 'Firm', 'to_firm_id'),

        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'use_nickname' => 'Use Nickname',
            'date' => 'Date',
            'introduction' => 'Salutation',
            're' => 'Re',
            'body' => 'Body',
            'footer' => 'Footer',
            'draft' => 'Draft',
            'direct_line' => 'Direct line',
            'fax' => 'Direct fax',
            'is_signed_off' => 'Approved by a clinician',
            'to_subspecialty_id' => 'To Service',
            'to_firm_id' => 'To Consultant',
            'is_urgent' => 'Mark as Urgent',
            'is_same_condition' => '',
            'site_id' => 'Site',
            'letter_type_id' => 'Letter Type',
        );
    }

    public function internalReferralServiceValidator($attribute, $params)
    {
        $letter_type = LetterType::model()->findByAttributes(array('name' => 'Internal Referral', 'is_active' => 1));

        if ($letter_type->id === $this->letter_type_id) {
            // internal referral posted
            if (!$this->to_subspecialty_id && $this->draft === '0') {
                $this->addError($attribute, $this->getAttributeLabel($attribute) . ": Please select a service.");
            }
        }
    }
    public function internalReferralConditionValidator($attribute, $params)
    {
        $letter_type = LetterType::model()->findByAttributes(array('name' => 'Internal Referral', 'is_active' => 1));

        // internal referral posted
        if (($letter_type->id === $this->letter_type_id) && !is_numeric($this->is_same_condition) && $this->draft === '0') {
            $this->addError($attribute, 'Same Condition' . ': Please select a condition.');
        }
    }

    public function internalReferralToLocationIdValidator($attribute, $params)
    {
        $letter_type = LetterType::model()->findByAttributes(array('name' => 'Internal Referral', 'is_active' => 1));
        $is_internal_referral_enabled = OphcocorrespondenceInternalReferralSettings::model()->getSetting('is_enabled');

        if ($is_internal_referral_enabled && ($letter_type->id === $this->letter_type_id)) {
            $validator = CValidator::createValidator('required', $this, $attribute, $params);
            $validator->validate($this);
        }
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
        $criteria->compare('event_id', $this->event_id, true);

        return new CActiveDataProvider(get_class($this), array(
            'criteria' => $criteria,
        ));
    }

    public function beforeValidate()
    {
        $purifier = new CHtmlPurifier();
        //The following option is necessary to allow form elements to be preserved
        //This is used in certain laser admission forms
        $purifier->setOptions(array('HTML.Trusted' => true));
        $this->body = $purifier->purify($this->body);
        if (isset($_POST['ElementLetter'])) {
            $_POST['ElementLetter']['body'] = preg_replace("/\n(?=<p><\/p>)/", "<br/>", $_POST['ElementLetter']['body']);
            $_POST['ElementLetter']['body'] = $this->purifyContent($_POST['ElementLetter']['body']);
        }
        return parent::beforeValidate();
    }

    public function requiredIfNotDraft($attribute, $params)
    {
        if ($this->draft !== 1 && !$this->$attribute) {
            $this->addError($attribute, $this->getAttributeLabel($attribute) . ': Cannot be empty');
        }
    }

    /**
     * This attribute only required when Document is posted, so old correspondece will save without letter type
     * @param string $attribute
     * @param array $params
     */
    public function requiredIfDocumentPosted($attribute, $params)
    {
        $post_document_targets = Yii::app()->request->getPost('DocumentTarget', null);
        if ($post_document_targets && !$this->$attribute) {
            $this->addError($attribute, $this->getAttributeLabel($attribute) . ': Cannot be empty');
        }
    }

    public function letterTypeValidator($attribute, $params)
    {
        if ($this->draft !== 1) {
            $this->requiredIfDocumentPosted($attribute, $params);
        }
    }

    public function isSignedOffValidator($attribute, $params)
    {
        if ($this->draft !== 1 && !$this->$attribute) {
            $this->addError($attribute, 'You have to check the following checkbox: Approved by a clinician');
        }
    }

    public function afterFind()
    {
        parent::afterFind();
        $this->source_address = $this->address;
    }

    /**
     * @return string
     */
    public function getExportUrl()
    {
        return Yii::app()->params['correspondence_export_url'];
    }

    /**
     * @param string $ws_type Web service type. Currently handled value is SOAP, though this can be extended in future to include RPC and REST.
     * @param mixed $client_obj The web service client object (if one has already been instantiated). If null, a client object will be created.
     * @return object The response object from the web service.
     * @throws SoapFault
     * @throws CHttpException
     * @throws Exception
     */
    public function export($file_path, $ws_type = 'SOAP', $client_obj = null)
    {
        if ($ws_type === 'SOAP') {
            $wsdl = $this->getExportUrl();

            if ($wsdl) {
                // This should only execute if a WSDL URL has been specified.
                $ws_params = array(
                    'trace' => Yii::app()->params['environment'] === 'DEV',
                    'encoding' => 'UTF-8',
                );
                if (Yii::app()->params['correspondence_export_location_url']) {
                    $ws_params['location'] = Yii::app()->params['correspondence_export_location_url'];
                }

                /** @var $user User */
                $user = User::model()->findByPk(Yii::app()->user->id);

                $wrapper = new stdClass();
                $credentials = new stdClass();

                $appid = new stdClass();
                $appid->Domain = 'SystemId';
                $appid->Value = '313';
                $credentials->ApplicationId = $appid;

                $user_credentials = $user->getAuthenticationForCurrentInstitution();

                // For now assuming the username in OE is the same as NADEX.
                $userid = new stdClass();
                $userid->Domain = 'CYMRU';
                $userid->Value = $user_credentials->username;
                $credentials->UserId = $userid;

                $wrapper->Credentials = $credentials;

                $doc = new stdClass();
                $header = new stdClass();

                if ($this->supersession_id) {
                    $header->DocumentSupersessionSetId = $this->supersession_id;
                }

                $header->DocumentDateTime = DateTime::createFromFormat('Y-m-d H:i:s', $this->event->event_date)->format('Y-m-d\TH:i:s');
                if ($this->event->worklist_patient_id) {
                    $wl_patient = WorklistPatient::model()->findByPk($this->event->worklist_patient_id);
                    $header->EventDateTime = DateTime::createFromFormat('Y-m-d H:i:s', $wl_patient->when)->format('Y-m-d\TH:i:s');
                }
                $header->MIMEtype = 'application/pdf';
                $header->VersionNumber = '1';
                $header->VersionDescription = 'OpenEyes ' . $this->letterType->name;
                $header->SensitivityTypeCode = '00';
                $header->LocationCode = 'NDR';

                $institution = Institution::model()->getCurrent();
                $site = Site::model()->findByPk(Yii::app()->session['selected_site_id']);
                $firm = Firm::model()->findByPk(Yii::app()->session['selected_firm_id']);

                if (!$site || !$firm) {
                    throw new Exception('Unable to retrieve site/firm details.');
                }

                $attr_list = array(
                    'Author' => $user->getReversedNameAndInstitutionUsername($institution->id),
                    'DocumentType' => 'Medical Assessment', // New field.
                    'DocumentSubType' => 'OpenEyes Assessment',
                    'DocumentTypeCode' => 'MEDICALASSESSMENT',
                    'DocumentCategory' => 'MEDICALASSESSMENT',
                    'DocumentCategoryCode' => 'AS',
                    'DocumentSubCategoryCode' => 'AS12',
                    'ExternalSupersessionId' => "313|182|001|$this->letter_type_id|{$this->event->episode->patient->getHos()}|$this->event_id",
                    'Consultant' => $this->event->episode->firm->getConsultantNameReversedAndUsername($institution->id),
                    'ConsultantCode' => $this->event->episode->firm->consultant->registration_code ?? '',
                    'Organisation' => $institution->name,
                    'OrganisationCode' => $institution->remote_id,
                    'Site' => $site->name,
                    'SiteCode' => $site->remote_id,
                    'Specialty' => 'Ophthalmology',
                    'SpecialtyCode' => 130,
                    'SourceApplication' => 313,
                );

                $header->DocumentAttribute = array();
                $index = 0;

                foreach ($attr_list as $key => $value) {
                    $attribute = new stdClass();
                    $attribute->Attribute = $key;
                    $attribute->Namespace = self::NDR_URI;
                    $attribute->Value = $value;
                    $header->DocumentAttribute[$index++] = $attribute;
                }

                $demographics = new stdClass();

                $identifiers = array();

                $patient_identifiers = PatientIdentifier::model()->findAll('patient_id = :id', [':id' => $this->event->episode->patient_id]);
                foreach ($patient_identifiers as $identifier_instance) {
                    $identifier = new stdClass();
                    if ($identifier_instance->patientIdentifierType->usage_type === 'GLOBAL') {
                        $identifier->Domain = 'NHS';
                    } else {
                        $identifier->Domain = $identifier_instance->patientIdentifierType->institution->pas_key;
                    }

                    $identifier->Value = $identifier_instance->value;
                    $identifiers[] = $identifier;
                }

                $demographics->SubjectIdentifier = $identifiers;
                $demographics->FamilyName = $this->event->episode->patient->last_name;
                $demographics->GivenName = $this->event->episode->patient->first_name;
                $demographics->DateOfBirth = $this->event->episode->patient->dob;
                $demographics->SexCode = $this->event->episode->patient->gender;
                $demographics->AddressLine1 = $this->event->episode->patient->contact->address->address1;
                $demographics->AddressLine2 = $this->event->episode->patient->contact->address->address2;
                $demographics->AddressLine3 = $this->event->episode->patient->contact->address->city;
                $demographics->AddressLine4 = $this->event->episode->patient->contact->address->county;
                $demographics->PostCode = $this->event->episode->patient->contact->address->postcode;

                $header->SubjectDemographicsAsRecorded = $demographics;

                $document_data = new stdClass();
                $document_category = new stdClass();
                $document_category->DocumentType = 'Medical Assessment';
                $document_category->DocumentSubType = 'OpenEyes Assessment';
                $document_category->DocumentSubTypeCode = 'MEDICALASSESSMENT';
                $document_data->DocumentCategory = $document_category;
                $document_data->Sensitivity = false;
                $header->DocumentData = $document_data;

                $doc->Header = $header;
                $body = new stdClass();

                $body->DocumentBase64 = file_get_contents($file_path);
                $doc->Body = $body;

                $wrapper->DocumentVersion = $doc;

                $request = new SoapParam($wrapper, 'StoreDocumentRequest');

                $client = $client_obj ?: new SoapClient($wsdl, $ws_params);
                $response = $client->StoreDocument($request);

                if (!$this->supersession_id && $response->Success) {
                    // Capture the supersession ID and store it to allow document versioning.
                    $this->supersession_id = $response->DocumentSupersessionSetId;
                    $this->save();
                }

                return $response;
            }
            throw new CHttpException(404, 'WSDL URL has not been specified.');
        }
        throw new CHttpException(400, 'Invalid or unsupported web service type specified');
    }

    /**
     * @return array
     * @throws Exception
     */
    public function getAddress_targets()
    {
        $patient_id = Yii::app()->request->getQuery('patient_id');
        $patient = null;

        if ($patient_id) {
            $patient = Patient::model()->with(array('gp', 'practice'))->findByPk($patient_id);
        } elseif (isset($this->event->episode->patient)) {
            $patient = $this->event->episode->patient;
        } else {
            throw new Exception('patient not found: ' . $patient_id);
        }

        $options = array('Patient' . $patient->id => $patient->fullname . ' (Patient)');
        if (!isset($patient->contact->address)) {
            $options['Patient' . $patient->id] .= ' - NO ADDRESS';
        }

        if ($patient->gp) {
            if (@$patient->gp->contact) {
                $options['Gp' . $patient->gp_id] = $patient->gp->contact->fullname . ' (' . ((isset($patient->gp->contact->label)) ? $patient->gp->contact->label->name : \SettingMetadata::model()->getSetting('gp_label')) . ')';
            } else {
                $options['Gp' . $patient->gp_id] = Gp::UNKNOWN_NAME . ' (' . \SettingMetadata::model()->getSetting('gp_label') . ')';
            }
            if (!$patient->practice || !@$patient->practice->contact->address) {
                $options['Gp' . $patient->gp_id] .= ' - NO ADDRESS';
            }
        } elseif ($patient->practice) {
            $options['Practice' . $patient->practice_id] = Gp::UNKNOWN_NAME . ' (' . \SettingMetadata::model()->getSetting('gp_label') . ')';
            if (@$patient->practice->contact && !@$patient->practice->contact->address) {
                $options['Practice' . $patient->practice_id] .= ' - NO ADDRESS';
            }
        }

        $patientOptometrist = $patient->getPatientOptometrist();
        if ($patientOptometrist) {
            $options['Optometrist' . $patientOptometrist->id] = $patientOptometrist->fullname . ' (Optometrist)';
        }
        // get the ids of the commissioning body types that should be shown as potential recipients to filter against
        $cbt_ids = array();
        foreach (OphCoCorrespondence_CommissioningBodyType_Recipient::model()->getCommissioningBodyTypes() as $cbt) {
            $cbt_ids[] = $cbt->id;
        }

        if ($cbs = $patient->getDistinctCommissioningBodiesByType()) {
            $criteria = new CDbCriteria();
            $criteria->addInCondition('id', array_keys($cbs));
            $cbtype_lookup = CHtml::listData(CommissioningBodyType::model()->findAll($criteria), 'id', 'name');

            foreach ($cbs as $cb_type_id => $cb_list) {
                foreach ($cb_list as $cb) {
                    if (in_array($cb_type_id, $cbt_ids, false)) {
                        $options['CommissioningBody' . $cb->id] = $cb->name . ' (' . $cbtype_lookup[$cb_type_id] . ')';
                        if (!$cb->getAddress()) {
                            $options['CommissioningBody' . $cb->id] .= ' - NO ADDRESS';
                        }
                    }

                    // include all services at the moment, regardless of whether the commissioning body type is filtered
                    if ($services = $cb->services) {
                        foreach ($services as $svc) {
                            $options['CommissioningBodyService' . $svc->id] = $svc->name . ' (' . $svc->getTypeShortName() . ')';
                        }
                    }
                }
            }
        }

        foreach (
            PatientContactAssignment::model()->with(array(
            'contact' => array(
                'with' => array('address'),
            ),
            'location' => array(
                'with' => array(
                    'contact' => array(
                        'alias' => 'contact2',
                        'with' => array(
                            'label',
                        ),
                    ),
                ),
            ),
            ))->findAll('patient_id=? AND contact.active = ?', array($patient->id, 1)) as $pca
        ) {
            if ($pca->location) {
                $options['ContactLocation' . $pca->location_id] = $pca->location->contact->fullName . ' (' . $pca->location->contact->label->name . ')';
            } elseif (!isset($pca->contact->label) || $pca->contact->label->name !== 'Optometrist') {
                $options['Contact' . $pca->contact_id] = $pca->contact->fullName . ' (' . (isset($pca->contact->label) ? $pca->contact->label->name : '');
                if ($pca->contact->address) {
                    $options['Contact' . $pca->contact_id] .= ', ' . $pca->contact->address->address1 . ')';
                } else {
                    $options['Contact' . $pca->contact_id] .= ') - NO ADDRESS';
                }
            }
        }

        $pcassocitates = PatientContactAssociate::model()->findAllByAttributes(array('patient_id' => $patient->id));
        if (isset($pcassocitates) && (Yii::app()->params['institution_code'] === 'CERA' || Yii::app()->params['use_contact_practice_associate_model'] == true)) {
            foreach ($pcassocitates as $pcassocitate) {
                $gp = $pcassocitate->gp;
                $cpa = ContactPracticeAssociate::model()->findByAttributes(array('gp_id' => $gp->id));
                if (isset($cpa->practice) && !empty($cpa->practice->getAddressLines())) {
                    $options['ContactPracticeAssociate' . $cpa->id] = $gp->contact->fullname . ' (' . ((isset($gp->contact->label)) ? $gp->contact->label->name : \SettingMetadata::model()->getSetting('gp_label')) . ')';
                }
            }
        }

        asort($options);

        return $options;
    }

    public function getStringGroups()
    {
        return LetterStringGroup::model()->findAll([
            'condition' => 'institution_id = :institution_id',
            'params' => [':institution_id' => Yii::app()->session['selected_institution_id']],
            'order' => 'display_order']);
    }

    public function calculateRe(Patient $patient = null)
    {
        if ($this->event && $this->event->institution) {
            $institution_id = $this->event->institution->id;
            $site_id = isset($this->event->site) ? $this->event->site->id : null;
            $patient = $patient ?? $this->event->getPatient();
        } else {
            $institution_id = Institution::model()->getCurrent()->id;
            $site_id = Yii::app()->session['selected_site_id'];
        }
        if (!$patient) {
            throw new \Exception('Patient not found.');
        }
        $primary_identifier = PatientIdentifierHelper::getIdentifierForPatient(
            Yii::app()->params['display_primary_number_usage_code'],
            $patient->id,
            $institution_id,
            $site_id
        );
        $secondary_identifier = PatientIdentifierHelper::getIdentifierForPatient(
            Yii::app()->params['display_secondary_number_usage_code'],
            $patient->id,
            $institution_id,
            $site_id
        );
        $re = 'Re: ' . $patient->first_name . ' ' . $patient->last_name;

        foreach (array('address1', 'address2', 'city', 'postcode') as $field) {
            if ($patient->contact->address && $patient->contact->address->{$field}) {
                $re .= ', ' . $patient->contact->address->{$field};
            }
        }
        if (Yii::app()->params['nhs_num_private'] == true || !$secondary_identifier) {
            return $re . ', DOB: ' . $patient->NHSDate('dob') . ', ' . PatientIdentifierHelper::getIdentifierPrompt($primary_identifier) . ': ' . PatientIdentifierHelper::getIdentifierValue($primary_identifier);
        }
        return $re . ', DOB: ' . $patient->NHSDate('dob') . ', ' . PatientIdentifierHelper::getIdentifierPrompt($primary_identifier) . ': ' . PatientIdentifierHelper::getIdentifierValue($primary_identifier) . ', ' . PatientIdentifierHelper::getIdentifierPrompt($secondary_identifier) . ': ' . PatientIdentifierHelper::getIdentifierValue($secondary_identifier);
    }

    /**
     * @param Patient|null $patient
     * @throws Exception
     */
    public function setDefaultOptions(Patient $patient = null)
    {
        if (Yii::app()->getController()->getAction()->id === 'create') {
            $this->site_id = Yii::app()->session['selected_site_id'];
            $api = Yii::app()->moduleAPI->get('OphCoCorrespondence');

            if (!$patient) {
                // determine if there are any circumstances where this is necessary. Almost certainly very redundant
                if (
                    !$patient = Patient::model()->with(array('contact' => array('with' => array('address'))))
                    ->findByPk(@$_GET['patient_id'])
                ) {
                    throw new Exception('Patient not found: ' . @$_GET['patient_id']);
                }
            }
            // default to GP
            if (isset($patient->gp)) {
                $this->introduction = $patient->gp->getLetterIntroduction();
            }

            $this->re = $this->calculateRe($patient);

            $user = Yii::app()->session['user'];
            $firm = Firm::model()
                ->with('serviceSubspecialtyAssignment')
                ->findByPk(Yii::app()->session['selected_firm_id']);

            $contact = $user->contact;

            // Footer will be built after signatures are recorded
            $this->footer = "";

            // If the event is being created from a worklist step, assign the default macro if one has been specified.
            if (isset(Yii::app()->session['active_step_state_data']['macro_id'])) {
                $this->macro = LetterMacro::model()
                    ->findByPk(Yii::app()->session['active_step_state_data']['macro_id']);
            }

            // Look for a macro based on the episode_status
            $episode = $patient->getEpisodeForCurrentSubspecialty();
            if ($episode && !$this->macro) {
                $this->macro = LetterMacro::model()
                    ->with('firms')
                    ->find(
                        'firms_firms.firm_id=? and episode_status_id=?',
                        array($firm->id, $episode->episode_status_id)
                    );
                if (!$this->macro && $firm->service_subspecialty_assignment_id) {
                    $subspecialty_id = $firm->serviceSubspecialtyAssignment->subspecialty_id;
                    $this->macro = LetterMacro::model()
                        ->with('subspecialties')
                        ->find(
                            'subspecialties_subspecialties.subspecialty_id=? and episode_status_id=?',
                            array($subspecialty_id, $episode->episode_status_id)
                        );
                    if (!$this->macro) {
                        $this->macro = LetterMacro::model()->with('sites', 'institutions')->find([
                            'condition' => '(institutions_institutions.institution_id=? OR sites_sites.site_id=?) AND episode_status_id=?',
                            'params' => array(
                                Yii::app()->session['selected_institution_id'],
                                Yii::app()->session['selected_site_id'],
                                $episode->episode_status_id
                            )]);
                    }
                }
            }

            if ($this->macro) {
                $this->populate_from_macro($patient);
            }

            if (Yii::app()->params['populate_clinic_date_from_last_examination'] && Yii::app()->findModule('OphCiExamination')) {
                $episode = $patient->getEpisodeForCurrentSubspecialty();
                if ($episode) {
                    $event_type = EventType::model()->find('class_name=?', array('OphCiExamination'));
                    if ($event_type) {
                        $criteria = new CDbCriteria();
                        $criteria->addCondition('event_type_id = ' . $event_type->id);
                        $criteria->addCondition('episode_id = ' . $episode->id);
                        $criteria->order = 'created_date desc';
                        $criteria->limit = 1;

                        $event = Event::model()->find($criteria);
                        if ($event) {
                            $this->clinic_date = $event->created_date;
                        }
                    }
                }
            }

            if ($dl = FirmSiteSecretary::model()->find('firm_id=? and site_id=?', array(Yii::app()->session['selected_firm_id'], $this->site_id))) {
                $this->direct_line = $dl->direct_line;
                $this->fax = $dl->fax;
            }

            if (Yii::app()->user) {
                $user = User::model()->findByPk(Yii::app()->user->getId());
                /** @var User $user */
                if ($user) {
                    if ($signOffUser = $user->signOffUser) {
                        $signature_text = $api->getFooterText($signOffUser, $episode->firm);
                        $this->footer = $signOffUser->correspondence_sign_off_text . "{e-signature}" . ($signature_text ? "\n" . nl2br($signature_text) : '');
                    }
                }
            }
        }
    }

    /**
     * @param $patient
     * @throws Exception
     */
    public function populate_from_macro($patient)
    {
        if ($this->macro->use_nickname) {
            $this->use_nickname = 1;
        }

        $address_contact = null;
        if ($this->macro->recipient && $this->macro->recipient->name === 'Patient') {
            $address_contact = $patient;
            $this->address_target = 'patient';
            $this->introduction = $patient->getLetterIntroduction(array(
                'nickname' => $this->use_nickname,
            ));
        } elseif ($this->macro->recipient && $this->macro->recipient->name === \SettingMetadata::model()->getSetting('gp_label')) {
            $this->address_target = 'gp';
            if ($patient->gp) {
                $this->introduction = $patient->gp->getLetterIntroduction(array(
                    'nickname' => $this->use_nickname,
                ));
                $address_contact = $patient->gp;
            } else {
                $this->introduction = 'Dear ' . Gp::UNKNOWN_SALUTATION . ',';
                $address_contact = @$patient->practice;
            }
        }

        if ($address_contact) {
            $this->address = $address_contact->getLetterAddress(array(
                'patient' => $patient,
                'include_name' => true,
                'include_label' => true,
                'delimiter' => "\n",
            ));
        }

        $this->macro->substitute($patient);
        $this->body = $this->macro->body;

        if ($this->macro->cc_patient && $patient->contact->address) {
            $this->cc = $patient->getLetterAddress(array(
                'include_name' => true,
                'include_prefix' => true,
                'delimiter' => '| ',
            ));
            $this->cc = str_replace(',', ';', $this->cc);
            $this->cc = str_replace('|', ',', $this->cc);
            $this->cc_targets[] = 'patient';
        }

        if ($this->macro->cc_doctor && $patient->gp && @$patient->practice->contact->address) {
            $this->cc = $patient->gp->getLetterAddress(array(
                'patient' => $patient,
                'include_name' => true,
                'include_label' => true,
                'delimiter' => '| ',
                'include_prefix' => true,
            ));
            $this->cc = str_replace(',', ';', $this->cc);
            $this->cc = str_replace('|', ',', $this->cc);
            $this->cc_targets[] = 'gp';
        }
    }

    /**
     * @return array
     * @throws Exception
     */
    public function getLetter_macros()
    {
        $macros = array();
        $macro_names = array();

        $firm = Firm::model()->with('serviceSubspecialtyAssignment')->findByPk(Yii::app()->session['selected_firm_id']);

        $criteria = new CDbCriteria();
        $criteria->with = ['institutions', 'sites', 'firms', 'subspecialties'];
        $criteria->condition = '(firms_firms.firm_id = :firm_id OR sites_sites.site_id = :site_id  OR institutions_institutions.institution_id = :institution_id';
        $criteria->params = [':firm_id' => $firm->id, ':site_id' => Yii::app()->session['selected_site_id'], ':institution_id' => Yii::app()->session['selected_institution_id']];
        if ($firm->service_subspecialty_assignment_id) {
            $criteria->condition .= ' OR subspecialties_subspecialties.subspecialty_id = :subspecialty_id';
            $criteria->params[':subspecialty_id'] = $firm->serviceSubspecialtyAssignment->subspecialty_id;
        }
        // Ensure that only installation-level macros
        // and macros applicable only to the current institution are returned.
        $criteria->condition .= ')';/* AND (institution_id IS NULL OR institution_id = :institution_id)';
        $criteria->params[':institution_id'] = Yii::app()->session['selected_institution_id'];*/
        $criteria->order = 'display_order asc';

        foreach (LetterMacro::model()->findAll($criteria) as $macro) {
            if (!in_array($macro->name, $macro_names, false)) {
                $macros[$macro->id] = $macro_names[] = $macro->name;
            }
        }

        return $macros;
    }

    public function beforeSave()
    {

        if (in_array(Yii::app()->getController()->getAction()->id, array('create', 'update'))) {
            if (isset($_POST['saveprint'])) {
                Yii::app()->request->cookies['savePrint'] = new CHttpCookie('savePrint', $this->event_id, [
                    'expire' => strtotime('+30 seconds')
                ]);
                $this->print = 1;
                $this->print_all = 1;
            }
        }

        foreach (array('address', 'introduction', 're', 'body', 'footer', 'cc') as $field) {
            $this->$field = trim($this->$field);
        }

        if (!$this->clinic_date) {
            $this->clinic_date = null;
        }
        $this->attachAssociatedEvent();
        return parent::beforeSave();
    }

    public function afterSave()
    {
        if (@$_POST['update_enclosures']) {
            foreach ($this->enclosures as $enclosure) {
                $enclosure->delete();
            }

            if (is_array(@$_POST['EnclosureItems'])) {
                $i = 1;

                foreach (@$_POST['EnclosureItems'] as $key => $value) {
                    if (trim($value) !== '') {
                        $enc = new LetterEnclosure();
                        $enc->element_letter_id = $this->id;
                        $enc->display_order = $i++;
                        $enc->content = $value;
                        if (!$enc->save()) {
                            throw new Exception('Unable to save EnclosureItem: ' . print_r($enc->getErrors(), true));
                        }
                    }
                }
            }
        }

        if ($this->draft) {
            $this->event->addIssue('Draft');
        } else {
            $this->event->deleteIssue('Draft');
        }

        if (isset($_POST['saveprint'])) {
            Yii::app()->user->setState('correspondece_element_letter_saved', true);
        }

        return parent::afterSave();
    }

    private function generateShortcodeByEventId($event_id)
    {
        $event = Event::model()->findByPk($event_id);
        $name = strtoupper(str_replace(' ', '_', $event->eventType->name));

        return $name . '_' . $event->eventType->id;
    }

    public function getInfotext()
    {
        if ($this->draft) {
            return 'Letter is being drafted';
        }
    }

    public function getCcTargets()
    {
        $targets = array();

        if ($this->document_instance) {
            if (isset($this->document_instance[0]->document_target)) {
                foreach ($this->document_instance[0]->document_target as $target) {
                    if ($target->ToCc === 'Cc') {
                        $targets[] = $target->contact_name . "\n" . $target->address;
                    }
                }
            }
        } else {
            if (trim($this->cc)) {
                foreach (explode("\n", trim($this->cc)) as $cc) {
                    $ex = explode(', ', trim($cc));

                    if (isset($ex[1]) && (ctype_digit($ex[1]) || is_int($ex[1]))) {
                        $ex[1] .= ' ' . $ex[2];
                        unset($ex[2]);
                    }

                    $cc = explode(',', implode(',', $ex));
                    $targets[] = implode("\n", preg_replace('/^[a-zA-Z]+: /', '', str_replace(';', ',', $cc)));
                }
            }
        }

        return $targets;
    }

    public function isEditable()
    {
        // admin can go to edit mode event if the document has been sent / warning set up in the actionUpdate()
        return (Yii::app()->user->checkAccess('admin') || !$this->isGeneratedFor(['Docman', 'Internalreferral', 'Email', 'Email (Delayed)']));
    }


    /**
     * Determinate if wheter PDF and XML files are generated for the DocMan
     * @return bool
     */
    public function isGeneratedFor($types)
    {
        if (!is_array($types)) {
            $types = array($types);
        }

        $criteria = new CDbCriteria();
        $criteria->join =   'JOIN document_instance ins ON t.id = ins.document_set_id ' .
            'JOIN document_target tar ON ins.id = tar.document_instance_id ' .
            'JOIN document_output output ON tar.id = output.document_target_id';

        $criteria->compare('t.event_id', $this->event_id);
        $criteria->compare('output.output_status', 'COMPLETE');
        $criteria->addInCondition('output.output_type', $types);

        return DocumentSet::model()->find($criteria) ? true : false;
    }

    public function getFirm_members()
    {
        $members = CHtml::listData(Yii::app()->getController()->firm->members, 'id', 'fullNameAndTitle');

        $user = Yii::app()->session['user'];

        if (!isset($members[$user->id])) {
            $members[$user->id] = $user->fullNameAndTitle;
        }

        return $members;
    }

    public function renderIntroduction()
    {
        return str_replace("\n", '<br/>', trim(CHtml::encode($this->introduction)));
    }

    public function renderBody()
    {

        // Earlier CHtml (wrapper of HTML purifier) was used to purify the text but
        // the functionality was quite limited in a sense that it was not possible to customise
        // the whitelist element list. So, it is replaced with HTML purifer.
        return $this->purifyContent(preg_replace("/<p>(<?((span style=\"\D{0,40};\")|(em)|(strong))?>){0,3}(<\/?((span)|(em)|(strong))?>){0,3}<\/p>/", "<br/>", $this->body));
    }

    /**
     * @param $content string the HTML to be sanitised.
     * @return string The output HTML without any malicious code
     */
    public function purifyContent($content)
    {
        require_once(Yii::getPathOfAlias('system.vendors.htmlpurifier') . DIRECTORY_SEPARATOR . 'HTMLPurifier.standalone.php');

        // Refer to http://htmlpurifier.org/docs/enduser-customize.html
        // for info on whitelisting elements.
        $config = HTMLPurifier_Config::createDefault();
        $config->set('HTML.DefinitionID', 'elementletter-customize.html input select option');
        // The HTML definitions are cached, so we need to increment this
        // whenever we make a change to flush the cache.
        $config->set('HTML.DefinitionRev', 4);
        $config->set('Cache.SerializerPath', Yii::app()->getRuntimePath());

        if ($def = $config->maybeGetRawHTMLDefinition()) {
            $input = $def->addElement(
                'input',   // name
                'Block',  // content set
                'Inline', // allowed children
                'Common', // attribute collection
                array(
                    'type' => 'Enum#checkbox,radio',
                    'checked' => 'Bool#checked',
                )
            );

            $select = $def->addElement(
                'select',   // name
                'Formctrl',  // content set
                'Required: option',
                'Common', // attribute collection
                array()
            );

            $options = $def->addElement(
                'option',   // name
                false,
                'Optional: #PCDATA',
                'Common', // attribute collection
                array(
                    'value' => 'CDATA',
                    'selected' => 'Bool#selected'
                )
            );
        }

        $Filter = new HTMLPurifier($config);
        return $Filter->purify($content);
    }

    public function getCreate_view()
    {
        return 'create_' . $this->getDefaultView();
    }

    public function getUpdate_view()
    {
        return 'update_' . $this->getDefaultView();
    }

    public function getPrint_view()
    {
        return 'print_' . $this->getDefaultView();
    }

    public function getContainer_print_view()
    {
        return false;
    }

    public function renderFooter()
    {
        $footer = "<div class=\"flex\">";
        if ($esign_element = $this->event->getElementByClass(Element_OphCoCorrespondence_Esign::class)) {
            /** @var Element_OphCoCorrespondence_Esign $esign_element*/
            $signatures = $esign_element->signatures;
            if ($primary_signature = array_pop($signatures)) {
                if (strpos($this->footer, "{e-signature}") !== false) {
                    if (strpos($primary_signature->getPrintout(), "Consultant:") === false && strpos($this->footer, "Consultant:") !== false) {
                        $footer .= "<div>" . nl2br(trim(explode("{e-signature}", $this->footer)[0])) . "<br/>" . $primary_signature->getPrintout() . "<br/>Consultant:" . nl2br(trim(explode("Consultant:", $this->footer)[1])) . "</div>";
                    } else {
                        $footer .= "<div>" . nl2br(trim(explode("{e-signature}", $this->footer)[0])) . "<br/>" . $primary_signature->getPrintout() . "</div>";
                    }
                } else {
                    $footer .= "<div>" . nl2br(trim($this->footer)) . "<br/>" . $primary_signature->getPrintout() . "</div>";
                }
            }
            if ($secondary_signature = array_pop($signatures)) {
                $sign_off_text = $secondary_signature->signedUser->correspondence_sign_off_text;
                $footer .= "<div>" . nl2br(trim($sign_off_text)) . "<br/>" . $secondary_signature->getPrintout() . "</div>";
            }
        }
        $footer .= "</div>";
        return $footer;
    }

    /**
     * Single line render of to address.
     *
     * @return mixed
     */
    public function renderToAddress()
    {
        return preg_replace('/[\r\n]+/', ', ', CHtml::encode($this->address));
    }

    /**
     * Single line render of source_address.
     *
     * @return mixed
     */
    public function renderSourceAddress($address)
    {
        return preg_replace('/[\r\n]+/', ', ', CHtml::encode($address));
    }

    public function getDocumentInstance()
    {
        return DocumentInstance::model()->findByAttributes(array('correspondence_event_id' => $this->event_id));
    }

    /**
     *
     * @param string|string[] $types
     * @return DocumentOutput
     */
    public function getOutputByType($types = 'Print')
    {
        $criteria = new CDbCriteria();
        $criteria->join =   'JOIN document_target target ON t.document_target_id = target.id ' .
            'JOIN document_instance instance ON target.document_instance_id = instance.id ';

        $criteria->compare('instance.correspondence_event_id', $this->event->id);

        if (!is_array($types)) {
            $types = array($types);
        }

        $criteria->addInCondition('t.output_type', $types);

        return DocumentOutput::model()->findAll($criteria);
    }

    public function getTargetByContactType($type = 'GP')
    {
        $criteria = new CDbCriteria();
        $criteria->join = 'JOIN document_instance instance ON t.document_instance_id = instance.id ';

        $criteria->compare('instance.correspondence_event_id', $this->event->id);
        if ($type) {
            $criteria->compare('t.contact_type', $type);
        }

        return DocumentTarget::model()->findAll($criteria);
    }

    public function isInternalReferralEnabled()
    {
        return LetterType::model()->findByAttributes(array('name' => 'Internal Referral')) ? true : false;
    }

    /**
     * If the letter is internal referral or not
     */
    public function isInternalReferral()
    {
        $internal_referral_letter_type = LetterType::model()->findByAttributes(array('name' => 'Internal Referral'));

        return $this->letter_type_id == $internal_referral_letter_type->id;
    }

    public function getInternalReferralSettings($key, $default = null)
    {
        $value = OphcocorrespondenceInternalReferralSettings::model()->getSetting($key);
        return $value ?? $default;
    }


    /**
     * Returns the list of selected sites
     *
     * @param bool $list
     * @return array|CActiveRecord[]
     */
    public function getToLocations($list = false)
    {
        $locations = OphCoCorrespondence_InternalReferral_ToLocation::model()->with('site')->findAll('t.is_active = 1');

        return $list ? CHtml::listData($locations, 'id', 'site.short_name') : $locations;
    }

    public function getAllAttachments()
    {
        /*
        * Attachments
        */

        $associated_content = EventAssociatedContent::model()
            ->with('initAssociatedContent')
            ->findAllByAttributes(
                array('parent_event_id' => $this->event->id),
                array('order' => 't.display_order asc')
            );
        $pdf_files = array();

        if ($associated_content) {
            foreach ($associated_content as $key => $ac) {
                if ($ac->associated_event_id) {
                    $pdf_files[$key]['associated_event_id'] = $ac->associated_event_id;
                }
            }
        }
        return $pdf_files;
    }

    /**
     * @return mixed|string
     */
    public function getToAddress()
    {
        if ($this->document_instance && $this->document_instance[0]->document_target) {
            foreach ($this->document_instance as $instance) {
                foreach ($instance->document_target as $target) {
                    if ($target->ToCc === 'To') {
                        if (($newlines_setting = (int) SettingMetadata::model()->getSetting('correspondence_address_max_lines')) >= 0) {
                            $addressPart = explode("\n", $target->address);
                            $address = '';
                            foreach ($addressPart as $index => $part) {
                                $part = trim($part);
                                if ($index == 0) {
                                    $address = $part;
                                } elseif ($index < $newlines_setting) {
                                    $address = $address . "\n" . $part;
                                } else {
                                    $address = $address . " " . $part;
                                }
                            }
                            return $target->contact_name . "\n" . $address;
                        } else {
                            return $target->contact_name . "\n" . $target->address;
                        }
                    }
                }
            }
        } else {
            // for old legacy letters
            return $this->address;
        }
    }

    /**
     * @return bool
     */
    public function isToAddressDocumentOutputEmail()
    {
        if ($this->event_id) {
            $documentInstance = DocumentInstance::model()->find('correspondence_event_id=' . $this->event_id);
            if ($documentInstance) {
                $documentTarget = $documentInstance->document_target[0];
                return $documentTarget->isRecipientDocumentOutputEmail();
            }
        }
    }

    public function getToAddressContactType()
    {
        if ($this->document_instance && $this->document_instance[0]->document_target) {
            foreach ($this->document_instance as $instance) {
                foreach ($instance->document_target as $target) {
                    if ($target->ToCc === 'To') {
                        if ($target->contact_type === "DRSS") {
                            return $target->commissioningBodyService && $target->commissioningBodyService->type ? $target->commissioningBodyService->type->shortname : null;
                        } else {
                            return $target->contact_type;
                        }
                    }
                }
            }
        } else {
            return null;
        }
    }

    /**
     * @return string
     */
    public function getCCString()
    {
        $ccString = '';

        $Australia = Yii::app()->params['default_country'] === 'Australia';

        if ($this->document_instance && $this->document_instance[0]->document_target) {
            foreach ($this->document_instance as $instance) {
                foreach ($instance->document_target as $target) {
                    if ($target->ToCc != 'To') {
                        if ($target->contact_type === "DRSS") {
                            $contact_type = $target->commissioningBodyService && $target->commissioningBodyService->type ? $target->commissioningBodyService->type->shortname : null;
                        } else {
                            $contact_type = $target->contact_type != \SettingMetadata::model()->getSetting('gp_label') ? ucfirst(strtolower($target->contact_type)) : $target->contact_type;
                        }
                        $ccString .= "CC: " . ($Australia ? "" : ($contact_type != "Other" ? $contact_type . ": " : "")) . $target->contact_name . ", " . $this->renderSourceAddress($target->address) . "<br/>";
                    }
                }
            }
        } else {
            // for old legacy letters
            foreach (explode("\n", trim($this->cc)) as $line) {
                if (trim($line)) {
                    $ccString .= 'CC: ' . str_replace(';', ',', $line) . '<br/>';
                }
            }
        }

        return $ccString;
    }

    /**
     * Sets the deleted flag for the document_* tables after the event has been deleted.
     * Called from the DefaultController afterSoftDelete
     *
     * Please note this function does NOT check if the Event is delete not DocumentOutpus status
     * ==> we do not delete records have "COMPELE" status from document_output table
     * @throws Exception
     */
    public function markDocumentRelationTreeDeleted()
    {
        // Ok, can someone explain me why this is not working here ?
        // $event = Event::model()->disableDefaultScope()->findByPk($this->event_id);
        $event = Event::model()->disableDefaultScope();
        $event->findByPk($this->event_id);
        $document_sets = DocumentSet::model()->findAllByAttributes(['event_id' => $this->event_id]);

        foreach ($document_sets as $document_set) {
            if ($document_set->saveAttributes(['deleted' => 1])) {
                Audit::add('DocumentSet', 'delete', 'Soft Delete: <br><pre>' . print_r($document_set->attributes, true) . '</pre>');
                $document_instances = $document_set->document_instance;
                foreach ($document_instances as $document_instance) {
                    if ($document_instance->saveAttributes(['deleted' => 1])) {
                        Audit::add('DocumentInstance', 'delete', 'Soft Delete: <br><pre>' . print_r($document_instance->attributes, true) . '</pre>');
                        foreach ($document_instance->document_instance_data as $document_instance_data) {
                            Audit::add('DocumentInstanceData', 'delete', 'Soft Delete: <br><pre>' . print_r($document_instance_data->attributes, true) . '</pre>');
                            $document_instance_data->saveAttributes(['deleted' => 1]);
                        }

                        $document_targets = $document_instance->document_target;
                        foreach ($document_targets as $document_target) {
                            if ($document_target->saveAttributes(['deleted' => 1])) {
                                Audit::add('DocumentTarget', 'delete', 'Soft Delete: <br><pre>' . print_r($document_target->attributes, true) . '</pre>');
                                $document_outputs = $document_target->document_output;
                                foreach ($document_outputs as $document_output) {
                                    if ($document_output->saveAttributes(['deleted' => 1])) {
                                        Audit::add('DocumentOutput', 'delete', 'Soft Delete: <br><pre>' . print_r($document_output->attributes, true) . '</pre>');
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        return true;
    }

    /**
     * @return string
     */
    public function checkPrint()
    {
        $cookies = Yii::app()->request->cookies;
        $print_output = $this->getOutputByType();
        $additional_print_info = (count($print_output) > 1 ? '&all=1' : '');
        if ($cookies->contains('savePrint')) {
            if (!(bool)$this->draft && $print_output) {
                return '1' . $additional_print_info;
            }
        }
        return '0' . $additional_print_info;
    }

    public function getInternalReferralEmail()
    {
        $serviceEmail = $this->toSubspecialty ? $this->toSubspecialty->getSubspecialtyEmail() : null;
        $contextEmail = $this->toFirm ? $this->toFirm->getContextEmail() : null;
        $email = null;
        if ($serviceEmail && !$contextEmail) {
            // Only Service is selected and email exists for the service
            $email = $serviceEmail;
        } elseif ($contextEmail) {
            // Both Service and context are selected and email exists for the context.
            $email = $contextEmail;
        }
        return $email;
    }

    public function attachAssociatedEvent()
    {
        if (Yii::app()->getController()->getAction()->id === 'create' || Yii::app()->getController()->getAction()->id === 'update') {
            EventAssociatedContent::model()->deleteAll(
                '`parent_event_id` = :parent_event_id',
                array(':parent_event_id' => $this->event->id)
            );
        }
        if (isset($_POST['attachments_event_id'])) {
            $attachments_last_event_id = Yii::app()->request->getPost('attachments_event_id');
            $attachments_system_hidden = Yii::app()->request->getPost('attachments_system_hidden');
            $attachments_id = Yii::app()->request->getPost('attachments_id');
            $attachments_print_appended = Yii::app()->request->getPost('attachments_print_appended');
            $attachments_short_code = Yii::app()->request->getPost('attachments_short_code');
            $attachments_protected_file_id = Yii::app()->request->getPost('file_id');
            $attachments_display_title = Yii::app()->request->getPost('attachments_display_title');

            if (isset($attachments_last_event_id)) {
                $order = 1;
                foreach ($attachments_last_event_id as $key => $last_event) {
                    $eventAssociatedContent = new EventAssociatedContent();
                    $eventAssociatedContent->parent_event_id = $this->event->id;

                    if (isset($attachments_id[$key])) {
                        $eventAssociatedContent->init_associated_content_id = $attachments_id[$key];
                    }

                    $eventAssociatedContent->is_system_hidden = $attachments_system_hidden[$key] ?? 0;

                    $eventAssociatedContent->is_print_appended = $attachments_print_appended[$key] ?? 0;

                    if (isset($attachments_short_code[$key]) && !empty($attachments_short_code[$key])) {
                        $eventAssociatedContent->short_code  = $attachments_short_code[$key];
                    } else {
                        $eventAssociatedContent->short_code = $this->generateShortcodeByEventId($attachments_last_event_id[$key]);
                    }

                    $eventAssociatedContent->display_title = $attachments_display_title[$key] ?? null;
                    $eventAssociatedContent->associated_protected_file_id = $attachments_protected_file_id[$key] ?? null;
                    $eventAssociatedContent->association_storage  = 'EVENT';
                    $eventAssociatedContent->associated_event_id  = $last_event;
                    $eventAssociatedContent->display_order   = $order;

                    //These errors are not communicated on the front end, and cannot be influenced by user error, so are logged instead
                    if (!$eventAssociatedContent->save()) {
                        OELog::log("Event associated content failed validations");
                        OELog::log(print_r($eventAssociatedContent->getErrors(), true));
                    }

                    $order++;
                }
            }
        }
    }
}
