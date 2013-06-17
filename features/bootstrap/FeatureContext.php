<?php

use Behat\Behat\Context\ClosuredContextInterface,
    Behat\Behat\Context\TranslatedContextInterface,
    Behat\Behat\Context\BehatContext,
    Behat\Behat\Exception\PendingException;
use Behat\Gherkin\Node\PyStringNode,
    Behat\Gherkin\Node\TableNode;

use Behat\MinkExtension\Context\MinkContext;

use Behat\YiiExtension\Context\YiiAwareContextInterface;

class FeatureContext extends MinkContext implements YiiAwareContextInterface
{
    private $yii;
    private $parameters;

    public function __construct(array $parameters)
    {
        $this->parameters = $parameters;
    }

    public function setYiiWebApplication(\CWebApplication $yii)
    {
        $this->yii = $yii;
    }

    /**
     * @BeforeScenario
     */
    public function loadDatabaseSample($event)
    {
        if (!$event->getScenario()->hasSteps()) {
            return;
        }

        chdir(__DIR__.'/../../');
        if (!is_file($this->parameters['sample_db'])) {
            throw new \RuntimeException(
                'Sample database not found. Have you forgot to clone it?'
            );
        }

        exec(sprintf($this->parameters['load_db_cmd'], $this->parameters['sample_db']));
    }

    /**
     * @Given /^I am logged in into the system$/
     */
    public function iAmLoggedInIntoTheSystem()
    {
        $con = $this->yii->db;

        $con->createCommand(sprintf('INSERT INTO user_firm VALUES(%s)', implode(', ', array(
            '1', '1', '18', '1', 'NOW()', '1', 'NOW()'
        ))))->execute();
        $con->createCommand('UPDATE user SET last_firm_id = 18 WHERE id = 1')->execute();

        $this->visit('/');
        $this->fillField('Username', 'admin');
        $this->fillField('Password', 'admin');
        $this->pressButton('Login');
    }

    /**
     * @Given /^there is an adult patient with operation$/
     */
    public function thereIsAnAdultPatientWithOperation()
    {
    }

    /**
     * @Given /^this operation does not need a consultant or an anaesthetist$/
     */
    public function thisOperationDoesNotNeedAConsultantOrAnAnaesthetist()
    {
        throw new PendingException();
    }

    /**
     * @Given /^I select awaiting patient from the waiting list$/
     */
    public function iSelectAwaitingPatientFromTheWaitingList()
    {
        throw new PendingException();
    }

    /**
     * @Given /^I select a date from the calendar$/
     */
    public function iSelectADateFromTheCalendar()
    {
        throw new PendingException();
    }

    /**
     * @Given /^I select available theatre session from the list$/
     */
    public function iSelectAvailableTheatreSessionFromTheList()
    {
        throw new PendingException();
    }

    /**
     * @Then /^operation should be assigned to the theatre session$/
     */
    public function operationShouldBeAssignedToTheTheatreSession()
    {
        throw new PendingException();
    }
}
