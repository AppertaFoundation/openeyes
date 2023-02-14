<?php

/**
 * This is the model class for table "trial".
 *
 * The followings are the available columns in table 'trial':
 * @property int $id
 * @property string $name
 * @property string $description
 * @property int $owner_user_id
 * @property bool $is_open
 * @property int $trial_type_id
 * @property string $started_date
 * @property string $closed_date
 * @property string $external_data_link
 * @property string $last_modified_date
 * @property string $last_modified_user_id
 * @property int $created_user_id
 * @property string $created_date
 * @property int $ethics_number
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
     * The return code for actionRemovePermission() if the user tried to remove admin from the Trial
     */
    const REMOVE_PERMISSION_RESULT_CANT_REMOVE_ADMIN = 'remove_admin_fail';
    /**
     * The return code for actionRemovePermission() if the user tried to remove owner from the Trial
     */
    const REMOVE_PERMISSION_RESULT_CANT_REMOVE_OWNER = 'remove_owner_fail';

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
            array('name, owner_user_id, trial_type_id', 'required'),
            array('name', 'length', 'max' => 200),
            array('name', 'unique', 'caseSensitive' => false),
            array('external_data_link', 'url', 'defaultScheme' => 'http'),
            array(
                'trial_type_id, owner_user_id, last_modified_user_id, created_user_id',
                'length',
                'max' => 10,
            ),
            array('started_date, closed_date', 'OEDateValidator', 'on' => 'manual'),
            array('closed_date', 'closedDateValidator', 'on' => 'manual'),
            array('description, last_modified_date, created_date, ethics_number', 'safe'),
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

    /*
    * Get the ethics number as a string
    */
    public function getEthicsNumberForDisplay()
    {
        return $this->ethics_number === null ? 'NA' : $this->ethics_number;
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
            'createdUser' => array(self::BELONGS_TO, 'User', 'created_user_id'),
            'lastModifiedUser' => array(self::BELONGS_TO, 'User', 'last_modified_user_id'),
            'trialPatients' => array(self::HAS_MANY, 'TrialPatient', 'trial_id'),
            'userAssignments' => array(self::HAS_MANY, 'UserTrialAssignment', 'trial_id'),
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
            'trial_type_id' => 'Trial Type',
            'started_date' => 'Start',
            'closed_date' => 'End',
            'last_modified_date' => 'Last Modified Date',
            'last_modified_user_id' => 'Last Modified User',
            'created_user_id' => 'Created User',
            'created_date' => 'Created Date',
            'external_data_link' => 'External Data Link',
            'ethics_number' => 'Ethics Number',
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
            if (isset($this->$date_column) && !empty($this->$date_column)) {
                $this->$date_column = Helper::convertNHS2MySQL($this->$date_column);
            } else {
                $this->$date_column = null;
            }
        }

        return true;
    }

    /**
     * Overrides CActiveModel::beforeSave()
     *
     * @return bool
     */
    public function beforeSave()
    {
        if ($this->closed_date !== null && $this->closed_date !== '') {
            $this->is_open = 0;
        } else {
            $this->is_open = 1;
        }

        return parent::beforeSave();
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
            if (array_key_exists('principal_investigator', $_SESSION) && !empty($_SESSION['principal_investigator'])) {
                $current_user_id = $_SESSION['principal_investigator'];
            } else {
                $current_user_id = Yii::app()->user->id;
            }

            // unsetting the session, so that if it is empty for the next row it won't insert the principal investigator that was entered for the previous row for the trial import.
            unset($_SESSION['principal_investigator']);

            $admin_user_group = User::model()->findAllByRoles(array('admin'));
            if (!in_array($current_user_id, $admin_user_group)) {
                array_push($admin_user_group, $current_user_id);
            }
            foreach ($admin_user_group as $user_id) {
                $newPermission = new UserTrialAssignment();
                $newPermission->user_id = $user_id;
                $newPermission->trial_id = $this->id;
                $newPermission->trial_permission_id = TrialPermission::model()->find('code = ?', array('MANAGE'))->id;
                if ($user_id == $current_user_id) {
                    // Always make the current user as the owner of the trial.
                    if (Yii::app()->user->id == $user_id) {
                        $newPermission->role = 'Trial Owner';
                    }
                    $newPermission->is_principal_investigator = 1;
                }
                if (!$newPermission->save()) {
                    throw new CHttpException(
                        500,
                        'The owner permission for the new trial could not be saved: '
                           . print_r($newPermission->getErrors(), true)
                    );
                }
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
        return TrialPatient::model()->exists(
            'trial_id = :trialId AND status_id = :patientStatus',
            array(
                ':trialId' => $this->id,
                ':patientStatus' => TrialPatientStatus::model()->find('code = "SHORTLISTED"')->id,
            )
        );
    }

  /**
   * Gets the data providers for each patient status
   * @param string $sort_by The field name to sort by
   * @param string $sort_dir The direction to sort the results by
   * @return array An array of data providers with one for each patient status
   */
    public function getPatientDataProviders($sort_by, $sort_dir)
    {
        $dataProviders = array();

        foreach (TrialPatientStatus::model()->findAll() as $index => $status) {
            $dataProviders[$status->code] = $this->getPatientDataProvider($status, $sort_by, $sort_dir);
        }

        return $dataProviders;
    }

    /**
     * Create a data provider for patients in the Trial
     * @param TrialPatientStatus $patient_status The status of patients of
     * @param string $sort_by The field name to sort by
     * @param string $sort_dir The direction to sort the results by
     * @return CActiveDataProvider The data provider of patients with the given status
     */
    public function getPatientDataProvider($patient_status, $sort_by, $sort_dir)
    {
        // Get the column to sort by ('t' => trial_patient, p => patient, e => ethnic_group, c => contact))
        $sortBySql = null;
        switch ($sort_by) {
            case 'Name':
            default:
                $sortBySql = "c.last_name $sort_dir, c.first_name";
                break;
            case 'Sex':
                $sortBySql = 'p.gender';
                break;
            case 'Age':
                $sortBySql = 'IFNULL(p.date_of_death, NOW()) - p.dob';
                break;
            case 'Ethnicity':
                $sortBySql = 'IFNULL(e.name, "Unknown")';
                break;
            case 'External Reference':
                $sortBySql = 'ISNULL(t.external_trial_identifier), t.external_trial_identifier';
                break;
            case 'Treatment Type':
                $sortBySql = 'ISNULL(t.treatment_type_id), t.treatment_type_id';
                break;
            case 'Accepted/Rejected Date':
                $sortBySql = 'ISNULL(t.status_update_date), t.status_update_date';
                break;
        }

        $sortExpr = "$sortBySql $sort_dir, c.last_name ASC, c.first_name ASC";

        return new CActiveDataProvider('TrialPatient', array(
            'criteria' => array(
                'condition' => 'trial_id = :trialId AND status_id = :patientStatus',
                'join' => 'JOIN patient p ON p.id = t.patient_id
                                   JOIN contact c ON c.id = p.contact_id
                                   LEFT JOIN ethnic_group e ON e.id = p.ethnic_group_id',
                'order' => $sortExpr,
                'params' => array(
                  ':trialId' => $this->id,
                  ':patientStatus' => $patient_status->id,
                ),
            ),
            'pagination' => array(
                'pageSize' => 10,
            ),
        ));
    }

    /**
     * Get a list of trials for a specific trial type. The output of this can be used to render drop-down lists.
     * @param int $type The trial type ID.
     * @return array A list of trials of the specified trial type.
     */
    public static function getTrialList($type)
    {
        if (!$type) {
            return CHtml::listData(Trial::model()->findAll(), 'id', 'name');
        }

        return CHtml::listData(
            Trial::model()->findAll('trial_type_id=:type', array(':type' => $type)),
            'id',
            'name'
        );
    }

    /**
     * Adds a patient to the trial
     *
     * @param Patient $patient The patient to add
     * @param TrialPatientStatus $patient_status The initial trial status for the patient (default to shortlisted)
     * @returns TrialPatient The new TrialPatient record
     * @return TrialPatient
     * @throws Exception Thrown if an error occurs when saving the TrialPatient record
     */
    public function addPatient(Patient $patient, $patient_status)
    {
        $trialPatient = new TrialPatient();
        $trialPatient->trial_id = $this->id;
        $trialPatient->patient_id = $patient->id;
        $trialPatient->status_id = $patient_status->id;
        $trialPatient->treatment_type_id = TreatmentType::model()->find('code = "UNKNOWN"')->id;

        if (!$trialPatient->save()) {
            throw new Exception(
                'Unable to create TrialPatient: ' . print_r($trialPatient->getErrors(), true)
            );
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

    public function getUserPermission($user_id)
    {
        return @UserTrialAssignment::model()->find(
            'user_id = :user_id AND trial_id = :trial_id',
            array(':user_id' => $user_id, ':trial_id' => $this->id)
        )->trialPermission;
    }

    /**
     * Creates a new Trial Permission using values in $_POST
     *
     * @param int $user_id The ID of the User record to add the permission to
     * @param TrialPermission $permission The permission level the user will be given (view/edit/manage)
     * @param string $role The role the user will have
     * @return string The return code
     * @throws Exception Thrown if the permission couldn't be saved
     */
    public function addUserPermission($user_id, $permission, $role)
    {
        if (UserTrialAssignment::model()->exists(
            'trial_id = :trialId AND user_id = :userId',
            array(
                ':trialId' => $this->id,
                ':userId' => $user_id,
            )
        )
        ) {
            return self::RETURN_CODE_USER_PERMISSION_ALREADY_EXISTS;
        }

        $userPermission = new UserTrialAssignment();
        $userPermission->trial_id = $this->id;
        $userPermission->user_id = $user_id;
        $userPermission->trial_permission_id = $permission->id;
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
     * @return string The return code
     * @throws Exception Thrown if the permission cannot be deleted
     */
    public function removeUserAssignment($permission_id)
    {
        $logMessage = null;
        /* @var UserTrialAssignment $permission */
        $assignment = UserTrialAssignment::model()->findByPk($permission_id);
        $admin_user_group = User::model()->findAllByRoles(array('admin'));
        if ($assignment->trial->id !== $this->id) {
            throw new Exception('Cannot remove permission from another trial');
        }

        if ($assignment->user_id === Yii::app()->user->id) {
            return self::REMOVE_PERMISSION_RESULT_CANT_REMOVE_SELF;
        }

        if ($assignment->user_id === $assignment->created_user_id) {
            return self::REMOVE_PERMISSION_RESULT_CANT_REMOVE_OWNER;
        }

        if (in_array($assignment->user_id, $admin_user_group)) {
            return self::REMOVE_PERMISSION_RESULT_CANT_REMOVE_ADMIN;
        }

        // The last Manage permission in a trial can't be removed (there always has to be one manager for a trial)
        if ($assignment->trialPermission->can_manage) {
            $managerCount = UserTrialAssignment::model()->count(
                'trial_id = :trialId AND EXISTS (
            SELECT tp.id FROM trial_permission tp WHERE tp.id = trial_permission_id AND tp.can_manage)',
                array(
                    ':trialId' => $this->id,
                )
            );

            if ($managerCount <= 1) {
                return self::REMOVE_PERMISSION_RESULT_CANT_REMOVE_LAST;
            }
        }


        if (!$assignment->delete()) {
            throw new Exception(
                'An error occurred when attempting to delete the permission: '
                . print_r($assignment->getErrors(), true)
            );
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
            $this->closed_date = date('Y-m-d');
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
     * @throws CException
     */
    public function deepDelete()
    {
        /**
         * @var $transaction CDbTransaction
         */
        $transaction = Yii::app()->db->beginTransaction();

        foreach ($this->userAssignments as $permission) {
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


    public function getTrialPrincipalInvestigators()
    {
        return UserTrialAssignment::model()->findAll('trial_id=? and is_principal_investigator = 1', array($this->id));
    }

    public function getTrialStudyCoordinators()
    {
        return UserTrialAssignment::model()->findAll('trial_id=? and is_study_coordinator = 1', array($this->id));
    }
    public function closedDateValidator($attribute, $params)
    {
        if ($this->hasErrors('closed_date')) {
            return;
        }
        if (isset($this->started_date) && isset($this->$attribute)) {
            $started_date = new DateTime($this->started_date);
            $closed_date = new DateTime($this->$attribute);
            if ($closed_date < $started_date) {
                $this->addError($attribute, 'Invalid date. Closed date cannot be earlier than started date.');
            }

            $now = new DateTime();
            if ($closed_date > $now) {
                $this->addError($attribute, 'Invalid date. Closed date cannot be in the future.');
            }
        }
    }
}
