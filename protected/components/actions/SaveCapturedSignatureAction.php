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

class SaveCapturedSignatureAction extends CAction
{
    use RenderJsonTrait;

    public function run()
    {
        if(!$img = Yii::app()->request->getPost("image")) {
            $this->renderJSON([
                "success" => false,
                "message" => "Image not provided"
            ]);
        }
        $img = base64_decode(str_replace('data:image/jpeg;base64,', '', $img));
        $file = ProtectedFile::createForWriting("signature_".uniqid());
        $file->title = "Signature";
        $file->mimetype = "image/jpeg";
        file_put_contents($file->getPath(), $img);
        if($file->save()) {
            if($this->saveSignatureWithElement($file->id)) {
                $this->renderJSON([
                    "success" => true
                ]);
            }
        }

        $this->renderJSON([
            "success" => false,
            "message" => "An error occurred while saving the signature."
        ]);
    }

    private function saveSignatureWithElement(int $signature_file_id) : bool
    {
        if($et_type = ElementType::model()->findByPk(Yii::app()->request->getParam("element_type_id"))) {
            $class_name = $et_type->class_name;
            if($element = $class_name::model()->findByPk(Yii::app()->request->getParam("element_id"))) {
                $sig_class = $element->relations()["signatures"][1];
                /** @var BaseSignature $signature */
                $signature = new $sig_class();
                $signature->setAttributes([
                    "element_id" => $element->id,
                    "timestamp" => (new DateTime())->getTimestamp(),
                    "signature_file_id" => $signature_file_id,
                    "signed_user_id" => null,
                    "type" => Yii::app()->request->getParam("signature_type"),
                    "signatory_role" => urldecode(Yii::app()->request->getParam("signatory_role")),
                    "signatory_name" => urldecode(Yii::app()->request->getParam("signatory_name")),
                ], false);
                foreach (["initiator_element_type_id", "initiator_row_id"] as $attr) {
                    if($signature->hasAttribute($attr)) {
                        $signature->$attr = Yii::app()->request->getParam($attr);
                    }
                }
                return $signature->save(false);
            }
        }

        return false;
    }
}