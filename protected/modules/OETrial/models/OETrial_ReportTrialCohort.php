<?php

use OEModule\OphCiExamination\widgets\HistoryMedications;

/**
 * Class OETrial_ReportTrialCohort
 */
class OETrial_ReportTrialCohort extends BaseReport
{
    /**
     * @var int The ID of the trial
     */
    public $trialID;
    /**
     * @var TrialPatient[] The patients for the trial
     */
    public $patients = array();

    /**
     * @return array
     */
    public function rules()
    {
        return array(
            array('trialID', 'safe'),
        );
    }

    /**
     * @return CDbCommand The search command
     */
    public function getDbCommand()
    {
        return Yii::app()->db->createCommand()
            ->from('trial t')
            ->join('trial_patient t_p', 't.id = t_p.trial_id')
            ->join('patient p', 'p.id = t_p.patient_id')
            ->join('contact c', 'p.contact_id = c.id')
            ->group('p.id, p.hos_num, c.first_name, c.last_name, p.dob, t_p.external_trial_identifier, t_p.treatment_type_id, t_p.status_id, t_p.comment')
            ->order('c.first_name, c.last_name');
    }

    /**
     * Runs the report and adds the result set to $patients
     *
     * @throws CException
     */
    public function run()
    {
        $select = 'p.id, p.hos_num, c.first_name, c.last_name, p.dob, t_p.external_trial_identifier, t_p.comment, t_p.id as trial_patient_id';

        $query = $this->getDbCommand();

        $or_conditions = array('t.id=:id');
        $whereParams = array(':id' => $this->trialID);

        $query->select($select);
        $condition = '( ' . implode(' AND ', $or_conditions) . ' )';

        $query->where($condition, $whereParams);

        foreach ($query->queryAll() as $item) {
            $this->addPatientResultItem($item);
        }
    }

    /**
     * Gets the description message to display at the top of the report
     *
     * @return string
     */
    public function description()
    {
        /* @var Trial $trial */
        $trial = Trial::model()->findByPk($this->trialID);

        return "Participants in trial: $trial->name";
    }

    /**
     * Adds one result row to $patients
     *
     * @param array $item
     */
    public function addPatientResultItem($item)
    {
        $this->patients[$item['id']] = array(
            'hos_num' => $item['hos_num'],
            'dob' => $item['dob'],
            'first_name' => $item['first_name'],
            'last_name' => $item['last_name'],
            'external_trial_identifier' => $item['external_trial_identifier'],
            'trial_patient_id' => $item['trial_patient_id'],
            'comment'=>$item['comment'],
        );
    }

    /**
     * Output the report in CSV format.
     *
     * @return string
     * @throws CHttpException
     */
    public function toCSV()
    {
        $rows = array();
        $cols = array();
        $cols[] = Patient::model()->getAttributeLabel('hos_num');
        $cols[] = Patient::model()->getAttributeLabel('dob');
        $cols[] = Patient::model()->getAttributeLabel('first_name');
        $cols[] = Patient::model()->getAttributeLabel('last_name');
        $cols[] = TrialPatient::model()->getAttributeLabel('external_trial_identifier');
        $cols[] = TrialPatient::model()->getAttributeLabel('treatment_type_id');
        $cols[] = TrialPatient::model()->getAttributeLabel('status_id');
        $cols[] = 'Diagnoses';
        $cols[] = 'Medications';
        $cols[] = TrialPatient::model()->getAttributeLabel('comment');
        $rows[] = $cols;

        foreach ($this->patients as $ts => $patient) {
            /* @var TrialPatient $trial_patient */
            $trial_patient = TrialPatient::model()->findByPk($patient['trial_patient_id']);

            $cols = array();
            $cols[] = $patient['hos_num'];
            $cols[] = ($patient['dob'] ? date('j M Y', strtotime($patient['dob'])) : 'Unknown');
            $cols[] = $patient['first_name'];
            $cols[] = $patient['last_name'];
            $cols[] = $patient['external_trial_identifier'];
            $cols[] = $trial_patient->treatmentType->name;
            $cols[] = $trial_patient->status->name;

            $diagnoses = array();
            foreach ($trial_patient->patient->getOphthalmicDiagnosesSummary() as $diagnosis) {
                $name = explode('~', $diagnosis, 3)[1];
                $diagnoses[] = $name;
            }
            foreach ($trial_patient->patient->systemicDiagnoses as $diagnosis) {
                $diagnoses[] = $diagnosis->disorder->term;
            }
            $cols[] = implode('; ', $diagnoses);

            /* @var HistoryMedications $medicationsWidget */
            $medicationsWidget = Yii::app()->getWidgetFactory()->createWidget(
                $this,
                HistoryMedications::class,
                array(
                    'patient' => $trial_patient->patient,
                    'mode' => BaseEventElementWidget::$PATIENT_SUMMARY_MODE,
                )
            );

            $medicationsWidget->init();
            $medicationData = $medicationsWidget->getViewData();
            $medications = array();
            foreach ($medicationData['current'] as $medication) {
                $medications[] = $medication->getMedicationDisplay();
            }
            $cols[] = implode('; ', $medications);
            $cols[] = $trial_patient->comment;
            $rows[] = $cols;
        }

        return $this->array2Csv($rows);
    }
}
