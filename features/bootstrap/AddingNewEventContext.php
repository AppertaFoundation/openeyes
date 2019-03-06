<?php
use Behat\Behat\Context\ClosuredContextInterface, Behat\Behat\Context\TranslatedContextInterface, Behat\Behat\Context\BehatContext, Behat\Behat\Exception\PendingException;
use Behat\Gherkin\Node\PyStringNode, Behat\Gherkin\Node\TableNode;
use Behat\MinkExtension\Context\MinkContext;

use Behat\Mink\Driver\Selenium2Driver;
use \SensioLabs\Behat\PageObjectExtension\Context\PageObjectContext;
use Behat\Behat\Exception\BehaviorException;
class AddingNewEventContext extends PageObjectContext {
	public function __construct(array $parameters) {
	}
	
	/**
	 * @Then /^I Select Add First New Episode and Confirm$/
	 */
	public function addFirstNewEpisode() {
		/**
		 *
		 * @var AddingNewEvent $addNewEvent
		 */
		$addNewEvent = $this->getPage ( 'AddingNewEvent' );
		$addNewEvent->open ( array (
				'eventId' => 517 
		) );
		$addNewEvent->addFirstNewEpisode ();
	}
	// Need to drop the database so this is the first new Episode
	
	/**
	 * @And /^I Select Add a New Episode and Confirm$/
	 */
	public function addNewEpisode() {
		/**
		 *
		 * @var AddingNewEvent $addNewEvent
		 */
		$addNewEvent = $this->getPage ( 'AddingNewEvent' );
		$addNewEvent->addNewEpisode ();
	}
	
	/**
	 * @Given /^I add a New Event "([^"]*)"$/
	 */
	public function iAddANewEvent($event) {
		/**
		 *
		 * @var AddingNewEvent $addNewEvent
		 */
		$addNewEvent = $this->getPage ( 'AddingNewEvent' );
		$addNewEvent->addNewEvent ( $event );
	}
	
//	/**
//	 * @Then /^I expand the Cataract sidebar$/
//	 */
//	public function iExpandTheCataractSidebar() {
//		/**
//		 *
//		 * @var AddingNewEvent $addNewEvent
//		 */
//		$addNewEvent = $this->getPage ( 'AddingNewEvent' );
//		$addNewEvent->expandCataract ();
//	}
	
//	/**
//	 * @Then /^I expand the "([^"]*)" sidebar$/
//	 */
//	public function iExpandTheSidebar($firm) {
//		/**
//		 *
//		 * @var AddingNewEvent $addNewEvent
//		 */
//		$addNewEvent = $this->getPage ( 'AddingNewEvent' );
//		if($firm==="cataract")
//		{
//			$addNewEvent->expandCataract ();
//		}
//		elseif($firm==="glaucoma")
//		{
//			$addNewEvent->expandGlaucoma ();
//		}
//		elseif($firm==="medicalRetinal")
//			{
//				$addNewEvent->expandMedicalRetinal ();
//			}
//		elseif($firm==="supportfirm")
//		{
//			$addNewEvent->expandSupportFirm ();
//		}
//		else{
//			throw new BehaviorException ( "WARNING!!! Invalid Event selected" );
//		}
//	}

	/**
	 * @Then /^I expand the "([^"]*)" sidebar$/
	 */
	public function iExpandTheSidebar($firm) {
		/**
		 *
		 * @var AddingNewEvent $addNewEvent
		 */
		$addNewEvent = $this->getPage ( 'AddingNewEvent' );
		if($firm==="Cataract"|| $firm==="Glaucoma" || $firm==="Medical Retinal"|| $firm==="Support Firm"){
			$addNewEvent->expandFirmSidebar($firm);
		}
		else{
			throw new BehaviorException ( "WARNING!!! Invalid Event selected, not in the list!" );
		}
	}
	/**
     * @Then /^I select the Medical Retina option in the sidebar$/
     */
	public function iSelectTheMedicalRetinaOptionInTheSidebar(){
        /**
         *
         * @var AddingNewEvent $addNewEvent
         */
        $addNewEvent = $this->getPage ( 'AddingNewEvent' );
    }


//	/**
//	 * @Then /^I expand the Glaucoma sidebar$/
//	 */
//	public function iExpandTheGlaucomaSidebar() {
//		/**
//		 *
//		 * @var AddingNewEvent $addNewEvent
//		 */
//		$addNewEvent = $this->getPage ( 'AddingNewEvent' );
//		$addNewEvent->expandGlaucoma ();
//	}
//
//	/**
//	 * @Then /^I expand the Medical Retinal sidebar$/
//	 */
//	public function iExpandTheMedicalRetinalSidebar() {
//		/**
//		 *
//		 * @var AddingNewEvent $addNewEvent
//		 */
//		$addNewEvent = $this->getPage ( 'AddingNewEvent' );
//		$addNewEvent->expandMedicalRetinal ();
//	}
//
//	/**
//	 * @Then /^I expand the Support Firm sidebar$/
//	 */
//	public function iExpandTheSupportFirmSidebar() {
//		/**
//		 *
//		 * @var AddingNewEvent $addNewEvent
//		 */
//		$addNewEvent = $this->getPage ( 'AddingNewEvent' );
//		$addNewEvent->expandSupportFirm ();
//	}

	}
