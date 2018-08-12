<?php

/**
 * Class TrialController
 */
class TrialController extends BaseModuleController
{
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
                'actions' => array('getTrialList', 'permissions'),
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
                'expression' => '$user->checkAccess("TaskViewTrial") && Trial::checkTrialAccess($user, Yii::app()->getRequest()->getQuery("id"), UserTrialPermission::PERMISSION_VIEW)',
            ),
            array(
                'allow',
                'actions' => array('update', 'addPatient', 'removePatient'),
                'expression' => '$user->checkAccess("TaskViewTrial") && Trial::checkTrialAccess($user, Yii::app()->getRequest()->getParam("id"), UserTrialPermission::PERMISSION_EDIT)',
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
                ),
                'expression' => '$user->checkAccess("TaskViewTrial") && Trial::checkTrialAccess($user, Yii::app()->getRequest()->getPost("id"), UserTrialPermission::PERMISSION_MANAGE)',
            ),
            array(
                'deny',  // deny all users
                'users' => array('*'),
            ),
        );
    }

    /**
     * @param CAction $action The action being called
     * @return bool Whether the action is
     */
    public function beforeAction($action)
    {
        $assetPath = Yii::app()->assetManager->publish(Yii::getPathOfAlias('application.modules.OETrial.assets'));
        Yii::app()->clientScript->registerCssFile($assetPath . '/css/module.css');

        return parent::beforeAction($action);
    }

    /**
     * Displays a particular model.
     * @param int $id the ID of the model to be displayed
     * @throws CException Thrown if an error occurs when loading the data providers
     */
    public function actionView($id)
    {
        $trial = $this->loadModel($id);
        $report = new OETrial_ReportTrialCohort();

        $sortDir = Yii::app()->request->getParam('sort_dir', '0') === '0' ? 'asc' : 'desc';
        $sortBy = null;

        switch (Yii::app()->request->getParam('sort_by', -1)) {
            case 1:
            default:
                $sortBy = 'name';
                break;
            case 2:
                $sortBy = 'gender';
                break;
            case 3:
                $sortBy = 'age';
                break;
            case 4:
                $sortBy = 'ethnicity';
                break;
            case 5:
                $sortBy = 'external_reference';
                break;
            case 6:
                $sortBy = 'treatment_type';
                break;
        }

        $this->breadcrumbs = array(
            'Trials' => array('index'),
            $trial->name,
        );

        $this->render('view', array(
            'trial' => $trial,
            'report' => $report,
            'dataProviders' => $trial->getPatientDataProviders($sortBy, $sortDir),
            'sort_by' => (int)Yii::app()->request->getParam('sort_by', null),
            'sort_dir' => (int)Yii::app()->request->getParam('sort_dir', null),
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
     */
    public function actionCreate()
    {
        $model = new Trial;
        $model->setScenario('manual');
        $model->is_open = 1;
        $model->trial_type = Trial::TRIAL_TYPE_NON_INTERVENTION;
        $model->owner_user_id = Yii::app()->user->id;
        $model->pi_user_id = Yii::app()->user->id;
        $model->started_date = date('d/m/Y');

        $this->performAjaxValidation($model);

        if (isset($_POST['Trial'])) {
            $model->attributes = $_POST['Trial'];
            if ($model->save()) {
                $this->redirect(array('view', 'id' => $model->id));
            }
        }

        $this->breadcrumbs = array(
            'Trials' => array('index'),
            'Create a Trial',
        );

        $this->render('create', array(
            'model' => $model,
        ));
    }

    /**
     * Updates a particular model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id the ID of the model to be updated
     * @throws CHttpException Thrown if the model cannot be loaded
     */
    public function actionUpdate($id)
    {
        $model = $this->loadModel($id);
        $model->setScenario('manual');

        if (isset($_POST['Trial'])) {
            $model->attributes = $_POST['Trial'];
            if ($model->save()) {
                $this->redirect(array('view', 'id' => $model->id));
            }
        }

        $this->breadcrumbs = array(
            'Trials' => array('index'),
            $model->name => array('view', 'id' => $model->id),
            'Edit',
        );

        $this->render('update', array(
            'model' => $model,
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
            case 0:
                $sortBy = 'LOWER(t.name)';
                break;
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

        $condition = "trial_type = :trialType AND EXISTS (
                        SELECT * FROM user_trial_permission utp WHERE utp.user_id = :userId AND utp.trial_id = t.id
                    ) ORDER BY $sortBy $sortDir, LOWER(t.name) ASC";

        $interventionTrialDataProvider = new CActiveDataProvider('Trial', array(
            'criteria' => array(
                'condition' => $condition,
                'join' => 'JOIN user u ON u.id = t.owner_user_id',
                'params' => array(
                    ':userId' => Yii::app()->user->id,
                    ':trialType' => Trial::TRIAL_TYPE_INTERVENTION,
                ),
            ),
        ));

        $nonInterventionTrialDataProvider = new CActiveDataProvider('Trial', array(
            'criteria' => array(
                'condition' => $condition,
                'join' => 'JOIN user u ON u.id = t.owner_user_id',
                'params' => array(
                    ':userId' => Yii::app()->user->id,
                    ':trialType' => Trial::TRIAL_TYPE_NON_INTERVENTION,
                ),
            ),
        ));

        $this->breadcrumbs = array(
            'Trials',
        );

        $this->render('index', array(
            'interventionTrialDataProvider' => $interventionTrialDataProvider,
            'nonInterventionTrialDataProvider' => $nonInterventionTrialDataProvider,
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
        $model = Trial::model()->findByPk($id);

        $permissionDataProvider = new CActiveDataProvider('UserTrialPermission', array(
            'criteria' => array(
                'condition' => 'trial_id = :trialId',
                'params' => array(
                    ':trialId' => $model->id,
                ),
                'order' => 'permission DESC',
            ),
        ));

        $newPermission = new UserTrialPermission();

        $this->breadcrumbs = array(
            'Trials' => array('index'),
            $model->name => array('view', 'id' => $model->id),
            'Permissions',
        );

        $this->render('permissions', array(
            'model' => $model,
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
        $trial = $this->loadModel($_POST['id']);
        /* @var Patient $patient */
        $patient = Patient::model()->findByPk($_POST['patient_id']);
        $trial->addPatient($patient, TrialPatient::STATUS_SHORTLISTED);
    }

    /**
     * @throws CHttpException Raised when the record cannot be found
     * @throws Exception Raised when an error occurs when removing the record
     */
    public function actionRemovePatient()
    {
        $trial = $this->loadModel($_POST['id']);
        $trial->removePatient($_POST['patient_id']);
    }

    /**
     * Creates a new Trial Permission using values in $_POST
     *
     * @throws Exception Thrown if the permission couldn't be saved
     */
    public function actionAddPermission()
    {
        $trial = $this->loadModel($_POST['id']);
        $result = $trial->addUserPermission($_POST['user_id'], $_POST['permission'], $_POST['role']);
        echo $result;
    }

    /**
     * Removes a UserTrialPermission
     *
     * @throws CHttpException Thrown if the permission cannot be found
     * @throws Exception Thrown if the permission cannot be deleted
     */
    public function actionRemovePermission()
    {
        $trial = $this->loadModel($_POST['id']);
        $result = $trial->removeUserPermission($_POST['permission_id']);
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

    public function actionClose()
    {
        $trial = $this->loadModel($_POST['id']);
        $trial->close();
        $this->redirect($this->createUrl('view', array('id' => $trial->id)));
    }

    public function actionReopen()
    {
        $trial = $this->loadModel($_POST['id']);
        $trial->reopen();
        $this->redirect($this->createUrl('view', array('id' => $trial->id)));
    }


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
     * Gets a JSON encoded list of users that can be assigned to the Trial and that match the search term.
     * Users will not be returned if they are already assigned to the trial, or if they don't have the "View Trial" permission.
     *
     * @param int $id The trial ID
     * @param string $term The term to search for
     * @return string A JSON encoded array of users with id, label, username and value
     * @throws CHttpException Thrown if an error occurs when loading the model
     */
    public function actionUserAutoComplete($id, $term)
    {
        $model = $this->loadModel($id);

        $res = array();
        $term = strtolower($term);

        $criteria = new \CDbCriteria;
        $criteria->compare('LOWER(username)', $term, true, 'OR');
        $criteria->compare('LOWER(first_name)', $term, true, 'OR');
        $criteria->compare('LOWER(last_name)', $term, true, 'OR');
        $words = explode(' ', $term);
        if (count($words) > 1) {
            $first_criteria = new \CDbCriteria();
            $first_criteria->compare('LOWER(first_name)', $words[0], true);
            $first_criteria->compare('LOWER(last_name)', implode(' ', array_slice($words, 1, count($words) - 1)), true);
            $last_criteria = new \CDbCriteria();
            $last_criteria->compare('LOWER(first_name)', $words[count($words) - 1], true);
            $last_criteria->compare('LOWER(last_name)', implode(' ', array_slice($words, 0, count($words) - 2)), true);
            $first_criteria->mergeWith($last_criteria, 'OR');
            $criteria->mergeWith($first_criteria, 'OR');
        }
        $criteria->addCondition('id NOT IN (SELECT user_id FROM user_trial_permission WHERE trial_id = ' . $model->id . ')');
        $criteria->addCondition('
            EXISTS(
                SELECT 1
                FROM authassignment aa
                JOIN authitemchild aic
                ON aic.parent = aa.itemname
                WHERE aa.userid = id 
                AND aic.child = \'TaskViewTrial\'
            )');
        $criteria->compare('active', true);

        /* @var User $user */
        foreach (User::model()->findAll($criteria) as $user) {

            $res[] = array(
                'id' => $user->id,
                'label' => $user->getFullNameAndTitle(),
                'value' => $user->getFullName(),
                'username' => $user->username,
            );
        }

        echo CJSON::encode($res);
    }

    /**
     * Change the pi_user_Id to the new value in POST
     *
     * @throws CHttpException Thrown if the change cannot be saved
     */
    public function actionChangePi()
    {
        $trial = $this->loadModel($_POST['id']);
        $trial->pi_user_id = $_POST['user_id'];

        if (!$trial->save()) {
            throw new CHttpException(500, 'Unable to change.' . Trial::model()->getAttributeLabel('pi_user_id'));
        }
    }


    /**
     * Changes the coordinator_user_id to the new value in POST
     *
     * @throws CHttpException Thrown if an error occurs when saving the change
     */
    public function actionChangeCoordinator()
    {
        $trial = $this->loadModel($_POST['id']);
        $trial->coordinator_user_id = $_POST['user_id'];

        if (!$trial->save()) {
            throw new CHttpException(500,
                'Unable to change ' . Trial::model()->getAttributeLabel('coordinator_user_id'));
        }
    }
}
