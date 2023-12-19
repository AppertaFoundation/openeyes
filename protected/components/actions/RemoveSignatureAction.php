<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2023
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2023, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

class RemoveSignatureAction extends \CAction
{
    use RenderJsonTrait;

    protected User $user;

    /**
     * @inheritDoc
     */
    public function run()
    {
        $signature_id = \Yii::app()->request->getPost('signature_id');
        $deleted = OphDrPrescription_Signature::model()->deleteByPk($signature_id);

        $response = $deleted ? $this->successResponse() : $this->errorResponse();
        $this->renderJSON($response);
    }

    protected function errorResponse(): array
    {
        return [
            'code' => 1,
            'error' => "Signature not found",
        ];
    }

    protected function successResponse(): array
    {
        return [
            'code' => 0,
            'error' => "",
        ];
    }
}
