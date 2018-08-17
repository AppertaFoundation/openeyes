<?php

/**
 * This is the model class for table "trial".
 *
 * The followings are the available columns in table 'trial':
 * @property int $id
 * @property string $name
 * @property string $description
 * @property int $owner_user_id
 * @property int $principle_investigator_user_id
 * @property int $coordinator_user_id
 * @property bool $is_open
 * @property int $trial_type_id
 * @property string $started_date
 * @property string $closed_date
 * @property string $external_data_link
 * @property string $last_modified_date
 * @property string $last_modified_user_id
 * @property int $created_user_id
 * @property string $created_date
 *
 * The followings are the available model relations:
 * @property TrialType $trialType
 * @property User $ownerUser
 * @property User $principalUser
 * @property User $coordinatorUser
 * @property User $createdUser
 * @property User $lastModifiedUser
 * @property TrialPatient[] $trialPatients
 * @property UserTrialAssignment[] $userAssignments
 */
class Trial extends BaseActiveRecordVersioned
{
    /**
     * The success return code for addUserPermission()
     */
    const RETURN_CODE_USER_PERMISSION_OK = 'success';

    /**
     * The return code for addUserPermission() if the user tried to share the trial with a user that it is
     * already shared with
     */
    const RETURN_CODE_USER_PERMISSION_ALREADY_EXISTS = 'permission_already_exists';

    /**
     * The return code for actionRemovePermission() if all went well
     */
    const REMOVE_PERMISSION_RESULT_SUCCESS = 'success';
    /**
     * The return code for actionRemovePermission() if the user tried to remove the last user with manage privileges
     */
    const REMOVE_PERMISSION_RESULT_CANT_REMOVE_LAST = 'remove_last_fail';
    /**
     * The return code for actionRemovePermission() if the user tried to remove themselves from the Trial
     */
    const REMOVE_PERMISSION_RESULT_CANT_REMOVE_SELF = 'remove_self_fail';

    /**
     * The return code for actionTransitionState() if the transition was a success
     */
    const RETURN_CODE_OK = 'success';

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'trial';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        return array(
            array('name, owner_user_id, principle_investigator_user_id, trial_type_id', 'required'),
            array('name', 'length', 'max' => 64),
            array('name', 'unique', 'caseSensitive' => false),
            array('external_data_link', 'url', 'defaultScheme' => 'http'),
            array('external_data_link', 'length', 'max' => 255),
            array(
                'trial_type_id, owner_user_id, principle_investigator_user_id, coordinator_user_id, last_modified_user_id, created_user_id',
                'length',
                'max' => 10,
            ),
            array('started_date, closed_date', 'dateFormatValidator', 'on' => 'manual'),
            array('description, last_modified_date, created_date', 'safe'),
        );
    }

    /**
     * Returns the date this trial was started as a string
     *
     * @return string The started date as a string
     */
    public function getStartedDateForDisplay()
    {
        return $this->started_date !== null ? Helper::formatFuzzyDate($this->started_date) : 'Pending';
    }

    /**
     * Returns the date this trial was closed as a string
     *
     * @return string The closed date
     */
    public function getClosedDateForDisplay()
    {
        if ($this->started_date === null) {
            return null;
        }

        if ($this->closed_date !== null) {
            return Helper::formatFuzzyDate($this->closed_date);
        }

        return 'present';
    }

    /**
     * Gets the relation rules for Trial
     *
     * @return array relational rules.
     */
    public function relations()
    {
        return array(
            'trialType' => array(self::BELONGS_TO, 'TrialType', 'trial_type_id'),
            'ownerUser' => array(self::BELONGS_TO, 'User', 'owner_user_id'),
            'principalUser' => array(self::BELONGS_TO, 'User', 'principle_investigator_user_id'),
            'coordinatorUser' => array(self::BELONGS_TO, 'User', 'coordinator_user_id'),
            'createdUser' => array(self::BELONGS_TO, 'User', 'created_user_id'),
            'lastModifiedUser' => array(self::BELONGS_TO, 'User', 'last_modified_user_id'),
            'trialPatients' => array(self::HAS_MANY, 'TrialPatient', 'trial_id'),
            'userPermissions' => array(self::HAS_MANY, 'UserTrialAssignment', 'trial_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'name' => 'Name',
            'description' => 'Description',
            'owner_user_id' => 'Owner User',
            'principle_investigator_user_id' => 'Principal Investigator',
            'coordinator_user_id' => 'Study Coordinator',
            'trial_type' => 'Trial Type',
            'started_date' => 'Start',
            'closed_date' => 'End',
            'last_modified_date' => 'Last Modified Date',
            'last_modified_user_id' => 'Last Modified User',
            'created_user_id' => 'Created User',
            'created_date' => 'Created Date',
            'external_data_link' => 'External Data Link',
        );
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return Trial the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    /**
     * Overrides CActiveModel::beforeSave()
     *
     * @return bool A value indicating whether the model can be saved
     */
    public function beforeSave()
    {
        foreach (array('started_date', 'closed_date') as $date_column) {
            $date = $this->{$date_column};
            if (strtotime($date)) {
                $this->{$date_column} = date('Y-m-d', strtotime($date));
            } else {
                $this->{$date_column} = null;
            }
        }

        return parent::beforeSave();
    }

    /**
     * Overrides CActiveModel::beforeValidate()
     *
     * @return bool A value indicating whether the trial passed validation
     */
    public function beforeValidate()
    {
        if (!parent::beforeValidate()) {
            return false;
        }

        foreach (array('started_date', 'closed_date') as $date_column) {
            $this->$date_column = str_replace('/', '-', $this->$date_column);
        }

        return true;
    }

    /**
     * Overrides CActiveModel::afterSave()
     *
     * @throws Exception Thrown if a new permission cannot be created
     */
    protected function afterSave()
    {
        parent::afterSave();

        if ($this->getIsNewRecord()) {

            // Create a new permission assignment for the user that created the Trial
            $newPermission = new UserTrialAssignment();
            $newPermission->user_id = Yii::app()->user->id;
            $newPermission->trial_id = $this->id;
            $newPermission->trial_permission_id = TrialPermission::model()->find('code = ?', array('MANAGE'))->id;
            $newPermission->role = 'Trial Owner';

            if (!$newPermission->save()) {
                throw new CHttpException(500, 'The owner permission for the new trial could not be saved: '
                    . print_r($newPermission->getErrors(), true));
            }
        }
    }

    /**
     * Returns whether or not this trial has any shortlisted patients
     *
     * @return bool True if the trial has one or more shortlisted patients, otherwise false
     */
    public function hasShortlistedPatients()
    {
        return TrialPatient::model()->exists('trial_id = :trialId AND patient_status = :patientStatus',
            array(':trialId' => $this->id, ':patientStatus' => TrialPatient::STATUS_SHORTLISTED));
    }

    /**
     * Gets the data providers for each patient status
     * @param string $sort_by The field name to sort by
     * @param string $sort_dir The direction to sort the results by
     * @return array An array of data providers with one for each patient status
     * @throws CException Thrown if an error occurs when created the data providers
     */
    public function getPatientDataProviders($sort_by, $sort_dir)
    {
        $dataProviders = array();
        foreach (TrialPatient::getAllowedStatusRange() as $index => $status) {
            $dataProviders[$status] = $this->getPatientDataProvider($status, $sort_by, $sort_dir);
        }

        return $dataProviders;
    }

    /**
     * Create a data provider for patients in the Trial
     * @param string $patient_status The status of patients of
     * @param string $sort_by The field name to sort by
     * @param string $sort_dir The direction to sort the results by
     * @return CActiveDataProvider The data provider of patients with the given status
     * @throws CException Thrown if the patient_status is invalid
     */
    public function getPatientDataProvider($patient_status, $sort_by, $sort_dir)
    {
        if (!in_array($patient_status, TrialPatient::getAllowedStatusRange(), true)) {
            throw new CException("Unknown Trial Patient status: $patient_status");
        }

        // Get the column to sort by ('t' => trial_patient, p => patient, e => ethnic_group, c => contact))
        $sortBySql = null;
        switch ($sort_by) {
            case 'name':
            default:
                $sortBySql = "c.last_name $sort_dir, c.first_name";
                break;
            case 'gender':
                $sortBySql = 'p.gender';
                break;
            case 'age':
                $sortBySql = 'NOW() - p.dob';
                break;
            case 'ethnicity':
                $sortBySql = 'IFNULL(e.name, "Unknown")';
                break;
            case 'external_reference':
                $sortBySql = 'ISNULL(t.external_trial_identifier), t.external_trial_identifier';
                break;
            case 'treatment_type':
                $sortBySql = 'ISNULL(treatment_type), t.treatment_type';
                break;
        }

        $sortExpr = "$sortBySql $sort_dir, c.last_name ASC, c.first_name ASC";

        $patientDataProvider = new CActiveDataProvider('TrialPatient', array(
            'criteria' => array(
                'condition' => 'trial_id = :trialId AND patient_status = :patientStatus',
                'join' => 'JOIN patient p ON p.id = t.patient_id
                           JOIN contact c ON c.id = p.contact_id
                           LEFT JOIN ethnic_group e ON e.id = p.ethnic_group_id',
                'order' => $sortExpr,
                'params' => array(
                    ':trialId' => $this->id,
                    ':patientStatus' => $patient_status,
                ),
            ),
            'pagination' => array(
                'pageSize' => 10,
            ),
        ));

        return $patientDataProvider;
    }

    /**
     * Get a list of trials for a specific trial type. The output of this can be used to render drop-down lists.
     * @param string $type The trial type.
     * @return array A list of trials of the specified trial type.
     */
    public static function getTrialList($type)
    {
        if ($type === null || $type === '') {
            return array();
        }

        $trialModels = Trial::model()->findAll('trial_type=:type', array(':type' => $type));

        return CHtml::listData($trialModels, 'id', 'name');
    }

    /**
     * Adds a patient to the trial
     *
     * @param Patient $patient The patient to add
     * @param string $patient_status The initial trial status for the patient (default to shortlisted)
     * @returns TrialPatient The new TrialPatient record
     * @throws Exception Thrown if an error occurs when saving the TrialPatient record
     */
    public function addPatient(Patient $patient, $patient_status)
    {
        $trialPatient = new TrialPatient();
        $trialPatient->trial_id = $this->id;
        $trialPatient->patient_id = $patient->id;
        $trialPatient->patient_status = $patient_status;
        $trialPatient->treatment_type = TrialPatient::TREATMENT_TYPE_UNKNOWN;

        if (!$trialPatient->save()) {
            throw new Exception(
                'Unable to create TrialPatient: ' . print_r($trialPatient->getErrors(), true));
        }

        $this->audit('trial', 'add-patient');

        return $trialPatient;
    }

    /**
     * @param int $patient_id The id of the patient to remove
     * @throws Exception Raised when an error occurs when removing the record
     */
    public function removePatient($patient_id)
    {
        $trialPatient = TrialPatient::model()->find(
            'patient_id = :patientId AND trial_id = :trialId',
            array(
                ':patientId' => $patient_id,
                ':trialId' => $this->id,
            )
        );

        if ($trialPatient === null) {
            throw new Exception("Patient $patient_id cannot be removed from Trial $this->>id");
        }

        if (!$trialPatient->delete()) {
            throw new Exception('Unable to delete TrialPatient: ' . print_r($trialPatient->getErrors(), true));
        }

        $this->audit('trial', 'remove-patient');
    }

    /**
     * Creates a new Trial Permission using values in $_POST
     *
     * @param int $user_id The ID of the User record to add the permission to
     * @param string $permission The permission level the user will be given (view/edit/manage)
     * @param string $role The role the user will have
     * @returns string The return code
     * @throws Exception Thrown if the permission couldn't be saved
     */
    public function addUserPermission($user_id, $permission, $role)
    {
        if (UserTrialAssignment::model()->exists(
            'trial_id = :trialId AND user_id = :userId',
            array(
                ':trialId' => $this->id,
                ':userId' => $user_id,
            ))
        ) {
            return self::RETURN_CODE_USER_PERMISSION_ALREADY_EXISTS;
        }

        $userPermission = new UserTrialAssignment();
        $userPermission->trial_id = $this->id;
        $userPermission->user_id = $user_id;
        $userPermission->permission = $permission;
        $userPermission->role = $role;

        if (!$userPermission->save()) {
            throw new Exception('Unable to create UserTrialAssignment: ' . print_r($userPermission->getErrors(), true));
        }

        $this->audit('trial', 'add-user-permission');

        return self::RETURN_CODE_USER_PERMISSION_OK;
    }

    /**
     * Removes a UserTrialAssignment
     *
     * @param int $permission_id The ID of the permission to remove
     * @throws CHttpException Thrown if the permission cannot be found
     * @return string The return code
     * @throws Exception Thrown if the permission cannot be deleted
     */
    public function removeUserPermission($permission_id)
    {
        $logMessage = null;
        /* @var UserTrialAssignment $permission */
        $permission = UserTrialAssignment::model()->findByPk($permission_id);
        if ($permission->trial->id !== $this->id) {
            throw new Exception('Cannot remove permission from another trial');
        }

        if ($permission->user_id === Yii::app()->user->id) {
            return self::REMOVE_PERMISSION_RESULT_CANT_REMOVE_SELF;
        }

        // The last Manage permission in a trial can't be removed (there always has to be one manager for a trial)
        if ($permission->permission === UserTrialAssignment::PERMISSION_MANAGE) {
            $managerCount = UserTrialAssignment::model()->count('trial_id = :trialId AND permission = :permission',
                array(
                    ':trialId' => $this->id,
                    ':permission' => UserTrialAssignment::PERMISSION_MANAGE,
                )
            );

            if ($managerCount <= 1) {
                return self::REMOVE_PERMISSION_RESULT_CANT_REMOVE_LAST;
            }
        }

        if ($this->principle_investigator_user_id === $permission->user_id) {
            $this->principle_investigator_user_id = $this->owner_user_id;

            if (!$this->save()) {
                throw new Exception('Unable to remove ' . $this->getAttributeLabel('principle_investigator_user_id') . ': ' . print_r($this->errors,
                        true));
            }
            $logMessage .= 'Principal Investigator removed. ';
        }

        if ($this->principle_investigator_user_id === $permission->user_id) {
            $this->coordinator_user_id = $this->owner_user_id;

            if (!$this->save()) {
                throw new Exception('Unable to remove ' . $this->getAttributeLabel('coordinator_user_id'));
            }
            $logMessage .= 'Coordinator removed. ';
        }


        if (!$permission->delete()) {
            throw new Exception('An error occurred when attempting to delete the permission: '
                . print_r($permission->getErrors(), true));
        }

        $this->audit('trial', 'remove-user-permission', null, $logMessage);

        return self::REMOVE_PERMISSION_RESULT_SUCCESS;
    }

    /**
     * Closes the trial and sets it closed date (if it isn't already set)
     *
     * @return bool True of if the trial can be closed, otherwise false
     * @throws Exception Thrown if an error occurs when saving the trial
     */
    public function close()
    {
        if ($this->is_open === 0) {
            return false;
        }

        $this->is_open = 0;
        if ($this->closed_date === null || $this->closed_date === '') {
            $this->closed_date = date('d-m-Y');
        }

        if (!$this->save()) {
            throw new Exception('An error occurred when closing the trial: ' . print_r($this->getErrors(), true));
        }

        return true;
    }

    /**
     * Re-opens a closed trial and clears the closed date
     *
     * @return bool True if the trial could be reopened, otherwise false
     * @throws Exception
     */
    public function reopen()
    {
        if ($this->is_open === 1) {
            return false;
        }

        $this->is_open = 1;
        $this->closed_date = null;

        if (!$this->save()) {
            throw new Exception('An error occurred when closing the trial: ' . print_r($this->getErrors(), true));
        }

        return true;
    }

    /**
     * Deletes this trial and all objects related to it
     *
     * @return bool True if the deletion was successful, otherwise false
     * @throws CDbException Thrown if an error occurs during rollback or commit
     */
    public function deepDelete()
    {
        $transaction = Yii::app()->db->beginTransaction();

        foreach ($this->userPermissions as $permission) {
            if (!$permission->delete()) {
                $transaction->rollback();

                return false;
            }
        }


        foreach ($this->trialPatients as $trialPatient) {
            if (!$trialPatient->delete()) {
                $transaction->rollback();

                return false;
            }
        }


        if (!$this->delete()) {
            $transaction->rollback();

            return false;
        }

        $transaction->commit();

        return true;
    }

    /**
     * This validator is added to the Trial object in TrialController create/update action
     *
     * Validating the date format
     * @param string $attribute The name of the date attribute the validate
     * @param mixed $params The validator params
     */
    public function dateFormatValidator($attribute, $params)
    {
        if ($this->$attribute === null || $this->$attribute === '') {
            return;
        }

        // because 02/02/198 is valid according to DateTime::createFromFormat('d-m-Y', ...)
        $format_check = preg_match("/^(0[1-9]|[1-2][0-9]|3[0-1])-(0[1-9]|1[0-2])-[0-9]{4}$/", $this->$attribute);

        $patient_dob_date = DateTime::createFromFormat('d-m-Y', $this->$attribute);

        if (!$patient_dob_date || !$format_check) {
            $this->addError($attribute, 'Wrong date format. Use dd/mm/yyyy' . $this->$attribute);
        }
    }
}
