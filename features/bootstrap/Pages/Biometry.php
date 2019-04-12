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
            'xpath' => "//*[@class='errorlink']//*[contains(text(),'No lens selected')]"
        ),
        'lensTypeRight' => array(
            'xpath' => "//*[@id='Element_OphInBiometry_Selection_lens_id_right']"
        ),
        'lensTypeDefaultRight' => array(
            'xpath' => "//*[@id='Element_OphInBiometry_Selection_lens_id_right']//*[contains(text(),'Please select')]"
        ),
        'lensTypeDefaultLeft' => array(
            'xpath' => "//*[@id='Element_OphInBiometry_Selection_lens_id_left']//*[contains(text(),'Please select')]"
        ),
        'userSummaryFooter' => array(
            'xpath' => "//*[@class='info']//*[contains(text(),'IOLMaster')]"
        ),
        'eventEditTab' => array(
            'xpath' => "//*[@class='inline-list tabs event-actions']//*[contains(text(),'Edit')]"
        ),
        'readonlyFields' => array(
            'xpath' => "//*[@class='row']//*[@class='readonly-box']"
        ),
        'createdByIOLMasterDesc' => array(
            'xpath' => "//*[@class='row']//*[contains(text(),'Created by IOL Master input')]"
        ),
        'biometryReport' => array(
            'xpath' => "//*[@class='highlight booking']"
        ),
        'biometryContinue' => array(
            'xpath' => "//*[@class='event-header']//*[contains(text(),'Continue')]"
        ),
        'eventHeader' => array(
            'xpath' => "//*[@class='event-header']"
        ),
        'eventContent' => array(
            'xpath' => "//*[@class='event-content']"
        ),

    );

    public function saveBiometry()
    {
        $this->getElement('saveBiometry')->click();
    }

    public function noLensErrorConfirm()
    {
        if ($this->find('xpath', $this->getElement('noLensError')->getXpath())) {
            throw new BehaviorException ("WARNING!!! ERROR SHOWN! LENS TYPE IS MANDATORY");
        }
    }

    public function noLensByDefaultConfirm()
    {
        if (!$this->getElement('lensTypeDefaultRight')->isSelected() && $this->getElement('lensTypeDefaultLeft')->isSelected()) {
            throw new BehaviorException ("WARNING!!! LENS TYPE SELECTED BY DEFAULT! TEST FAILED!!");
        }
    }

    public function verifyEventIsAuto()
    {
        $this->waitForElementDisplayNone('userSummaryFooter');
        if ($this->getElement('userSummaryFooter')->isVisible()) {
            $this->getElement('eventEditTab')->click();
            $this->waitForElementDisplayBlock('readonlyFields');
        }
    }

    public function selectAutoBiometryEvent()
    {
        $this->waitForElementDisplayNone('biometryReport');
        $this->getElement('biometryReport')->click();
        $this->getElement('biometryContinue')->click();
    }

    public function selectAutoBiometryEventByDateTime($dateTime)
    {
        $this->waitForElementDisplayNone('biometryReport');
        $this->elements['biometryReportByDateTime'] = array(
            'xpath' => "//*[@class='element-fields']//*[contains(text(),'$dateTime')]"
        );
        $this->getElement('biometryReportByDateTime')->click();
        $this->getElement('biometryContinue')->click();
    }

    public function selectTabOnEventSummaryPage($eventTab)
    {
        $this->waitForElementDisplayNone('eventHeader');
        $this->elements['eventHeaderTab'] = array(
            'xpath' => "//*[@class='inline-list tabs event-actions']//*[contains(text(),'$eventTab')]"
        );
        $this->getElement('eventHeaderTab')->click();
    }

    public function lookDataInBiometryPage($value, $eyeSide, $tabType)
    {
        $this->waitForElementDisplayNone('eventContent');
        if ($tabType == 'Edit') {
            $this->elements['eyeSideData'] = array(
                'xpath' => "//*[@id='$eyeSide-eye-lens']//*[contains(text(),'$value')]"
            );
        } elseif ($tabType == 'View') {
            $this->elements['eyeSideData'] = array(
                'xpath' => "//*[@class='js-element-eye $eyeSide-eye column']//*[contains(text(),'$value')]"
            );
        } else {
            throw new BehaviorException ("WARNING!!! INVALID EYE SELECTION PROVIDED! TEST FAILED!!");
        }

        if (!$this->getElement('eyeSideData')->isVisible()) {
            throw new BehaviorException ("WARNING!!! INVALID VALUE DISPLAYED! TEST FAILED!!");
        }
    }

    public function lookForEventSummaryInfoAlert($infoAlert)
    {
        $this->waitForElementDisplayNone('eventContent');
        $this->elements['eventPageInfoAlert'] = array(
            'xpath' => "//*[@class='row']//*[contains(text(),'$infoAlert')]"
        );
        if (!$this->getElement('eventPageInfoAlert')->isVisible()) {
            throw new BehaviorException ("WARNING!!! INVALID ALERT DISPLAYED! TEST FAILED!!");
        }
    }

    public function checkLensDropDown($lensType, $eyeSide)
    {
        $this->elements['eyeTypeLensDropDown'] = array(
            'xpath' => "//*[@id='Element_OphInBiometry_Selection_lens_id_$eyeSide']"
        );
        if (!$this->noLensDropDown()) {
            $this->getElement('eyeTypeLensDropDown')->selectOption($lensType);
        }
    }

    public function noLensDropDown()
    {
        return $this->find('xpath', $this->getElement('eyeTypeLensDropDown')->getXpath());
    }

    public function checkFormulaDropDown($formula, $eyeSide)
    {
        $this->elements['eyeTypeFormulaDropDown'] = array(
            'xpath' => "//*[@id='Element_OphInBiometry_Selection_formula_id_$eyeSide']"
        );
        if (!$this->noFormulaDropDown()) {
            $this->getElement('eyeTypeFormulaDropDown')->selectOption($formula);
        }
    }

    public function noFormulaDropDown()
    {
        return $this->find('xpath', $this->getElement('eyeTypeFormulaDropDown')->getXpath());
    }

    public function checkMeasurementsNotRecorded($eyeSide, $tabType)
    {
        $this->waitForElementDisplayNone('eventContent');
        if ($tabType == 'View') {
            $this->elements['measurementNotRecorded'] = array(
                'xpath' => "//*[@class='element Element_OphInBiometry_Measurement']//*[@class='js-element-eye $eyeSide-eye column']//*[contains(text(),'Not recorded')]"
            );
        } elseif ($tabType == 'Edit') {
            if ($eyeSide == 'right') {
                $this->elements['measurementNotRecorded'] = array(
                    'xpath' => "//*[@id='right-eye-lens']//*[contains(text(),'Add Right side')]"
                );
            } elseif ($eyeSide == 'left') {
                $this->elements['measurementNotRecorded'] = array(
                    'xpath' => "//*[@id='left-eye-lens']//*[contains(text(),'Add left side')]"
                );
            } else {
                throw new BehaviorException ("WARNING!!! INVALID EYE PROVIDED! TEST FAILED!!");
            }
        } else {
            throw new BehaviorException ("WARNING!!! INVALID TAB VIEW SELECTED! TEST FAILED!!");
        }

        if (!$this->getElement('measurementNotRecorded')->isVisible()) {
            throw new BehaviorException ("WARNING!!! MEASUREMENT IS DISPLAYED ! TEST FAILED!!");
        }

    }

    public function checkLensNotRecorded($eyeSide)
    {
        $this->waitForElementDisplayNone('eventContent');
        $this->elements['lensNotRecorded'] = array(
            'xpath' => "//*[@id='$eyeSide-eye-selection']//*[contains(text(),'Set $eyeSide side lens type')]"
        );

        if (!$this->getElement('lensNotRecorded')->isVisible()) {
            throw new BehaviorException ("WARNING!!! LENS IS DISPLAYED ! TEST FAILED!!");
        }
    }

    public function checkFormulaNotRecorded($eyeSide)
    {
        $this->waitForElementDisplayNone('eventContent');
        $this->elements['formulaNotRecorded'] = array(
            'xpath' => "//*[@data-element-type-class='Element_OphInBiometry_Calculation']//*[contains(text(),'Set $eyeSide side lens type')]"
        );

        if (!$this->getElement('formulaNotRecorded')->isVisible()) {
            throw new BehaviorException ("WARNING!!! FORMULA IS DISPLAYED ! TEST FAILED!!");
        }
    }

    public function cancelEventCreation()
    {
        $wdSession = $this->getSession()->getDriver()->getWebDriverSession();
        $wdSession->accept_alert();
    }

}
