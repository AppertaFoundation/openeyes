<?php

class DefaultController extends BaseEventTypeController
{

    // The side that should be injected by default
    public $side_to_inject = null;

    /**
     * use the split event type javascript and styling.
     *
     * @param CAction $action
     *
     * @return bool
     */
    protected function beforeAction($action)
    {
        Yii::app()->assetManager->registerScriptFile('js/spliteventtype.js', null, null, AssetManager::OUTPUT_SCREEN);

        return parent::beforeAction($action);
    }

    /**
     * set flash message for ptient allergies.
     */
    protected function showAllergyWarning()
    {
        if ($this->patient->no_allergies_date) {
            Yii::app()->user->setFlash('info.prescription_allergy', $this->patient->getAllergiesString());
        } else {
            Yii::app()->user->setFlash('warning.prescription_allergy', $this->patient->getAllergiesString());
        }
    }

    /**
     * Ensures flash message set for allergies and other elements.
     */
    protected function editInit()
    {
        if (empty(OphTrIntravitrealinjection_InjectionUser::model()->getUsers())) {
            Yii::app()->user->setFlash('error.no_injection_user', "No injection user has been added, please contact your administrator to add injection users.");
        }
        $this->showAllergyWarning();
    }

    /**
     * Call edit init and set attribute for alerting which side the latest injection management
     * implies should be injected.
     *
     * @see BaseEventTypeController::createInit()
     */
    protected function initActionCreate()
    {
        parent::initActionCreate();
        $this->editInit();

        // set up the injection side if provided by the injection management in examination
        $exam_api = Yii::app()->moduleAPI->get('OphCiExamination');
        // We only want to show the injection mananagement side highlighting from injection management that has
        // been created on the day of this injection
        $since = new DateTime();
        $since->setTime(0, 0, 0);

        if ($this->episode && $exam_api && $imc = $exam_api->getLatestInjectionManagementComplex($this->patient, $since)) {
            if ($side = $imc->getInjectionSide()) {
                $this->side_to_inject = $side;
            } else {
                $this->side_to_inject = 0;
            }
        }
    }

    /**
     * call edit init.
     *
     * @see BaseEventTypeController::updateInit()
     */
    protected function initActionUpdate()
    {
        parent::initActionUpdate();
        $this->editInit();
    }

    /**
     * Set the default options based on episode and injection status for the patient.
     *
     * (non-PHPdoc)
     *
     * @see parent::setElementOptions($action)
     */
    protected function setElementOptions($action)
    {
        parent::setElementOptions($action);

        if ($action != 'create') {
            return;
        }

        // set any calculated defaults on the elements
        $therapy_api = Yii::app()->moduleAPI->get('OphCoTherapyapplication');
        $injection_api = Yii::app()->moduleAPI->get('OphTrIntravitrealinjection');
        $exam_api = Yii::app()->moduleAPI->get('OphCiExamination');

        $default_eye = Eye::BOTH;
        $default_left_drug = null;
        $default_right_drug = null;

        $since = new DateTime();
        $since->setTime(0, 0, 0);

        if ($this->episode && $exam_api && $imc = $exam_api->getLatestInjectionManagementComplex($this->patient, $since)) {
            if ($side = $imc->getInjectionSide()) {
                $default_eye = $side;
                $default_left_drug = $imc->left_treatment;
                $default_right_drug = $imc->right_treatment;
            }
        }
        // get the side of the latest therapy application
        elseif ($this->episode && $therapy_api && $side = $therapy_api->getLatestApplicationSide($this->patient, $this->episode)) {
            $default_eye = $side;
        }

        // if we haven't got the default drug from the imc, try therapy application
        if ($therapy_api) {
            if ($default_eye != Eye::RIGHT && !$default_left_drug) {
                $default_left_drug = $therapy_api->getLatestApplicationDrug($this->patient, $this->episode, 'left');
            }
            if ($default_eye != Eye::LEFT && !$default_right_drug) {
                $default_right_drug = $therapy_api->getLatestApplicationDrug($this->patient, $this->episode, 'right');
            }
        }

        // set up the values for the potentially allergy restricted drugs in treatment
        $pre_skin_default = OphTrIntravitrealinjection_SkinDrug::getDefault();
        $pre_anti_default = OphTrIntravitrealinjection_AntiSepticDrug::getDefault();

        if ($pre_skin_default) {
            foreach ($pre_skin_default->allergies as $allergy) {
                if ($this->patient->hasAllergy($allergy)) {
                    $pre_skin_default = null;
                }
            }
        }

        if ($pre_anti_default) {
            foreach ($pre_anti_default->allergies as $allergy) {
                if ($this->patient->hasAllergy($allergy)) {
                    $pre_anti_default = null;
                }
            }
        }

        foreach ($this->open_elements as $element) {
            if ($element->hasAttribute('eye_id')) {
                $element->eye_id = $default_eye;
            }

            if (get_class($element) === 'Element_OphTrIntravitrealinjection_Treatment') {
                if ($therapy_api) {
                    // get the latest drug that has been applied for and set it as default (for the appropriate eye)
                    if ($default_left_drug) {
                        $element->left_drug_id = $default_left_drug->id;
                        $previous = $injection_api->previousInjections($this->patient, $this->episode, 'left', $default_left_drug);
                        $element->left_number = count($previous) + 1;
                    }
                    if ($default_right_drug) {
                        $element->right_drug_id = $default_right_drug->id;
                        $previous = $injection_api->previousInjections($this->patient, $this->episode, 'right', $default_right_drug);
                        $element->right_number = count($previous) + 1;
                    }
                }
                $element->left_pre_skin_drug_id = $pre_skin_default ? $pre_skin_default->id : null;
                $element->right_pre_skin_drug_id = $pre_skin_default ? $pre_skin_default->id : null;
                $element->left_pre_antisept_drug_id = $pre_anti_default ? $pre_anti_default->id : null;
                $element->right_pre_antisept_drug_id = $pre_anti_default ? $pre_anti_default->id : null;
                $element->left_injection_given_by_id = Yii::app()->user->id;
                $element->right_injection_given_by_id = Yii::app()->user->id;
            }
            if (get_class($element) === 'Element_OphTrIntravitrealinjection_Site') {
                $element->site_id = $this->selectedSiteId;
            }
        }
    }

    /**
     * (non-PHPdoc).
     *
     * @see BaseEventTypeController::setPOSTManyToMany()
     */
    protected function setElementComplexAttributesFromData($element, $data, $index = null)
    {
        foreach (array('left', 'right') as $side) {
            if (get_class($element) == 'Element_OphTrIntravitrealinjection_Complications') {
                if (isset($data['Element_OphTrIntravitrealinjection_Complications'][$side.'_complications']) && is_array($data['Element_OphTrIntravitrealinjection_Complications'][$side.'_complications'])) {
                    $complications = array();

                    foreach ($data['Element_OphTrIntravitrealinjection_Complications'][$side.'_complications'] as $comp_id) {
                        if ($comp = OphTrIntravitrealinjection_Complication::model()->findByPk($comp_id)) {
                            $complications[] = $comp;
                        }
                    }
                    $element->{$side.'_complications'} = $complications;
                }
            } elseif (get_class($element) == 'Element_OphTrIntravitrealinjection_Treatment') {
                foreach (array('pre', 'post') as $stage) {
                    if (isset($data['Element_OphTrIntravitrealinjection_Treatment'][$side.'_'.$stage.'_ioploweringdrugs']) && is_array($data['Element_OphTrIntravitrealinjection_Treatment'][$side.'_'.$stage.'_ioploweringdrugs']) ) {
                        $ioplowerings = array();

                        foreach ($data['Element_OphTrIntravitrealinjection_Treatment'][$side.'_'.$stage.'_ioploweringdrugs'] as $ioplowering_id) {
                            if ($ioplowering = OphTrIntravitrealinjection_IOPLoweringDrug::model()->findByPk($ioplowering_id)) {
                                $ioplowerings[] = $ioplowering;
                            }
                        }
                        $element->{$side.'_'.$stage.'_ioploweringdrugs'} = $ioplowerings;
                    }
                }
            }
        }
    }

    /**
     * similar to setPOSTManyToMany, but will actually call methods on the elements that will create database entries
     * should be called on create and update.
     *
     * @param $data
     *
     * @internal param \BaseEventTypeElement[] $elements
     */
    protected function saveEventComplexAttributesFromData($data)
    {
        foreach ($this->open_elements as $el) {
            foreach (array('left' => SplitEventTypeElement::LEFT, 'right' => SplitEventTypeElement::RIGHT) as $side => $sconst) {
                if (get_class($el) == 'Element_OphTrIntravitrealinjection_Complications') {
                    $comps = array();
                    if (isset($data['Element_OphTrIntravitrealinjection_Complications'][$side.'_complications']) &&
                        ($el->eye_id == $sconst || $el->eye_id == Eye::BOTH)
                    ) {
                        // only set if relevant to element side, otherwise force reset of data
                        $comps = $data['Element_OphTrIntravitrealinjection_Complications'][$side.'_complications'];
                    }
                    $el->updateComplications($sconst, $comps);
                } elseif (get_class($el) == 'Element_OphTrIntravitrealinjection_Treatment') {
                    $drugs = array();
                    if ($el->{$side.'_pre_ioplowering_required'} &&
                        isset($data['Element_OphTrIntravitrealinjection_Treatment'][$side.'_pre_ioploweringdrugs']) &&
                        ($el->eye_id == $sconst || $el->eye_id == Eye::BOTH)
                    ) {
                        // only set if relevant to element side, otherwise force reset of data
                        $drugs = $data['Element_OphTrIntravitrealinjection_Treatment'][$side.'_pre_ioploweringdrugs'];
                    }
                    $el->updateIOPLoweringDrugs($sconst, true, $drugs);
                    // reset for post
                    $drugs = array();
                    if ($el->{$side.'_post_ioplowering_required'} &&
                        isset($data['Element_OphTrIntravitrealinjection_Treatment'][$side.'_post_ioploweringdrugs']) &&
                        ($el->eye_id == $sconst || $el->eye_id == Eye::BOTH)
                    ) {
                        // only set if relevant to element side, otherwise force reset of data
                        $drugs = $data['Element_OphTrIntravitrealinjection_Treatment'][$side.'_post_ioploweringdrugs'];
                    }
                    $el->updateIOPLoweringDrugs($sconst, false, $drugs);
                }
            }
        }
    }
}
