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
 * Class SignatureCapture
 * A generic signature capture widget that can be used throughout the Application
 */
class SignatureCapture extends BaseCWidget
{
    /** @var string an URL where the signature image will be POSTed */
    public string $submit_url = "";
    /** @var string JS callback to be fired after submission of signature */
    public string $after_submit_js = "function(response, widget){}";
    /** @var string An unique identifier of this widget */
    protected string $uid;

    /**
     * @inheritDoc
     */
    public function init()
    {
        parent::init();
        $this->uid = uniqid("oesignwidget");
        $assetManager = Yii::app()->getAssetManager();
        $assetManager->registerScriptFile('signature_pad.min.js',  'application.assets.components.signature_pad' , $this->scriptPriority);
        $assetManager->registerScriptFile("SignatureCapture.js", "application.widgets.js", $this->scriptPriority);
    }

    /**
     * @inheritDoc
     */
    public function run()
    {
        $this->render('SignatureCapture');
    }

    /**
     * @return string The unique identifier
     */
    public function getUid() : string
    {
        return $this->uid;
    }
}