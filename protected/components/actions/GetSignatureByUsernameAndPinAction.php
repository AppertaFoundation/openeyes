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

class GetSignatureByUsernameAndPinAction extends GetSignatureByPinAction
{
    protected bool $is_secretary_signing = false;

    protected function getUser(): void
    {
        $user_id = Yii::app()->request->getPost('user_id');
        $this->user = User::model()->findByPk($user_id);
        if (!$this->user) {
            throw new Exception("An error occurred while trying to fetch your signature. Please contact support.");
        }
    }

    protected function checkPIN(): void
    {
        $secretary_can_sign = $this->controller->secretary_can_sign ?? false;
        if ($this->pin === Yii::app()->params["secretary_pin"] && $secretary_can_sign) {
            $this->is_secretary_signing = true;
            $this->checkSecretaryPIN();
        } else {
            parent::checkPIN();
        }
    }

    private function checkSecretaryPIN()
    {
        if (!Yii::app()->user->checkAccess('SignEvent')) {
            throw new Exception("We're sorry, you are not authorized to sign events. Please contact support.");
        }
    }

    protected function getSignatureFile(): void
    {
        if(!$this->is_secretary_signing) {
            parent::getSignatureFile();
        }
    }

    protected function successResponse()
    {
        if ($this->is_secretary_signing) {
            $this->renderJSON([
                "code" => 0,
                "error" => "",
                "signature_proof" => $this->signature_proof,
                'date' => $this->date_time->format(Helper::NHS_DATE_FORMAT),
                'time' => $this->date_time->format("H:i"),
                'signed_by_secretary' => true,
            ]);
        } else {
            parent::successResponse();
        }
    }
}
