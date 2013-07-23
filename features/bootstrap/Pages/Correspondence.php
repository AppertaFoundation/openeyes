<?php

use SensioLabs\Behat\PageObjectExtension\PageObject\Page;

class Correspondence extends Page
{
    public  $siteDropdown = "//select[@id='elementletter_site_id']";
    public  $addressTarget = "//select[@id='address_target']";
    public  $letterAddress = "//textarea[@id='elementletter_address']";
    public  $letterDate = "//input[@id='elementletter_date_0']";
    public  $clinicDate = "//input[@id='elementletter_clinic_date_0']";
    public  $macro = "//select[@id='macro']";
    public  $letterIntro = "//textarea[@id='elementletter_introduction']";
    public  $letterRef = "//textarea[@id='elementletter_re']";
    public  $introduction = "//select[@id='introduction']";
    public  $diagnosis = "//select[@id='diagnosis']";
    public  $management = "//select[@id='management']";
    public  $drugs = "//select[@id='drugs']";
    public  $outcome = "//select[@id='outcome']";
    public  $letterFooter = "//textarea[@id='elementletter_footer']";
    public  $letterCc = "//select[@id='cc']";
    public  $letterElement = "//textarea[@id='elementletter_cc']";
    public  $addEnclosure = "//button[@type='button']//*[contains(text(), 'add')]";
}