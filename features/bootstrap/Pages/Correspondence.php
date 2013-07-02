<?php

use SensioLabs\Behat\PageObjectExtension\PageObject\Page;

class Correspondence extends Page
{
    public static $siteDropdown = "//select[@id='elementletter_site_id']";
    public static $addressTarget = "//select[@id='address_target']";
    public static $letterAddress = "//textarea[@id='elementletter_address']";
    public static $letterDate = "//input[@id='elementletter_date_0']";
    public static $clinicDate = "//input[@id='elementletter_clinic_date_0']";
    public static $macro = "//select[@id='macro']";
    public static $letterIntro = "//textarea[@id='elementletter_introduction']";
    public static $letterRef = "//textarea[@id='elementletter_re']";
    public static $introduction = "//select[@id='introduction']";
    public static $diagnosis = "//select[@id='diagnosis']";
    public static $management = "//select[@id='management']";
    public static $drugs = "//select[@id='drugs']";
    public static $outcome = "//select[@id='outcome']";
    public static $letterFooter = "//textarea[@id='elementletter_footer']";
    public static $letterCc = "//select[@id='cc']";
    public static $letterElement = "//textarea[@id='elementletter_cc']";
    public static $addEnclosure = "//button[@type='button']//*[contains(text(), 'add')]";
}