<?php
/**
 * Created by PhpStorm.
 * User: veta
 * Date: 11/08/2016
 * Time: 15:36
 */

namespace OEModule\OphCoCvi\controllers;

use OEModule\OphCoCvi\components\SignatureQRCodeGenerator;

/**
 * Class QRSignatureController
 *
 * @package OEModule\OphCoCvi\controllers
 */
class QRSignatureController extends \BaseController
{
    /**
     * @return array
     */
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

    /**
     * @param $QRContent
     */
    public function actionGenerateQRSignature($QRContent){
        $QRHelper = new SignatureQRCodeGenerator();
        $QRHelper->generateQRSignatureBox($QRContent);
    }

}
