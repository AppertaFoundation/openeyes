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
 * Class BaseSignature
 * This class holds common functionality for active records that represent
 * a single e-signature (consultant's, patient's, etc)
 *
 * Descendant AR's db tables must provide at least have the following fields:
 * @property int $type
 * @property int $signature_file_id
 * @property string $signatory_role
 * @property string $signatory_name
 * @property int $signed_user_id
 * @property int $timestamp
 *
 * @property ProtectedFile $signatureFile
 * @property BaseEsignElement $element
 */
abstract class BaseSignature extends BaseActiveRecordVersioned
{
    /** @var int Signed by the logged in user */
    public const TYPE_LOGGEDIN_USER = 1;
    /** @var int Signed by a user other than the one logged in */
    public const TYPE_OTHER_USER = 2;
    /** @var int Captured signature of a non-user */
    public const TYPE_PATIENT = 3;
    /** @var int Signed by the logged in user Medication Management*/
    public const TYPE_LOGGEDIN_MED_USER = 5;

    /** @var string The proof string previously created on server and sent to the client */
    public string $proof = "";

    /**
     * Recreate signature data based on the the encrypted proof of the signature
     */
    public function setDataFromProof() : void
    {
        if ($this->proof !== "") {
            $decrypted = unserialize((new EncryptionDecryptionHelper())->decryptData($this->proof));
            $this->signature_file_id = $decrypted["signature_file_id"];
            $this->timestamp = $decrypted["timestamp"];
            $this->signed_user_id = $decrypted["user_id"];
        }
    }

    /**
     * @return string A formatted date string
     */
    public function getSignedDate() : string
    {
        if (is_null($this->timestamp)) {
            return "-";
        }
        return (new DateTime())
            ->setTimestamp($this->timestamp)
            ->format(Helper::NHS_DATE_FORMAT);
    }

    /**
     * @return string A formatted time string
     */
    public function getSignedTime() : string
    {
        if (is_null($this->timestamp)) {
            return "-";
        }
        return (new DateTime())
            ->setTimestamp($this->timestamp)
            ->format("H:i");
    }

    /**
     * Returns whether signed by the user
     * Child classes may implement different logic
     *
     * @return bool
     */
    public function isSigned() : bool
    {
        return !is_null($this->signature_file_id);
    }

    /**
     * Ensure that the signature is immutable once it is saved
     *
     * @return bool
     */
    public function beforeSave()
    {
        return $this->isNewRecord && parent::beforeSave();
    }

    /**
     * Update any signature requests that are waiting for this signature
     */
    public function afterSave()
    {
        SignatureRequest::model()->updateAll(
            [
                "signature_date" => (new DateTime())->setTimestamp($this->timestamp)->format("Y-m-d H:i:s")
            ],
            "signature_date IS NULL
              AND event_id =:event_id
              AND element_type_id=:element_type_id
              AND signature_type=:signature_type",
            [
                ":event_id" => $this->element->event_id,
                ":element_type_id" => $this->element->getElementType()->id,
                ":signature_type" => $this->type,
            ]
        );

        parent::afterSave();
    }

    /**
     * @return bool Whether an E-sign device can be used to capture the signature
     */
    public function usesEsignDevice() : bool
    {
        return $this->type === self::TYPE_PATIENT;
    }

    /**
     * Get signature output to the printout
     */
    public function getPrintout() : string
    {
        // To be implemented in another task
        return "";
    }

    /**
     * @return string[] If not empty, the user is allowed to select a signatory role from this list
     */
    public function getRoleOptions() : array
    {
        return [];
    }
}
