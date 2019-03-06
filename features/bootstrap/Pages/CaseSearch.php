<?php

use Behat\Behat\Exception\BehaviorException;

class CaseSearch extends OpenEyesPage
{
    protected $path = "/OECaseSearch/caseSearch/index";
    protected $elements = array(
        'addParam' => array(
            'xpath' => "//*[@id='js-add-param']"
        ),
        'lastParam' => array(
            'xpath' => "//*[@id='param-list']//*[@class='parameter'][last()]"
        ),
        'searchBtn' => array(
            'xpath' => "//*[@class='js-search-btn']"
        ),
        'searchResults' => array(
            'xpath' => "//*[@id='case-search-results']"
        ),
        'ageOperation' => array(
            'xpath' => "//*[@class='js-age-operation']"
        ),
        'ageMinVal' => array(
            'xpath' => "//*[@class='js-age-min']"
        ),
        'ageMaxVal' => array(
            'xpath' => "//*[@class='js-age-max']"
        ),
        'ageVal' => array(
            'xpath' => "//*[@class='js-age-value']"
        ),
        'diagnosis'=>array(
          'css'=>".diagnosis"
        ),
        'clear'=>array(
            'xpath'=>"//*[@id='clear-search']"
        ),
        'medication'=>array(
            'css'=>'.medication'
        ),
        'allergy'=>array(
            'css'=>'.allergy'
        ),
        'family'=>array(
            'css'=>'.family_history'
        ),
        'patientName'=>array(
            'css'=>'.patient_name'
        ),
        'patientNumber'=>array(
            'css'=>'.patient_number'
        ),
        'previousProcedure'=>array(
            'css'=>'.previous_procedures'
        ),


    );

    /**
     * @param $lowerAge int|null
     * @param $upperAge int|null
     *
     * @throws \Behat\Mink\Exception\ElementNotFoundException
     */
    public function addAgeParam($lowerAge, $upperAge)
    {
        $this->getElement('addParam')->selectOption('Patient Age');
        $this->waitForElementDisplayBlock('Patient Age', 500);

        $ageParam = $this->getElement('lastParam');
        $operation = $ageParam->find('xpath', $this->getElement('ageOperation')->getXpath());
        $ageVal = $ageParam->find('xpath', $this->getElement('ageVal')->getXpath());

        if ($lowerAge && $upperAge) {
            $operation->selectOption('Between');
            $ageParam->find('xpath', $this->getElement('ageMinVal')->getXpath())->setValue($lowerAge);
            $ageParam->find('xpath', $this->getElement('ageMaxVal')->getXpath())->setValue($upperAge);
        } else if ($lowerAge && !$upperAge) {
            $operation->selectOption('Older than');
            $ageVal->setValue($lowerAge);
        } else if (!$lowerAge && $upperAge) {
            $operation->selectOption('Younger than');
            $ageVal->setValue($upperAge);
        }
    }

    /**
     * @param $with boolean
     * @param $diagnosis string
     * @param $site string
     * @param $latest boolean
     */

    public function addDiagnosis($with, $diagnosis, $site, $latest){
        $this->getElement('addParam')->selectOption('Diagnosis');
        $this->waitForElementDisplayBlock('Diagnosis', 500);

        if ($with){
            $this->getElement('diagnosis')->find('xpath',"//select[contains(@id,'operation')]")->selectOption('Diagnosed with');
        }else{
            $this->getElement('diagnosis')->find('xpath',"//select[contains(@id,'operation')]")->selectOption('Not diagnosed with');
        }
        $this->getElement('diagnosis')->find('xpath',"//*[contains(@id,'diagnosis')]")->setValue($diagnosis);
        $this->getElement('diagnosis')->find('xpath',"//select[contains(@id,'firm')]")->selectOption($site);
        if ($latest){
            $this->getElement('diagnosis')->find('xpath',"//input[@value='1']")->check();
        }
    }


    public function addMedication($has, $medication){
        $this->getElement('addParam')->selectOption('Medication');
        $this->waitForElementDisplayBlock('Medication', 500);
        if ($has){
            $this->getElement('medication')->find('xpath',"//select[contains(@id,'MedicationParameter')]")->selectOption('Has taken');
        }else{
            $this->getElement('medication')->find('xpath',"//select[contains(@id,'MedicationParameter')]")->selectOption('Has not taken');
        }
        $this->getElement('medication')->find('xpath',"//input[contains(@id,'medication')]")->setValue($medication);
    }

    /**
     * @param $is
     * @param $allergy
     * @throws \Behat\Mink\Exception\ElementNotFoundException
     */
    public function addAllergy($is, $allergy){
        $this->getElement('addParam')->selectOption('Patient Allergy');
        $this->waitForElementDisplayBlock('Patient Allergy', 500);
        if ($is){
            $this->getElement('allergy')->find('xpath',"//select[contains(@id,'PatientAllergy')]")->selectOption('Is allergic to');
        }else{
            $this->getElement('allergy')->find('xpath',"//select[contains(@id,'PatientAllergy')]")->selectOption('Is not allergic to');
        }
        $this->getElement('allergy')->find('xpath',"//input[contains(@id,'allergy')]")->setValue($allergy);
    }

    /**
     * @param $side string
     * @param $relative string
     * @param $operation string
     * @param $condition string
     * @throws \Behat\Mink\Exception\ElementNotFoundException
     */
    public function addFamilyHistory($side,$relative,$operation,$condition){
        $this->getElement('addParam')->selectOption('Family History');
        $this->waitForElementDisplayBlock('Family History', 500);
        $this->getElement('family')->find('xpath',"//select[contains(@id,'side')]")->selectOption($side);
        $this->getElement('family')->find('xpath',"//select[contains(@id,'relative')]")->selectOption($relative);
        $this->getElement('family')->find('xpath',"//select[contains(@id,'operation')]")->selectOption($operation);
        $this->getElement('family')->find('xpath',"//select[contains(@id,'condition')]")->selectOption($condition);
    }

    /**
     * @param $patient_name string
     */
    public function addPatientName($patient_name){
        $this->getElement('patientName')->find('xpath',"//input")->setValue($patient_name);
    }

    /**
     * @param $patient_number string
     */
    public function addPatientNumber($patient_number){
        $this->getElement('patientNumber')->find('xpath',"//input")->setValue($patient_number);
    }
    /**
     * @param $has
     * @param $procedure
     * @throws \Behat\Mink\Exception\ElementNotFoundException
     */
    public function addPreviousProcedure($has, $previous_procedure){
        $this->getElement('addParam')->selectOption('Previous Procedures');
        $this->waitForElementDisplayBlock('Previous Procedures', 500);
        if ($has){
            $this->getElement('previousProcedure')->find('xpath',"//select")->selectOption('Has had a');
        }else{
            $this->getElement('previousProcedure')->find('xpath',"//select")->selectOption('Has not had a');
        }
        $this->getElement('previousProcedure')->find('xpath',"//input")->setValue($previous_procedure);
    }
    public function clear(){
        $this->getElement('clear')->click();
    }



    public function specificResultExist($nhs){
        $exist = false;
        $next_page=$this->find('css','.oe-i.arrow-right-bold');
        while (!$next_page->hasClass('disabled')){
            foreach ( $this->findAll('xpath',"//*[@class='nhs-number']") as $item) {
                $exist = strpos($item->getText(),$nhs);
                if ($exist){
                    break;
                }
            }
            if ($exist){
                break;
            }
            $next_page->click();
            $next_page=$this->find('css','.oe-i.arrow-right-bold');
        }
        if (!$exist){
            throw new BehaviorException ( "WARNING!!!  The specific result has NOT been found!!  WARNING!!" );
        }

    }



    public function search()
    {
        $this->getElement('searchBtn')->click();
    }

    public function resultsExist()
    {
        if (!$this->has('xpath', $this->elements['searchResults']['xpath'])) {
            throw new \Behat\Mink\Exception\ElementNotFoundException(
                $this->getSession(),
                null,
                'xpath',
                $this->elements['searchResults']['xpath']
            );
        }
    }
}

