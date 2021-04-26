<?php

/**
 * This is the model class for table "trial_patient".
 *
 * The followings are the available columns in table 'trial_patient':
 * @property int $id
 * @property string $external_trial_identifier
 * @property int $trial_id
 * @property int $patient_id
 * @property int $status_id
 * @property int $treatment_type_id
 * @property int $last_modified_user_id
 * @property string $last_modified_date
 * @property int $created_user_id
 * @property string $created_date
 * @property string $comment
 * @property string $status_update_date
 *
 * The followings are the available model relations:
 * @property Patient $patient
 * @property User $createdUser
 * @property User $lastModifiedUser
 * @property Trial $trial
 * @property TrialPatientStatus $status
 * @property TreatmentType $treatmentType
 */
class TrialPatient extends BaseActiveRecordVersioned
{
    /**
     * Gets whether a patient is currently in an open Intervention trial (other than the given trial)
     *
     * @param Patient $patient The patient to test for
     * @param int $trial_id If set, this function will ignore trials with this ID
     *
     * @return bool Returns true if this patient is currently in an open Intervention trial, otherwise false
     */
    public static function isPatientInInterventionTrial(Patient $patient, $trial_id = null)
    {
        foreach ($patient->trials as $trialPatient) {
            if ($trialPatient->status->code === TrialPatientStatus::ACCEPTED_CODE &&
                $trialPatient->trial->trialType->code === TrialType::INTERVENTION_CODE &&
                $trialPatient->trial->is_open &&
                ($trial_id === null || $trialPatient->trial_id !== $trial_id)
            ) {
                return true;
            }
        }

        return false;
    }

    /**
     * Gets the "highest" treatment type that a patient has undergone from previous intervention trials
     * If the patient has been in multiple intervention trials, then "Intervention" treatment will be favoured over
     * "Unknown" which will be favoured over "Placebo"
     *
     * @param Patient $patient The patient to test for
     * @param int $trial_id If set, this function will ignore trials with this ID
     *
     * @return TreatmentType Returns the treatment type that the patient has undergone as part of previous trials.
     */
    public static function getLastPatientTreatmentType(Patient $patient, $trial_id = null)
    {
        $treatmentType = null;

        foreach ($patient->trials as $trialPatient) {
            if ($trialPatient->status->code === TrialPatientStatus::ACCEPTED_CODE &&
                $trialPatient->trial->trialType->code === TrialType::INTERVENTION_CODE &&
                !$trialPatient->trial->is_open &&
                ($trial_id === null || $trialPatient->trial_id !== $trial_id)
            ) {
                switch ($trialPatient->treatmentType->code) {
                    case TreatmentType::INTERVENTION_CODE:
                        return $trialPatient->treatmentType;
                    case TreatmentType::UNKNOWN_CODE:
                        $treatmentType = $trialPatient->treatmentType;
                        break;
                    case TreatmentType::PLACEBO_CODE:
                        if ($treatmentType === null) {
                            $treatmentType = $trialPatient->treatmentType;
                        }
                        break;
                }
            }
        }

        return $treatmentType;
    }

    /**
     * @param $patient Patient record.
     * @param $status_code string Patient's status in a trial.
     * @return string Number of trials the patient is in where they have the given status within a trial. This is returned as a string to preserve max. precision.
     */
    public static function getTrialCount($patient, $status_code)
    {
        $status = TrialPatientStatus::model()->find('code = ?', array($status_code));

        $criteria = new CDbCriteria();
        $criteria->compare('status_id', $status->id);
        $criteria->compare('patient_id', $patient->id);
        return TrialPatient::model()->count($criteria);
    }

    /**
     * @param $patient Patient record.
     * @param $trial_id int Trial ID.
     * @return BaseActiveRecordVersioned|null TrialPatient record.
     */
    public static function getTrialPatient($patient, $trial_id)
    {
        $criteria = new CDbCriteria();
        $criteria->compare('trial_id', $trial_id);
        $criteria->compare('patient_id', $patient->id);
        return TrialPatient::model()->find($criteria);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'trial_patient';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        return array(
            array('trial_id, patient_id, status_id', 'required'),
            array('trial_id', 'numerical', 'integerOnly' => true),
            array('external_trial_identifier', 'length', 'max' => 100),
            array('patient_id, status_id, treatment_type_id', 'length', 'max' => 10),
            // The trial_id and the patient_id must be unique together
            array(
                'trial_id',
                'unique',
                'criteria' => array(
                    'condition' => '`patient_id`= :patientId',
                    'params' => array(
                        ':patientId' => $this->patient_id,
                    ),
                ),
            ),
            array('last_modified_date, created_date, status_update_date, comment', 'safe'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'patient' => array(self::BELONGS_TO, 'Patient', 'patient_id'),
            'status' => array(self::BELONGS_TO, 'TrialPatientStatus', 'status_id'),
            'createdUser' => array(self::BELONGS_TO, 'User', 'created_user_id'),
            'lastModifiedUser' => array(self::BELONGS_TO, 'User', 'last_modified_user_id'),
            'trial' => array(self::BELONGS_TO, 'Trial', 'trial_id'),
            'treatmentType' => array(self::BELONGS_TO, 'TreatmentType', 'treatment_type_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'external_trial_identifier' => 'Trial Identifier',
            'trial_id' => 'Trial',
            'patient_id' => 'Patient',
            'patient_status' => 'Patient Status',
            'status_update_date' => 'Accepted/Rejected Date',
            'treatment_type_id' => 'Treatment Type',
            'last_modified_user_id' => 'Last Modified User',
            'last_modified_date' => 'Last Modified Date',
            'created_user_id' => 'Created User',
            'created_date' => 'Created Date',
            'comment'=>'Comments',
        );
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return TrialPatient the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    /**
     * Changes the status of a patient in a trial to a given value
     * @param TrialPatientStatus $new_status The new status of the TrialPatient
     * @returns string The return code
     * @throws Exception Thrown the model cannot be saved
     */
    public function changeStatus(TrialPatientStatus $new_status)
    {
        if ($new_status->code === TrialPatientStatus::ACCEPTED_CODE &&
            $this->trial->trialType->code === TrialType::INTERVENTION_CODE &&
            self::isPatientInInterventionTrial($this->patient, $this->trial->id)
        ) {
            throw new CHttpException(400, "You can't accept this participant into your Trial because that participant has already been accepted into another Intervention trial.");
        }

        $this->status_id = $new_status->id;
        $this->status_update_date = date('Y-m-d H:i:s');
        if (!$this->save()) {
            throw new CHttpException(500, 'An error occurred when saving the model: ' . print_r($this->getErrors(), true));
        }

        $this->audit('trial-patient', 'change-status');
    }

    /**
     * Changes the external_trial_identifier of a TrialPatient record
     *
     * @param string $new_external_id The new external reference
     * @throws Exception Thrown if an error occurs when saving the model or if it cannot be found
     */
    public function updateExternalId($new_external_id)
    {
        $this->external_trial_identifier = $new_external_id;

        if (!$this->save()) {
            throw new Exception('An error occurred when saving the model: ' . print_r($this->getErrors(), true));
        }

        $this->audit('trial-patient', 'update-external-id');
    }

    /**
     * Updates the comment of a TrialPatient record
     *
     * @param string $new_comment The new comment
     * @throws Exception Thrown if an error occurs when saving the model or if it cannot be found
     */
    public function updateComment($new_comment)
    {
        $this->comment = $new_comment;

        if (!$this->save()) {
            throw new Exception('An error occurred when saving the model: ' . print_r($this->getErrors(), true));
        }

        $this->audit('trial-patient', 'update-comment');
    }

    /**
     * Updates the treatment type of a trial-patient with a new treatment type
     *
     * @param TreatmentType $treatment_type The new treatment type
     * @throws Exception Thrown if an error occurs when saving the TrialPatient
     */
    public function updateTreatmentType($treatment_type)
    {
        if ($this->trial->is_open) {
            throw new Exception('You cannot change the treatment type until the trial is closed.');
        }

        $this->treatment_type_id = $treatment_type->id;

        if (!$this->save()) {
            throw new Exception('An error occurred when saving the model: ' . print_r($this->getErrors(), true));
        }

        $this->audit('trial-patient', 'update-treatment-type');
    }

}
