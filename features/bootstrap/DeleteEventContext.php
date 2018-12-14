<?php
/**
 * Created by PhpStorm.
 * User: fivium
 * Date: 12/12/18
 * Time: 10:54 AM
 */
use Behat\Behat\Context\ClosuredContextInterface, Behat\Behat\Context\TranslatedContextInterface, Behat\Behat\Context\BehatContext, Behat\Behat\Exception\PendingException;
use Behat\Gherkin\Node\PyStringNode, Behat\Gherkin\Node\TableNode;
use Behat\MinkExtension\Context\MinkContext;

use Behat\Mink\Driver\Selenium2Driver;
use \SensioLabs\Behat\PageObjectExtension\Context\PageObjectContext;
use WebDriver\WebDriver;

class DeleteEventContext extends PageObjectContext
{
    public function __construct(array $parameters)
    {
    }

    /**
     * @Then/^I select the event "([^"]*)"$/
     */
    public function iSelectTheEvent($event_id){
        /**
         * @var DeleteEvent $delete_event
         */
        $delete_event = $this->getPage('DeleteEvent');
        $delete_event->selectEvent($event_id);
    }
    /**
     * @Then/^I delete the event$/
     */
    public function iDeleteTheEvent(){
        /**
         * @var DeleteEvent $delete_event
         */
        $delete_event = $this->getPage('DeleteEvent');
        $delete_event->selectDelete();
    }

    /**
     * @Then/^I edit the event$/
     */
    public function iEditTheEvent(){
        /**
         * @var DeleteEvent $delete_event
         */
        $delete_event = $this->getPage('DeleteEvent');
        $delete_event ->selectEdit();
    }


}