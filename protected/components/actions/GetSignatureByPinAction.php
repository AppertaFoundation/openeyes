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

class GetSignatureByPinAction extends \CAction
{
    use RenderJsonTrait;

    protected User $user;
    protected string $pin;
    protected DateTime $date_time;
    protected string $signature_proof;
    protected string $thumbnail_src1;
    protected string $thumbnail_src2;
    protected ?int $signature_file_id = null;
    protected int $context;

    protected bool $is_secretary_signing = false;

    protected function getUser() : void
    {
        $this->user = User::model()->findByPk(Yii::app()->user->id);
        if (!$this->user) {
            throw new Exception("An error occurred while trying to fetch your signature. Please contact support.");
        }

        // Check if the user has the Prescribe role, if not throw an exception.
        $user_roles = Yii::app()->user->getRole(Yii::app()->user->id);
        $can_prescribe = in_array('Prescribe', array_values($user_roles));
        if (!$can_prescribe) {
            throw new Exception("You do not have the necessary user rights to sign this prescription.");
        }
    }

    protected function getSignatureFile() : void
    {
        if(!$this->is_secretary_signing) {
            $signature_data = SignatureHelper::getUserSignatureFile($this->user->id);
            $this->signature_file_id = $signature_data['signature_file_id'];
            $this->thumbnail_src1 = $signature_data['thumbnail_src1'];
            $this->thumbnail_src2 = $signature_data['thumbnail_src2'];
        }
    }

    protected function checkPIN() : void
    {
        if ($this->is_secretary_signing) {
            $this->checkSecretaryPIN();
        } else {
            if(strlen($this->pin) === 0) {
                throw new Exception("Empty PIN was provided, please enter PIN and click 'PIN sign' again.");
            }
            if(!$this->user->checkPin($this->pin)) {
                throw new Exception("Incorrect PIN");
            }
        }
    }

    private function checkSecretaryPIN() : void
    {
        if (!Yii::app()->user->checkAccess('SignEvent')) {
            throw new Exception("We're sorry, you are not authorized to sign events. Please contact support.");
        }
    }

    private function checkIsSecretarySigning(): void {
        $secretary_can_sign = $this->controller->secretary_can_sign ?? false;
        $this->is_secretary_signing = ($this->pin === Yii::app()->params["secretary_pin"] && $secretary_can_sign);
    }

    /**
     * @inheritDoc
     */
    public function run()
    {
        $this->pin = Yii::app()->request->getPost('pin');

        try {
            $this->checkIsSecretarySigning();

            if ($this->is_secretary_signing) {
                $user_id = Yii::app()->request->getPost('user_id');
                $this->user = User::model()->findByPk($user_id);
                if (!$this->user) {
                    throw new Exception("An error occurred while trying to fetch your signature. Please contact support.");
                }
            } else {
                $this->user = SignatureHelper::getUserForSigning();
            }

            // Check if the user has the necessary permissions to sign this event, if not throw an exception.
            if (isset($this->controller->required_user_sign_permissions)){
                foreach ($this->controller->required_user_sign_permissions as $permission)
                {
                    if (!Yii::app()->user->checkAccess($permission)) {
                        throw new Exception("Unable to sign, user does not have necessary permissions.");
                    }
                }
            }
            $this->checkPIN();
            $this->getSignatureFile();
        } catch (Exception $e) {
            $this->renderJSON([
                'code' => 1,
                'error' => $e->getMessage(),
            ]);
        }

        $this->date_time = new DateTime();
        $this->signature_proof = $this->getSignatureProof($this->signature_file_id);

        if(Yii::app()->request->getPost('mode') !== "edit") {
            $this->updateElement(
                (int)Yii::app()->request->getPost('element_id'),
                (int)Yii::app()->request->getPost('element_type_id'),
                (int)Yii::app()->request->getPost('signature_type')
            );
        }
        $this->successResponse();
    }

    protected function successResponse()
    {
        $response = [
            'code' => 0,
            'error' => "",
            'signature_proof' => $this->signature_proof,
            'date' => $this->date_time->format(Helper::NHS_DATE_FORMAT),
            'time' => $this->date_time->format("H:i"),
            'signed_by_secretary' => $this->is_secretary_signing,
        ];

        if (!$this->is_secretary_signing) {
            $response['signature_image1_base64'] = $this->thumbnail_src1;
            $response['signature_image2_base64'] = $this->thumbnail_src2;
        }

        $this->renderJSON($response);
    }

    /**
     * Creates a proof that can be safely transferred to the client
     * without the risk of being spoofed. This proof then can be used
     * to recreate the signature date on the server when the form is
     * finally submitted.
     *
     * @param int|null $signature_file_id
     * @return string
     */
    private function getSignatureProof(?int $signature_file_id) : string
    {
        return (new EncryptionDecryptionHelper())->encryptData(serialize([
            "signature_file_id" => $signature_file_id,
            "timestamp" => $this->date_time->getTimestamp(),
            "user_id" => $this->user->id,
        ]));
    }

    /**
     * Updates the element. This is required when signing in view mode
     * @return void
     */
    protected function updateElement(int $element_id, int $element_type_id, int $signature_type) : void
    {
        /** @var Event $event */
        if($event = Event::model()->findByPk(Yii::app()->request->getPost("event_id"))) {
            $els = array_filter(
                $event->getElements(),
                function ($e) use ($element_id, $element_type_id) {
                    return (int)$e->id === $element_id && (int)$e->getElementType()->id === $element_type_id;
                }
            );
            if($els) {
                /** @var BaseEsignElement $element */
                $element = array_shift($els);
                $sig_class = $element->relations()["signatures"][1];
                /** @var BaseSignature $signature */
                $signature = new $sig_class();
                $signature->setAttributes([
                    "element_id" => $element_id,
                    "timestamp" => $this->date_time->getTimestamp(),
                    "signature_file_id" => $this->signature_file_id,
                    "signed_user_id" => $this->user->id,
                    "type" => $signature_type,
                    "signatory_role" => urldecode(Yii::app()->request->getPost("signatory_role")),
                    "signatory_name" => urldecode(Yii::app()->request->getPost("signatory_name")),
                ], false);
                if($signature->hasAttribute("secretary") && property_exists($this, "is_secretary_signing")) {
                    $signature->secretary = $this->is_secretary_signing;
                }
                foreach (["initiator_element_type_id", "initiator_row_id"] as $attr) {
                    if($signature->hasAttribute($attr)) {
                        $signature->$attr = Yii::app()->request->getPost($attr);
                    }
                }
                $signature->save(false);
            }
        }
    }
}