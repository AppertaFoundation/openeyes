<?php

use Behat\Behat\Context\ClosuredContextInterface,
    Behat\Behat\Context\TranslatedContextInterface,
    Behat\Behat\Context\BehatContext,
    Behat\Behat\Exception\PendingException;
use Behat\Gherkin\Node\PyStringNode,
    Behat\Gherkin\Node\TableNode;
use Behat\MinkExtension\Context\MinkContext;

use Behat\Mink\Driver\Selenium2Driver;
use \SensioLabs\Behat\PageObjectExtension\Context\PageObjectContext;

class LoginContext extends PageObjectContext
{
    public function __construct(array $parameters)
    {

    }

    /**
     * @Given /^I enter login credentials "([^"]*)" and "([^"]*)"$/
     * @And /^I enter login credentials "([^"]*)" and "([^"]*)"$/
     */
    public function iEnterLoginCredentials($user, $password)
    {
        /**
         * @var Login $loginPage
         */
        $loginPage = $this->getPage('Login');
        $loginPage->open();
//        $loginPage->maximizeBrowserWindow();
        $loginPage->halfBrowserWindow();
        $loginPage->loginWith($user, $password);
    }

}