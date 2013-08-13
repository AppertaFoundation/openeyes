<?php

use SensioLabs\Behat\PageObjectExtension\PageObject\Page;

class HomePage extends Page
{
    protected $path = '/';

    protected $elements = array(
        'siteID' => array('xpath' => "//*[@id='SiteAndFirmForm_site_id']"),
        'firmDropdown' => array('xpath' => "//*[@id='SiteAndFirmForm_firm_id']"),
        'confirmSiteAndFirmButton' => array('xpath' => "//*[@id='site-and-firm-form']//*[@value='Confirm']"),
        'mainSearch' => array('xpath' => "//input[@id='query']"),
        'searchSubmit' => array('xpath' => "//button[@type='submit']"),
        'changeFirmHeaderLink' => array('xpath' => "//*[@id='user_firm']//*[contains(text(), 'Change')]")
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

    public function searchPatientName ($first, $last)
    {
        $this->getElement('mainSearch')->setValue($first, $last);
    }

    public function searchNhsNumber ($nhs)
    {
        $this->getElement('mainSearch')->setValue($nhs);
    }

    public function searchSubmit ()
    {
        $this->getElement('searchSubmit')->press();
    }

}