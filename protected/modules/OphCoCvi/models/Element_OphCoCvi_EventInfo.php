<?php

/**
 * (C) Copyright Apperta Foundation 2021
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2021, Apperta Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

namespace OEModule\OphCoCvi\models;

/**
 * This is the model class for table "et_ophcocvi_eventinfo".
 *
 * The followings are the available columns in table:
 * @property string $id
 * @property integer $event_id
 * @property integer $is_draft
 * @property integer $generated_document_id
 * @property string last_modified_date
 * @property integer $site_id
 *
 * @property int $gp_delivery
 * @property int $la_delivery
 * @property int $rco_delivery
 *
 * @property string $gp_delivery_status
 * @property string $la_delivery_status
 * @property string $rco_delivery_status
 *
 * The followings are the available model relations:
 *
 * @property \ElementType $element_type
 * @property \EventType $eventType
 * @property \Event $event
 * @property \User $user
 * @property \User $usermodified
 * @property \ProtectedFile $generated_document
 * @property \Site
 * @property Element_OphCoCvi_ClinicalInfo $clinical_element
 * @property Element_OphCoCvi_ClericalInfo $clerical_element
 * @property Element_OphCoCvi_ConsentSignature $consent_element
 * @property Element_OphCoCvi_Demographics $demographics_element
 * @property \Firm $consultantInChargeOfThisCvi
 */

class Element_OphCoCvi_EventInfo extends \BaseEventTypeElement
{
    const DELIVERY_STATUS_PENDING = "PENDING";
    const DELIVERY_STATUS_SENT = "SENT";
    const DELIVERY_STATUS_ERROR = "ERROR";

    private $defaultScopeDisabled = false;
    /**
     * Returns the static model of the specified AR class.
     * @return the static model class
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
        return 'et_ophcocvi_eventinfo';
    }

    /**
     * Sets default scope for events such that we never pull back any rows that have deleted set to 1.
     *
     * @return array of mandatory conditions
     */
    public function defaultScope()
    {
        if ($this->getDefaultScopeDisabled()) {
            return [];
        }

        return array(
            'with' => array(
                'event' => array(
                    'condition' => 'event.deleted = false'
                )
            )
        );
    }

    /**
     * Turn off the default scope
     *
     * @return $this
     */
    public function disableDefaultScope(): \BaseActiveRecord
    {
        $this->defaultScopeDisabled = true;

        return $this;
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        return array(
            array('event_id, is_draft, generated_document_id, site_id, consultant_in_charge_of_this_cvi_id', 'safe'),
            array('id, event_id, is_draft, generated_document_id, ', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        return array(
            'element_type' => array(
                self::HAS_ONE,
                'ElementType',
                'id',
                'on' => "element_type.class_name='" . get_class($this) . "'"
            ),
            'eventType' => array(self::BELONGS_TO, 'EventType', 'event_type_id'),
            'event' => array(self::BELONGS_TO, 'Event', 'event_id'),
            'user' => array(self::BELONGS_TO, 'User', 'created_user_id'),
            'usermodified' => array(self::BELONGS_TO, 'User', 'last_modified_user_id'),
            'generated_document' => array(self::BELONGS_TO, 'ProtectedFile', 'generated_document_id'),
            'site' => array(self::BELONGS_TO, 'Site', 'site_id'),
            // define duplicate event relations for elements to be eager loaded in the same call
            'clinical_event' => array(self::BELONGS_TO, 'Event', 'event_id'),
            'clinical_element' => array(
                self::HAS_ONE,
                'OEModule\OphCoCvi\models\Element_OphCoCvi_ClinicalInfo',
                array('id' => 'event_id'),
                'through' => 'clinical_event'
            ),
            'clerical_event' => array(self::BELONGS_TO, 'Event', 'event_id'),
            'clerical_element' => array(
                self::HAS_ONE,
                'OEModule\OphCoCvi\models\Element_OphCoCvi_ClericalInfo',
                array('id' => 'event_id'),
                'through' => 'clerical_event'
            ),
            'demographics_event' => array(self::BELONGS_TO, 'Event', 'event_id'),
            'demographics_element' => array(
                self::HAS_ONE,
                'OEModule\OphCoCvi\models\Element_OphCoCvi_Demographics',
                array('id' => 'event_id'),
                'through' => 'demographics_event'
            ),
            'consultantInChargeOfThisCvi' => array(self::BELONGS_TO, 'Firm', 'consultant_in_charge_of_this_cvi_id'),
            'consultant_event'  => array(self::BELONGS_TO, 'Event', 'event_id'),

            'esign_event' => array(self::BELONGS_TO, 'Event', 'event_id'),
            'esign_element' => array(
                self::HAS_ONE,
                'OEModule\OphCoCvi\models\Element_OphCoCvi_Esign',
                array('id' => 'event_id'),
                'through' => 'esign_event'
            ),
            'consent_event' => array(self::BELONGS_TO, 'Event', 'event_id'),
            'consent_element' => array(
                self::HAS_ONE,
                'OEModule\OphCoCvi\models\Element_OphCoCvi_Consent',
                array('id' => 'event_id'),
                'through' => 'consent_event'
            ),


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
            'is_draft' => 'Is draft',
            'generated_document_id' => 'Generated file',
            'site_id' => 'Site',
            'consultant_in_charge_of_this_cvi_id' => 'Consultant in charge of this CVI',
        );
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search()
    {
        $criteria = new CDbCriteria();

        $criteria->compare('id', $this->id, true);
        $criteria->compare('event_id', $this->event_id, true);
        $criteria->compare('is_draft', $this->is_draft);
        $criteria->compare('generated_document_id', $this->generated_document_id);

        return new CActiveDataProvider(get_class($this), array(
            'criteria' => $criteria,
        ));
    }

    /**
     * @TODO: Should probably be storing a fixed date for issue rather than relying on modified date
     *
     * @return null|string
     */
    public function getIssueDateForDisplay()
    {
        if (!$this->is_draft) {
            return $this->last_modified_date;
        }
        return null;
    }

    /**
     * @return string
     */
    public function getIssueStatusForDisplay()
    {
        if ($this->is_draft) {
            return $this->event->info ? $this->event->info : 'Draft';
        }
        if ($this->event->info !== null) {
            return $this->event->info;
        }
        return 'Issued';
    }

    /**
     * Returns an associative array of the data values for printing
     */
    public function getStructuredDataForPrint()
    {
        $result = array();

        return $result;
    }

    /**
     * @return string
     */
    public function getConsultantSignature()
    {
        foreach ($this->esign_element->getSignatures() as $signature) {
            if ((int)$signature->type === \BaseSignature::TYPE_LOGGEDIN_USER) {
                return file_get_contents($signature->signatureFile->getPath());
            }
        }
    }

    public function getSignatureByType(int $type): ?\OphCoCvi_Signature
    {
        if ($this->esign_element) {
            foreach ($this->esign_element->getSignatures() as $signature) {
                if ((int)$signature->type === $type && $signature->signatureFile) {
                    return $signature;
                }
            }
        }

        return null;
    }

    /*
     * Get elements for CVI PDF
     *
     * @return array
     */
    public function getElementsForCVIpdf()
    {
        $siteAddress1 = '';
        $siteAddress2 = '';
        if ($this->site) {
            $siteAddress1 = str_replace(array("\n","\r"), ' ', $this->site->contact->address->address1);
            $siteAddress2 = $this->site->contact->address->city . ' ' . $this->site->contact->address->postcode;
        }

        $elements = [
            'consultantSignature' => $this->getConsultantSignature(),
            'Hospital_address1' => $siteAddress1,
            'Hospital_address2' => $siteAddress2,
        ];

        return $elements;
    }
}
