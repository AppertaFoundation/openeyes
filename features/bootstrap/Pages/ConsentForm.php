<?php
use Behat\Behat\Exception\BehaviorException;
class ConsentForm extends OpenEyesPage
{
	protected $path = "OphTrConsent/default/view/{eventId}}";

	protected $elements = array(
		'unbookedProcedure' => array('xpath' => "//input[contains(@value, 'unbooked')]"),
		'createConsentForm' => array('xpath' => "//*[@class='button-bar right']//*[@id='et_save']"),
		'consentType' => array('xpath' => "//*[@id='Element_OphTrConsent_Type_type_id']"),
		'rightEye' => array('xpath' => "//*[@id='Element_OphTrConsent_Procedure_eye_id_2']"),
		'bothEyes' => array('xpath' => "//*[@id='Element_OphTrConsent_Procedure_eye_id_3']"),
		'leftEyes' => array('xpath' => "//*[@id='Element_OphTrConsent_Procedure_eye_id_1']"),
		'commonProcedure' => array('xpath' => "//*[@id='select_procedure_id_procedures']"),
		'procedureType' => array('xpath' => "//input[@id='autocomplete_procedure_id_procedures']"),
		'chooseLaser' => array('xpath' => "//a[contains(text(),'Laser iridoplasty')]"),
		'anaestheticTypeLA' => array('xpath' => "//*[@id='Element_OphTrConsent_Procedure_anaesthetic_type_id_3']"),
		'anaestheticTypeLAC' => array('xpath' => "//*[@id='Element_OphTrConsent_Procedure_anaesthetic_type_id_2']"),
		'permissionsImagesNO' => array('xpath' => "//*[@id='Element_OphTrConsent_Permissions_images_id_2']"),
		'permissionsImagesYES' => array('xpath' => "//*[@id='Element_OphTrConsent_Permissions_images_id_1']"),
		'informationLeaflet' => array('xpath' => "//*[@id='Element_OphTrConsent_Other_information']"),
		'anaestheticLeaflet' => array('xpath' => "//*[@id='Element_OphTrConsent_Other_anaesthetic_leaflet']"),
		'witnessRequired' => array('xpath' => "//*[@id='Element_OphTrConsent_Other_witness_required']"),
		'witnessName' => array('xpath' => "//*[@id='Element_OphTrConsent_Other_witness_name']"),
		'interpreterRequired' => array('xpath' => "//*[@id='Element_OphTrConsent_Other_interpreter_required']"),
		'interpreterName' => array('xpath' => "//*[@id='Element_OphTrConsent_Other_interpreter_name']"),
		'supplementaryConsent' => array('xpath' => "//*[@id='Element_OphTrConsent_Other_include_supplementary_consent']"),
        'saveConsentForm' => array('xpath' => "//*[@id='et_save']"),
		'saveConsentFormDraft' => array('xpath' => "//*[@id='et_save_draft']"),
        'saveConsentFormOK' => array('xpath' => "//*[@id='flash-success']"),
		'test' => array('xpath' => "//*[@id='div_Element_OphTrConsent_Other_anaesthetic_leaflet']"),
	);


	public function unbookedProcedure()
	{
		$this->getElement('unbookedProcedure')->check();
	}

	public function createConsentForm()
	{
		$this->getElement('createConsentForm')->click();
	}

	public function chooseType($type)
	{
		$this->getElement('consentType')->selectOption($type);
	}

	public function procedureEye($eye)
	{
		if ($eye === ('Right')) {
			$this->getElement('rightEye')->press();
		}
		if ($eye === ('Both')) {
			$this->getElement('bothEyes')->press();
		}
		if ($eye === ('Left')) {
			$this->getElement('leftEyes')->press();
		}
	}

	public function commonProcedure($common)
	{
		$this->getElement('commonProcedure')->selectOption($common);
		$this->getSession()->wait(5000);
	}

	public function procedureType($type)
	{
		$this->getElement('procedureType')->click();
		$this->getElement('procedureType')->setValue($type);
//    $this->getSession()->wait(5000);
		$this->getElement('chooseLaser')->click();
	}

	public function anaestheticTypeLA()
	{
		$this->getElement('anaestheticTypeLA')->click();
	}

	public function anaestheticTypeLAC()
	{
		$this->getElement('anaestheticTypeLAC')->click();
	}

	public function permissionImagesNo()
	{
		//focus added before click because the click event was propagated to another dom element and
		// not to permissionImagesNO
		$el = $this->getElement('permissionsImagesNO'); //->focus()->click();
		$el->focus();
		$el->click();
	}

	public function permissionImagesYes()
	{
		$this->getElement('permissionsImagesYES')->click();
	}

	public function informationLeaflet()
	{
		$this->getElement('informationLeaflet')->check();
	}

	public function anaestheticLeaflet()
	{
		$this->getElement('test')->click();
		$this->getElement('anaestheticLeaflet')->click();
	}

	public function witnessRequired()
	{
		$this->getElement('witnessRequired')->click();
	}

	public function witnessName($witness)
	{
		$this->getElement('witnessName')->setValue($witness);
	}

	public function interpreterRequired()
	{
		$this->getElement('interpreterRequired')->click();
	}

	public function interpreterName($name)
	{
		$this->getElement('interpreterName')->setValue($name);
	}

	public function supplementaryConsent()
	{
		$this->getElement('supplementaryConsent')->click();
	}

	public function saveConsentFormDraft()
	{
		$this->getElement('saveConsentFormDraft')->click();
	}

    public function saveConsentForm ()
    {
        $this->getElement('saveConsentForm')->click();
    }

    protected function hasConsentSaved ()
    {
        return (bool) $this->find('xpath', $this->getElement('saveConsentFormOK')->getXpath());;
    }

    public function saveConsentAndConfirm ()
    {
        $this->getElement('saveConsentFormDraft')->click();

        if ($this->hasConsentSaved()) {
            print "Consent has been saved OK";
        }

        else {
            throw new BehaviorException("WARNING!!!  Consent has NOT been saved!!  WARNING!!");
        }
    }
}