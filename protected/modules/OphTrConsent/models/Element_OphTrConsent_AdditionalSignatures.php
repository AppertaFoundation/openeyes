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

namespace OEModule\OphTrConsent\models;

/**
 * This is the model class for table "et_ophtrconsent_additional_signatures".
 *
 * The followings are the available columns in table 'et_ophtrconsent_additional_signatures':
 * @property integer $id
 * @property string $event_id
 * @property integer $interpreter_required
 * @property string $interpreter_name
 * @property integer $witness_required
 * @property string $witness_name
 * @property integer $child_agreement
 * @property string $last_modified_user_id
 * @property string $last_modified_date
 * @property string $created_user_id
 * @property string $created_date
 * @property int $guardian_required
 * @property string $guardian_name
 * @property int $interpreter_signature_id
 * @property int $witness_signature_id
 * @property int $guardian_signature_id
 * @property int $patient_signature_id
 * @property int $child_signature_id
 *
 * The followings are the available model relations:
 * @property \User $createdUser
 * @property \User $lastModifiedUser
 * @property \Event $event
 * @property \OphTrConsent_Signature $guardianSignature
 * @property \OphTrConsent_Signature $witnessSignature
 * @property \OphTrConsent_Signature $interpreterSignature
 * @property \OphTrConsent_Signature $childSignature
 * @property \OphTrConsent_Signature $patientSignature
 */

class Element_OphTrConsent_AdditionalSignatures extends \BaseEventTypeElement implements RequiresSignature
{
    public $cf_type_id;

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'et_ophtrconsent_additional_signatures';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('interpreter_required, witness_required, child_agreement, guardian_required', 'numerical', 'integerOnly' => true),
            array('interpreter_signature_id, witness_signature_id, guardian_signature_id, patient_signature_id, child_signature_id', 'numerical', 'integerOnly' => true),
            array('event_id, last_modified_user_id, created_user_id', 'length', 'max' => 10),
            array('interpreter_name, witness_name, guardian_name, guardian_relationship', 'length', 'max' => 255),
            array('last_modified_date, created_date, cf_type_id', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, event_id, interpreter_required, interpreter_name, witness_required, witness_name, child_agreement, last_modified_user_id, last_modified_date, created_user_id, created_date', 'safe', 'on'=>'search'),
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
            'event' => array(self::BELONGS_TO, 'Event', 'event_id'),
            'interpreterSignature' => array(self::BELONGS_TO, \OphTrConsent_Signature::class, 'interpreter_signature_id'),
            'witnessSignature' => array(self::BELONGS_TO, \OphTrConsent_Signature::class, 'witness_signature_id'),
            'guardianSignature' => array(self::BELONGS_TO, \OphTrConsent_Signature::class, 'guardian_signature_id'),
            'patientSignature' => array(self::BELONGS_TO, \OphTrConsent_Signature::class, 'patient_signature_id'),
            'childSignature' => array(self::BELONGS_TO, \OphTrConsent_Signature::class, 'child_signature_id'),
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
            'interpreter_required' => 'Interpreter Required',
            'interpreter_name' => 'Interpreter Name',
            'witness_required' => 'Witness Required',
            'witness_name' => 'Witness Name',
            'child_agreement' => 'Child Agreement',
            'guardian_required' => 'Parent / Guardian signature',
            'guardian_name' => 'Name of Parent / Guardian',
            'guardian_relationship' => 'Relationship',
            'last_modified_user_id' => 'Last Modified User',
            'last_modified_date' => 'Last Modified Date',
            'created_user_id' => 'Created User',
            'created_date' => 'Created Date',
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
     * @return \CActiveDataProvider the data provider that can return the models
     * based on the search/filter conditions.
     */
    public function search()
    {
        // @todo Please modify the following code to remove attributes that should not be searched.

        $criteria=new \CDbCriteria;

        $criteria->compare('id', $this->id);
        $criteria->compare('event_id', $this->event_id, true);
        $criteria->compare('interpreter_required', $this->interpreter_required);
        $criteria->compare('interpreter_name', $this->interpreter_name, true);
        $criteria->compare('witness_required', $this->witness_required);
        $criteria->compare('witness_name', $this->witness_name, true);
        $criteria->compare('child_agreement', $this->child_agreement);
        $criteria->compare('last_modified_user_id', $this->last_modified_user_id, true);
        $criteria->compare('last_modified_date', $this->last_modified_date, true);
        $criteria->compare('created_user_id', $this->created_user_id, true);
        $criteria->compare('created_date', $this->created_date, true);

        return new \CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return Element_OphTrConsent_AdditionalSignatures the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    /**
     * @inheritDoc
     */
    protected function beforeSave()
    {
        if ((int)$this->witness_required === 0) {
            $this->witness_name = null;
        }
        if ((int)$this->interpreter_required === 0) {
            $this->interpreter_name = null;
        }
        if ((int)$this->guardian_required === 0) {
            $this->guardian_name = null;
            $this->guardian_relationship = null;
        }

        return parent::beforeSave();
    }

    public function eventScopeValidation(array $elements)
    {
        if (!empty($elements)) {
            if ($this->witness_required === "1" && strlen(trim($this->witness_name)) < 1) {
                $this->addError(
                    "witness_name",
                    "Witness name cannot be empty"
                );
            }

            if ($this->interpreter_required === "1" && strlen(trim($this->interpreter_name)) < 1) {
                $this->addError(
                    "interpreter_name",
                    "Interpreter name cannot be empty"
                );
            }

            if ($this->guardian_required === "1" && strlen(trim($this->guardian_name)) < 1) {
                $this->addError(
                    "guardian_name",
                    "Patient / Guardian name cannot be empty"
                );
            }
        }
    }

    public function getConsentTypeID()
    {
        return $this->event->getElementByClass(\Element_OphTrConsent_Type::class)->type_id;
    }

    /**
     * @inheritDoc
     */
    public function getRequiredSignatures(): array
    {
        $signatures = [];
        switch ($this->getConsentTypeID()) {
            case \Element_OphTrConsent_Type::TYPE_PATIENT_AGREEMENT_ID:
            case \Element_OphTrConsent_Type::TYPE_PATIENT_PARENTAL_AGREEMENT_ID:
                $signatures[] = $this->patientSignature ?? $this->getPatientSignatureInstance();
                if ($this->witness_required) {
                    $signatures[] = $this->witnessSignature ?? $this->getWitnessSignatureInstance();
                }
                if ($this->interpreter_required) {
                    $signatures[] = $this->interpreterSignature ?? $this->getInterpreterSignatureInstance();
                }
                break;
            case \Element_OphTrConsent_Type::TYPE_PARENTAL_AGREEMENT_ID:
                if ($this->interpreter_required) {
                    $signatures[] = $this->interpreterSignature ?? $this->getInterpreterSignatureInstance();
                }
                if ($this->guardian_required) {
                    $signatures[] = $this->guardianSignature ?? $this->getParentGuardianSignatureInstance();
                }
                if ($this->child_agreement) {
                    $signatures[] = $this->childSignature ?? $this->getChildSignatureInstance();
                }
                break;
        }
        return $signatures;
    }

    /**
     * @inheritDoc
     */
    public function afterSignedCallback(int $row_id, int $signature_id): void
    {
        $row_id_map = [
            1 => "witness_signature_id",
            2 => "interpreter_signature_id",
            3 => "guardian_signature_id",
            4 => "child_signature_id",
            5 => "patient_signature_id",
        ];
        if(!array_key_exists($row_id, $row_id_map)) {
            throw new \Exception("Invalid row_id: $row_id");
        }
        $field = $row_id_map[$row_id];
        $this->$field = $signature_id;
        $this->save(false, [$field]);
    }

    private function getSignatureInstance(array $attrs): \OphTrConsent_Signature
    {
        $signature = new \OphTrConsent_Signature();
        $signature->setAttributes($attrs);
        return $signature;
    }

    private function getWitnessSignatureInstance(): \OphTrConsent_Signature
    {
        return $this->getSignatureInstance([
            "type" => \BaseSignature::TYPE_PATIENT,
            "signatory_role" => "Witness",
            "signatory_name" => $this->witness_name,
            "initiator_row_id" => 1,
        ]);
    }

    private function getInterpreterSignatureInstance(): \OphTrConsent_Signature
    {
        return $this->getSignatureInstance([
            "type" => \BaseSignature::TYPE_PATIENT,
            "signatory_role" => "Interpreter",
            "signatory_name" => $this->interpreter_name,
            "initiator_row_id" => 2,
        ]);
    }

    private function getParentGuardianSignatureInstance(): \OphTrConsent_Signature
    {
        return $this->getSignatureInstance([
            "type" => \BaseSignature::TYPE_PATIENT,
            "signatory_role" => "Parent / Guardian",
            "signatory_name" => $this->guardian_name,
            "initiator_row_id" => 3,
        ]);
    }

    private function getChildSignatureInstance(): \OphTrConsent_Signature
    {
        return $this->getSignatureInstance([
            "type" => \BaseSignature::TYPE_PATIENT,
            "signatory_role" => "Child",
            "signatory_name" => $this->event->patient->getFullName(),
            "initiator_row_id" => 4,
        ]);
    }

    private function getPatientSignatureInstance(): \OphTrConsent_Signature
    {
        return $this->getSignatureInstance([
            "type" => \BaseSignature::TYPE_PATIENT,
            "signatory_role" => "Patient",
            "signatory_name" => $this->event->patient->getFullName(),
            "initiator_row_id" => 5,
        ]);
    }
}
