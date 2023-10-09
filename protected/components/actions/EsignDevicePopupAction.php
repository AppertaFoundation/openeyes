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
class EsignDevicePopupAction extends CAction
{
    /**
     * @inheritDoc
     */
    public function run()
    {
        $this->controller->renderPartial(
            "//popup/_esigndevice",
            [
                "qr" => QRCodeGenerator::create(
                    $this->getESignPINURL(
                        Yii::app()->user,
                    )
                ),
                "url" => $this->getESignURL(),
            ]
        );
    }

    private function getEsignURL() : string
    {
        return Yii::app()->createAbsoluteUrl("/site/login/type/esigndevice");
    }

    private function getESignPINURL(OEWebUser $user) : string
    {
        return Yii::app()->createAbsoluteUrl(
            "/site/login",
            [
                "type" => BaseEsignElement::ESIGN_DEVICE_PIN_TYPE,
                "user_id" => $user->id,
                "username" => Yii::app()->session["user_auth"]->username,
                "institution_id" => Institution::model()->getCurrent()->id,
                "site_id" => Yii::app()->session["selected_site_id"],
            ],
        );
    }
}