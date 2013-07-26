<?php

use SensioLabs\Behat\PageObjectExtension\PageObject\Page;

class Login extends Page
{
    protected $path = '/site/login';

    public static $login = "//input[@id='loginform_username']";
    public static $pass = "//input[@id='loginform_password']";
    public static $siteId = "/*[@id='SiteAndFirmForm_site_id']";
    public static $loginButton = "//button[@id='login_button']";
    public static $mainSearch = "//input[@id='query']";
    public static $searchSubmit = "//button[@type='submit']";
    public static $firmDropdown = "//*[@id='SiteAndFirmForm_firm_id']";
    public static $confirmSiteAndFirmButton = "//*[@id='site-and-firm-form']//*[@value='Confirm']";
}