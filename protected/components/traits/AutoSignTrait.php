<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2022
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2022, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

/**
  * Element trait to apply to EventType Elements that require signatures to be attached.
  *
  * Supports automatic signatures when events are configured to not require PIN entry (see system settings)
  */
trait AutoSignTrait
{
    /**
     * Attempt to add a signature for the current user to the implementing element
     * @return bool
     */
    public function attemptAutoSign(): bool
    {
        require_once($this->signature_class . '.php');
        if (in_array(Yii::app()->controller->getAction()->getId(), array('create', 'update')) && SettingMetadata::model()->checkSetting($this->pin_required_setting_name, 'no')) {

            $this->signatures = [$this->createUserSignature(true)];

            return true;
        }

        return false;
    }

    /**
     * Create a signature for this event for the current user
     * @return bool
     */
    public function createUserSignature(bool $add_proof, $signature_type = BaseSignature::TYPE_LOGGEDIN_USER): BaseSignature {
        $user = SignatureHelper::getUserForSigning();

        $signature_class = $this->signature_class;

        $user_signature = new $signature_class();
        $user_signature->type = $signature_type;

        if ($add_proof) {
            $user_signature->proof = \SignatureHelper::getSignatureProof($user->signature->id, new \DateTime(), $user->id);
            $user_signature->setDataFromProof();
        }

        if(isset($this->auto_sign_role)) {
            $user_signature->signatory_role = $this->auto_sign_role;
        }

        return $user_signature;
    }
}