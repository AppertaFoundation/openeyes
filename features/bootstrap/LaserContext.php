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

    /**
     * @Then /^I save the Laser Event$/
     */
    public function iSaveTheLaserEvent()
    {
        /**
         * @var laser $laserPage
         */
        $laserPage = $this->getPage('laser');
        $laserPage->saveLaser();
    }

    /**
     * @Then /^I save the Laser Event and confirm it has been created successfully$/
     */
    public function iSaveTheLaserEventAndConfirm
    ()
    {
        /**
         * @var laser $laserPage
         */
        $laserPage = $this->getPage('laser');
        $laserPage->saveLaserAndConfirm();
    }

    /**
     * @Given /^I Confirm that the Laser Validation error messages are displayed$/
     */
    public function iConfirmThatTheLaserValidationErrorMessagesAreDisplayed()
    {
        /**
         * @var laser $laserPage
         */
        $laserPage = $this->getPage('laser');
        $laserPage->laserValidationCheck();
    }

    /**
     * @Given /^I remove the last added Procedure$/
     */
    public function iRemoveTheLastAddedProcedure()
    {
        /**
         * @var laser $laserPage
         */
        $laserPage = $this->getPage('laser');
        $laserPage->removeLastProcedure();
    }

    /**
     * @Then /^I remove the right eye$/
     */
    public function iRemoveTheRightEye()
    {
        /**
         * @var laser $laserPage
         */
        $laserPage = $this->getPage('laser');
        $laserPage->removeRightEye();
    }

    /**
     * @Given /^I add the right eye$/
     */
    public function iAddTheRightEye()
    {
        /**
         * @var laser $laserPage
         */
        $laserPage = $this->getPage('laser');
        $laserPage->addRightEye();
    }

    /**
     * @Then /^I add expand the Comments section$/
     */
    public function iAddExpandTheCommentsSection()
    {
        /**
         * @var laser $laserPage
         */
        $laserPage = $this->getPage('laser');
        $laserPage->expandComments();
    }

    /**
     * @Given /^I add "([^"]*)" into the Comments section$/
     */
    public function iAddIntoTheCommentsSection($comments)
    {
        /**
         * @var laser $laserPage
         */
        $laserPage = $this->getPage('laser');
        $laserPage->addComments($comments);
    }

    /**
     * @Then /^I remove the Comments section$/
     */
    public function iRemoveTheCommentsSection()
    {
        /**
         * @var laser $laserPage
         */
        $laserPage = $this->getPage('laser');
        $laserPage->removeComments();
    }


}