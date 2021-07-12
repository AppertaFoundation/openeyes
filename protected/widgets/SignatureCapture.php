<?php
/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

class SignatureCapture extends BaseCWidget
{

    public $assetFolder = 'application.assets.components.signature_pad';

    /**
     * @var bool
     *
     * Whether to secure the signature image with a PIN
     */

    public $pinSecured = false;

    public $showMessage = true;

    protected $uniqueCode = "";
    protected $key = "";

    public $submitURL = "";
    public $onSubmit = "function(data){}";

    public $buttonText = "Capture a new signature";

    public $embedded = false;
    public $embedded_canvas_selector = "";

    
    public function init()
    {
        $this->attachBehavior("unique_codes", array("class" => UniqueUserCodes::class));
        
        $assetManager = Yii::app()->getAssetManager();

        $assetManager->registerScriptFile('signature_pad.min.js',  'application.assets.components.signature_pad' , $this->scriptPriority);
        $assetManager->registerScriptFile('md5.js',  'application.assets.components.crypto-js' , $this->scriptPriority - 1);
        $assetManager->registerScriptFile('CryptoJS.js',  'application.assets.components.crypto-js' , $this->scriptPriority);
        $assetManager->registerScriptFile('mcrypt.js',  'application.assets.components.jsmcrypt' , $this->scriptPriority);
        $assetManager->registerScriptFile('Rijndael.js',  'application.assets.components.jsaes' , $this->scriptPriority);
        $assetManager->registerScriptFile("signature.js", "application.assets.js.signature_capture", $this->scriptPriority);
        $assetManager->registerScriptFile("SignatureCapture.js", "application.widgets.js", $this->scriptPriority);
    }

    public function run()
    {
        if($this->pinSecured) {
            $user = User::model()->findByPk(Yii::app()->user->id);
            $user_code = $this->getUniqueCodeForUser();

            if (!$user_code) {
                throw new CHttpException('Could not get unique code for user - unique codes might need to be generated');
            }

            $this->uniqueCode = $user->generateUniqueCodeWithChecksum($user_code);
            $this->key = md5(Yii::app()->user->id);
        }

        $this->render('SignatureCapture');
    }
}