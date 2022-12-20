<?php

/**
 * Class TrialController
 *
 * @property Trial $model
 */
class TrialController extends BaseModuleController
{
    public $model;

    public bool $fixedHotlist = true;

    /**
     * @return array action filters
     */
    public function filters()
    {
        return array(
            'accessControl',
        );
    }

    /**
     * Specifies the access control rules.
     * This method is used by the 'accessControl' filter.
     * @return array access control rules
     */
    public function accessRules()
    {
        return array(
            array(
                'allow',
                'actions' => array('getTrialList', 'permissions', 'renderPopups', 'trialAutocomplete'),
                'users' => array('@'),
            ),
            array(
                'allow',
                'actions' => array('index', 'userAutoComplete'),
                'roles' => array('TaskCreateTrial', 'TaskViewTrial'),
            ),
            array(
                'allow',
                'actions' => array('create'),
                'roles' => array('TaskCreateTrial'),
            ),
            array(
                'allow',
                'actions' => array('view'),
                'expression' => function ($user) {
                    return $user->checkAccess('TaskViewTrial') && @TrialController::getCurrentUserPermission()->can_view;
                },
            ),
            array(
                'allow',
                'actions' => array('update', 'addPatient', 'removePatient'),
                'expression' => function ($user) {
                    return $user->checkAccess('TaskViewTrial') && @TrialController::getCurrentUserPermission()->can_edit;
                },
            ),
            array(
                'allow',
                'actions' => array(
                    'addPermission',
                    'removePermission',
                    'close',
                    'delete',
                    'reopen',
                    'changePi',
                    'changeCoordinator',
                    'changeTrialUserPosition'
                ),
                'expression' => function ($user) {
                    return $user->checkAccess('TaskViewTrial') && @TrialController::getCurrentUserPermission()->can_manage;
                },
            ),
            array(
                'deny',  // deny all users
                'users' => array('*'),
            ),
        );
    }

    public static function getCurrentUserPermission()
    {
        $trial = Trial::model()->findByPk(Yii::app()->getRequest()->getParam('id'));
        return $trial !== null ? $trial->getUserPermission(Yii::app()->user->id) : null;
    }

    /**
     * Displays a particular model.
     * @param int $id the ID of the model to be displayed
     * @throws CException Thrown if an error occurs when loading the data providers
     */
    public function actionView($id)
    {
        $this->model = $this->loadModel($id);
        $report = new OETrial_ReportTrialCohort();

        $sortDir = Yii::app()->request->getParam('sort_dir', '0') === '0' ? 'asc' : 'desc';
        $sortBy = Yii::app()->request->getParam('sort_by', 'Name');
        $page = (int)Yii::app()->request->getParam('TrialPatient_page', 0);

        $this->render('view', array(
            'permission' => self::getCurrentUserPermission(),
            'trial' => $this->model,
            'report' => $report,
            'dataProviders' => $this->model->getPatientDataProviders($sortBy, $sortDir),
            'sort_by' => $sortBy,
            'sort_dir' => (int)Yii::app()->request->getParam('sort_dir', 0),
            'page' => $page
        ));
    }

    /**
     * Returns the data model based on the primary key given in the GET variable.
     * If the data model is not found, an HTTP exception will be raised.
     * @param int $id the ID of the model to be loaded
     * @return Trial the loaded model
     * @throws CHttpException
     */
    public function loadModel($id)
    {
        /* @var Trial $model */
        $model = Trial::model()->findByPk($id);
        if ($model === null) {
            throw new CHttpException(404, 'The requested page does not exist.');
        }

        return $model;
    }

    /**
     * Creates a new model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @throws Exception
     */
    public function actionCreate()
    {
        $this->model = new Trial();
        $this->model->setScenario('manual');
        $this->model->is_open = 1;
        $this->model->trial_type_id = TrialType::model()->find('code = ?', array(TrialType::NON_INTERVENTION_CODE))->id;
        $this->model->owner_user_id = Yii::app()->user->id;
        $this->model->started_date = date('d M Y');

        $this->performAjaxValidation($this->model);

        if (isset($_POST['Trial'])) {
            $this->model->attributes = $_POST['Trial'];
            if ($this->model->save()) {
                $this->redirect(array('view', 'id' => $this->model->id));
            }
        }

        $this->render('create', array(
            'trial' => $this->model,
        ));
    }

    /**
     * Updates a particular model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id the ID of the model to be updated
     * @throws CHttpException Thrown if the model cannot be loaded
     * @throws Exception
     */
    public function actionUpdate($id)
    {
        $this->model = $this->loadModel($id);
        $this->model->setScenario('manual');

        if (isset($_POST['Trial'])) {
            $this->model->attributes = $_POST['Trial'];
            if ($this->model->save()) {
                $this->redirect(array('view', 'id' => $this->model->id));
            }
        }

        $this->render('update', array(
            'trial' => $this->model,
        ));
    }

    /**
     * Lists all models.
     */
    public function actionIndex()
    {
        // Get the sort direction, defaulting to ascending
        $sortDir = Yii::app()->request->getParam('sort_dir', '0') === '0' ? 'asc' : 'desc';

        // Get the column to sort by (the 't' table is the trial table, 'u' is the user that owns the trial)
        // Default to sorting by status
        $sortBy = Yii::app()->request->getParam('sort_by', -1);
        switch ($sortBy) {
            case 1:
                $sortBy = 't.started_date';
                break;
            case 2:
                $sortBy = 't.closed_date';
                break;
            case 3:
                $sortBy = "LOWER(u.first_name) $sortDir, LOWER(u.last_name)";
                break;
            case 4:
                $sortBy = '-t.is_open'; // Open trials  first, then closed trials
                break;
            default:
                $sortBy = 'LOWER(t.name)';
                break;
        }

        $condition = "EXISTS (
                        SELECT * FROM user_trial_assignment utp WHERE utp.user_id = :userId AND utp.trial_id = t.id
                    ) ORDER BY $sortBy $sortDir, LOWER(t.name) ASC";

        $trialDataProvider = new CActiveDataProvider('Trial', array(
            'criteria' => array(
                'condition' => $condition,
                'join' => 'JOIN user u ON u.id = t.owner_user_id',
                'params' => array(
                    ':userId' => Yii::app()->user->id,
                ),
            ),
        ));

        $trialSearchDataProvider = new CActiveDataProvider('Trial', array(
            'criteria' => array(
                'condition' => $condition,
                'join' => 'JOIN user u ON u.id = t.owner_user_id',
                'params' => array(
                    ':userId' => Yii::app()->user->id,
                ),
            ),
            'pagination'=>false,
        ));


        $this->render('index', array(
            'trialDataProvider' => $trialDataProvider,
            'trialSearchDataProvider' => $trialSearchDataProvider,
            'sort_by' => (int)Yii::app()->request->getParam('sort_by', null),
            'sort_dir' => (int)Yii::app()->request->getParam('sort_dir', null),
        ));
    }

    /**
     * Displays the permissions screen
     *
     * @param int $id The ID of the Trial
     */
    public function actionPermissions($id)
    {
        $this->model = Trial::model()->findByPk($id);

        $permissionDataProvider = new CActiveDataProvider('UserTrialAssignment', array(
            'criteria' => array(
//                Showing only those users who are either trial owners, principal investigators or study coordinators - CERA-523 - other admins with no relation to the trial are not shown, as requested by CERA
                'condition' => 'trial_id = :trialId AND (role IS NOT NULL OR is_principal_investigator != 0 OR is_study_coordinator != 0 )' ,
                'params' => array(
                    ':trialId' => $this->model->id,
                ),
            ),
        ));

        $newPermission = new UserTrialAssignment();

        $this->render('permissions', array(
            'trial' => $this->model,
            'permission' => self::getCurrentUserPermission(),
            'newPermission' => $newPermission,
            'permissionDataProvider' => $permissionDataProvider,
        ));
    }

    /**
     * Adds a patient to the trial
     *
     * @throws Exception Thrown if an error occurs when saving the TrialPatient record
     */
    public function actionAddPatient()
    {
        $trial = $this->loadModel($_GET['id']);
        /* @var Patient $patient */
        $patient = Patient::model()->findByPk($_GET['patient_id']);
        $trial_patient = $trial->addPatient(
            $patient,
            TrialPatientStatus::model()->find('code = ?', array(TrialPatientStatus::SHORTLISTED_CODE))
        );

        $coordinators = array_map(
            static function($coordinator) { return $coordinator->user->getFullName(); },
            $trial->getTrialStudyCoordinators()
        );

        $this->renderJSON([
            'name' => ['name' => CHtml::encode($trial->name), 'link' => Yii::app()->controller->createUrl('/OETrial/trial/view', array('id' => $trial->id))],
            'started-date' => $trial->getStartedDateForDisplay(),
            'closed-date' => $trial->getClosedDateForDisplay(),
            'coordinators' => implode(', ', $coordinators),
            'trial-type' => $trial->trialType->name,
            'treatment-type' => $trial_patient->treatmentType->name,
            'status-name' => $trial_patient->status->name,
            'status-update-date' => isset($trial_patient->status_update_date) ? Helper::formatFuzzyDate($trial_patient->status_update_date) : null,
        ]);
    }

    /**
     * @throws CHttpException Raised when the record cannot be found
     * @throws Exception Raised when an error occurs when removing the record
     */
    public function actionRemovePatient()
    {
        $trial = $this->loadModel(Yii::app()->request->getParam('id'));
        $trial->removePatient(Yii::app()->request->getParam('patient_id'));
    }

    /**
     * Creates a new Trial Permission using values in $_POST
     *
     * @throws Exception Thrown if the permission couldn't be saved
     */
    public function actionAddPermission()
    {
        $trial = $this->loadModel($_POST['id']);
        $permission = TrialPermission::model()->findByPk($_POST['permission']);
        $result = $trial->addUserPermission($_POST['user_id'], $permission, $_POST['role']);
        echo $result;
    }

    /**
     * Removes a UserTrialAssignment
     *
     * @throws CHttpException Thrown if the permission cannot be found
     * @throws Exception Thrown if the permission cannot be deleted
     */
    public function actionRemovePermission()
    {
        $trial = $this->loadModel($_POST['id']);
        $result = $trial->removeUserAssignment($_POST['permission_id']);
        echo $result;
    }

    /**
     * Performs the AJAX validation.
     * @param Trial $model the model to be validated
     */
    protected function performAjaxValidation($model)
    {
        if (isset($_POST['ajax']) && $_POST['ajax'] === 'trial-form') {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }
    }

    /**
     * @param $id
     * @throws CHttpException
     * @throws Exception
     */
    public function actionClose($id)
    {
        $trial = $this->loadModel($id);
        $trial->close();
        $this->redirect($this->createUrl('view', array('id' => $trial->id)));
    }

    /**
     * @param $id
     * @throws CHttpException
     * @throws Exception
     */
    public function actionReopen($id)
    {
        $trial = $this->loadModel($id);
        $trial->reopen();
        $this->redirect($this->createUrl('view', array('id' => $trial->id)));
    }

    /**
     * @throws CDbException
     * @throws CHttpException
     */
    public function actionDelete()
    {
        $trial = $this->loadModel($_POST['id']);
        $trial->deepDelete();
        Yii::app()->user->setFlash('success', "$trial->name has been deleted");
        $this->redirect('index');
    }


    /**
     * Get a HTML list of all trials for the specified trial type.
     * @param $type string The trial type.
     */
    public function actionGetTrialList($type)
    {
        $trials = Trial::getTrialList($type);

        // Always pass the default list option (Any)
        echo CHtml::tag('option', array('value' => ''), CHtml::encode('Any'), true);

        // Pass all distinct trials that fall under the selected type. Perfectly OK for there to be no values here.
        foreach ($trials as $value => $key) {
            echo CHtml::tag('option', array('value' => $value), CHtml::encode($key), true);
        }
    }

    /**
     * Get a JSON list of all trials that match the search term that the patient refered to by
     * patient_id is not a member of. Only return those that 1) are not already associated with the patient,
     * 2) not already been chosen by the user 3) have a 'can_edit' permission for the user
     * @param $type string The trial type.
     */
    public function actionTrialAutocomplete($patient_id, $already_selected_ids, $term)
    {
        $already_selected_ids = json_decode($already_selected_ids);

        $criteria = new CDbCriteria();
        $criteria->distinct = true;
        $criteria->select = 't.id, t.name, t.description';
        $criteria->join = 'JOIN user_trial_assignment uta ON uta.trial_id = t.id JOIN trial_permission tp ON tp.id = uta.trial_permission_id';

        $criteria->addCondition('uta.user_id = :user_id');
        $criteria->addCondition('tp.can_edit = 1');
        $criteria->addCondition('t.is_open = 1 AND t.closed_date IS NULL');
        $criteria->addCondition('t.id NOT IN (SELECT trial_id FROM trial_patient WHERE patient_id = :patient_id)');
        $criteria->params = [':user_id' => Yii::app()->user->id, ':patient_id' => $patient_id];

        $criteria->addNotInCondition('t.id', $already_selected_ids);

        $criteria2 = new CDbCriteria();
        $criteria2->addSearchCondition('LOWER(t.name)', strtolower($term));
        $criteria2->addSearchCondition('LOWER(t.description)', strtolower($term), true, 'OR');
        $criteria->mergeWith($criteria2);

        $trials = array_map(
            static function ($trial) {
                return ['id' => $trial->id, 'label' => $trial->name];
            },
            Trial::model()->findAll($criteria)
        );

        $this->renderJSON($trials);
    }

    /**
     * Gets a JSON encoded list of users that can be assigned to the Trial and that match the search term.
     * Users will not be returned if they are already assigned to the trial, or if they don't have the "View Trial" permission.
     *
     * @param int $id The trial ID
     * @param string $term The term to search for
     * @throws CHttpException Thrown if an error occurs when loading the model
     */
    public function actionUserAutoComplete($id, $term)
    {
        $model = $this->loadModel($id);

        $res = array();
        $term = strtolower($term);

        $criteria = new CDbCriteria();
        $criteria->join = 'JOIN user_authentication user_auth ON t.id = user_auth.user_id';
        $criteria->compare('LOWER(user_auth.username)', $term, true, 'OR');
        $criteria->compare('LOWER(first_name)', $term, true, 'OR');
        $criteria->compare('LOWER(last_name)', $term, true, 'OR');
        $words = explode(' ', $term);
        if (count($words) > 1) {
            $first_criteria = new CDbCriteria();
            $first_criteria->compare('LOWER(first_name)', $words[0], true);
            $first_criteria->compare('LOWER(last_name)', implode(' ', array_slice($words, 1, count($words) - 1)), true);
            $last_criteria = new CDbCriteria();
            $last_criteria->compare('LOWER(first_name)', $words[count($words) - 1], true);
            $last_criteria->compare('LOWER(last_name)', implode(' ', array_slice($words, 0, count($words) - 2)), true);
            $first_criteria->mergeWith($last_criteria, 'OR');
            $criteria->mergeWith($first_criteria, 'OR');
        }
        $criteria->addCondition('t.id NOT IN (SELECT user_id FROM user_trial_assignment WHERE trial_id = ' . $model->id . ')');
        $criteria->addCondition('
            EXISTS(
                SELECT 1
                FROM authassignment aa
                JOIN authitemchild aic
                ON aic.parent = aa.itemname
                WHERE aa.userid = t.id
                AND aic.child = \'TaskViewTrial\'
            )');
        $criteria->compare('user_auth.active', true);

        /* @var User $user */
        foreach (User::model()->findAll($criteria) as $user) {
            $res[] = array(
                'id' => $user->id,
                'label' => $user->getFullNameAndTitle(),
                'value' => $user->getFullName(),
            );
        }

        $this->renderJSON($res);
    }

    /**
     * Change user position to the new value in POST
     *
     * @throws Exception Thrown if the change cannot be saved
     */

    public function actionChangeTrialUserPosition()
    {

        $user_id = $_POST['user_id'];
        $trial_id = $_POST['id'];
        $isTrue = $_POST['isTrue'];
        $column_name = $_POST['column_name'];

        $existing = UserTrialAssignment::model()->findAll('trial_id=? and '.$column_name.'=1', array($trial_id));
        if ($column_name==='is_principal_investigator'&&sizeof($existing)===1&&$existing[0]->user_id===$user_id) {
            $res = array(
            'Error' => 'At least one principal investigator should be selected.'
            );
            $this->renderJSON($res);
            Yii::app()->end();
        }
        $userPermission = UserTrialAssignment::model()->find('user_id=? and trial_id=?', array($user_id, $trial_id));
        $userPermission->$column_name = $isTrue;


        if (!$userPermission->save()) {
            throw new Exception('Unable to save principal investigator: '.print_r($userPermission->getErrors(), true));
        }
    }

    public function actionRenderPopups()
    {
        if (isset($_GET["trialId"])) {
            $trial = $this->loadModel($_GET["trialId"]);
            $sortDir = Yii::app()->request->getParam('sort_dir', '0') === '0' ? 'asc' : 'desc';
            $sortBy = Yii::app()->request->getParam('sort_by', 'Name');
            $dataProviders = $trial->getPatientDataProviders($sortBy, $sortDir);
            foreach ($dataProviders as $i => $dataProvider) {
                foreach ($dataProvider->getData() as $data) {
                    $this->renderPartial('application.widgets.views.PatientIcons', array('data' => ($data->patient), 'page' => 'TrialPatient'));
                }
            }
        }
    }
}
