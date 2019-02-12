<?php
use Behat\Behat\Exception\BehaviorException;
class TherapyApplication extends OpenEyesPage {
	protected $path = "/site/OphCoTherapyapplication/Default/create?patient_id={parentId}";
	protected $elements = array (
			
			// Diagnosis
			'addRightSide' => array (
					'xpath' => "//*[@id='clinical-create']/div[3]/div/div[1]/div[2]/a" 
			),
			'leftDiagnosis' => array (
					'xpath' => "//*[@id='Element_OphCoTherapyapplication_Therapydiagnosis_left_diagnosis1_id']" 
			),
			'rightDiagnosis' => array (
					'xpath' => "//*[@id='Element_OphCoTherapyapplication_Therapydiagnosis_right_diagnosis1_id']" 
			),
			'rightSecondaryTo' => array (
					'xpath' => "//*[@id='Element_OphCoTherapyapplication_Therapydiagnosis_right_diagnosis2_id']" 
			),
			'leftSecondaryTo' => array (
					'xpath' => "//*[@id='Element_OphCoTherapyapplication_Therapydiagnosis_left_diagnosis2_id']" 
			),
            'leftSideClose' => array (
                'xpath' => "//div[contains(@class,'left-eye')]//a[contains(@class,'icon-remove-side')]"
            ),
			
			// Patient Suitability
			'rightTreatment' => array (
					'xpath' => "//*[@id='Element_OphCoTherapyapplication_PatientSuitability_right_treatment_id']" 
			),
			'leftTreatment' => array (
					'xpath' => "//*[@id='Element_OphCoTherapyapplication_PatientSuitability_left_treatment_id']" 
			),
			'hopefullFIXRight' => array (
					'xpath' => "//form[@id='clinical-create']/div[4]/div/div[2]/div/div[2]" 
			),
			'hopefullFIXLeft' => array (
					'xpath' => "//form[@id='clinical-create']/div[4]/div/div/div/div[2]" 
			),
			'rightAngiogramDate' => array (
					'xpath' => "//*[@id='Element_OphCoTherapyapplication_PatientSuitability_right_angiogram_baseline_date_0']" 
			),
			'leftAngiogramDate' => array (
					'xpath' => "//*[@id='Element_OphCoTherapyapplication_PatientSuitability_left_angiogram_baseline_date_0']" 
			),
			'calendarDate' => array (
					'xpath' => "//*[@id='ui-datepicker-div']/table/tbody//*[contains(text(),'1')]" 
			),
			
			// RIGHT Relative ContraIndications
			'cerebrovascularYes' => array (
					'xpath' => "//*[@id='Element_OphCoTherapyapplication_RelativeContraindications_cerebrovascular_accident_1']" 
			),
			'patientconsentYes' => array (
					'xpath' => "//*[@id='Element_OphCoTherapyapplication_MrServiceInformation_patient_sharedata_consent_1']"
			),
			'cerebrovascularNo' => array (
					'xpath' => "//*[@id='Element_OphCoTherapyapplication_RelativeContraindications_cerebrovascular_accident_0']" 
			),
			'ischaemicYes' => array (
					'xpath' => "//*[@id='Element_OphCoTherapyapplication_RelativeContraindications_ischaemic_attack_1']" 
			),
			'ischaemicNo' => array (
					'xpath' => "//*[@id='Element_OphCoTherapyapplication_RelativeContraindications_ischaemic_attack_0']" 
			),
			'myocardialYes' => array (
					'xpath' => "//*[@id='Element_OphCoTherapyapplication_RelativeContraindications_myocardial_infarction_1']" 
			),
			'myocardialNo' => array (
					'xpath' => "//*[@id='Element_OphCoTherapyapplication_RelativeContraindications_myocardial_infarction_0']" 
			),
			
			// RIGHT Exceptional Circumstances
			'standardExistsYes' => array (
					'xpath' => "//input[@id='Element_OphCoTherapyapplication_ExceptionalCircumstances_right_standard_intervention_exists_1']" 
			),
			'standardExistsNo' => array (
					'xpath' => "//input[@id='Element_OphCoTherapyapplication_ExceptionalCircumstances_right_standard_intervention_exists_0']" 
			),
			'standardIntervention' => array (
					'xpath' => "//select[@id='Element_OphCoTherapyapplication_ExceptionalCircumstances_right_standard_intervention_id']" 
			),
			'standardPreviousYes' => array (
					'xpath' => "//input[@id='Element_OphCoTherapyapplication_ExceptionalCircumstances_right_standard_previous_1']" 
			),
			'standardPreviousNo' => array (
					'xpath' => "//input[@id='Element_OphCoTherapyapplication_ExceptionalCircumstances_right_standard_previous_0']" 
			),
			'standardAdditional' => array (
					'xpath' => "//input[@id='Element_OphCoTherapyapplication_ExceptionalCircumstances_right_intervention_id_1']" 
			),
			'standardDeviation' => array (
					'xpath' => "//input[@id='Element_OphCoTherapyapplication_ExceptionalCircumstances_right_intervention_id_2']" 
			),
			'detailsAdditionalOrDeviation' => array (
					'xpath' => "//*[@id='Element_OphCoTherapyapplication_ExceptionalCircumstances_right_description']" 
			),
			'reasonForNotUsingIntervention' => array (
					'xpath' => "//select[@id='Element_OphCoTherapyapplication_ExceptionalCircumstances_right_deviationreasons']"
			),
			'patientSignificantDifferent' => array (
					'xpath' => "//*[@id='Element_OphCoTherapyapplication_ExceptionalCircumstances_right_patient_different']" 
			),
			'patientMoreBenefit' => array (
					'xpath' => "//*[@id='Element_OphCoTherapyapplication_ExceptionalCircumstances_right_patient_gain']" 
			),
			'patientFactorsYes' => array (
					'xpath' => "//*[@id='Element_OphCoTherapyapplication_ExceptionalCircumstances_right_patient_factors_1']" 
			),
			'patientFactorsNo' => array (
					'xpath' => "//*[@id='Element_OphCoTherapyapplication_ExceptionalCircumstances_right_patient_factors_0']" 
			),
			'patientFactorDetails' => array (
					'xpath' => "//*[@id='Element_OphCoTherapyapplication_ExceptionalCircumstances_right_patient_factor_details']" 
			),
			'patientExpectations' => array (
					'xpath' => "//*[@id='Element_OphCoTherapyapplication_ExceptionalCircumstances_right_patient_expectations']" 
			),
			'anticipatedStartDate' => array (
					'xpath' => "//select[@id='Element_OphCoTherapyapplication_ExceptionalCircumstances_right_start_period_id']" 
			),
            'rightClinicalReasonForUrgency' => array (
                    'xpath' => "//textarea[@id='Element_OphCoTherapyapplication_ExceptionalCircumstances_right_urgency_reason']"
            ),
			
			// LEFT Exceptional Circumstances
			'leftstandardExistsYes' => array (
					'xpath' => "//input[@id='Element_OphCoTherapyapplication_ExceptionalCircumstances_left_standard_intervention_exists_1']" 
			),
			'leftstandardExistsNo' => array (
					'xpath' => "//input[@id='Element_OphCoTherapyapplication_ExceptionalCircumstances_left_standard_intervention_exists_0']" 
			),
			'leftstandardIntervention' => array (
					'xpath' => "//select[@id='Element_OphCoTherapyapplication_ExceptionalCircumstances_left_standard_intervention_id']"

			),
			'leftstandardPreviousYes' => array (
					'xpath' => "//input[@id='Element_OphCoTherapyapplication_ExceptionalCircumstances_left_standard_previous_1']" 
			),
			'leftstandardPreviousNo' => array (
					'xpath' => "//input[@id='Element_OphCoTherapyapplication_ExceptionalCircumstances_left_standard_previous_0']" 
			),
			'leftstandardAdditional' => array (
					'xpath' => "//input[@id='Element_OphCoTherapyapplication_ExceptionalCircumstances_left_intervention_id_1']" 
			),
			'leftstandardDeviation' => array (
					'xpath' => "//input[@id='Element_OphCoTherapyapplication_ExceptionalCircumstances_left_intervention_id_2']" 
			),
			'leftdetailsAdditionalOrDeviation' => array (
					'xpath' => "//*[@id='Element_OphCoTherapyapplication_ExceptionalCircumstances_left_description']" 
			),
			'leftreasonForNotUsingIntervention' => array (
					'xpath' => "//*[@id='Element_OphCoTherapyapplication_ExceptionalCircumstances_left_deviation_fields']" 
			),
			'leftpatientSignificantDifferent' => array (
					'xpath' => "//*[@id='Element_OphCoTherapyapplication_ExceptionalCircumstances_left_patient_different']" 
			),
			'leftpatientMoreBenefit' => array (
					'xpath' => "//*[@id='Element_OphCoTherapyapplication_ExceptionalCircumstances_left_patient_gain']" 
			),
			'leftpatientFactorsYes' => array (
					'xpath' => "//*[@id='Element_OphCoTherapyapplication_ExceptionalCircumstances_left_patient_factors_1']" 
			),
			'leftpatientFactorsNo' => array (
					'xpath' => "//*[@id='Element_OphCoTherapyapplication_ExceptionalCircumstances_left_patient_factors_0']" 
			),
			'leftpatientFactorDetails' => array (
					'xpath' => "//*[@id='Element_OphCoTherapyapplication_ExceptionalCircumstances_left_patient_factor_details']" 
			),
			'leftpatientExpectations' => array (
					'xpath' => "//*[@id='Element_OphCoTherapyapplication_ExceptionalCircumstances_left_patient_expectations']" 
			),
			'leftanticipatedStartDate' => array (
					'xpath' => "//select[@id='Element_OphCoTherapyapplication_ExceptionalCircumstances_left_start_period_id']" 
			),
            'leftClinicalReasonForUrgency' => array (
                'xpath' => "//textarea[@id='Element_OphCoTherapyapplication_ExceptionalCircumstances_left_urgency_reason']"
            ),

        'patientVenousYes' => array (
					'xpath' => "//select[@name='Element_OphCoTherapyapplication_PatientSuitability[right_DecisionTreeResponse][48]']" 
			),
			'CRVOYes' => array (
					'xpath' => "//select[@name='Element_OphCoTherapyapplication_PatientSuitability[right_DecisionTreeResponse][49]']" 
			),
			'saveTherapyApplication' => array (
					'xpath' => "//button[@id='et_save']" 
			),
			'saveTherapyOK' => array (
					'xpath' => "//*[@id='flash-success']" 
			),
			
			'removeRightEye' => array (
					'xpath' => "//*[@class='icon-remove-side remove-side']" 
			),
			'addRightEye' => array (
					'xpath' => "//*[@class='inactive-form']//*[contains(text(),'Add right side')]" 
			),
			
			// Scenario 2
			'leftPatientHasCnvYes' => array (
					'xpath' => "//select[@id='Element_OphCoTherapyapplication_PatientSuitability_left_DecisionTreeResponse_2']//*[@value=1]" 
			),
			'leftPatientHasCnvNo' => array (
					'xpath' => "//select[@id='Element_OphCoTherapyapplication_PatientSuitability_left_DecisionTreeResponse_2']//*[@value=0]" 
			),
			'rightPatientHasCnvYes' => array (
					'xpath' => "//select[@id='Element_OphCoTherapyapplication_PatientSuitability_right_DecisionTreeResponse_33']//*[@value=1]" 
			),
			'rightPatientHasCnvNo' => array (
					'xpath' => "//select[@id='Element_OphCoTherapyapplication_PatientSuitability_right_DecisionTreeResponse_33']//*[@value=0]" 
			),
			'rightPatientNoCnvPDTRoute' => array (
					'xpath' => "//select[@id='Element_OphCoTherapyapplication_PatientSuitability_right_DecisionTreeResponse_33']//*[@value=0]" 
			),
			
			'leftPatientHasMacularOdemaYes' => array (
					'xpath' => "//select[@id='Element_OphCoTherapyapplication_PatientSuitability_left_DecisionTreeResponse_14']//*[@value=1]" 
			),
			'leftPatientHasMacularOdemaNo' => array (
					'xpath' => "//select[@id='Element_OphCoTherapyapplication_PatientSuitability_left_DecisionTreeResponse_14']//*[@value=0]" 
			),
			'rightPatientHasMacularOdemaYes' => array (
					'xpath' => "//select[@id='Element_OphCoTherapyapplication_PatientSuitability_right_DecisionTreeResponse_14']//*[@value=1]" 
			),
			'rightPatientHasMacularOdemaNo' => array (
					'xpath' => "//select[@id='Element_OphCoTherapyapplication_PatientSuitability_right_DecisionTreeResponse_14']//*[@value=0]" 
			),
			
			'leftPatientHasDiabeticMacularOdemaYes' => array (
					'xpath' => "//select[@id='Element_OphCoTherapyapplication_PatientSuitability_left_DecisionTreeResponse_15']//*[@value=1]" 
			),
			'leftPatientHasDiabeticMacularOdemaNo' => array (
					'xpath' => "//select[@id='Element_OphCoTherapyapplication_PatientSuitability_left_DecisionTreeResponse_15']//*[@value=0]" 
			),
			'rightPatientHasDiabeticMacularOdemaYes' => array (
					'xpath' => "//select[@id='Element_OphCoTherapyapplication_PatientSuitability_right_DecisionTreeResponse_15']//*[@value=1]" 
			),
			'rightPatientHasDiabeticMacularOdemaNo' => array (
					'xpath' => "//select[@id='Element_OphCoTherapyapplication_PatientSuitability_right_DecisionTreeResponse_15']//*[@value=0]" 
			),
			
			'leftPatientCRT>=400Yes' => array (
					'xpath' => "//select[@id='Element_OphCoTherapyapplication_PatientSuitability_left_DecisionTreeResponse_16']//*[@value=1]" 
			),
			'leftPatientCRT>=400No' => array (
					'xpath' => "//select[@id='Element_OphCoTherapyapplication_PatientSuitability_left_DecisionTreeResponse_16']//*[@value=0]" 
			),
			'rightPatientCRT>=400Yes' => array (
					'xpath' => "//select[@id='Element_OphCoTherapyapplication_PatientSuitability_right_DecisionTreeResponse_16']//*[@value=1]" 
			),
			'rightPatientCRT>=400No' => array (
					'xpath' => "//select[@id='Element_OphCoTherapyapplication_PatientSuitability_right_DecisionTreeResponse_16']//*[@value=0]" 
			),
			
			// Scenario 3
			'rightOcularConditionRareYes' => array (
					'xpath' => "//*[@id='Element_OphCoTherapyapplication_ExceptionalCircumstances_right_condition_rare_1']" 
			),
			'rightOcularConditionRareNo' => array (
					'xpath' => "//*[@id='Element_OphCoTherapyapplication_ExceptionalCircumstances_right_condition_rare_0']" 
			),
			'rightIncidenceDetails' => array (
					'xpath' => "//*[@id='Element_OphCoTherapyapplication_ExceptionalCircumstances_right_incidence']" 
			),
			'rightPatientSignificantlyDifferent' => array (
					'xpath' => "//*[@id='Element_OphCoTherapyapplication_ExceptionalCircumstances_right_patient_different']" 
			),
			'rightPatientLikelyToGain' => array (
					'xpath' => "//*[@id='Element_OphCoTherapyapplication_ExceptionalCircumstances_right_patient_gain']" 
			),
			
			'leftVenousOcclusionYes' => array (
					'xpath' => "//select[@id='Element_OphCoTherapyapplication_PatientSuitability_left_DecisionTreeResponse_48']//*[@value=1]" 
			),
			'leftCRVOYes' => array (
					'xpath' => "//select[@id='Element_OphCoTherapyapplication_PatientSuitability_left_DecisionTreeResponse_49']//*[@value=1]" 
			) ,
            // MR Service Information
            'consultantOf' => array(
                    'xpath' => "//*[@id='Element_OphCoTherapyapplication_MrServiceInformation_consultant_id']"
            ),
            'intendedSite' => array (
                'xpath' => "//*[@id='Element_OphCoTherapyapplication_MrServiceInformation_site_id']"
            )
	);

	public function removeRightEye() {
		$this->getElement ( 'removeRightEye' )->click ();
	}
	public function addRightEye() {
		$this->getElement ( 'addRightEye' )->click ();
	}
	public function addRightSide() {
		// $this->getSession()->wait(3000);
		$this->getElement ( 'addRightSide' )->click ();
	}
	public function rightSideDiagnosis($diagnosis) {
		$this->getElement ( 'rightDiagnosis' )->selectOption ( $diagnosis );
	}
	public function leftSideDiagnosis($diagnosis) {
		$this->getElement ( 'leftDiagnosis' )->selectOption ( $diagnosis );
	}
	public function rightSecondaryTo($secondary) {
		$this->getElement ( 'rightSecondaryTo' )->selectOption ( $secondary );
	}
	public function leftSecondaryTo($secondary) {
		$this->getElement ( 'leftSecondaryTo' )->selectOption ( $secondary );
	}
	public function rightTreatment($treatment) {
		$this->getElement ( 'rightTreatment' )->selectOption ( $treatment );
	}
	public function rightDate($date) {
		
		// $this->getSession()->wait(7000, "$('#ui-datepicker-div').css('display') == 'block'");
		$this->getElement ( 'rightAngiogramDate' )->click ();
		$this->getElement ( 'rightAngiogramDate' )->click ();
		// $this->getSession()->wait(5000);
		$this->getElement ( 'calendarDate' )->click ();
	}
	public function leftTreatment($treatment) {
		$this->getElement ( 'leftTreatment' )->selectOption ( $treatment );
	}
	public function leftDate($date) {
		
		// $this->getSession()->wait(7000, "$('#ui-datepicker-div').css('display') == 'block'");
		$this->getElement ( 'leftAngiogramDate' )->click ();
		$this->getElement ( 'leftAngiogramDate' )->click ();
		// $this->getSession()->wait(3000);
		$this->getElement ( 'calendarDate' )->click ();
	}
	public function RightCerebYes() {
		$element = $this->getElement ( 'cerebrovascularYes' )->doubleClick();
		//$this->scrollWindowToElement ( $element );
		//$element->click ();
	}	
	public function PatientConsentYes() {
		$this->getElement ( 'patientconsentYes' )->doubleClick ();

		//$element = $this->getElement ( 'patientconsentYes' );
		//$this->scrollWindowToElement ( $element );
		//$element->doubleClick();
	}
	public function RightCerebNo() {
		$element = $this->getElement ( 'cerebrovascularNo' );
		$this->scrollWindowToElement ( $element );
		$element->click ();
	}
	public function RightIschaemicYes() {
		$element = $this->getElement ( 'ischaemicYes' );
		$this->scrollWindowToElement ( $element );
		$element->doubleClick();
	}
	public function RightIschaemicNo() {
		$element = $this->getElement ( 'ischaemicNo' );
		$this->scrollWindowToElement ( $element );
		$element->click ();
	}
	public function RightMyocardialYes() {
		$element = $this->getElement ( 'myocardialYes' );
		$this->scrollWindowToElement ( $element );
		$element->doubleClick();
	}
	public function RightMyocardialNo() {
		$element = $this->getElement ( 'myocardialNo' );
		$this->scrollWindowToElement ( $element );
		$element->click ();
		;
	}
	public function RightIntendedSite($site) {
		$element = $this->getElement ( 'intendedSite' );
		$this->scrollWindowToElement ( $element );
		$element->setValue ( $site );
	}
    public function consultantOf($id) {
        $element = $this->getElement('consultantOf');
        $element->setValue($id);
    }

	public function RightStandardExistsYes() {
		$this->getElement ( 'standardExistsYes' )->click ();
	}
	public function RightStandardExistsNo() {
		$this->getElement ( 'standardExistsNo' )->click ();
	}
	public function RightStandardIntervention($standard) {
		$this->getElement ( 'standardIntervention' )->selectOption ( $standard );
	}
	public function RightStandardPreviousYes() {
		$this->getElement ( 'standardPreviousYes' )->click ();
	}
	public function RightStandardPreviousNo() {
		$this->getElement ( 'standardPreviousNo' )->click ();
	}
	public function RightStandardAdditional() {
		$this->getElement ( 'standardAdditional' )->click ();
		// $this->getSession()->wait(3000);
	}
	public function RightStandardDeviation() {
		$this->getElement ( 'standardDeviation' )->click ();
		// $this->getSession()->wait(3000);
	}
	public function RightAdditionalOrDeviationComments($details) {
		$this->getElement ( 'detailsAdditionalOrDeviation' )->setValue ( $details );
	}
	public function RightNotUsingStandardIntervention($option) {
		$this->getElement ( 'reasonForNotUsingIntervention' )->selectOption ( $option );
	}
	public function RightPatientSignificantDifferent($comments) {
		$this->getElement ( 'patientSignificantDifferent' )->setValue ( $comments );
	}
	public function RightPatientMoreBenefit($comments) {
		$this->getElement ( 'patientMoreBenefit' )->setValue ( $comments );
	}
	public function RightPatientFactorsYes() {
		$this->getElement ( 'patientFactorsYes' )->click ();
	}
	public function RightPatientFactorsNo() {
		$this->getElement ( 'patientFactorsNo' )->click ();
	}
	public function RightPatientFactorDetails($comments) {
		$this->getElement ( 'patientFactorDetails' )->setValue ( $comments );
	}
	public function RightPatientExpectations($comments) {
		$this->getElement ( 'patientExpectations' )->setValue ( $comments );
	}
	public function RightAnticipatedStartDate($date) {
		$this->getElement ( 'anticipatedStartDate' )->selectOption ( $date );
	}
	public function LeftStandardExistsYes() {
		//$this->getElement ( 'leftstandardExistsYes' )->doubleClick();

		$element = $this->getElement ( 'leftstandardExistsYes' );
		$this->scrollWindowToElement ( $element );
		sleep(5);
		$element->doubleClick();
	}
	public function LeftStandardExistsNo() {
		$this->getElement ( 'leftstandardExistsNo' )->click ();
		// TO CODE
	}
	public function LeftStandardIntervention($standard) {
		//$this->getElement ( 'leftstandardIntervention' )->selectOption ( $standard );

		$element = $this->getElement ( 'leftstandardIntervention' );
		$this->scrollWindowToElement ( $element );
		sleep(5);
		$element->selectOption ( $standard );
	}
	public function LeftStandardPreviousYes() {
		$this->getElement ( 'leftstandardPreviousYes' )->click ();
	}
	public function LeftStandardPreviousNo() {
		$this->getElement ( 'leftstandardPreviousNo' )->click ();
		// TO CODE
	}
	public function LeftStandardAdditional() {
		$this->getElement ( 'leftstandardAdditional' )->click ();
		// $this->getSession()->wait(3000);
	}
	public function LeftStandardDeviation() {
		$this->getElement ( 'leftstandardDeviation' )->click ();
		// $this->getSession()->wait(3000);
	}
	public function LeftAdditionalOrDeviationComments($details) {
		$this->getElement ( 'leftdetailsAdditionalOrDeviation' )->setValue ( $details );
	}
	public function LeftNotUsingStandardIntervention($option) {
		//$this->getElement ( 'reasonForNotUsingIntervention' )->selectOption ( $option );

		$element = $this->getElement ( 'reasonForNotUsingIntervention' );
		$this->scrollWindowToElement ( $element );
		sleep(5);
		$element->selectOption ( $option );
	}
	public function LeftPatientSignificantDifferent($comments) {
		$this->getElement ( 'leftpatientSignificantDifferent' )->setValue ( $comments );
	}
	public function LeftPatientMoreBenefit($comments) {
		$this->getElement ( 'leftpatientMoreBenefit' )->setValue ( $comments );
	}
	public function LeftPatientFactorsYes() {
		$this->getElement ( 'leftpatientFactorsYes' )->click ();
	}
	public function LeftPatientFactorsNo() {
		$this->getElement ( 'leftpatientFactorsNo' )->click ();
	}
	public function LeftPatientFactorDetails($comments) {
		$this->getElement ( 'leftpatientFactorDetails' )->setValue ( $comments );
	}
	public function LeftPatientExpectations($comments) {
		$this->getElement ( 'leftpatientExpectations' )->setValue ( $comments );
	}
	public function LeftAnticipatedStartDate($date) {
		$this->getElement ( 'leftanticipatedStartDate' )->selectOption ( $date );
	}
	public function patientVenousYes($option) {
		// $this->getSession()->wait(5000);
		$this->getElement ( 'patientVenousYes' )->selectOption ( $option );
	}
	public function CRVOyes($option) {
		$this->getElement ( 'CRVOYes' )->selectOption ( $option );
		// $this->getSession()->wait(5000);
	}
	public function saveTherapy() {
		$this->getElement ( 'saveTherapyApplication' )->click ();
	}
	protected function hasTherapySaved() {
		return ( bool ) $this->find ( 'xpath', $this->getElement ( 'saveTherapyOK' )->getXpath () );
		;
	}
	public function saveTherapyAndConfirm() {
		$this->getElement ( 'saveTherapyApplication' )->click ();

		$this->getSession ()->wait ( 5000 );
		if (!$this->hasTherapySaved ()) {
			throw new BehaviorException ( "WARNING!!!  Therapy has NOT been saved!!  WARNING!!" );
		}
	}

    public function getDecisionTreeQuestionElementId($side, $question, $waitTime = 2000)
    {
        $id = "OphCoTherapyapplication_ComplianceCalculator_" . $side;
        $selector = "#" . $id;
        $driver = $this->getDriver();
        $driver->wait($waitTime, "window.$ && ($(\"" . $selector . "\").attr('data-defn') != undefined && ($(\"" . $selector . "\").attr('data-defn') !== false))" );
        $this->elements['decisionTreeElement'] = array('xpath' => "//*[contains(text(),'$question')]");
        $element = $this->getElement('decisionTreeElement');
        return $element->getAttribute('for');
    }

    protected function getNoOptionXpathForQuestion($side, $question)
    {
        return "//select[@id='" .
        $this->getDecisionTreeQuestionElementId($side, $question) .
        "']//*[@value=0]";
    }

	public function rightPatientCnvNO() {
        $this->elements['rightPatientCnvNOElement'] = array('xpath' => $this->getNoOptionXpathForQuestion('right', 'Patient has CNV'));
		$this->getElement( 'rightPatientCnvNOElement' )->click ();
	}
	public function rightMacularOdemaYes() {
		$this->waitForElementDisplayBlock ( '#Element_OphCoTherapyapplication_PatientSuitability_right_DecisionTreeResponse_14' );
		$this->getElement ( 'rightPatientHasMacularOdemaYes' )->click ();
	}
	public function rightDiabeticMacularOdemaYes() {
		$this->waitForElementDisplayBlock ( '#Element_OphCoTherapyapplication_PatientSuitability_right_DecisionTreeResponse_15' );
		$this->getElement ( 'rightPatientHasDiabeticMacularOdemaYes' )->click ();
	}
	public function rightCRT400Yes() {
		$this->waitForElementDisplayBlock ( '#Element_OphCoTherapyapplication_PatientSuitability_right_DecisionTreeResponse_16' );
		$this->getElement ( 'rightPatientCRT>=400Yes' )->click ();
	}
	public function leftPatientCnvNO() {
		$this->waitForElementDisplayBlock ( '#Element_OphCoTherapyapplication_PatientSuitability_left_DecisionTreeResponse_2' );
		$this->getElement ( 'leftPatientHasCnvNo' )->click ();
	}
	public function rightPatientCnvNoPDTRoute() {
		// $this->waitForElementDisplayBlock('#Element_OphCoTherapyapplication_PatientSuitability_left_DecisionTreeResponse_33');
		$this->getElement ( 'rightPatientNoCnvPDTRoute' )->click ();
		$this->getSession ()->wait ( 5000 );
	}
	public function leftMacularOdemaYes() {
		$this->getSession ()->wait ( 2000 );
		$this->waitForElementDisplayBlock ( '#Element_OphCoTherapyapplication_PatientSuitability_left_DecisionTreeResponse_14' );
		$this->getElement ( 'leftPatientHasMacularOdemaYes' )->click ();
	}
	public function leftDiabeticMacularOdemaYes() {
		$this->waitForElementDisplayBlock ( '#Element_OphCoTherapyapplication_PatientSuitability_left_DecisionTreeResponse_15' );
		$this->getElement ( 'leftPatientHasDiabeticMacularOdemaYes' )->click ();
	}
	public function leftCRT400Yes() {
		$this->waitForElementDisplayBlock ( '#Element_OphCoTherapyapplication_PatientSuitability_left_DecisionTreeResponse_16' );
		$this->getElement ( 'leftPatientCRT>=400Yes' )->click ();
	}
	public function rightOcularConditionRareYes() {
		$this->getElement ( 'rightOcularConditionRareYes' )->click ();
	}
	public function rightOcularConditionRareNo() {
		$this->getElement ( 'rightOcularConditionRareNo' )->click ();
	}
	public function rightIncidenceDetails($comments) {
		$this->getElement ( 'rightIncidenceDetails' )->setValue ( $comments );
	}
	public function rightPatientSignificantlyDifferent($comments) {
		$this->getElement ( 'rightPatientSignificantlyDifferent' )->setValue ( $comments );
	}
	public function rightPatientLikelyToGainBenefit($comments) {
		$this->getElement ( 'rightPatientLikelyToGain' )->setValue ( $comments );
	}
	public function leftPatientVenousOcclusionYes() {
		$this->waitForElementDisplayBlock ( '#Element_OphCoTherapyapplication_PatientSuitability_left_DecisionTreeResponse_48' );
		$this->getElement ( 'leftVenousOcclusionYes' )->click ();
	}
	public function leftPatientCRVOYes() {
		$this->waitForElementDisplayBlock ( '#Element_OphCoTherapyapplication_PatientSuitability_left_DecisionTreeResponse_49' );
		$this->getElement ( 'leftCRVOYes' )->click ();
	}
    public function removeLeftSide() {
        $this->getElement('leftSideClose')->click();
        #TODO: check that the left diagnosis dropdown is no longer visible
    }

    public function addRightClinicalReasonForUrgency($reason)
    {
        $this->getElement ( 'rightClinicalReasonForUrgency' )->setValue ( $reason );
    }
}



