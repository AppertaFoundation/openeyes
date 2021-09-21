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
class PostSignRequestAction extends CAction
{
    use RenderJsonTrait;

    private int $event_id;
    private int $element_type_id;
    private int $signature_type;
    private string $signatory_name;
    private string $signatory_role;

    /**
     * @inheritDoc
     */
    public function run()
    {
        $this->parseInput();
        $signature_request = new SignatureRequest();
        $signature_request->setAttributes([
            "event_id" => $this->event_id,
            "element_type_id" => $this->element_type_id,
            "signature_type" => $this->signature_type,
            "signatory_role" => $this->signatory_role,
            "signatory_name" => $this->signatory_name,
        ]);
        foreach (["initiator_element_type_id", "initiator_row_id"] as $attr) {
            $signature_request->$attr = Yii::app()->request->getPost($attr);
        }
        $success = $signature_request->save();
        // We don't care if it's a duplicate error
        // bc users might want to press the button
        // multiple times and expect no error
        $errors = $signature_request->errors;
        if(array_key_exists("id", $errors)) {
            $success = true;
            $errors = [];
        }
        $this->renderJSON([
            "success" => $success,
            "errors" => $errors,
        ]);
    }

    private function parseInput() : void
    {
        $this->event_id = (int)filter_var(Yii::app()->request->getPost("event_id"), FILTER_SANITIZE_NUMBER_INT);
        $this->element_type_id = (int)filter_var(Yii::app()->request->getPost("element_type_id"), FILTER_SANITIZE_NUMBER_INT);
        $this->signature_type = (int)filter_var(Yii::app()->request->getPost("signature_type"), FILTER_SANITIZE_NUMBER_INT);
        $this->signatory_role = Yii::app()->request->getPost("signatory_role");
        $this->signatory_name = Yii::app()->request->getPost("signatory_name");
        if (count(array_filter([
                $this->event_id,
                $this->element_type_id,
                $this->signature_type,
                $this->signatory_role,
                $this->signatory_name,
            ])) < 5) {
            throw new CHttpException(400, "Missing parameter(s)");
        }
    }
}