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
 * Class Element_OphDrPrescription_Esign
 *
 * @property int $id
 * @property int $event_id
 *
 * @property Event $event
 * @property OphDrPrescription_Signature[] $signatures
 */

use OEModule\OphDrPrescription\widgets\PrescriptionEsignElementWidget;

class Element_OphDrPrescription_Esign extends BaseEsignElement
{
    use AutoSignTrait;
    private $signature_class = \OphDrPrescription_Signature::class;
    private $pin_required_setting_name = 'require_pin_for_prescription';
    public $auto_sign_role = 'Prescriber';

    protected $widgetClass = PrescriptionEsignElementWidget::class;

    private array $required_secondary_signatories = [];

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
        return 'et_ophdrprescription_esign';
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
            'event' => array(self::BELONGS_TO, Event::class, 'event_id'),
            'user' => array(self::BELONGS_TO, User::class, 'created_user_id'),
            'usermodified' => array(self::BELONGS_TO, User::class, 'last_modified_user_id'),
            'signatures' => array(self::HAS_MANY, OphDrPrescription_Signature::class, 'element_id')
        );
    }

    public function getDetailsElement():? Element_OphDrPrescription_Details
    {
        return Element_OphDrPrescription_Details::model()->findByAttributes(['event_id' => $this->event_id]);
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

    public function getSignatures(): array
    {
        if (!empty(\Yii::app()->session['user']->grade->grade)) {
            $this->auto_sign_role = \Yii::app()->session['user']->grade->grade;
        }

        if (in_array(\Yii::app()->controller->action->id ?? null, ["create", "update"])) {
            return $this->getCreateSignatures();
        }

        return $this->getViewSignatures();
    }

    private function getPrescriberSignatureObject()
    {
        $prescriber = array_filter($this->signatures, fn($sign) => (int)$sign->type === BaseSignature::TYPE_LOGGEDIN_USER)[0] ?? null;

        if (!$prescriber) {
            $prescriber = new OphDrPrescription_Signature();
            $prescriber->type = BaseSignature::TYPE_LOGGEDIN_USER;
        }

        $prescriber->signatory_role = $this->auto_sign_role;

        return $prescriber;
    }

    public function getSecondarySignatures(Institution $institution = null): array
    {
        if ($this->required_secondary_signatories) {
            return $this->required_secondary_signatories;
        }

        $required_signatories = [];
        $current_institution = $institution ?: Institution::model()->getCurrent();

        $secondary_signatories = SecondarySignatory::model()->findAll('institution_id = :institution_id OR institution_id IS NULL', [
            ':institution_id' => $current_institution->id
        ]);

        foreach ($secondary_signatories as $secondary_signatory) {
            $signatory = new OphDrPrescription_Signature();
            $signatory->signatory_role = $secondary_signatory->name;
            $signatory->type = \BaseSignature::TYPE_OTHER_USER;

            $required_signatories[] = $signatory;
        }

        $this->required_secondary_signatories = $required_signatories;

        return $this->required_secondary_signatories;
    }

    public function getCreateSignatures(): array
    {
        return [$this->getPrescriberSignatureObject()];
    }

    public function getViewSignatures(): array
    {
        $signatures = $this->getRelated('signatures', true);
        $secondary_signatories = $this->getSecondarySignatures($this->event->institution);
        $signature_list = [];
        if ($this->isNewRecord) {
            $prescriber = $this->getPrescriberSignatureObject();
            $signature_list = array_merge([$prescriber], $secondary_signatories);
        } else {
            $filtered_signatures = array_filter($signatures, fn($sign) => $sign->signatory_role === $this->auto_sign_role);
            $prescriber = reset($filtered_signatures);

            // Prescription saved without prescriber's sign
            if (!$prescriber) {
                $signature_list[] = $this->getPrescriberSignatureObject();
            } else {
                $signature_list[] = $prescriber;
            }

            foreach ($secondary_signatories as $secondary_signatory) {
                $saved_signature = array_filter($signatures, function ($sign) use ($secondary_signatory) {
                    return $sign->signatory_role === $secondary_signatory->signatory_role;
                });

                $signature_list[] = $saved_signature ? array_shift($saved_signature) : $secondary_signatory;
            }
        }

        return $this->signatures = $signature_list;
    }

    /**
     * A prescription is signed
     * @return bool
     */
    public function isSigned(): bool
    {
        return !empty(
            array_filter(
                $this->getSignatures(),
                function ($signature) {
                    return $signature->isSigned();
                }
            )
        );
    }

    /**
     * @inheritDoc
     */
    public function getUnsignedMessage(): string
    {
        return "This prescription must be signed before it can be issued.";
    }

    /**
     * @param array $elements
     */
    public function eventScopeValidation(array $elements)
    {
        $elements = array_filter(
            $elements,
            function ($element) {
                return $element instanceof Element_OphDrPrescription_Details;
            }
        );
        if (!empty($elements)) {
            $prescription_details = $elements[0];
            /** @var Element_OphDrPrescription_Details $prescription_details */
            if (!$this->isSigned() && !$prescription_details->draft) {
                $this->addError(
                    "id",
                    "Signature must be provided to finalize this Prescription."
                );
            }
        }
    }

    public function getViewTitle(): string
    {
        return "Electronic Signature";
    }

    public function getContainer_print_view()
    {
        return false;
    }
}
