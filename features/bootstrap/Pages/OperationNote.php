<?php
use Behat\Behat\Exception\BehaviorException;
class OperationNote extends OpenEyesPage {
	protected $path = "/site/OphTrOperationbooking/Default/create?patient_id={parentId}";
	protected $elements = array (
			
			'emergencyBooking' => array (
					'xpath' => "//*[@value='emergency']" 
			),
			'createOperationNote' => array (
					'xpath' => "//*[@form='operation-note-select']" 
			),
			'rightProcedureEye' => array (
					'xpath' => "//*[@id='Element_OphTrOperationnote_ProcedureList_eye_id_2']" 
			),
			'leftProcedureEye' => array (
					'xpath' => "//*[@id='Element_OphTrOperationnote_ProcedureList_eye_id_1']" 
			),
			'commonProcedure' => array (
					'xpath' => "//*[@id='select_procedure_id_procs']" 
			),
			'anaestheticTopical' => array (
					'xpath' => "//*[@id='Element_OphTrOperationnote_Anaesthetic_anaesthetic_type_id_1']" 
			),
			'anaestheticLA' => array (
					'xpath' => "//*[@id='Element_OphTrOperationnote_Anaesthetic_anaesthetic_type_id_3']" 
			),
			'anaestheticLAC' => array (
					'xpath' => "//*[@id='Element_OphTrOperationnote_Anaesthetic_anaesthetic_type_id_2']" 
			),
			'anaestheticLAS' => array (
					'xpath' => "//*[@id='Element_OphTrOperationnote_Anaesthetic_anaesthetic_type_id_4']" 
			),
			'anaestheticGA' => array (
					'xpath' => "//*[@id='Element_OphTrOperationnote_Anaesthetic_anaesthetic_type_id_5']" 
			),
			'givenAnaesthetist' => array (
					'xpath' => "//*[@id='Element_OphTrOperationnote_Anaesthetic_anaesthetist_id_1']" 
			),
			'givenSurgeon' => array (
					'xpath' => "//*[@id='Element_OphTrOperationnote_Anaesthetic_anaesthetist_id_2']" 
			),
			'givenNurse' => array (
					'xpath' => "//*[@id='Element_OphTrOperationnote_Anaesthetic_anaesthetist_id_3']" 
			),
			'givenAnaestheticTechnician' => array (
					'xpath' => "//*[@id='Element_OphTrOperationnote_Anaesthetic_anaesthetist_id_4']" 
			),
			'givenAnaestheticOther' => array (
					'xpath' => "//*[@id='Element_OphTrOperationnote_Anaesthetic_anaesthetist_id_5']" 
			),
			'deliveryRetrobulbar' => array (
					'xpath' => "//*[@id='Element_OphTrOperationnote_Anaesthetic_anaesthetic_delivery_id_1']" 
			),
			'deliveryPeribulbar' => array (
					'xpath' => "//*[@id='Element_OphTrOperationnote_Anaesthetic_anaesthetic_delivery_id_2']" 
			),
			'deliverySubtenon' => array (
					'xpath' => "//*[@id='Element_OphTrOperationnote_Anaesthetic_anaesthetic_delivery_id_3']" 
			),
			'deliverySubconjunctival' => array (
					'xpath' => "//*[@id='Element_OphTrOperationnote_Anaesthetic_anaesthetic_delivery_id_4']" 
			),
			'deliveryTopical' => array (
					'xpath' => "//*[@id='Element_OphTrOperationnote_Anaesthetic_anaesthetic_delivery_id_5']" 
			),
			'deliveryTopicalIntracameral' => array (
					'xpath' => "//*[@id='Element_OphTrOperationnote_Anaesthetic_anaesthetic_delivery_id_6']" 
			),
			'deliveryOther' => array (
					'xpath' => "//*[@id='Element_OphTrOperationnote_Anaesthetic_anaesthetic_delivery_id_7']" 
			),
			'anaestheticAgents' => array (
					'xpath' => "//*[@id='AnaestheticAgent']" 
			),
			'complications' => array (
					'xpath' => "//*[@id='OphTrOperationnote_AnaestheticComplications']" 
			),
			'anaestheticComments' => array (
					'xpath' => "//*[@id='Element_OphTrOperationnote_Anaesthetic_anaesthetic_comment']" 
			),
			'surgeon' => array (
					'xpath' => "//*[@id='Element_OphTrOperationnote_Surgeon_surgeon_id']" 
			),
			'supervisingSurgeon' => array (
					'xpath' => "//*[@id='Element_OphTrOperationnote_Surgeon_supervising_surgeon_id']" 
			),
			'assistant' => array (
					'xpath' => "//*[@id='Element_OphTrOperationnote_Surgeon_assistant_id']" 
			),
			'perOpDrug' => array (
					'xpath' => "//*[@id='Drug']" 
			),
			'operationComments' => array (
					'xpath' => "//*[@id='Element_OphTrOperationnote_Comments_comments']" 
			),
			'postOpInstructions' => array (
					'xpath' => "//*[@id='dropDownTextSelection_Element_OphTrOperationnote_Comments_postop_instructions']" 
			),
			'saveOpNote' => array (
					'xpath' => "//*[@id='et_save']" 
			),
			'savedOkMessage' => array (
					'xpath' => "//*[@id='flash-success'][@class='alert-box with-icon info'][contains(text(), 'Operation Note created.')]" 
			),
		'cataractComplicationErrorMessage' => array(
			'xpath' => "//*[@class='alert-box error with-icon']//*[contains(text(),'Cataract Complications cannot be blank')]"
		),
		'anaestheticComplicationErrorMessage' =>array(
			'xpath' => "//*[@class='alert-box error with-icon']//*[contains(text(),'Anaesthetic Complications cannot be blank')]"
		),
		'selectPCRRisk' => array(
			'xpath' => "//*[@id='ophCiExaminationPCRRiskEyeLabel']"
		),
		'referencePCRRisk' => array(
			'xpath' => "//*[contains(text(),'Calculation data derived from')]"
		),
		'referencePCRRiskLink' => array(
			'xpath' => "//*[contains(text(),'Narendran et al. The Cataract National Dataset electronic multicentre audit of 55,567 operations')][1]"
		),
		'scrollTo' => array(
			'xpath' => "//*[@class='element-header']//*[contains(text(),'Anaesthetic')]"
		)
	);
	public function emergencyBooking() {
		$this->getElement ( 'emergencyBooking' )->click ();
	}
	public function createOperationNote() {
		$this->getElement ( 'createOperationNote' )->click ();
	}
	public function procedureRightEye() {
		$this->getElement ( 'leftProcedureEye' )->click ();
	}
	public function procedureLeftEye() {
		$this->getElement ( 'rightProcedureEye' )->click ();
	}
	public function commonProcedure($common) {
		$this->getElement ( 'commonProcedure' )->selectOption ( $common );
	}
	public function typeTopical() {
		$this->getElement ( 'anaestheticTopical' )->click ();
	}
	public function typeLA() {
		$this->getElement ( 'anaestheticLA' )->click ();
	}
	public function typeLAC() {
		$this->getElement ( 'anaestheticLAC' )->click ();
	}
	public function typeLAS() {
		$this->getElement ( 'anaestheticLAS' )->click ();
	}
	public function typeGA() {
		$this->getElement ( 'anaestheticGA' )->click ();
	}
	public function givenAnaesthetist() {
		sleep(3);
		$this->getElement ( 'givenAnaesthetist' )->click ();
	}
	public function givenSurgeon() {
		sleep(3);
		$this->getElement ( 'givenSurgeon' )->click ();
	}
	public function givenNurse() {
		sleep(3);
		$this->getElement ( 'givenNurse' )->click ();
	}
	public function givenAnaesthetistTechnician() {
		sleep(5);
		$this->getElement ( 'givenAnaestheticTechnician' )->click ();
	}
	public function givenOther() {
		sleep(5);
		$this->getElement ( 'givenAnaestheticOther' )->click ();
	}
	public function deliveryRetrobulbar() {
		$this->getElement ( 'deliveryRetrobulbar' )->click ();
	}
	public function deliveryPeribulbar() {
		$this->getElement ( 'deliveryPeribulbar' )->click ();
	}
	public function deliverySubtenon() {
		$this->getElement ( 'deliverySubtenon' )->click ();
	}
	public function deliverySubconjunctival() {
		$this->getElement ( 'deliverySubconjunctival' )->click ();
	}
	public function deliveryTopical() {
		$this->getElement ( 'deliveryTopical' )->click ();
	}
	public function deliveryTopicalIntracameral() {
		$this->getElement ( 'deliveryTopicalIntracameral' )->click ();
	}
	public function deliveryOther() {
		$this->getElement ( 'deliveryOther' )->click ();
	}
	public function anaestheticAgent($agent) {
		// $this->getElement('anaestheticAgents')->selectOption($agent);
		// TODO TEST DATA REQUIRED HERE
	}
	public function complications($complication) {
		$this->getElement ( 'complications' )->selectOption ( $complication );
	}
	public function anaestheticComments($comments) {
		$this->getElement ( 'anaestheticComments' )->setValue ( $comments );
	}
	public function surgeon($surgeon) {
		$this->getElement ( 'surgeon' )->selectOption ( $surgeon );
	}
	public function supervisingSurgeon($super) {
		$this->getElement ( 'supervisingSurgeon' )->selectOption ( $super );
	}
	public function assistant($assistant) {
		$this->getElement ( 'assistant' )->selectOption ( $assistant );
	}
	public function perOpDrug($drug) {
		// $this->getElement('perOpDrug')->selectOption($drug);
		// TODO TEST DATA REQUIRED HERE
	}
	public function operationComments($comments) {
		$this->getElement ( 'operationComments' )->setValue ( $comments );
	}
	public function postOpInstructions($instructions) {
		sleep(4);
		// $this->getElement('postOpInstructions')->selectOption($instructions);
		// TODO TEST DATA REQUIRED HERE
	}
	public function saveOpNote() {
		sleep(3);
		$this->getElement ( 'saveOpNote' )->click ();
	}
	protected function hasOpNoteSaved() {
		return ( bool ) $this->find ( 'xpath', $this->getElement ( 'savedOkMessage' )->getXpath () );
	}
	public function saveOpNoteAndConfirm() {
		$this->getElement ( 'saveOpNote' )->click ();

		$this->getSession ()->wait ( 5000, 'window.$ && $.active == 0' );
		if ($this->hasOpNoteSaved ()) {
			print "Operation Note has been saved OK";
		} 

		else {
			throw new BehaviorException ( "WARNING!!!  Operation Note has NOT been saved!!  WARNING!!" );
		}
	}

	public function cataractComplicationErrorMessage(){
		if($this->find ( 'xpath', $this->getElement ( 'cataractComplicationErrorMessage' )->getXpath () )){
				print "Complications Missing Error is shown, Test Passed!";
		}
		else{
			throw new BehaviorException ( "WARNING!!! Complications missing error Message not shown!!  WARNING!!" );
		}
	}

	public function anaestheticComplicationErrorMessage(){
		if($this->find ( 'xpath', $this->getElement ( 'anaestheticComplicationErrorMessage' )->getXpath () )){
			print "Complications Missing Error is shown, Test Passed!";
		}
		else{
			throw new BehaviorException ( "WARNING!!! Complications missing error Message not shown!!  WARNING!!" );
		}
	}

	public function iSelectPCRRisk(){
		sleep(3);
		$this->getElement('selectPCRRisk')->click();
		sleep(5);
	}

	public function referencePCRRisk(){
		$this->getElement('referencePCRRisk');
	}

	public function clickReferencePCRRiskLink(){
		//$this->getDriver()->();
		//$this->scrollWindowTo('selectPCRRisk');
		//$this->scrollWindowTo('scrollTo');
		$this->getElement('referencePCRRiskLink')->click();
		sleep(3);
	}

	}