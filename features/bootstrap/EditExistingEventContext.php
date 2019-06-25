<?php
/**
 * Created by PhpStorm.
 * User: Hemanth
 * Date: 11/11/2015
 * Time: 14:38
 */
use Behat\Behat\Context\ClosuredContextInterface, Behat\Behat\Context\TranslatedContextInterface, Behat\Behat\Context\BehatContext, Behat\Behat\Exception\PendingException;
use Behat\Gherkin\Node\PyStringNode, Behat\Gherkin\Node\TableNode;
use Behat\MinkExtension\Context\MinkContext;

use Behat\Mink\Driver\Selenium2Driver;
use \SensioLabs\Behat\PageObjectExtension\Context\PageObjectContext;
use Behat\Behat\Exception\BehaviorException;
class EditExistingEventContext extends PageObjectContext
{
//    public function __construct(array $parameters)
//    {
//    }
//
//    /**
//     * @Then /^I click to expand the "([^"]*)" sidebar$/
//     */
//    public function iClickToExpandTheSidebar($firm) {
//        /**
//         *
//         * @var EditExistingEvent $editExistingEvent
//         */
//        $editExistingEvent = $this->getPage ( 'Biometry' );
//        if($firm==="cataract")
//        {
//            $editExistingEvent->expandCataract ();
//        }
//        elseif($firm==="glaucoma")
//        {
//            $editExistingEvent->expandGlaucoma ();
//        }
//        elseif($firm==="medicalretinal")
//        {
//            $editExistingEvent->expandMedicalRetinal ();
//        }
//        elseif($firm==="supportfirm")
//        {
//            $editExistingEvent->expandSupportFirm ();
//        }
//        else{
//            throw new BehaviorException ( "WARNING!!! Invalid Event selected, not in the list!" );
//        }
//    }

        /**
         * @Then /^I click to expand the "([^"]*)" sidebar$/
         */
    public function iClickToExpandTheSidebar($firm) {
        /**
         *
         * @var EditExistingEvent $editExistingEvent
         */
        $editExistingEvent = $this->getPage ( 'Biometry' );
        if($firm==="Cataract"|| $firm==="Glaucoma" || $firm==="Medical Retinal"|| $firm==="Support Firm"){
            $editExistingEvent->expandFirm ($firm);
        }
        else{
            throw new BehaviorException ( "WARNING!!! Invalid Event selected, not in the list!" );
        }
    }
    /**
     * @Then /^I click on existing "([^"]*)"$/
     */
    public function iClickOnExistingEvent($event)
    {
        /**
         *
         * @var EditExistingEvent $editExistingEvent
         */
        $editExistingEvent = $this->getPage('Biometry');
        $editExistingEvent->clickExistingEvent($event);
    }

    /**
     * @Then /^I edit the existing event$/
     */
    public function iEditTheExistingEvent()
    {
        /**
         *
         * @var EditExistingEvent $editExistingEvent
         */
        $editExistingEvent = $this->getPage('EditExistingEvent');
        $editExistingEvent->selectEdit();
    }
}
