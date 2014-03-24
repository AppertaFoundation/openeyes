<?php
use Behat\Behat\Exception\BehaviorException;

class Phasing extends OpenEyesPage
{
    protected $path = "/site/OphCiPhasing/Default/create?patient_id={parentId}";

    protected $elements = array(
        'phasingLogo' => array('xpath' => "//*[@id='event-content']//*[contains(text(),'Phasing')]"),

        'phasingInstrumentRight' => array('xpath' => "//*[@id='Element_OphCiPhasing_IntraocularPressure_right_instrument_id']"),
        'phasingDilationRightYes' => array('xpath' => "//*[@id='Element_OphCiPhasing_IntraocularPressure_right_dilated_1']"),
        'phasingDilationRightNo' => array('xpath' => "//*[@id='Element_OphCiPhasing_IntraocularPressure_right_dilated_0']"),
        'phasingReadingTimeRight' => array('xpath' => "//*[@id='intraocularpressure_reading_0_measurement_timestamp']"),
        'phasingPressureRight' => array('xpath' => "//input[@id='intraocularpressure_reading_0_value']"),
        'phasingCommentsRight' => array('xpath' => "//*[@id='Element_OphCiPhasing_IntraocularPressure_right_comments']"),
        'phasingInstrumentLeft' => array('xpath' => "//*[@id='Element_OphCiPhasing_IntraocularPressure_left_instrument_id']"),
        'phasingDilationLeftYes' => array('xpath' => "//*[@id='Element_OphCiPhasing_IntraocularPressure_left_dilated_1']"),
        'phasingDilationLeftNo' => array('xpath' => "//*[@id='Element_OphCiPhasing_IntraocularPressure_left_dilated_0']"),
        'phasingReadingTimeLeft' => array('xpath' => "//*[@id='intraocularpressure_reading_1_measurement_timestamp']"),
        'phasingPressureLeft' => array('xpath' => "//input[@id='intraocularpressure_reading_1_value']"),
        'phasingCommentsLeft' => array('xpath' => "//*[@id='Element_OphCiPhasing_IntraocularPressure_left_comments']"),

        'phasingReadingAddLeft' => array('xpath' => "//*[@class='element-eye left-eye column side right']//*[contains(text(),'Add')]"),
        'phasingReadingTimeLeft2' => array('xpath' => "//*[@id='intraocularpressure_reading_2_measurement_timestamp']"),
        'phasingPressureLeft2' => array('xpath' => "//input[@id='intraocularpressure_reading_2_value']"),

        'phasingReadingAddRight' => array('xpath' => "//*[@class='element-eye right-eye column side left']//*[contains(text(),'Add')]"),
        'phasingReadingTimeRight2' => array('xpath' => "//*[@id='intraocularpressure_reading_3_measurement_timestamp']"),
        'phasingPressureRight2' => array('xpath' => "//input[@id='intraocularpressure_reading_3_value']"),
        'removeLeft' => array('xpath' => "//*[@class='readings-right']//*[contains(text(),'Remove')]"),
        'removeRight' => array('xpath' => "//*[@class='readings-left']//*[contains(text(),'Remove')]"),
        'savePhasingEvent' => array('xpath' => "//*[@id='et_save']"),
        'phasingSavedOk' => array('xpath' => "//*[@id='flash-success']"),
        'rightReadingTimeInvalid' => array('xpath' => "//*[@class='alert-box alert with-icon']//*[contains(text(),'Intraocular Pressure Phasing: Right reading (1): Invalid Time')]"),
        'leftReadingTimeInvalid' => array('xpath' => "//*[@class='alert-box alert with-icon']//*[contains(text(),'Intraocular Pressure Phasing: Left reading (1): Invalid Time')]")
    );

    protected function doesPhasingLogoExist()
    {
        return (bool) $this->find('xpath', $this->getElement('phasingLogo')->getXpath());
    }

    public function confirmPhasingLogoExist ()
    {
        if ($this->doesPhasingLogoExist()){
        }
        elseif (print "Logo MISSING!");
    }

    public function rightInstrument ($rightEye)
    {
        $this->getElement('phasingInstrumentRight')->selectOption($rightEye);
    }

    public function rightDilationYes ()
    {
        $this->getElement('phasingDilationRightYes')->click();
    }

    public function rightDilationNo ()
    {
        $this->getElement('phasingDilationRightNo')->click();
    }

    public function rightPressureTime ($time)
    {
        $this->getElement('phasingReadingTimeRight')->setValue($time);
    }

    public function rightPressure ($rightEye)
    {
        $this->getElement('phasingPressureRight')->setValue($rightEye);
    }

    public function rightComments ($comments)
    {
        $this->getElement('phasingCommentsRight')->setValue($comments);
    }

    public function leftInstrument ($leftEye)
    {
        $this->getElement('phasingInstrumentLeft')->selectOption($leftEye);
    }

    public function leftDilationYes ()
    {
        $this->getElement('phasingDilationLeftYes')->click();
    }

    public function leftDilationNo ()
    {
        $this->getElement('phasingDilationLeftNo')->click();
    }

    public function leftPressureTime ($time)
    {
        $this->getElement('phasingReadingTimeLeft')->setValue($time);
    }

    public function leftPressure ($leftEye)
    {
        $this->getElement('phasingPressureLeft')->setValue($leftEye);
    }

    public function leftComments ($comments)
    {
        $this->getElement('phasingCommentsLeft')->setValue($comments);
    }

    public function addLeftReading ()
    {
        $this->getElement('phasingReadingAddLeft')->click();
    }

    public function secondLeftTime ($time)
    {
        $this->getElement('phasingReadingTimeLeft2')->setValue($time);
    }

    public function secondLeftReading ($reading)
    {
        $this->getElement('phasingPressureLeft2')->setValue($reading);
    }

    public function addRightReading ()
    {
        $this->getElement('phasingReadingAddRight')->click();
    }

    public function secondRightTime ($time)
    {
        $this->getElement('phasingReadingTimeRight2')->setValue($time);
    }

    public function secondRightReading ($reading)
    {
        $this->getElement('phasingPressureRight2')->setValue($reading);
    }

    public function removeRightReading ()
    {
        $this->getElement('removeRight')->click();
    }

    public function removeLeftReading ()
    {
        $this->getElement('removeLeft')->click();
    }

    public function savePhasingEvent ()
    {
        $this->getElement('savePhasingEvent')->click();
    }

    protected function hasPhasingSaved ()
    {
        return (bool) $this->find('xpath', $this->getElement('phasingSavedOk')->getXpath());;
    }

    public function savePhasingAndConfirm ()
    {
        $this->getElement('savePhasingEvent')->click();

        if ($this->hasPhasingSaved()) {
            print "Phasing has been saved OK";
        }

        else {
            throw new BehaviorException("WARNING!!!  Phasing has NOT been saved!!  WARNING!!");
        }
    }

    protected function hasPhasingTimeErrorDisplayed ()
    {
        return (bool) $this->find('xpath', $this->getElement('rightReadingTimeInvalid')->getXpath()) &&
        (bool) $this->find('xpath', $this->getElement('leftReadingTimeInvalid')->getXpath());

    }

    public function phasingTimeErrorValidation()
    {
        if ($this->hasPhasingTimeErrorDisplayed()) {
            print "Phasing Reading Invalid time error displayed OK";
        }

        else {
            throw new BehaviorException("WARNING!!!  Phasing Reading Invalid time error NOT displayed WARNING!!!");
        }
    }



}