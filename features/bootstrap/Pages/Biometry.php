<?php
use Behat\Behat\Exception\BehaviorException;
class Biometry extends OpenEyesPage
{
    protected $path = "OphInBiometry/Default/create?patient_id={patientId}";
    protected $elements = array(
        'saveBiometry' => array(
            'xpath' => "//*[@id='et_save']"
        ),
        'noLensError' => array(
            'xpath'=> "//*[@class='errorlink']//*[contains(text(),'No lens selected')]"
        ),
        'lensTypeRight'=> array(
            'xpath'=> "//*[@id='Element_OphInBiometry_Selection_lens_id_right']"
        ),
        'lensTypeDefaultRight'=> array(
            'xpath'=> "//*[@id='Element_OphInBiometry_Selection_lens_id_right']//*[contains(text(),'Please select')]"
        ),
        'lensTypeDefaultLeft'=> array(
            'xpath'=> "//*[@id='Element_OphInBiometry_Selection_lens_id_left']//*[contains(text(),'Please select')]"
        ),
        'userSummaryFooter'=> array(
            'xpath'=> "//*[@class='info']//*[contains(text(),'IOLMaster')]"
        ),
        'eventEditTab'=> array(
            'xpath'=> "//*[@class='inline-list tabs event-actions']//*[contains(text(),'Edit')]"
        ),
        'readonlyFields'=> array(
            'xpath'=> "//*[@class='row field-row']//*[@class='readonly-box']"
        ),
        'createdByIOLMasterDesc'=> array(
            'xpath'=> "//*[@class='row field-row']//*[contains(text(),'Created by IOL Master input')]"
        )
    );
    public function saveBiometry()
    {
        $this->getElement('saveBiometry')->click();
    }

    public function noLensErrorConfirm(){
        if($this->find ( 'xpath', $this->getElement ( 'noLensError' )->getXpath () ))
        {
            throw new BehaviorException ( "WARNING!!! ERROR SHOWN! LENS TYPE IS MANDATORY" );
        }
        else
        {
        print
        "*****
        ****
        TEST PASSED!! Lens type is not mandatory
        ******
        ******";
        }
    }

    public function noLensByDefaultConfirm(){
        //$this->getElement('lensTypeRight')->click();
        if($this->getElement('lensTypeDefaultRight')->isSelected()&&$this->getElement('lensTypeDefaultLeft')->isSelected()){
            print "No Lens Type is selected by default!! TEST PASSED!";
        }

        else{
            print "WARNING!!! LENS TYPE SELECTED BY DEFAULT! TEST FAILED!!";
            throw new BehaviorException ( "WARNING!!! LENS TYPE SELECTED BY DEFAULT! TEST FAILED!!");
        }
    }

    public function verifyEventIsAuto(){
       $this->waitForElementDisplayNone('userSummaryFooter');
        if($this->getElement('userSummaryFooter')->isVisible()){
            $this->getElement('eventEditTab')->click();
            $this->waitForElementDisplayBlock('readonlyFields');
            if($this->getElement('readonlyFields')->isVisible()||$this->getElement('createdByIOLMasterDesc')->isVisible()){
                print "Event Created from IOL Master";
            }
        }
        else{
            print "Event not created from IOL Master!!";
        }
    }

}