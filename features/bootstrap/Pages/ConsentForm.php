<?php

use SensioLabs\Behat\PageObjectExtension\PageObject\Page;

class ConsentForm extends Page
{
    protected $path = "OphTrConsent/default/view/{eventId}}";

    protected $elements = array(
        'createConsentForm' => array('xpath' => "//*[@id='et_save']//*[contains(text(),'Create Consent Form')]"),
        'consentType' => array ('xpath' => "//*[@id='Element_OphTrConsent_Type_type_id']"),
        'rightEye' => array('xpath' => "//*[@id='Element_OphTrConsent_Procedure_eye_id_2']"),
        'bothEyes' => array('xpath' => "//*[@id='Element_OphTrConsent_Procedure_eye_id_3']"),
        'leftEyes' => array('xpath' => "//*[@id='Element_OphTrConsent_Procedure_eye_id_1']")
    );


 public function createConsentForm ()
 {
     $this->getElement('createConsentForm')->click();
 }

 public function chooseType ($type)
 {
     if ($type===('Right')) {
         $this->getElement('rightEye')->press();
     }
     if ($type===('Both'))  {
         $this->getElement('bothEyes')->press();
     }
     if ($type===('Left'))  {
         $this->getElement('leftEyes')->press();
     }
 }


}