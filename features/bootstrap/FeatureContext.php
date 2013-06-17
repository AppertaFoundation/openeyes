<?php

use Behat\Behat\Context\ClosuredContextInterface,
    Behat\Behat\Context\TranslatedContextInterface,
    Behat\Behat\Context\BehatContext,
    Behat\Behat\Exception\PendingException;
use Behat\Gherkin\Node\PyStringNode,
    Behat\Gherkin\Node\TableNode;

use Behat\MinkExtension\Context\MinkContext;

use Behat\YiiExtension\Context\YiiAwareContextInterface;
use Behat\Mink\Driver\Selenium2Driver;

class FeatureContext extends MinkContext implements YiiAwareContextInterface
{
    private $yii;
    private $parameters;

    private $patient;

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
     * @BeforeScenario @javascript
     */
    public function maximizeBrowserWindow()
    {
        $this->getSession()->resizeWindow(1280, 800);
    }

    /**
     * @Given /^I am logged in into the system$/
     */
    public function iAmLoggedInIntoTheSystem()
    {
        $this->visit('/');
        $this->fillField('Username', 'admin');
        $this->fillField('Password', 'admin');
        $this->pressButton('Login');
    }

    /**
     * @Given /^I am a cataract specialist$/
     */
    public function iAmACataractSpecialist()
    {
        $this->pressButton('Yes');
        $this->getSession()->wait(5000, "$('#profile_firm_id').length");
        $this->selectOption('profile_firm_id', 'Allan Bruce (Cataract)');
        $this->clickLink('Home');
        $this->getSession()->wait(5000, "$('.ui-dialog').length");
        $this->pressButton('Confirm');
    }

    /**
     * @Given /^I am a strabismus specialist$/
     */
    public function iAmAStrabismusSpecialist()
    {
        $this->pressButton('Yes');
        $this->getSession()->wait(5000, "$('#profile_firm_id').length");
        $this->selectOption('profile_firm_id', 'Adams Gill (Strabismus)');
        $this->clickLink('Home');
        $this->getSession()->wait(5000, "$('.ui-dialog').length");
        $this->pressButton('Confirm');
    }

    /**
     * @BeforeStep
     * @AfterStep
     */
    public function waitForActionToFinish()
    {
        if ($this->getSession()->getDriver() instanceof Selenium2Driver) {
            try {
                $this->getSession()->wait(2000, "$.active == 0");
            } catch (\Exception $e) {}
        }
    }

    /**
     * @Given /^there is an adult patient with operation that does not need a consultant or an anaesthetist$/
     */
    public function thereIsAnAdultPatientWithOperationThatDoesNotNeedAConsultantAndNoAnaesthetist()
    {
        $this->patient = 'TIBBETTS, Josephine';
    }

    /**
     * @Given /^there is an adult patient with operation that does need a consultant but no anaesthetist$/
     */
    public function thereIsAnAdultPatientWithOperationThatDoesNeedAConsultantButNoAnaesthetist()
    {
        $this->patient = 'JOSEPHSON, Ottilie';
    }

    /**
     * @Given /^I select awaiting patient from the waiting list$/
     */
    public function iSelectAwaitingPatientFromTheWaitingList()
    {
        $this->clickLink($this->patient);
    }

    /**
     * @Given /^I click on available date in the calendar$/
     */
    public function iSelectADateFromTheCalendar()
    {
        $this->assertSession()->elementExists('css', '#calendar td.available');
        $this->getSession()->getPage()->find('css', '#calendar td.available')->click();
    }

    /**
     * @Given /^I select available theatre session from the list$/
     */
    public function iSelectAvailableTheatreSessionFromTheList()
    {
        $this->clickLink('08:30 - 13:00');
    }
}
