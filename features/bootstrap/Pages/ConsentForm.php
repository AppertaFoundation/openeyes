<?php

use SensioLabs\Behat\PageObjectExtension\PageObject\Page;

class ConsentForm extends Page
{
    protected $path = "OphTrConsent/default/view/{eventId}}";

    protected $elements = array(
        'createConsentForm' => array('xpath' => "//*[@id='et_save']//*[contains(text(),'Create Consent Form')]"),
  );

 public function createConsentForm ()
 {
     $this->getElement('createConsentForm')->click();
 }

}