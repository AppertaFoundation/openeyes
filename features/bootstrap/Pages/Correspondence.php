<?php
use Behat\Behat\Exception\BehaviorException;
class Correspondence extends OpenEyesPage
{
    protected $path ="/site/OphCoCorrespondence/Default/create?patient_id={patientId}";

    protected $elements = array(
        'siteDropdown' => array('xpath' => "//*[@id='ElementLetter_site_id']"),
        'addressTarget' => array('xpath' => "//select[@id='address_target']"),
        'macro' => array('xpath' => "//select[@id='macro']"),
        'clinicDate' => array('xpath' => "//*[@id='ElementLetter_clinic_date_0']"),
        'clinicDateCalendar' => array('xpath' => "//*[@id='ui-datepicker-div']"),
        'introduction' => array('xpath' => "//select[@id='introduction']"),
        'findings' => array('xpath' => "//*[@id='findings']"),
        'diagnosis' => array('xpath' => "//select[@id='diagnosis']"),
        'management' => array('xpath' => "//select[@id='management']"),
        'drugs' => array('xpath' => "//select[@id='drugs']"),
        'outcome' => array('xpath' => "//select[@id='outcome']"),
        'letterCc' => array('xpath' => "//select[@id='cc']"),
        'addEnclosure' => array('xpath' => "//*[@class='field-row']//*[contains(text(),'Add')]"),
        'enterEnclosure' => array('xpath' => "//div[@id='enclosureItems']/div/div/input"),
        'saveDraft' => array('xpath' => "//*[@id='et_save_draft']"),
        'saveCorrespondenceOK' => array('xpath' => "//*[@id='flash-success']"),
    );

    public function siteDropdown ($site)
    {
        $this->getElement('siteDropdown')->selectOption($site);
    }

    public function addressTarget ($address)
    {
        $this->getElement('addressTarget')->selectOption($address);
    }

    public function macro ($macro)
    {
        $this->getElement('macro')->selectOption($macro);
    }

    public function clinicDate ($date)
    {
        $this->getElement('clinicDate')->click();
        $this->getElement('clinicDateCalendar')->selectOption($date);
    }

    public function introduction ($intro)
    {
        $this->getElement('introduction')->selectOption($intro);
    }

    public function findings ($findings)
    {
        $this->getElement('findings')->selectOption($findings);
    }

    public function diagnosis ($diagnosis)
    {
        $this->getElement('diagnosis')->selectOption($diagnosis);
    }

    public function management ($management)
    {
        $this->getElement('management')->selectOption($management);
    }

    public function drugs ($drugs)
    {
        $this->getElement('drugs')->selectOption($drugs);
    }

    public function outcome ($outcome)
    {
        $this->getElement('outcome')->selectOption($outcome);
    }

    public function CC ($cc)
    {
        $this->getElement('letterCc')->selectOption($cc);
    }

    public function enclosure ($enclosure)
    {

        $element = $this->getElement('addEnclosure');
        $this->scrollWindowToElement($element);
        $element->click();
        $this->waitForElementDisplayBlock('#enclosureItems');
        $this->getElement('enterEnclosure')->setValue($enclosure);
    }

    public function saveDraft ()
    {
        $this->getElement('saveDraft')->click();
    }

    protected function hasConsentSaved ()
    {
        return (bool) $this->find('xpath', $this->getElement('saveCorrespondenceOK')->getXpath());;
    }

    public function saveCorrespondenceAndConfirm ()
    {
        $this->getElement('saveDraft')->click();

        if ($this->hasConsentSaved()) {
            print "Correspondence has been saved OK";
        }

        else {
            throw new BehaviorException("WARNING!!!  Correspondence has NOT been saved!!  WARNING!!");
        }
    }

}
