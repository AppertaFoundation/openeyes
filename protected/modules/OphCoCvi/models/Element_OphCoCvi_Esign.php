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

use AutoSignTrait;

/**
 * Class Element_OphCoCvi_Esign
 *
 * @property int $id
 * @property int $event_id
 *
 * @property Event $event
 * @property \OphCoCvi_Signature[] $signatures
 */
class Element_OphCoCvi_Esign extends \BaseEsignElement
{
    use AutoSignTrait;
    private $signature_class = \OphCoCvi_Signature::class;
    private $pin_required_setting_name = 'require_pin_for_cvi';

    protected $widgetClass = \OEModule\OphCoCvi\widgets\EsignElementWidget::class;
    /**
     * Returns the static model of the specified AR class.
     *
     * @return static the static model class
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
        return 'et_ophcocvi_esign';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        return array(
            array('event_id', 'safe'),
            array('id, event_id', 'safe', 'on' => 'search'),
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
            'event' => array(self::BELONGS_TO, \Event::class, 'event_id'),
            'user' => array(self::BELONGS_TO, \User::class, 'created_user_id'),
            'usermodified' => array(self::BELONGS_TO, \User::class, 'last_modified_user_id'),
            'signatures' => array(self::HAS_MANY, \OphCoCvi_Signature::class, 'element_id', 'condition' => 'status = ' . \OphCoCvi_Signature::STATUS_ACTIVE . ''),
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
        );
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     *
     * @return \CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search()
    {
        // Warning: Please modify the following code to remove attributes that
        // should not be searched.

        $criteria = new \CDbCriteria();

        $criteria->compare('id', $this->id, true);
        $criteria->compare('event_id', $this->event_id, true);

        return new \CActiveDataProvider(get_class($this), array(
            'criteria' => $criteria,
        ));
    }

    /**
     * @return \OphCoCvi_Signature[]
     */
    public function getSignatures(): array
    {
        $signatures = $this->signatures;
        $existing_types = array_map(function ($e) {
            return $e->type;
        }, $signatures);

        if (!in_array(\BaseSignature::TYPE_LOGGEDIN_USER, $existing_types)) {
            $signatures[] = $this->generateDefaultConsultantSignature();
        }

        if (!in_array(\BaseSignature::TYPE_PATIENT, $existing_types)) {
            if ($patient_signature = $this->generateDefaultPatientSignature()) {
                $signatures[] = $patient_signature;
            }
        }

        return $signatures;
    }

    /**
     * A CVI is signed if all of the signatures
     * (consultant and patient) is done
     *
     * @return bool
     */
    public function isSigned(): bool
    {
        foreach ($this->getSignatures() as $signature) {
            if (!$signature->isSigned()) {
                return false;
            }
        }
        return true;
    }

    public function isSignedByConsultant(): bool
    {
        foreach ($this->getSignatures() as $signature) {
            if ((int)$signature->type === \BaseSignature::TYPE_LOGGEDIN_USER) {
                return $signature->isSigned();
            }
        }

        return false;
    }

    public function isSignedByPatient(): bool
    {
        foreach ($this->getSignatures() as $signature) {
            if ((int)$signature->type === \BaseSignature::TYPE_PATIENT) {
                return $signature->isSigned();
            }
        }

        return false;
    }

    /**
     * @inheritDoc
     */
    public function getUnsignedMessage(): string
    {
        return "Please note the CVI is only valid if signed by a Consultant and the Patient as well.";
    }

    /**
     * @inheritDoc
     */
    public function getInfoMessages(): array
    {
        if (
            !$this->getSignaturesByType(\BaseSignature::TYPE_PATIENT)
            && (!$this->event || $this->event->isNewRecord)
        ) {
            return ["Patient's E-Sign will be available once the CVI is saved."];
        }
        return [];
    }

    /**
     * This function generate default signature data for esign elements
     * @return \OphCoCvi_Signature consultant signature
     */
    protected function generateDefaultConsultantSignature()
    {
        $user = $this->getChangeUser();
        $consultant_signature = new \OphCoCvi_Signature();
        $consultant_signature->signatory_role = !empty($user->grade) ? $user->grade->grade : "Unknown grade";
        $consultant_signature->type = \BaseSignature::TYPE_LOGGEDIN_USER;
        return $consultant_signature;
    }

    /**
     * This function generate default signature data for esign elements
     * @return \OphCoCvi_Signature patient signature | null
     */
    protected function generateDefaultPatientSignature()
    {
        if ($this->event && !$this->event->isNewRecord) {
            $patient = $this->event->patient;

            $patient_signature = new \OphCoCvi_Signature();
            $patient_signature->type = \BaseSignature::TYPE_PATIENT;
            $patient_signature->signatory_role = "Patient";
            $patient_signature->signatory_name = $patient->getFullName();

            return $patient_signature;
        }

        return null;
    }
}
