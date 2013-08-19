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

class LaserContext extends PageObjectContext
{
    public function __construct(array $parameters)
    {

    }

    /**
     * @Then /^I select a Laser site ID "([^"]*)"$/
     */
    public function iSelectALaserSiteId($site)
    {
        /**
         * @var laser $laserPage
         */
        $laserPage = $this->getPage('laser');
        $laserPage->laserSiteID($site);
    }

    /**
     * @Given /^I select a Laser of "([^"]*)"$/
     */
    public function iSelectALaserOf($ID)
    {
        /**
         * @var laser $laserPage
         */
        $laserPage = $this->getPage('laser');
        $laserPage->laserID($ID);
    }

    /**
     * @Given /^I select a Laser Surgeon of "([^"]*)"$/
     */
    public function iSelectALaserSurgeonOf($surgeon)
    {
        /**
         * @var laser $laserPage
         */
        $laserPage = $this->getPage('laser');
        $laserPage->laserSurgeon($surgeon);
    }

    /**
     * @Then /^I select a Right Procedure of "([^"]*)"$/
     */
    public function iSelectARightProcedureOf($right)
    {
        /**
         * @var laser $laserPage
         */
        $laserPage = $this->getPage('laser');
        $laserPage->rightProcedure($right);
    }

    /**
     * @Then /^I select a Left Procedure of "([^"]*)"$/
     */
    public function iSelectALeftProcedureOf($left)
    {
        /**
         * @var laser $laserPage
         */
        $laserPage = $this->getPage('laser');
        $laserPage->leftProcedure($left);
    }
}