<?php

use SensioLabs\Behat\PageObjectExtension\PageObject\Page;

class Login extends Page
{
    protected $path = '/site/login';

    protected $elements = array(
        'user' => array('xpath' => "//input[@id='LoginForm_username']"),
        'pass' => array('xpath' => "//input[@id='LoginForm_password']"),
        'loginButton' => array('xpath' => "//button[@id='login_button']")
    );

    public function maximizeBrowserWindow ()
    {
        $this->getSession()->resizeWindow(2650,1600);
    }

    public function loginWith($user, $password)
    {
        $this->getElement('user')->setValue($user);
        $this->getElement('pass')->setValue($password);
        $this->getElement('loginButton')->press();
    }

}