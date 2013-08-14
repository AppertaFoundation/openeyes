<?php

use SensioLabs\Behat\PageObjectExtension\PageObject\Page;

class Correspondence extends Page
{
    protected $elements = array(
        'siteDropdown' => array('xpath' => "//select[@id='elementletter_site_id']"),
        'addressTarget' => array('xpath' => "//select[@id='address_target']"),
        'letterAddress' => array('xpath' => "//textarea[@id='elementletter_address']"),
        'letterDate' => array('xpath' => "//input[@id='elementletter_date_0']"),
        'clinicDate' => array('xpath' => "//input[@id='elementletter_clinic_date_0']"),
        'macro' => array('xpath' => "//select[@id='macro']"),
        'letterIntro' => array('xpath' => "//textarea[@id='elementletter_introduction']"),
        'letterRef' => array('xpath' => "//textarea[@id='elementletter_re']"),
        'introduction' => array('xpath' => "//select[@id='introduction']"),
        'diagnosis' => array('xpath' => "//select[@id='diagnosis']"),
        'management' => array('xpath' => "//select[@id='management']"),
        'drugs' => array('xpath' => "//select[@id='drugs']"),
        'outcome' => array('xpath' => "//select[@id='outcome']"),
        'letterFooter' => array('xpath' => "//textarea[@id='elementletter_footer']"),
        'letterCc' => array('xpath' => "//select[@id='cc']"),
        'letterElement' => array('xpath' => "//textarea[@id='elementletter_cc']"),
        'addEnclosure' => array('xpath' => "//button[@type='button']//*[contains(text(), 'add')]"),
    );
}