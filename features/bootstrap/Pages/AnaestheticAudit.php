<?php
use Behat\Behat\Exception\BehaviorException;
class AnaestheticAudit extends OpenEyesPage 

{
	protected $path = "/site/OphOuAnaestheticsatisfactionaudit/Default/create?patient_id={patientId}";
	protected $elements = array (
			
			'anaesthetist' => array (
					'xpath' => "//*[@id='Element_OphOuAnaestheticsatisfactionaudit_Anaesthetist_anaesthetist_select']" 
			),
			'pain' => array (
					'xpath' => "//*[@id='Element_OphOuAnaestheticsatisfactionaudit_Satisfaction_pain']" 
			),
			'nausea' => array (
					'xpath' => "//*[@id='Element_OphOuAnaestheticsatisfactionaudit_Satisfaction_nausea']" 
			),
			'vomitCheckBox' => array (
					'xpath' => "//*[@id='Element_OphOuAnaestheticsatisfactionaudit_Satisfaction_vomited']" 
			),
			'respiratoryRate' => array (
					'xpath' => "//*[@id='Element_OphOuAnaestheticsatisfactionaudit_VitalSigns_respiratory_rate_id']" 
			),
			'oxygenSaturation' => array (
					'xpath' => "//*[@id='Element_OphOuAnaestheticsatisfactionaudit_VitalSigns_oxygen_saturation_id']" 
			),
			'systolicBloodPressure' => array (
					'xpath' => "//*[@id='Element_OphOuAnaestheticsatisfactionaudit_VitalSigns_systolic_id']" 
			),
			'bodyTemp' => array (
					'xpath' => "//*[@id='Element_OphOuAnaestheticsatisfactionaudit_VitalSigns_body_temp_id']" 
			),
			'heartRate' => array (
					'xpath' => "//*[@id='Element_OphOuAnaestheticsatisfactionaudit_VitalSigns_heart_rate_id']" 
			),
			'consciousLevelAvpu' => array (
					'xpath' => "//*[@id='Element_OphOuAnaestheticsatisfactionaudit_VitalSigns_conscious_lvl_id']" 
			),
			'comments' => array (
					'xpath' => "//*[@id='Element_OphOuAnaestheticsatisfactionaudit_Notes_comments']" 
			),
			'dischargeYes' => array (
					'xpath' => "//*[@id='Element_OphOuAnaestheticsatisfactionaudit_Notes_ready_for_discharge_id_1']" 
			),
			'dischargeNo' => array (
					'xpath' => "//*[@id='Element_OphOuAnaestheticsatisfactionaudit_Notes_ready_for_discharge_id_2']" 
			),
			'save' => array (
					'xpath' => "//*[@id='et_save']" 
			),
			'ASASavedOk' => array (
					'xpath' => "//*[@id='flash-success']" 
			),
			'edit' => array (
					'xpath' => "//*[@class='inline-list tabs event-actions']//*[contains(text(),'Edit')]" 
			),
			'deleteEvent' => array (
					'xpath' => "//*[@class=' delete event-action button button-icon small']//*[@class='icon-button-small-trash-can']" 
			),
			'confirmDeleteEvent' => array (
					'xpath' => "//button[@id='et_deleteevent']" 
			),
			'anaesthetistValidationError' => array (
					//'xpath' => "//*[@class='alert-box error with-icon']//*[contains(text(),'Anaesthetist: Anaesthetist cannot be blank.')]"
					'xpath' => "//*[@class='alert-box error with-icon']//*[contains(text(),'Anaesthetist cannot be blank.')]"
			),
			'vitalRespiratoryValidationError' => array (
					//'xpath' => "//*[@class='alert-box error with-icon']//*[contains(text(),'Vital Signs: Respiratory Rate cannot be blank.')]"
					'xpath' => "//*[@class='alert-box error with-icon']//*[contains(text(),'Respiratory Rate cannot be blank.')]"
			),
			'vitalOxygenSaturationValidationError' => array (
					//'xpath' => "//*[@class='alert-box error with-icon']//*[contains(text(),'Vital Signs: Oxygen Saturation cannot be blank.')]"
					'xpath' => "//*[@class='alert-box error with-icon']//*[contains(text(),'Oxygen Saturation cannot be blank.')]"
			),
			'vitalSystolicBloodPressureValidationError' => array (
					//'xpath' => "//*[@class='alert-box error with-icon']//*[contains(text(),'Vital Signs: Systolic Blood Pressure cannot be blank.')]"
					'xpath' => "//*[@class='alert-box error with-icon']//*[contains(text(),'Systolic Blood Pressure cannot be blank.')]"
			),
			'vitalBodyTempValidationError' => array (
					//'xpath' => "//*[@class='alert-box error with-icon']//*[contains(text(),'Vital Signs: Body Temperature cannot be blank.')]"
					'xpath' => "//*[@class='alert-box error with-icon']//*[contains(text(),'Body Temperature cannot be blank.')]"
			),
			'vitalHeartRateValidationError' => array (
					//'xpath' => "//*[@class='alert-box error with-icon']//*[contains(text(),'Vital Signs: Heart Rate cannot be blank.')]"
					'xpath' => "//*[@class='alert-box error with-icon']//*[contains(text(),'Heart Rate cannot be blank.')]"
			),
			'vitalAVPUValidationError' => array (
					//'xpath' => "//*[@class='alert-box error with-icon']//*[contains(text(),'Vital Signs: Conscious Level AVPU cannot be blank.')]"
					'xpath' => "//*[@class='alert-box error with-icon']//*[contains(text(),'Conscious Level AVPU cannot be blank.')]"
			),
			'readyForDischargeValidationError' => array (
					//'xpath' => "//*[@class='alert-box error with-icon']//*[contains(text(),'Notes: Ready for discharge from recovery cannot be blank.')]"
					'xpath' => "//*[@class='alert-box error with-icon']//*[contains(text(),'Ready for discharge from recovery cannot be blank.')]"
			),
			'deleteSuccess' => array (
					'xpath' => "//*[contains(text(), 'An event was deleted, please ensure the episode status is still correct.')]" 
			) 
	)
	;
	public function anaesthetist($anaesthetist) {
		$this->getElement ( 'anaesthetist' )->selectOption ( $anaesthetist );
	}
	public function pain($pain) {
		$this->getElement ( 'pain' )->selectOption ( $pain );
	}
	public function nausea($nausea) {
		$this->getElement ( 'nausea' )->selectOption ( $nausea );
	}
	public function vomitCheckBoxYes() {
		$this->getElement ( 'vomitCheckBox' )->check ();
	}
	public function vomitCheckBoxNo() {
		$this->getElement ( 'vomitCheckBox' )->uncheck ();
	}
	public function respiratoryRate($rate) {
		$this->getElement ( 'respiratoryRate' )->selectOption ( $rate );
	}
	public function oxygenSaturation($saturation) {
		$this->getElement ( 'oxygenSaturation' )->selectOption ( $saturation );
	}
	public function systolicBlood($systolic) {
		$this->getElement ( 'systolicBloodPressure' )->selectOption ( $systolic );
	}
	public function bodyTemp($temp) {
		$this->getElement ( 'bodyTemp' )->selectOption ( $temp );
	}
	public function heartRate($rate) {
		$this->getElement ( 'heartRate' )->selectOption ( $rate );
	}
	public function consciousLevel($level) {
		$this->getElement ( 'consciousLevelAvpu' )->selectOption ( $level );
	}
	public function comments($comments) {
		$this->getElement ( 'comments' )->setValue ( $comments );
	}
	public function dischargeYes() {
		$this->getElement ( 'dischargeYes' )->click ();
	}
	public function dischargeNo() {
		$this->getElement ( 'dischargeNo' )->click ();
	}

	protected function hasASASaved() {
		return ( bool ) $this->find ( 'xpath', $this->getElement ( 'ASASavedOk' )->getXpath () );
		;
	}
	public function saveASAAndConfirm() {
		$this->getElement ( 'save' )->click ();

		$this->getSession ()->wait ( 5000, 'window.$ && $.active == 0' );
		if (!$this->hasASASaved ()) {
			throw new BehaviorException ( "WARNING!!!  ASA has NOT been saved!!  WARNING!!" );
		}
	}
	public function editEvent() {
		$this->getElement ( 'edit' )->click ();
		$this->getSession ()->wait ( 5000, 'window.$ && $(".event-actions .selected a").text() == "Edit"' );
	}
	protected function deleteSuccessCheck() {
        $this->getDriver()->wait ( 1000, "window.$ && $('#flash-success').css('display') == 'inline-block'" );
		return ( bool ) $this->find ( 'xpath', $this->getElement ( 'deleteSuccess' )->getXpath () );
	}
	public function deleteEvent() {
		$this->getElement ( 'deleteEvent' )->click ();
		$this->getDriver()->wait ( 5000, "window.$ && $('#et_deleteevent').css('display') == 'inline-block'" );
		$this->getElement ( 'confirmDeleteEvent' )->click ();

		if (!$this->deleteSuccessCheck ()) {
			throw new BehaviorException ( "WARNING!!! Deletion of event has NOT been successful WARNING!!!" );
		}
	}

    public function validationErrorCheck() {
        $this->getSession ()->wait ( 5000, 'window.$ && $.active == 0' );
        foreach (array(
                     'anaesthetistValidationError',
                     'vitalRespiratoryValidationError',
                     'vitalOxygenSaturationValidationError',
                     'vitalSystolicBloodPressureValidationError',
                     'vitalBodyTempValidationError',
                     'vitalHeartRateValidationError',
                     'vitalAVPUValidationError',
                     'readyForDischargeValidationError'

                 ) as $error)
        if (!$this->find ( 'xpath', $this->getElement ( $error )->getXpath () )) {
            throw new BehaviorException("Didn't find validation error message for $error");
        }
        return true;
	}
}