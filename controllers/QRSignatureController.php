<?php
/**
 * Created by PhpStorm.
 * User: veta
 * Date: 11/08/2016
 * Time: 15:36
 */

namespace OEModule\OphCoCvi\controllers;

use OEModule\OphCoCvi\components\SignatureQRCodeGenerator;

class QRSignatureController extends \BaseController
{
    public function accessRules()
    {
        return array_merge(
            array(
                array('allow',
                    'actions'=>array('GenerateQRSignature'),
                    // TODO: need to add all CVI role here!
                    'roles'=>array('OprnEditClericalCvi', 'admin'),
                ),
            ),
            parent::accessRules()
        );
    }

    public function actionGenerateQRSignature(){
        $QRHelper = new SignatureQRCodeGenerator();
        $QRHelper->testQRCode();
    }

}