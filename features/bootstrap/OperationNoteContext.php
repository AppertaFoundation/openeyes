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

class OperationNoteContext extends PageObjectContext
{
    public function __construct(array $parameters)
    {

    }

    /**
     * @Then /^I select an Emergency Operation Note$/
     */
    public function iSelectAnEmergencyOperationNote()
    {
        /**
         * @var OperationNote $opNote
         */
        $opNote = $this->getPage('OperationNote');
        $opNote->emergencyBooking();
    }

    /**
     * @Given /^I select Create Operation Note$/
     */
    public function iSelectCreateOperationNote()
    {
        /**
         * @var OperationNote $opNote
         */
        $opNote = $this->getPage('OperationNote');
        $opNote->createOperationNote();
    }

    /**
     * @Then /^I select Procedure Right Eye$/
     */
    public function iSelectProcedureRightEye()
    {
        /**
         * @var OperationNote $opNote
         */
        $opNote = $this->getPage('OperationNote');
    }

    /**
     * @Then /^I select Procedure Left Eye$/
     */
    public function iSelectProcedureLeftEye()
    {
        /**
         * @var OperationNote $opNote
         */
        $opNote = $this->getPage('OperationNote');
    }


}