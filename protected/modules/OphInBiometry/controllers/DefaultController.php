<?php

class DefaultController extends BaseEventTypeController
{
    public $flash_message = '<b>Data source</b>: Manual entry <i>This data should not be relied upon for clinical purposes</i>';
    public $is_auto = 0;
    public $iolRefValues = array();
    public $selectionValues = array();
    public $calculationValues = array();
    public $quality = 0;
    public $checkalqual = array();
    const BADCOMSNRLIMIT = 10;
    const BORDERCOMSNRLIMIT = 15;
    const ALDIFFONBOTHEYES = 0.3;
    const SHORTALLIMIT = 22;
    const LONGALLIMIT = 25;

    protected $render_optional_elements = false;

    /**
     * @param Event                         $unlinkedEvent
     * @param OphInBiometry_Imported_Events $importedEvent
     */
    private function updateImportedEvent(Event $unlinkedEvent, OphInBiometry_Imported_Events $importedEvent)
    {
        $transaction = Yii::app()->db->beginTransaction();
        try {
            $unlinkedEvent->setScenario('allowFutureEvent');
            $unlinkedEvent->episode_id = $this->episode->id;
            $unlinkedEvent->created_user_id = Yii::app()->user->getId();

            if ($unlinkedEvent->save()) {
                $importedEvent->is_linked = 1;
                $importedEvent->save();
            }

            $transaction->commit();
        } catch (Exception $e) {
            $transaction->rollBack();
            Yii::app()->user->setFlash('warning.event_error', "{$e->getMessage()}");
            Yii::log($e->getMessage(), CLogger::LEVEL_ERROR);
        }
    }

    /**
     * Handle the selection of a booking for creating an op note.
     *
     * (non-phpdoc)
     *
     * @see parent::actionCreate()
     */
    public function actionCreate()
    {
        $errors = array();
        // if we are after the submit we need to check if any event is selected
        if (preg_match('/^biometry([0-9]+)$/', Yii::app()->request->getPost('SelectBiometry'), $matches)) {
            $this->updateHotlistItem($this->patient);
            $importedEvent = OphInBiometry_Imported_Events::model()->findByPk($matches[1]);
            $this->updateImportedEvent(Event::model()->findByPk($importedEvent->event_id), $importedEvent);
            $this->redirect(array('/OphInBiometry/default/view/' . $importedEvent->event_id . '?autosaved=1'));
        }

        $criteria = new CDbCriteria();

        // we are looking for the unlinked imported events in the database
        $criteria->addCondition('patient_id = :patient_id');
        $criteria->addCondition('is_linked = 0');
        $criteria->addCondition('event.deleted = 0');
        $criteria->params = array(':patient_id' => $this->patient->id);
        $unlinkedEvents = OphInBiometry_Imported_Events::model()->with(array('patient', 'event'))->findAll($criteria);

        // if we have 0 unlinked event we follow the manual process
        if (count($unlinkedEvents) === 0 || Yii::app()->request->getQuery('force_manual') == '1') {
            Yii::app()->user->setFlash('issue.formula', $this->flash_message);
            parent::actionCreate();
        } elseif (count($unlinkedEvents) === 1) {
            // if we have only 1 unlinked event we just simply link that event to the episode
            $this->updateImportedEvent(Event::model()->findByPk($unlinkedEvents[0]->event_id), $unlinkedEvents[0]);
            $this->redirect(array('/OphInBiometry/default/update/' . $unlinkedEvents[0]->event_id));
        } else {
            // if we have more than 1 event we render the selection screen
            $this->title = 'Please Select a Biometry Report';
            $this->event_tabs = array(
                array(
                    'label' => 'Select a report to add',
                    'active' => true,
                ),
            );
            $cancel_url = (new CoreAPI())->generatePatientLandingPageLink($this->patient);
            $this->event_actions[] =
                EventAction::link(
                    'Cancel',
                    Yii::app()->createUrl($cancel_url),
                    null,
                    array('class' => 'button small warning')
                );


            $this->render('select_imported_event', array(
                'errors' => $errors,
                'imported_events' => $unlinkedEvents,
            ));
        }
    }

    /**
     * @param $id
     */
    private function setFlashMessage($id)
    {
        if ($this->isAutoBiometryEvent($id)) {
            $this->is_auto = 1;
            $event_data = $this->getAutoBiometryEventData($id);
            $isAlMod = $this->isAlModified();
            $isKMod = $this->isKModified();
            $issue_flash_message = '';
            $info_flash_message = '';
            $success_flash_message = '';

            if (($isAlMod['left']) && ($isAlMod['right'])) {
                $issue_flash_message .= '<li>AL for both eyes was entered manually. Possibly Ultrasound? Use with caution.</li>';
            } else {
                if ($isAlMod['left']) {
                    $issue_flash_message .= '<li>AL for left eye was entered manually. Possibly Ultrasound? Use with caution.</li>';
                } elseif ($isAlMod['right']) {
                    $issue_flash_message .= '<li>AL for right eye was entered manually. Possibly Ultrasound? Use with caution.</li>';
                }
            }

            if (($isKMod['left']) && ($isKMod['right'])) {
                $issue_flash_message .= '<li>K value for both eyes was entered manually. Use with caution.</li>';
            } else {
                if ($isKMod['left']) {
                    $issue_flash_message .= '<li>K value for left eye was entered manually. Use with caution.</li>';
                } elseif ($isKMod['right']) {
                    $issue_flash_message .= '<li>K value for right eye was entered manually. Use with caution.</li>';
                }
            }

            foreach ($event_data as $detail) {
                $info_flash_message .= '<b>Data Source</b>: ' . $detail['device_name'] . ' (<i>' . $detail['device_manufacturer'] . ' ' . $detail['device_model'] . '</i>)';

                if ($detail['is_merged']) {
                    $success_flash_message .= 'New data has been added to this event.<br>';
                    $this->mergedView();
                }

                if (Yii::app()->request->getParam('autosaved')) {
                    $success_flash_message .= 'The event has been added to this episode.<br>';
                }
            }
            $quality = $this->isBadQuality();

            if (!empty($quality) && (!empty($quality['reason']))) {
                $issue_flash_message .= '<li><b>The quality of this data is bad and not recommended for clinical use </b>: (' . $quality['reason'] . ')</li>';
            } else {
                $quality = $this->isBordelineQuality();
                if (!empty($quality) && (!empty($quality['reason']))) {
                    $issue_flash_message .= '<li><b>The quality of this data is borderline </b>: (' . $quality['reason'] . ')</li>';
                }
            }

            $checkalqual = $this->checkALQuality();
            if (!empty($checkalqual) && ($checkalqual['code'])) {
                foreach ($checkalqual['reason'] as $v) {
                    if (!empty($v)) {
                        $issue_flash_message .= '<li>' . $v . '</li>';
                    }
                }
            }

            $checkaldiff = $this->checkALDiff();
            if (!empty($checkaldiff) && (!empty($checkaldiff['reason']))) {
                $issue_flash_message .= '<li>' . $checkaldiff['reason'] . '</li>';
            }

            if (count($this->iolRefValues) !== 0) {
                foreach ($this->iolRefValues as $measurementData) {
                    if (!empty($measurementData->{'iol_ref_values_left'})) {
                        $lens_left[] = $measurementData->{'lens_id'};
                    }
                    if (!empty($measurementData->{'iol_ref_values_right'})) {
                        $lens_right[] = $measurementData->{'lens_id'};
                    }
                }
            }

            if (empty($lens_left) && empty($lens_right) && $this->getLensCalc(1) && $this->getLensCalc(2)) {
                $issue_flash_message .= '<li>No lens options are available for either eye. Please recalculate lenses on the IOL Master device and resend</li>';
            } else {
                if (empty($lens_left) && $this->getLensCalc(1)) {
                    $issue_flash_message .= '<li>No lens options are available for the left eye. Please recalculate lenses on the IOL Master device and resend</li>';
                } elseif (empty($lens_right) && $this->getLensCalc(2)) {
                    $issue_flash_message .= '<li>No lens options are available for the right eye. Please recalculate lenses on the IOL Master device and resend</li>';
                }
            }

            if ($issue_flash_message != '') {
                Yii::app()->user->setFlash('issue.data', '<ul>' . $issue_flash_message . '</ul>');
            }
            if ($info_flash_message != '') {
                Yii::app()->user->setFlash('info.data', '<ul>' . $info_flash_message . '</ul>');
            }
            if ($success_flash_message != '') {
                Yii::app()->user->setFlash('success.data', '<ul>' . $success_flash_message . '</ul>');
            }
        } else {
            Yii::app()->user->setFlash('info.formula', $this->flash_message);
        }
    }

    /**
     * @param $id
     * @param $eye
     *
     * @return int
     */
    public function getLensCalc($eye)
    {
        $available = 0;
        $measurementValues = $this->getMeasurementData();
        $measurementData = $measurementValues[0];
        if ($eye == 1) {
            if (($measurementData->{'axial_length_left'}) > 0 || ($measurementData->{'k1_left'}) > 0 || ($measurementData->{'k2_left'}) > 0) {
                $available = 1;
            }
        } elseif ($eye == 2) {
            if (($measurementData->{'axial_length_right'}) > 0 || ($measurementData->{'k1_right'}) > 0 || ($measurementData->{'k2_right'}) > 0) {
                $available = 1;
            }
        }

        return $available;
    }

    /**
     * @param $id
     */
    public function actionUpdate($id)
    {
        if ($this->event != null && $this->event->id > 0) {
            $this->iolRefValues = Element_OphInBiometry_IolRefValues::Model()->findAllByAttributes(
                array(
                    'event_id' => $this->event->id,
                )
            );
            $this->selectionValues = Element_OphInBiometry_Selection::Model()->findAllByAttributes(
                array(
                    'event_id' => $this->event->id,
                )
            );
            $this->calculationValues = Element_OphInBiometry_Calculation::Model()->findAllByAttributes(
                array(
                    'event_id' => $this->event->id,
                )
            );
        } else {
            $this->iolRefValues = array();
        }
        $this->setFlashMessage($id);
        parent::actionUpdate($id);
    }

    /**
     * @param $id
     */
    public function actionView($id)
    {
        if ($this->event != null && $this->event->id > 0) {
            $this->iolRefValues = Element_OphInBiometry_IolRefValues::Model()->findAllByAttributes(
                array(
                    'event_id' => $this->event->id,
                )
            );
            $this->selectionValues = Element_OphInBiometry_Selection::Model()->findAllByAttributes(
                array(
                    'event_id' => $this->event->id,
                )
            );
        }
        $this->setFlashMessage($id);

        parent::actionView($id);
    }

    /**
     * @param int $id
     */
    public function actionPrint($id)
    {
        parent::actionPrint($id);
    }

    /**
     * Process the js vars
     */
    public function processJsVars()
    {
        $lens_types = array();

        $criteria = new CDbCriteria();
        $criteria->with = 'institutions';
        $criteria->condition = 'institutions_institutions.institution_id = :institution_id';
        $criteria->params[':institution_id'] = Institution::model()->getCurrent()->id;

        foreach (OphInBiometry_LensType_Lens::model()->findAll($criteria) as $lens) {
            $lens_types[$lens->name] = array(
                'model' => $lens->name,
                'description' => $lens->description,
                'acon' => (float)$lens->acon,
            );
        }

        $this->jsVars['OphInBioemtry_lens_types'] = $lens_types;

        parent::processJsVars();
    }

    /**
     * use the split event type javascript and styling.
     *
     * @param CAction $action
     *
     * @return bool
     * @throws CHttpException
     */
    protected function beforeAction($action)
    {
        Yii::app()->assetManager->registerScriptFile('js/spliteventtype.js', null, null, AssetManager::OUTPUT_SCREEN);

        return parent::beforeAction($action);
    }

    public function afterAction($action)
    {
        return parent::afterAction($action);
    }

    /**
     * Check Automatic Biometrc Event.
     *
     * @param $id
     *
     * @return bool
     */
    protected function isAutoBiometryEvent($id)
    {
        return OphInBiometry_Imported_Events::model()->countByAttributes(['event_id' => $id]) > 0;
    }

    /**
     * Get Auto Event data.
     *
     * @param $id
     *
     * @return mixed
     */
    protected function getAutoBiometryEventData($id)
    {
        return OphInBiometry_Imported_Events::model()->findAllByAttributes(array('event_id' => $id));
    }

    /**
     * @param $id
     */
    protected function getMeasurementData()
    {
        return Element_OphInBiometry_Measurement::Model()->findAllByAttributes(
            array(
                'event_id' => $this->event->id,
            )
        );
    }

    /**
     * @param $id
     */
    protected function mergedView()
    {
        Yii::app()->db->createCommand()
            ->update(
                'ophinbiometry_imported_events',
                array('is_merged' => 0),
                'event_id=:id',
                array(':id' => $this->event->id)
            );
    }

    /**
     *
     * @return array
     */
    private function isBadQuality()
    {
        if ($this->isAutoBiometryEvent($this->event->id) && $this->getAutoBiometryEventData($this->event->id)[0]->is700()) {
            return array();
        }

        $reason = array();
        $measurementValues = $this->getMeasurementData();
        $measurementData = $measurementValues[0];

        if (($measurementData->{'snr_left'}) < self::BADCOMSNRLIMIT || ($measurementData->{'snr_right'} < self::BADCOMSNRLIMIT)) {
            if ($measurementData->{'eye_id'} == 3) {
                $reason['code'] = 1;
                if (
                    ($measurementData->{'snr_right'} < self::BADCOMSNRLIMIT) && ($measurementData->{'snr_left'} < self::BADCOMSNRLIMIT)
                    && ($measurementData->{'snr_right'}) > 0
                    && ($measurementData->{'snr_left'} > 0 && !$measurementData->{'al_modified_left'} && !$measurementData->{'al_modified_right'})
                ) {
                    $reason['reason'] = 'the composite SNR for both eyes is less than 10';
                } else {
                    if ($measurementData->{'snr_right'} < self::BADCOMSNRLIMIT && $measurementData->{'snr_right'} > 0 && !$measurementData->{'al_modified_right'}) {
                        $reason['reason'] = 'the composite SNR for the right eye is less than 10';
                    } elseif ($measurementData->{'snr_left'} < self::BADCOMSNRLIMIT && $measurementData->{'snr_left'} > 0 && !$measurementData->{'al_modified_left'}) {
                        $reason['reason'] = 'the composite SNR for the left eye is less than 10';
                    }
                }
            } else {
                if (
                    $measurementData->{'eye_id'} == 2
                    && ($measurementData->{'snr_right'} < self::BADCOMSNRLIMIT)
                    && $measurementData->{'snr_right'} > 0 && !$measurementData->{'al_modified_right'}
                    ) {
                    $reason['code'] = 1;
                    $reason['reason'] = 'the composite SNR for the right eye is less than 10';
                } elseif (
                    $measurementData->{'eye_id'} == 1 && ($measurementData->{'snr_left'} < self::BADCOMSNRLIMIT)
                    && $measurementData->{'snr_left'} > 0 && !$measurementData->{'al_modified_left'}
                    ) {
                    $reason['code'] = 1;
                    $reason['reason'] = 'the composite SNR for the left eye is less than 10';
                }
            }
        }

        return $reason;
    }

    /**
     *
     * @return array
     */
    private function isBordelineQuality()
    {
        if ($this->isAutoBiometryEvent($this->event->id) && $this->getAutoBiometryEventData($this->event->id)[0]->is700()) {
            return array();
        }

        $reason = array();
        $measurementValues = $this->getMeasurementData();
        $measurementData = $measurementValues[0];

        if (($measurementData->{'snr_left'}) < self::BORDERCOMSNRLIMIT || ($measurementData->{'snr_right'} < self::BORDERCOMSNRLIMIT)) {
            if ($measurementData->{'eye_id'} == 3) {
                if (
                    (($measurementData->{'snr_right'} > 0) && $measurementData->{'snr_right'} < self::BORDERCOMSNRLIMIT)
                    && ($measurementData->{'snr_left'} > 0) && ($measurementData->{'snr_left'}) < self::BORDERCOMSNRLIMIT
                    && !$measurementData->{'al_modified_left'} && !$measurementData->{'al_modified_right'}
                ) {
                    $reason['reason'] = 'the composite SNR for both eyes is less than 15';
                    $reason['code'] = 1;
                } else {
                    if ($measurementData->{'snr_right'} > 0 && $measurementData->{'snr_right'} < self::BORDERCOMSNRLIMIT && !$measurementData->{'al_modified_right'}) {
                        $reason['reason'] = 'the composite SNR for the right eye is less than 15';
                        $reason['code'] = 1;
                    } elseif ($measurementData->{'snr_left'} > 0 && ($measurementData->{'snr_left'}) < self::BORDERCOMSNRLIMIT && !$measurementData->{'al_modified_left'}) {
                        $reason['reason'] = 'the composite SNR for the left eye is less than 15';
                        $reason['code'] = 1;
                    }
                }
            } else {
                if (
                    ($measurementData->{'eye_id'} == 2) && ($measurementData->{'snr_right'} > 0)
                    && ($measurementData->{'snr_right'} < self::BORDERCOMSNRLIMIT && !$measurementData->{'al_modified_right'})
                ) {
                    $reason['code'] = 1;
                    $reason['reason'] = 'the composite SNR for the right eye is less than 15';
                } elseif (
                    $measurementData->{'eye_id'} == 1 && ($measurementData->{'snr_left'} > 0)
                    && ($measurementData->{'snr_left'}) < self::BORDERCOMSNRLIMIT && !$measurementData->{'al_modified_left'}
                ) {
                    $reason['code'] = 1;
                    $reason['reason'] = 'the composite SNR for the left eye is less than 15';
                }
            }
        }

        return $reason;
    }

    /**
     *
     * @return array
     */
    private function checkALQuality()
    {
        $reason = array();
        $reason['code'] = 0;
        $measurementValues = $this->getMeasurementData();
        $measurementData = $measurementValues[0];

        if (($measurementData->{'axial_length_left'}) < self::SHORTALLIMIT || ($measurementData->{'axial_length_right'} < self::SHORTALLIMIT)) {
            if ($measurementData->{'eye_id'} == 3) {
                if (($measurementData->{'axial_length_right'} < self::SHORTALLIMIT) && ($measurementData->{'axial_length_right'} > 0)) {
                    $reason['reason'][0] = 'Right eye is short';
                    $reason['code'] = 1;
                }

                if (($measurementData->{'axial_length_left'} < self::SHORTALLIMIT) && ($measurementData->{'axial_length_left'}) > 0) {
                    $reason['reason'][1] = 'Left eye is short';
                    $reason['code'] = 1;
                }
            } else {
                if (($measurementData->{'eye_id'} == 2) && ($measurementData->{'axial_length_right'} < self::SHORTALLIMIT) && $measurementData->{'axial_length_right'} > 0) {
                    $reason['code'] = 1;
                    $reason['reason'][2] = 'Right eye is short';
                } elseif ($measurementData->{'eye_id'} == 1 && $measurementData->{'axial_length_left'} < self::SHORTALLIMIT) {
                    $reason['code'] = 1;
                    $reason['reason'][3] = 'Left eye is short';
                }
            }
        }

        if (($measurementData->{'axial_length_left'}) > self::LONGALLIMIT || ($measurementData->{'axial_length_right'} > self::LONGALLIMIT)) {
            if ($measurementData->{'eye_id'} == 3) {
                if ($measurementData->{'axial_length_right'} > self::LONGALLIMIT) {
                    $reason['reason'][4] = 'Right eye is long';
                    $reason['code'] = 1;
                }
                if ($measurementData->{'axial_length_left'} > self::LONGALLIMIT) {
                    $reason['reason'][5] = 'Left eye is long';
                    $reason['code'] = 1;
                }
            } else {
                if (($measurementData->{'eye_id'} == 2) && ($measurementData->{'axial_length_right'} > self::LONGALLIMIT)) {
                    $reason['code'] = 1;
                    $reason['reason'][6] = 'Right eye is long';
                } elseif ($measurementData->{'eye_id'} == 1 && $measurementData->{'axial_length_left'} > self::LONGALLIMIT) {
                    $reason['code'] = 1;
                    $reason['reason'][7] = 'Left eye is long';
                }
            }
        }

        //echo '<pre>'; print_r($reason); die;
        return $reason;
    }

    /**
     * @return array
     */
    private function checkALDiff()
    {
        $reason = array();
        $reason['code'] = 0;
        $measurementValues = $this->getMeasurementData();
        $measurementData = $measurementValues[0];

        if ((($measurementData->{'axial_length_left'}) > ($measurementData->{'axial_length_right'}))) {
            if ($measurementData->{'eye_id'} == 3) {
                if ((($measurementData->{'axial_length_left'}) - ($measurementData->{'axial_length_right'})) >= self::ALDIFFONBOTHEYES) {
                    if ($measurementData->{'axial_length_left'} > 0 && $measurementData->{'axial_length_right'} > 0) {
                        $reason['code'] = 1;
                        $reason['reason'] = 'The difference between Axial Length for the left eye and right eye is 0.3mm or greater.';
                    }
                }
            }
        } elseif ((($measurementData->{'axial_length_left'}) < ($measurementData->{'axial_length_right'}))) {
            if ($measurementData->{'eye_id'} == 3) {
                if ((($measurementData->{'axial_length_right'}) - ($measurementData->{'axial_length_left'})) >= self::ALDIFFONBOTHEYES) {
                    if ($measurementData->{'axial_length_left'} > 0 && $measurementData->{'axial_length_right'} > 0) {
                        $reason['code'] = 1;
                        $reason['reason'] = 'The difference between Axial Length for the left eye and right eye is 0.3mm or greater.';
                    }
                }
            }
        }

        return $reason;
    }

    /**
     * @param $search
     * @param $arr
     *
     * @return null
     */
    public function getClosest($search, $arr)
    {
        $closest = null;
        foreach ($arr as $item) {
            if ($closest === null || abs($search - $closest) > abs($item - $search)) {
                $closest = $item;
            }
        }

        return $closest;
    }

    /**
     * @param $data
     *
     * @return mixed
     */
    public function orderIOLData($data)
    {
        // A simple bubble order (should work with just array_size/2)
        for ($j = 0; $j < count($data['IOL']); ++$j) {
            for ($i = count($data['IOL']) - 1; $i > 0; --$i) {
                if ($data['IOL'][$i] > $data['IOL'][$i - 1]) {
                    $temp = $data['IOL'][$i];
                    $data['IOL'][$i] = $data['IOL'][$i - 1];
                    $data['IOL'][$i - 1] = $temp;
                    $temp = $data['REF'][$i];
                    $data['REF'][$i] = $data['REF'][$i - 1];
                    $data['REF'][$i - 1] = $temp;
                }
            }
        }

        return $data;
    }

    /**
     * @param $id
     *
     * @return mixed
     */
    private function isAlModified()
    {
        $measurementValues = $this->getMeasurementData();
        $measurementData = $measurementValues[0];

        $data['left'] = $measurementData->{'al_modified_left'} && (($measurementData->{'eye_id'} == 1) || ($measurementData->{'eye_id'} == 3));
        $data['right'] = $measurementData->{'al_modified_right'} && (($measurementData->{'eye_id'} == 2) || ($measurementData->{'eye_id'} == 3));

        return $data;
    }

    /**
     * @param $id
     *
     * @return mixed
     */
    private function isKModified()
    {
        $measurementValues = $this->getMeasurementData();
        $measurementData = $measurementValues[0];

        $data['left'] = $measurementData->{'k_modified_left'} && (($measurementData->{'eye_id'} == 1) || ($measurementData->{'eye_id'} == 3));
        $data['right'] = $measurementData->{'k_modified_right'} && (($measurementData->{'eye_id'} == 2) || ($measurementData->{'eye_id'} == 3));

        return $data;
    }

    /**
     * @return bool
     */
    protected function isManualEntryDisabled()
    {
        $state = Yii::app()->db->createCommand()
            ->select('value')
            ->from('setting_installation')
            ->where('`key`=:id', array(':id' => 'disable_manual_biometry'))
            ->queryRow();

        if ($state['value'] === 'off') {
            return true;
        } else {
            return false;
        }
    }

    public function formatAconst($aconst)
    {
        /* based on the requirements:
        Valid results*
        * 118.0
        * 118.1*
        * 118.12*
        * 118.123*
        * 118.102
        * 118.001*

        *Invalid results*
        * 118
        * 118.000
        * 118.100
        * 118.120

        */
        $formatted = (float)$aconst;
        if ($formatted == (int)$formatted) {
            $formatted .= '.0';
        }

        return $formatted;
    }
}
