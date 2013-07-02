<?php

use SensioLabs\Behat\PageObjectExtension\PageObject\Page;

class Login extends Page
{
    public static $login = "//input[@id='loginform_username']";
    public static $pass = "//input[@id='loginform_password']";
    public static $siteId = "//select[@id='loginform_siteid']";
    public static $loginButton = "//button[@id='login_button']";
    public static $mainSearch = "//input[@id='query']";
    public static $searchSubmit = "//button[@type='submit']";
    public static $firmDropdown = "//*[@id='selected_firm_id']";
}