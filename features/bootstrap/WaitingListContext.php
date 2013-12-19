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

class WaitingListContext extends PageObjectContext
{
    private $patient;

    public function __construct(array $parameters)
    {

    }

    /**
     * @Given /^there is an adult patient with operation that does not need a consultant or an anaesthetist$/
     */
    public function thereIsAnAdultPatientWithOperationThatDoesNotNeedAConsultantAndNoAnaesthetist()
    {
        $this->patient = 'AINSWORTH, Ruby';
    }

    /**
     * @Given /^there is an adult patient with operation that does need a consultant but no anaesthetist$/
     */
    public function thereIsAnAdultPatientWithOperationThatDoesNeedAConsultantButNoAnaesthetist()
    {
        $this->patient = 'BEERBOHM, Vicary';
    }

    /**
     * @Given /^there is an adult patient with operation that does not need a consultant but anaesthetist with no GA$/
     */
    public function thereIsAnAdultPatientWithOperationThatDoesNotNeedAConsultantButAnaesthetistWithNoGA()
    {
        $this->patient = 'BEWLEY, Melinda';
    }

    /**
     * @Given /^there is an adult patient with operation that does not need a consultant but anaesthetist with GA$/
     */
    public function thereIsAnAdultPatientWithOperationThatDoesNotNeedAConsultantButAnaesthetistWithGA()
    {
        $this->patient = 'GOODFELLOW, Kit';
    }

    /**
     * @Given /^there is an adult patient with operation that does need a consultant and anaesthetist with no GA$/
     */
    public function thereIsAnAdultPatientWithOperationThatDoesNeedAConsultantAndAnaesthetistWithNoGA()
    {
        $this->patient = 'RICHARDSON, Valerie';
    }

    /**
     * @Given /^there is an adult patient with operation that does need a consultant and anaesthetist with GA$/
     */
    public function thereIsAnAdultPatientWithOperationThatDoesNeedAConsultantAndAnaesthetistWithGA()
    {
        $this->patient = 'BESTOR, Jenny';
    }

    /**
     * @Given /^there is a child patient with operation that does not need a consultant or an anaesthetist$/
     */
    public function thereIsAChildPatientWithOperationThatDoesNotNeedAConsultantAndNoAnaesthetist()
    {
        $this->patient = 'SAVIDGE, Kylie';
    }

    /**
     * @Given /^there is a child patient with operation that does need a consultant but no anaesthetist$/
     */
    public function thereIsAChildPatientWithOperationThatDoesNeedAConsultantButNoAnaesthetist()
    {
        $this->patient = 'JACOBS, Eleanor';
    }

    /**
     * @Given /^there is a child patient with operation that does not need a consultant but anaesthetist with no GA$/
     */
    public function thereIsAChildPatientWithOperationThatDoesNotNeedAConsultantButAnaesthetistWithNoGA()
    {
        $this->patient = 'CRESSWELL, Teresa';
    }

    /**
     * @Given /^there is a child patient with operation that does not need a consultant but anaesthetist with GA$/
     */
    public function thereIsAChildPatientWithOperationThatDoesNotNeedAConsultantButAnaesthetistWithGA()
    {
        $this->patient = 'WIDDRINGTON, Sophia';
    }

    /**
     * @Given /^there is a child patient with operation that does need a consultant and anaesthetist with no GA$/
     */
    public function thereIsAChildPatientWithOperationThatDoesNeedAConsultantAndAnaesthetistWithNoGA()
    {
        $this->patient = 'WHITTINGHAM, Chet';
    }

    /**
     * @Given /^there is a child patient with operation that does need a consultant and anaesthetist with GA$/
     */
    public function thereIsAChildPatientWithOperationThatDoesNeedAConsultantAndAnaesthetistWithGA()
    {
        $this->patient = 'GOLDEN, Edmund';
    }

    /**
     * @Given /^I select awaiting patient from the waiting list$/
     */
    public function iSelectAwaitingPatientFromTheWaitingList()
    {
        $waitingList = $this->getPage('WaitingList');
        $waitingList->getSession()->wait(8000);
        $waitingList->clickLink($this->patient);
        $waitingList->getSession()->wait(3000);
    }

    /**
 * @Given /^I click on available date in the calendar$/
 */
    public function iClickOnAvailableDateInTheCalendar()
    {
        /**
         * @var OperationBooking $operationBooking
         */
        $operationBooking = $this->getPage('OperationBooking');
        $operationBooking->getSession()->wait(10000, "$.active == 0");
        $operationBooking->availableSlot();
    }

    /**
     * @Given /^I click on available date in the calendar Outside RTT$/
     */
    public function iClickOnAvailableDateInTheCalendarOutsideRTT()
    {
        /**
         * @var OperationBooking $operationBooking
         */

        $operationBooking = $this->getPage('OperationBooking');
        $operationBooking->getSession()->wait(10000, "$.active == 0");
        $operationBooking->availableSlotOutsideRTT();
    }

    /**
     * @Given /^I select available theatre session from the list$/
     */
    public function iSelectAvailableTheatreSessionFromTheList()
    {
        /**
         * @var OperationBooking $operationBooking
         */
        $operationBooking = $this->getPage('OperationBooking');
        $operationBooking->getSession()->wait(10000, "$.active == 0");
        $operationBooking->availableSessionTime();
    }


    /**
     * @Given /^I press "Confirm slot"$/
     */
    public function iPressConfirmSlot()
    {
        /**
         * @var OperationBooking $operationBooking
         */
        $operationBooking = $this->getPage('OperationBooking');
        $operationBooking->confirmSlot();
    }

    /**
     * @Then /^I should see "([^"]*)"$/
     */
    public function iShouldSee($text)
    {
        /**
         * @var OperationBooking $operationBooking
         */
        $operationBooking = $this->getPage('OperationBooking');
        $operationBooking->hasContent($text);
    }
}