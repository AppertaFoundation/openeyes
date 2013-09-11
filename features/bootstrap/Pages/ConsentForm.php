<?php

use SensioLabs\Behat\PageObjectExtension\PageObject\Page;

class ConsentForm extends Page
{
    protected $path = "OphTrConsent/default/view/{eventId}}";

    protected $elements = array(
        'unbookedProcedure' => array('xpath' => "//input[contains(@value, 'unbooked')]"),
        'createConsentForm' => array('xpath' => "//*[@id='et_save']//*[contains(text(),'Create Consent Form')]"),
        'consentType' => array ('xpath' => "//*[@id='Element_OphTrConsent_Type_type_id']"),
        'rightEye' => array('xpath' => "//*[@id='Element_OphTrConsent_Procedure_eye_id_2']"),
        'bothEyes' => array('xpath' => "//*[@id='Element_OphTrConsent_Procedure_eye_id_3']"),
        'leftEyes' => array('xpath' => "//*[@id='Element_OphTrConsent_Procedure_eye_id_1']"),
        'commonProcedure' => array('xpath' => "//*[@id='select_procedure_id_procedures']"),
        'procedureType' => array('xpath' => "//input[@id='autocomplete_procedure_id_procedures']"),
        'chooseLaser' => array ('xpath' => "//a[contains(text(),'Laser iridoplasty')]"),
        'anaestheticType' => array('xpath' => "//*[@id='Element_OphTrConsent_Procedure_anaesthetic_type_id_3']"),
        'permissionsImagesNO' => array('xpath' => "//*[@id='Element_OphTrConsent_Permissions_images_id_2']"),
        'informationLeaflet' => array('xpath' => "//div[@id='div_Element_OphTrConsent_Other_information']/div[2]/input[2]"),
        'anaestheticLeaflet' =>array('xpath' => "//*[@id='Element_OphTrConsent_Other_anaesthetic_leaflet']"),
        'witnessRequired' => array('xpath' => "//*[@id='Element_OphTrConsent_Other_witness_required']"),
        'witnessName' => array('xpath' => "//*[@id='Element_OphTrConsent_Other_witness_name']"),
        'interpreterRequired' => array('xpath' => "//*[@id='Element_OphTrConsent_Other_interpreter_required']"),
        'interpreterName' => array('xpath' => "//*[@id='Element_OphTrConsent_Other_interpreter_name']"),
        'supplementaryConsent' => array('xpath' => "//*[@id='Element_OphTrConsent_Other_include_supplementary_consent']"),
        'saveConsentForm' => array('xpath' => "//*[@id='et_save']"),
        'test' => array ('xpath' => "//*[@id='div_Element_OphTrConsent_Other_anaesthetic_leaflet']"),
    );


 public function unbookedProcedure ()
 {
     $this->getElement('unbookedProcedure')->check();
 }

 public function createConsentForm ()
 {
     $this->getElement('createConsentForm')->click();
 }

 public function chooseType ($type)
 {
     $this->getElement('consentType')->selectOption($type);
 }

 public function procedureEye ($eye)
 {
     if ($eye===('Right')) {
         $this->getElement('rightEye')->press();
     }
     if ($eye===('Both'))  {
         $this->getElement('bothEyes')->press();
     }
     if ($eye===('Left'))  {
         $this->getElement('leftEyes')->press();
     }
 }

public function commonProcedure ($common)
{
    $this->getElement('commonProcedure')->selectOption($common);
}

public function procedureType ($type)
{
    $this->getElement('procedureType')->click();
    $this->getElement('procedureType')->setValue($type);
//    $this->getSession()->wait(5000);
    $this->getElement('chooseLaser')->click();
}

public function anaestheticTypeLA ()
{
    $this->getElement('anaestheticType')->click();
}

public function permissionImages ()
{
    $this->getElement('permissionsImagesNO')->click();
}

public function informationLeaflet ()
{
    $this->getElement('informationLeaflet')->click();
}

public function anaestheticLeaflet()
{
    $this->getElement('test')->click();
    $this->getElement('anaestheticLeaflet')->click();
}

public function witnessRequired ()
{
    $this->getElement('witnessRequired')->click();
}

public function witnessName ($witness)
{
    $this->getElement('witnessName')->setValue($witness);
}

public function interpreterRequired ()
{
    $this->getElement('interpreterRequired')->click();
}

public function interpreterName ($name)
{
    $this->getElement('interpreterName')->setValue($name);
}

public function supplementaryConsent ()
{
    $this->getElement( 'supplementaryConsent')->click();
}

public function saveConsentForm ()
{
    $this->getSession()->wait(10000);
    $this->getElement('saveConsentForm')->click();
}
}