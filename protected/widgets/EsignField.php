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

abstract class EsignField extends BaseCWidget
{
    /** @var string An identifier that is unique within the element */
    public string $row_id;

    /** @var int|null The id of the signature file, if already added */
    public ?int $signature_file_id;

    /** @var string A label displaying who is signing */
    public string $signatory_label;

    /**
     * @return string   The action within the module's controller that will be called
     *                  to validate user input and save the signature if passed validation
     */
    abstract public function getAction() : string;

    public function init()
    {
        parent::init();
        $assetManager = Yii::app()->getAssetManager();
        $widgetPath = $assetManager->publish(__DIR__. "/js", true);
        $scriptPath = $widgetPath . '/EsignPinWidget.js';
        $assetManager->registerScriptFile($scriptPath, "application.widgets.EsignField", $this->scriptPriority, AssetManager::OUTPUT_ALL, true, true);
    }

    /**
     * @return bool Whether the signature has already been added
     */
    public function isSigned() : bool
    {
        return !is_null($this->signature_file_id);
    }

    protected function getSignatureFile(): ProtectedFile
    {
        $model = ProtectedFile::model()->findByPk($this->signature_file_id);
        if(!$model) {
            throw new Exception("Signature file not found");
        }
        return $model;
    }

    /**
     * Display signature image if signed
     *
     * @return void
     */
    public function displaySignature() : void
    {
        if($this->isSigned()) {
            $file = ProtectedFile::model()->findByPk($this->signature_file_id);
            $thumbnail1 = $file->getThumbnail("72x24", true);
            $thumbnail2 = $file->getThumbnail("150x50", true);

            $thumbnail1_source = file_get_contents($thumbnail1['path']);
            $thumbnail1_base64 = 'data:' . $file->mimetype . ';base64,' . base64_encode($thumbnail1_source);

            $thumbnail2_source = file_get_contents($thumbnail2['path']);
            $thumbnail2_base64 = 'data:' . $file->mimetype . ';base64,' . base64_encode($thumbnail2_source);

            echo '
                <div 
                    class="esign-check js-has-tooltip" 
                    data-tooltip-content="<img src=\''.($thumbnail2_base64).'\'>"
                    style="background-image: url('.$thumbnail1_base64.');">
                </div>
            ';
        }
    }

    /**
     * Display signature date in NHS format or "-" if not signed
     *
     * @return void
     * @throws Exception If file does not exist
     */
    public function displaySignatureDate() : void
    {
        if($this->isSigned()) {
            echo Helper::convertDate2NHS($this->getSignatureFile()->created_date);
        }
        else {
            echo "-";
        }
    }
}