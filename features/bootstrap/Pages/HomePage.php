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
        'searchSubmit' => array('xpath' => "//button[@type='submit']")
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

}