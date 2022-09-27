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
 * Class BaseEsignElement
 *
 * @property BaseSignature[] $signatures
 */
abstract class BaseEsignElement extends BaseEventTypeElement
{
    /** @var int Dummy property to allow saving without any fields filled in */
    public $dummy;

    protected $widgetClass = EsignElementWidget::class;

    /**
     * This method must be implemented in child classes and must
     * return an array of signatures. If there are no saved signatures
     * yet, the method must return the default empty signatures
     *
     * @return BaseSignature[]
     */
    abstract public function getSignatures() : array;

    /**
     * This method must be implemented in child classes and must
     * tell whether the element is considered to be signed based
     * on any specific rules defined in the element.
     * E. g. some elements may require all signatures while others
     * may require at least one signature.
     *
     * @return bool
     */
    abstract public function isSigned() : bool;

    /**
     * A message to be displayed if the element has not been signed yet.
     * It is advised to override this method in child classes to display
     * a more informational message.
     *
     * @return string
     */
    public function getUnsignedMessage() : string
    {
        return "This event must be signed.";
    }

    /**
     * @return bool Whether an E-sign device can be used to capture the signature
     */
    public function usesEsignDevice() : bool
    {
        return !empty(
            array_filter($this->getSignatures(), function ($signature) {
                return $signature->usesEsignDevice();
            })
        );
    }

    /**
     * @inheritDoc
     */
    public function beforeValidate()
    {
        // Remove empty signatures so that they do not get saved
        $this->signatures = array_filter(
            $this->signatures,
            function ($signature) {
                return $signature->isSigned();
            }
        );
        parent::beforeValidate();
    }

    /**
     * @inheritDoc
     */
    public function afterSave()
    {
        // Save signatures
        foreach ($this->signatures as $signature) {
            $signature->element_id = $this->id;
            $signature->save(false);
        }
        parent::afterSave();
    }

    /** @return array Informational messages to display */
    public function getInfoMessages(): array
    {
        return [];
    }

    /** @return array Warning messages to display */
    public function getWarningMessages(): array
    {
        return [];
    }

    /**
     * @param int $type
     * @return BaseSignature[] All captured signatures of the given type
     */
    public function getSignaturesByType(int $type, bool $include_signed = true)
    {
        return array_filter($this->signatures, function ($signature) use ($type, $include_signed) {
            return (int)$signature->type === $type && ($include_signed || !$signature->isSigned());
        });
    }

    /**
     * This function generate default signature data for esign elements
     * @return \OphCoCvi_Signature consultant signature
     */
    protected function generateDefaultConsultantSignature()
    {
        $consultant_signature = new \OphCoCvi_Signature();

        if (isset($this->getApp()->user)) {
            $uid = $this->getApp()->user->id === null ? $this->user->id : $this->getApp()->user->id;
            $user = \User::model()->findByPk($uid);
        } else {
            $user = $this->user;
        }

        $consultant_signature->signatory_role = !empty($user->grade) ? $user->grade->grade : "Unknown grade";
        $consultant_signature->type = \BaseSignature::TYPE_LOGGEDIN_USER;
        return $consultant_signature;
    }

    /**
     * This function generate default signature data for esign elements
     * @return \OphCoCvi_Signature patient signature
     */
    protected function generateDefaultPatientSignature()
    {
        $patient_signature = new \OphCoCvi_Signature();
        $patient_signature->signatory_role = "Patient";
        $patient = \Yii::app()->getController()->patient;

        if (isset($patient)) {
            $patient_signature->signatory_name = $patient->getFullName();
        }

        $patient_signature->type = \BaseSignature::TYPE_PATIENT;
        return $patient_signature;
    }
}
