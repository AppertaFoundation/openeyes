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
 * Class EsignElementWidget
 *
 * @property BaseEsignElement $element
 */
class EsignElementWidget extends BaseEventElementWidget
{
    /**
     * @inheritDoc
     */
    public function init()
    {
        parent::init();
        $asset_manager = Yii::app()->getAssetManager();
        $widget_path = $asset_manager->publish(__DIR__. "/js", true);
        $script_path = $widget_path . '/EsignElementWidget.js';
        $asset_manager->registerScriptFile($script_path, "application.widgets.EsignelementWidget", 9, AssetManager::OUTPUT_ALL, true, true);
    }
    /**
     * @return string[]
     */
    private static function getFieldTypes() : array
    {
        return [
            BaseSignature::TYPE_LOGGEDIN_USER => EsignPINField::class,
            BaseSignature::TYPE_OTHER_USER => EsignUsernamePINField::class,
            BaseSignature::TYPE_PATIENT => EsignSignatureCaptureField::class,
        ];
    }

    /**
     * Returns a field widget class by type
     *
     * @param int $type
     * @return string
     * @throws Exception In case $type is invalid
     */
    public function getWidgetClassByType(int $type) : string
    {
        $field_types = self::getFieldTypes();
        if (array_key_exists($type, $field_types)) {
            return $field_types[$type];
        }

        throw new Exception("Signature type $type not defined");
    }

    /**
     * @return bool Whether signing is allowed in the current mode
     */
    protected function isSigningAllowed() : bool
    {
        return in_array($this->mode, [
            self::$EVENT_EDIT_MODE, self::$EVENT_VIEW_MODE
        ]);
    }

    /**
     * @inheritdoc
     */
    protected function getView()
    {
        $prefix = get_class($this);
        if ($this->mode === self::$EVENT_PRINT_MODE) {
            return $prefix."_event_print";
        }
        // View is the same in edit mode and view mode
        return $prefix."_event_edit";
    }

    /**
     * @inheritDoc
     */
    protected function updateElementFromData($element, $data)
    {
        /** @var BaseEsignElement $element */
        parent::updateElementFromData($element, $data);
        $rels = $element->relations();
        if (!array_key_exists("signatures", $rels)) {
            throw new Exception("Relation with key 'signatures' must be is set up in ".get_class($element));
        }
        $signature_class = $rels["signatures"][1];
        if (array_key_exists("signatures", $data)) {
            $models = [];
            foreach ($data["signatures"] as $signature_data) {
                if ((int)$signature_data["id"] > 0) {
                    $model = $signature_class::model()->findByPk($signature_data["id"]);
                } else {
                    $model = new $signature_class;
                }
                $model->setAttributes($signature_data);
                $model->proof = $signature_data["proof"];
                $model->setDataFromProof();
                array_push($models, $model);
            }
            $element->signatures = $models;
        }
    }

    /**
     * @return bool True if the signature is the one being signed in print mode
     */
    protected function isBeingSigned(BaseSignature $signature) : bool
    {
        $req = Yii::app()->request;
        return (int)$req->getParam("sign") > 0
            && (int)$this->element->getElementType()->id === (int)$req->getParam("element_type_id")
            && (int)$this->element->id === (int)$req->getParam("element_id")
            && (int)$signature->type === (int)$req->getParam("signature_type");
    }
}
