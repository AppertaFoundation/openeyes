<?php
use Behat\Behat\Exception\BehaviorException;
include('OpenEyesPage.php');
class AddingNewEvent extends OpenEyesPage {
	protected $path = "OphCiExamination/default/view/{eventId}}";
	protected $elements = array (
			'addFirstNewEpisode' => array (
					'xpath' => "//*[@id='event_display']/div[3]/button//*[contains(text(), 'Add episode')]" 
			),
			'addEpisodeConfirm' => array (
					'xpath' => "//*[@id='add-new-episode-form']//*[contains(text(), 'Confirm')]" 
			),
			'addEpisodeCancel' => array (
					'xpath' => "//*[@id='add-new-episode-form']//*[contains(text(), 'Cancel')]" 
			),
			'addNewEpisodeButton' => array (
					'xpath' => "//*[@id='episodes_sidebar']//*[contains(text(),'Add episode')]" 
			),
//			'expandCataractEpisode' => array (
//					'xpath' => "//*[@class='episode-title']//*[contains(text(),'Cataract')]"
//			),
//			'expandGlaucomaEpisode' => array (
//					'xpath' => "//*[@class='episode-title']//*[contains(text(),'Glaucoma')]"
//			),
//			'expandRefractiveEpisode' => array (
//					'xpath' => "//*[@class='episode-title']//*[contains(text(),'Refractive')]"
//			),
//			'expandMedicalRetinalEpisode' => array (
//					'xpath' => "//*[@class='episode-title']//*[contains(text(),'Medical Retinal')]"
//			),
//			'expandSupportFirm' => array (
//					'xpath' => "//*[@class='episode closed clearfix']//*[contains(text(),'Support Services')]"
//			),
			'addNewCataractEventButton' => array (
					'xpath' => "//*[@class='events-container show']//*[@data-attr-subspecialty-id=4]" 
			),
			'addNewGlaucomaEventButton' => array (
					'xpath' => "//*[@class='events-container show']//*[@data-attr-subspecialty-id=7]" 
			),
			'addNewMedicalRetinalEventButton' => array (
					'xpath' => "//*[@class='events-container show']//*[@data-attr-subspecialty-id=8]" 
			),
			'addNewSupportFirmEventButton' => array (
					'xpath' => "//*[@class='events-container show']//*[@data-attr-subspecialty-id='']" 
			)
//			'anaestheticSatisfaction' => array (
//					'xpath' => "//*[@id='add-new-event-dialog']//*[contains(text(), 'Anaesthetic Satisfaction Audit')]"
//			),
//			'consentForm' => array (
//					'xpath' => "//*[@id='add-new-event-dialog']//*[contains(text(), 'Consent form')]"
//			),
//			'correspondence' => array (
//					'xpath' => "//*[@id='add-new-event-dialog']//*[contains(text(), 'Correspondence')]"
//			),
//			'examination' => array (
//					'xpath' => "//*[@id='add-new-event-dialog']//*[contains(text(), 'Examination')]"
//			),
//			'operationBooking' => array (
//					'xpath' => "//*[@id='add-new-event-dialog']//*[contains(text(), 'Operation booking')]"
//			),
//			'operationNote' => array (
//					'xpath' => "//*[@id='add-new-event-dialog']//*[contains(text(), 'Operation Note')]"
//			),
//			'phasing' => array (
//					'xpath' => "//*[@id='add-new-event-dialog']//*[contains(text(), 'Phasing')]"
//			),
//			'prescription' => array (
//					'xpath' => "//*[@id='add-new-event-dialog']//*[contains(text(), 'Prescription')]"
//			),
//			'intravitreal' => array (
//					'xpath' => "//*[@id='add-new-event-dialog']//*[contains(text(), 'Intravitreal injection')]"
//			),
//			'laser' => array (
//					'xpath' => "//*[@id='add-new-event-dialog']//*[contains(text(), 'Laser')]"
//			),
//			'therapyApplication' => array (
//					'xpath' => "//*[@id='add-new-event-dialog']//*[contains(text(), 'Therapy Application')]"
//			),
//		'biometry' => array(
//			'xpath' => "//*[@id='add-new-event-dialog']//*[contains(text(), 'Biometry')]"
//		)

	);

	public function addFirstNewEpisode() {
		$this->getElement ( 'addFirstNewEpisode' )->click ();
		$this->getElement ( 'addEpisodeConfirm' )->click ();
	}
	public function addNewEpisode() {
		$this->getElement ( 'addNewEpisodeButton' )->click ();
	}


	public function expandFirmSidebar($firm){
		$this->elements['expandFirmEpisode'] = array(
			'xpath' => "//*[@class='episode-title']//*[contains(text(),'$firm')]"
		);
		$this->getElement('expandFirmEpisode')->click();
		$this->getSession ()->wait ( 5000, 'window.$ && $.active ==0' );
		switch ($firm){
			case "Cataract":
				$this->getElement ( 'addNewCataractEventButton' )->click ();
      			break;

			case "Glaucoma":
				$this->getElement ( 'addNewGlaucomaEventButton' )->click ();
      			break;

			case "Medical Retinal":
				$this->getElement ( 'addNewMedicalRetinalEventButton' )->click ();
				break;

			default:
				throw new BehaviorException ( "WARNING!!! Invalid Firm Episode selected, not in the list!" );
		}
		$this->getSession ()->wait ( 5000, 'window.$ && $.active ==0' );
	}


//	public function expandCataract() {
//		$this->getElement ( 'expandCataractEpisode' )->click ();
//		$this->getSession ()->wait ( 5000, 'window.$ && $.active ==0' );
//		$this->getElement ( 'addNewCataractEventButton' )->click ();
//		$this->getSession ()->wait ( 5000, 'window.$ && $.active ==0' );
//	}
//	public function expandGlaucoma() {
//		$this->getElement ( 'expandGlaucomaEpisode' )->click ();
//		$this->getSession ()->wait ( 5000, 'window.$ && $.active ==0' );
//		$this->getElement ( 'addNewGlaucomaEventButton' )->click ();
//		$this->getSession ()->wait ( 5000, 'window.$ && $.active ==0' );
//	}
//	public function expandMedicalRetinal() {
//		$this->getElement ( 'expandMedicalRetinalEpisode' )->click ();
//		$this->getSession ()->wait ( 5000, 'window.$ && $.active ==0' );
//		$this->getElement ( 'addNewMedicalRetinalEventButton' )->click ();
//		$this->getSession ()->wait ( 5000, 'window.$ && $.active ==0' );
//	}
//	public function expandSupportFirm() {
//		$this->getElement ( 'expandSupportFirm' )->click ();
//		$this->getSession ()->wait ( 5000, 'window.$ && $.active ==0' );
//		$this->getElement ( 'addNewSupportFirmEventButton' )->click ();
//		$this->getSession ()->wait ( 5000, 'window.$ && $.active ==0' );
//	}


	public function addNewEvent($event){
		$this->elements['addEvent'] = array(
			'xpath' => "//*[@id='add-event']"
		);
		$this->elements['newEvent'] = array(
		    'xpath' => "//*[@id='event-type-list']//*[@id='".$event."-link']"
        );
		$this->getElement ( 'addEvent' )->click ();
		$this->getElement ('newEvent')->click();
	}


//	public function addNewEvent($event) {
//		if ($event === "Satisfaction") {
//			$this->getElement ( 'anaestheticSatisfaction' )->click ();
//		}
//		if ($event === "Consent") {
//			$this->getElement ( 'consentForm' )->click ();
//		}
//		if ($event === "Correspondence") {
//			$this->getElement ( 'correspondence' )->click ();
//		}
//		if ($event === "Examination") {
//			$this->getElement ( 'examination' )->click ();
//		}
//		if ($event === "OpBooking") {
//			$this->getElement ( 'operationBooking' )->click ();
//		}
//		if ($event === "OpNote") {
//			sleep(3);
//			$this->getElement ( 'operationNote' )->click ();
//		}
//		if ($event === "Phasing") {
//			$this->getElement ( 'phasing' )->click ();
//		}
//		if ($event === "Prescription") {
//			$this->getElement ( 'prescription' )->click ();
//		}
//		if ($event === "Laser") {
//			$this->getElement ( 'laser' )->click ();
//		}
//		if ($event === "Intravitreal") {
//			$this->getElement ( 'intravitreal' )->click ();
//		}
//		if ($event === "Therapy") {
//			$this->getElement ( 'therapyApplication' )->click ();
//		}
//		if ($event === "Biometry") {
//			$this->getElement ( 'biometry' )->click ();
//		}
//	}


}
