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

class GetSecretarySignatureByPinAction extends GetSignatureByPinAction
{
    protected function getSignatureFile(): void
    {
        // This method is intentionally left empty
    }

    protected function checkPIN(): void
    {
        if(!Yii::app()->user->checkAccess('SignEvent')) {
            throw new Exception("We're sorry, you are not authorized to sign events. Please contact support.");
        }

        if(strlen($this->pin) === 0) {
            throw new Exception("Empty PIN was provided, please enter PIN and click 'PIN sign' again.");
        }
        // TODO implement required logic once it is discussed
        if($this->pin !== "456789") {
            throw new Exception("Incorrect PIN");
        }
    }

    protected function successResponse()
    {
        $this->renderJSON([
            "code" => 0,
            "error" => "",
            "signature_proof" => $this->signature_proof,
            'date' => $this->date_time->format(Helper::NHS_DATE_FORMAT),
            'time' => $this->date_time->format("H:i"),
        ]);
    }
}