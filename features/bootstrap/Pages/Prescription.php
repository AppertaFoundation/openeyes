<?php

use Behat\Behat\Exception\BehaviorException;


class Prescription extends EventPage
{
    public function __construct(\Behat\Mink\Session $session, \SensioLabs\Behat\PageObjectExtension\Context\PageFactoryInterface $pageFactory, array $parameters = array())
    {
        parent::__construct($session, $pageFactory, $parameters);
        $this->elements = array_merge($this->elements, self::getPageElements());
    }
    protected $path = "/site/OphDrPrescription/Default/create?patient_id={parentId}";

    protected static function getPageElements()
    {
        return array(
            'filterBy' => array(
                'xpath' => "//*[@id='drug_type_id']"
            ),
            'noPreservative' => array(
                'xpath' => "//*[@id='preservative_free']"
            ),
            'prescriptionCommonDrug' => array(
                'xpath' => "//*[@id='common_drug_id']"
            ),
            'prescriptionStandardSet' => array(
                'xpath' => "//*[@id='drug_set_id']"
            ),
            'prescriptionDoseItem0' => array(
                //'xpath' => "//*[@id='prescription_item_0_dose']"
                'xpath' => "//*[@class='prescriptionItemDose']//*[@id='prescription_item_0_dose']"
            ),
            'prescriptionRouteItem0' => array(
                'xpath' => "//*[@id='prescription_item_0_route_id']"
            ),
            'prescriptionEyeOptionItem0' => array(
                'xpath' => "//*[@id='prescription_item_0_route_option_id']"
            ),
            'prescriptionEyeOptionItem1' => array(
                'xpath' => "//*[@id='prescription_item_1_route_option_id']"
            ),
            'prescriptionEyeOptionItem2' => array(
                'xpath' => "//*[@id='prescription_item_2_route_option_id']"
            ),
            'prescriptionFrequencyItem0' => array(
                'xpath' => "//*[@id='prescription_item_0_frequency_id']"
            ),
            'prescriptionDurationItem0' => array(
                'xpath' => "//*[@id='prescription_item_0_duration_id']"
            ),
            'prescriptionComments' => array(
                'xpath' => "//textarea[@id='Element_OphDrPrescription_Details_comments']"
            ),
            'savePrescriptionandPrint' => array(
                'xpath' => "//*[@id='et_save_print']"
            ),
            'prescriptionSaveDraft' => array(
                'xpath' => "//*[@id='et_save_draft']"
            ),
            'addTaper' => array(
                'xpath' => "//*[@class='taperItem']"
            ),
            'firstTaperDose' => array(
                'xpath' => "//*[@id='prescription_item_0_taper_0_dose']"
            ),
            'firstTaperFrequency' => array(
                'xpath' => "//*[@id='prescription_item_0_taper_0_frequency_id']"
            ),
            'firstTaperDuration' => array(
                'xpath' => "//*[@id='prescription_item_0_taper_0_duration_id']"
            ),
            'secondTaperDose' => array(
                'xpath' => "//*[@id='prescription_item_0_taper_1_dose']"
            ),
            'secondTaperFrequency' => array(
                'xpath' => "//*[@id='prescription_item_0_taper_1_frequency_id']"
            ),
            'secondTaperDuration' => array(
                'xpath' => "//*[@id='prescription_item_0_taper_1_duration_id']"
            ),
            'removeThirdTaper' => array(
                'xpath' => "//*[@data-taper='2']//*[@class='removeTaper']"
            ),
            'prescriptionValidationWarning' => array(
                'xpath' => "//*[contains(text(),'Items cannot be blank.')]"
            ),
            'standardSetRepeatDrug1' => array(
                'xpath' => "//*[@class='prescription-item prescriptionItem even']//*[contains(text(),'atropine 1% eye drops')]"
            ),
            'standardSetRepeatDrug2' => array(
                'xpath' => "//*[@class='prescription-item prescriptionItem odd']//*[contains(text(),'chlorAMPhenicol 0.5% eye drops')]"
            ),
            'standardSetRepeatDrug3' => array(
                'xpath' => "//*[@class='prescription-item prescriptionItem even']//*[contains(text(),'dexamethasone 0.1% eye drops')]"
            ),
            'repeatPrescription' => array(
                'xpath' => "//*[@id='repeat_prescription']"
            ),
            'previousPrescription' => array(
                'xpath' => 'repeatDrugCheck'
            ),

            'prescriptionExist' => array(
                'xpath' => "//*[@class='events']//*[@class='tooltip quicklook']//*[contains(text(),'Prescription')]"
            ),

            'prescriptionHover' => array(
                'xpath' => "//*[@class='event-type']"
            ),
            'prescriptionHoverText' => array(
                'xpath' => "//*[@class='events-container show']//*[contains(text(),'Prescription')]"
            ),

            'deleteEvent' => array(
                'xpath' => "//*[@class=' delete event-action button button-icon small']"
            ),
            'deleteEventButton' => array(
                'xpath' => "//*[@id='et_deleteevent']"
            ),
            'prescriptionExistWarning' => array(
                'xpath' => "//*[@class='alert-box alert with-icon']//*[contains(text(),'WARNING: A Prescription has already been created for this patient today. ')]"
            ),

            'prescriptionExistWarning2' => array(
                'xpath' => "//*[@class='alert-box alert with-icon']//*[contains(text(),'WARNING: Prescriptions have already been created for this patient today.')]"
            ),
            'prescriptionExistsYesOption' => array(
                'xpath' => "//*[@id='prescription-yes']"
            ),
            'prescriptionExistsNoOption' => array(
                'xpath' => "//*[@id='prescription-no']"
            ),
            'eventCreationPage' => array(
                'xpath' => "//*[@class='selected']//*[contains(text(),'Create')]"
            ),
            'eventSummaryPage' => array(
                'xpath' => "//*[@class='inline-list tabs event-actions']//*[contains(text(),'View')]"
            ),
            //new code from here
            'addPrescriptionBtn' => array(
                'xpath' => "//*[@id='add-prescription-btn']"
            ),
            'searchBar' => array(
                'css' => '.search.cols-full.js-search-autocomplete'
            ),
            'drugList' => array(
                'css' => '.oe-add-select-search.auto-width'
            ),
            'drugSearchList' => array(
                'css' => '.add-options.js-search-results'
            ),
            'dispenseCondition' => array(
                'xpath' => "//*[@id='prescription_item_0_dispense_condition_id']"
            ),
            'dispenseLocation' => array(
                'xpath' => "//*[@id='prescription_item_0_dispense_location_id']"
            ),
        );
    }

    //new code from here
    public function addDrugs($drug)
    {
        $this->getElement('addPrescriptionBtn')->click();
        $this->getElement('searchBar')->setValue($drug);
        $this->getSession()->wait(5000);

        $this->elements['common_drug_val'] = array(
            'css' => 'li[data-value=\'' . $drug . '\']'
        );

        $this->getElement('drugSearchList')->find('xpath', $this->getElement('common_drug_val')->getXpath())->click();

    }

    public function confirmDrugAdded()
    {

        foreach ($this->findAll('css', '.add-icon-btn') as $btn) {
            if ($btn->isVisible()) {
                $btn->click();
            }
        }
    }

    public function selectDispenseCondition($condition)
    {
        $this->getElement('dispenseCondition')->selectOption($condition);
    }

    public function selectDespenseLocation($location)
    {
        $this->getElement('dispenseLocation')->selectOption($location);
    }

    public function filterBy($filter)
    {
        $this->getElement('filterBy')->selectOption($filter);
    }

    public function addTaper()
    {
        $this->getElement('addTaper')->click();
    }

    public function firstTaperDose($taper)
    {
        //$this->getElement ( 'firstTaperDose' )->selectOption ( $taper );

        $this->getElement('firstTaperDose')->setValue($taper);
    }

    public function firstTaperFrequency($frequency)
    {
        $this->getElement('firstTaperFrequency')->selectOption($frequency);
    }

    public function firstTaperDuration($duration)
    {
        $this->getElement('firstTaperDuration')->selectOption($duration);
    }

    public function secondTaperDose($taper)
    {
        $this->getElement('secondTaperDose')->setValue($taper);
    }

    public function secondTaperFrequency($frequency)
    {
        $this->getElement('secondTaperFrequency')->selectOption($frequency);
    }

    public function secondTaperDuration($duration)
    {
        $this->getElement('secondTaperDuration')->selectOption($duration);
    }

    public function removeThirdTaper()
    {
        $element = $this->getElement('removeThirdTaper');
        $this->scrollWindowToElement($element);
        $element->click();
    }

    public function noPreservativeCheckbox()
    {
        $this->getElement('noPreservative')->check();
    }

    public function prescriptionDropdown($drug)
    {
        $this->getElement('prescriptionCommonDrug')->selectOption($drug);
        //$repeatDrug=$drug;
        sleep(3);
    }

    public function standardSet($set)
    {
        $this->getElement('prescriptionStandardSet')->selectOption($set);
        $this->getSession()->wait(1000);
    }

    public function item0DoseDrops($drops)
    {

        $this->getElement('prescriptionDoseItem0')->setValue($drops);
    }

    public function item0Route($route)
    {
        $this->getElement('prescriptionRouteItem0')->selectOption($route);
    }

    public function eyeOptionItem0($eyes)
    {
        sleep(15);
        $this->getElement('prescriptionEyeOptionItem0')->selectOption($eyes);
    }

    public function eyeOptionItem1($eyes)
    {
        $this->getElement('prescriptionEyeOptionItem1')->selectOption($eyes);
    }

    public function eyeOptionItem2($eyes)
    {
        $this->getElement('prescriptionEyeOptionItem2')->selectOption($eyes);
    }

    public function frequencyItem0($frequency)
    {
        $this->getElement('prescriptionFrequencyItem0')->selectOption($frequency);
    }

    public function durationItem1($duration)
    {
        $this->getElement('prescriptionDurationItem0')->selectOption($duration);
        $this->getSession()->wait(1000);
    }

    public function comments($comments)
    {
        $this->getElement('prescriptionComments')->setValue($comments);
    }

    public function repeatPrescription()
    {
        $this->getElement('repeatPrescription')->click();
        $this->getSession()->wait(1000);
    }

    public function savePrescriptionAndConfirm()
    {
        $this->getElement('prescriptionSaveDraft')->click();

        if (!$this->eventSaved()) {
            throw new BehaviorException ("WARNING!!!  Prescription has NOT been saved!!  WARNING!!");
        }
    }

    public function savePrescription()
    {
        $this->getElement('prescriptionSaveDraft')->click();

    }

    protected function doesPrescriptionValidationExist()
    {
        $this->waitForElementDisplayBlock('.alert-box.alert.with-icon ul');
        return ( bool )$this->find('xpath', $this->getElement('prescriptionValidationWarning')->getXpath());
    }

    public function confirmPrescriptionValidationError()
    {
        if (!$this->doesPrescriptionValidationExist()) {
            throw new BehaviorException ("WARNING!!! NO Please fix the following input errors WARNING!!!");
        }
    }

    protected function hasRepeatPrescriptionBeenApplied()
    {
        return ( bool )$this->find('xpath', $this->getElement('standardSetRepeatDrug1')->getXpath()) && ( bool )$this->find('xpath', $this->getElement('standardSetRepeatDrug2')->getXpath()) && ( bool )$this->find('xpath', $this->getElement('standardSetRepeatDrug3')->getXpath());
    }

    public function repeatPrescriptionCheck()
    {
        if (!$this->hasRepeatPrescriptionBeenApplied()) {
            throw new BehaviorException ("WARNING!!!  Repeat Prescription has NOT been applied!!  WARNING!!");
        }
    }

    protected function isPreviousPrescriptionChecked()
    {
        return ( bool )$this->find('xpath', $this->getElement('')->getXpath());
    }

    public function previousPrescriptionCheck()
    {
        if (!$this->isPreviousPrescriptionChecked()) {
            throw new BehaviorException ("WARNING!! PREVIOUS PRESCRIPTION NOT APPLIED!!!");
        }
    }

    public function removePrescriptionEvents()
    {
        sleep(3);
        $this->prescriptionExists();
    }


    protected function prescriptionExists()
    {
        $this->getElement('prescriptionHover')->mouseOver();

        if ($this->getElement('prescriptionHoverText')->isVisible()) {
            $this->deletePrescription();
            sleep(5);
            $this->removePrescriptionEvents();
        }
    }

    protected function deletePrescription()
    {
        $this->getElement('prescriptionHover')->click();

        sleep(3);
        $this->getElement('deleteEvent')->click();
        sleep(2);
        $this->getElement('deleteEventButton')->click();
    }

    public function checkWarningShown()
    {
        sleep(3);
        if (!$this->doesWarningShow() && !$this->doesWarningShow2()) {
            throw new BehaviorException ("NO WARNING SHOWN!!! TEST FAILED!");
        }
    }

    protected function doesWarningShow()
    {
        return ( bool )($this->find('xpath', $this->getElement('prescriptionExistWarning')->getXpath()));
    }

    protected function doesWarningShow2()
    {
        return ( bool )($this->find('xpath', $this->getElement('prescriptionExistWarning2')->getXpath()));
    }

    public function iClickOnYes()
    {
        $this->getElement('prescriptionExistsYesOption')->click();
        sleep(3);
        if (!$this->iAmOnCreateEventPage() || !$this->iAmOnPrescriptionPage()) {
            throw new BehaviorException("User not redirected to Prescription Creation Page! TEST FAILED!!");
        }
    }

    public function iClickOnNo()
    {
        $this->getElement('prescriptionExistsNoOption')->click();
        sleep(3);
        if (!$this->iAmOnLatestEventPage()) {
            throw new BehaviorException("User not redirected to Latest Event Summary Page! TEST FAILED!!");
        }
    }

    protected function iAmOnCreateEventPage()
    {
        return ( bool )$this->find('xpath', $this->getElement('eventCreationPage')->getXpath());
    }

    protected function iAmOnPrescriptionPage()
    {
        return ( bool )$this->find('xpath', $this->getElement('repeatPrescription')->getXpath());
    }

    protected function iAmOnLatestEventPage()
    {
        return ( bool )$this->find('xpath', $this->getElement('eventSummaryPage')->getXpath());
    }

    public function duplicatePrescriptionOk()
    {
        $this->popupOk('duplicatePrescriptionOk');
    }
}