<?php

class Login extends OpenEyesPage
{
    protected $path = '/site/login';

    protected $elements = array(
        'user' => array('xpath' => "//input[@id='LoginForm_username']"),
        'pass' => array('xpath' => "//input[@id='LoginForm_password']"),
        'loginButton' => array('xpath' => "//button[@id='login_button']")
    );

    public function maximizeBrowserWindow ()
    {
        $this->getSession()->resizeWindow(1280,800); # Smaller screen on Mac Second Monitor
//        $this->getSession()->resizeWindow(2650,1600); # Full screen on Mac Second Monitor
    }

    public function loginWith($user, $password)
    {
        $this->getElement('user')->setValue($user);
        $this->getElement('pass')->setValue($password);
        $this->getElement('loginButton')->press();
    }

}