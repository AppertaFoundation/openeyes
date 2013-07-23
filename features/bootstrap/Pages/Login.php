<?php

class Login
{
    public  $login = "//input[@id='loginform_username']";
    public  $pass = "//input[@id='loginform_password']";
    public  $siteId = "/*[@id='SiteAndFirmForm_site_id']";
    public  $loginButton = "//button[@id='login_button']";
    public  $mainSearch = "//input[@id='query']";
    public  $searchSubmit = "//button[@type='submit']";
    public  $firmDropdown = "//*[@id='SiteAndFirmForm_firm_id']";
    public  $confirmSiteAndFirmButton = "//*[@id='site-and-firm-form']//*[@value='Confirm']";
}