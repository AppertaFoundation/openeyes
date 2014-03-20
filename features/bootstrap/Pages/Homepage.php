<?php
use Behat\Behat\Exception\BehaviorException;
class Homepage extends OpenEyesPage
{
    protected $path = '/';

    protected $elements = array(
        'siteID' => array('xpath' => "//*[@id='SiteAndFirmForm_site_id']"),
        'firmDropdown' => array('xpath' => "//*[@id='SiteAndFirmForm_firm_id']"),
        'confirmSiteAndFirmButton' => array('xpath' => "//*[@id='site-and-firm-form']//*[@value='Confirm']"),
        'mainSearch' => array('xpath' => "//input[@id='query']"),
        'searchSubmit' => array('xpath' => "//button[@type='submit']"),
        'changeFirmHeaderLink' => array('xpath' => "//*[@id='user_firm']//*[contains(text(), 'Change')]"),
        'invalidLogin' => array('xpath' => "//*[contains(text(),'Invalid login.')]"),
    );

    public function selectSiteID($siteAddress)
    {
        $this->getElement('siteID')->selectOption($siteAddress);
    }

    public function selectFirm ($firm)
    {
        $this->getElement('firmDropdown')->selectOption($firm);
    }

    public function confirmSelection()
    {
        $this->getElement('confirmSiteAndFirmButton')->press();
    }

    public function changeFirm ()
    {
        $this->getElement('changeFirmHeaderLink')->press();
    }

    public function searchHospitalNumber ($hospital)
    {
        $this->getElement('mainSearch')->setValue($hospital);
    }

    public function searchPatientName ($last, $first)
    {
        $this->getElement('mainSearch')->setValue($last . ' ' . $first);
    }

    public function searchNhsNumber ($nhs)
    {
        $this->getElement('mainSearch')->setValue($nhs);
    }

    public function searchSubmit ()
    {
      $this->getElement('searchSubmit')->press();
			//make sure the patient page is shown after a search
			$this->waitForTitle('Patient summary');
			//$this->getSession()->wait(15000, "window.$ && $('h1.badge').html() ==  'Patient summary' ");
    }

    public function followLink($link)
    {
        $this->clickLink($link);
    }

    public function invalidLoginMessage ()
    {
        return (bool) $this->find('xpath', $this->getElement('invalidLogin')->getXpath());
    }

    public function isInvalidLoginShown ()
    {
        if ($this->invalidLoginMessage()){
            print "Invalid Login message displayed OK";
        }

        else {
            throw new BehaviorException("WARNING!!! Invalid Login is NOT displayed WARNING!!!");
        }

    }

}