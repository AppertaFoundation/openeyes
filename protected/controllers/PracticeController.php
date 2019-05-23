<?php

class PracticeController extends BaseController
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
                // allow users with either the TaskViewPractice or TaskCreatePractice roles to view Practice data
                'actions' => array('index', 'view'),
                'roles' => array('TaskViewPractice', 'TaskCreatePractice'),
            ),
            array(
                'allow',
                // allow users with either the TaskCreatePractice or TaskAddPatient roles to perform 'create' actions
                'actions' => array('create','createAssociate'),
                'roles' => array('TaskCreatePractice', 'TaskAddPatient'),
            ),
            array(
                'allow', // allow users with the TaskCreatePractice role to perform 'update' actions
                'actions' => array('update'),
                'roles' => array('TaskCreatePractice'),
            ),
            array(
                'deny',  // deny all other users
                'users' => array('*'),
            ),
        );
    }

    /**
     * Displays a particular model.
     * @param integer $id the ID of the model to be displayed
     */
    public function actionView($id)
    {
        $this->render('view', array(
            'model' => $this->loadModel($id),
        ));
    }

    /**
     * Creates a new model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @param $context string The context through which create action is invoked. Is either 'AJAX' or null.
     */
    public function actionCreate($context = null)
    {

        $contact = new Contact('manage_practice');
        $address = new Address('manage_practice');
        $practice = new Practice('manage_practice');
        $this->performAjaxValidation(array($practice, $contact, $address));
        if (isset($_POST['Contact'])) {
            $contact->attributes = $_POST['Contact'];
            $address->attributes = $_POST['Address'];
            $practice->attributes = $_POST['Practice'];
            list($contact, $practice, $address) = $this->performPracticeSave($contact, $practice, $address,
                $context === 'AJAX');
        }
        if ($context === 'AJAX') {
            if (isset($practice->contact)){
                $displayText = ($practice->contact->first_name ? ($practice->contact->first_name . ', ') : '') . $practice->getAddressLines();
                echo CJSON::encode(array(
                    'label' => $displayText,
                    'value' => $displayText,
                    'id' => $practice->getPrimaryKey(),
                ));
            }
        } else {
            $this->render('create', array(
                'model' => $practice,
                'address' => $address,
                'contact' => $contact,
            ));
        }
    }


    public function actionCreateAssociate(){
        $contact = new Contact('manage_practice');
        $address = new Address('manage_practice');
        $practice = new Practice('manage_practice');
        $this->performAjaxValidation(array($practice, $contact, $address));


        if (isset($_POST['Contact'])) {
            $contact->attributes = $_POST['Contact'];
            $address->attributes = $_POST['Address'];
            $practice->attributes = $_POST['Practice'];
            list($contact, $practice, $address) = $this->performPracticeSave($contact, $practice, $address,
                true);
        }


        if (isset($practice->contact)){
            $practice_contact_associate = new ContactPracticeAssociate();
            $practice_contact_associate->gp_id = $_POST['PracticeAssociate']['gp_id'];
            $practice_contact_associate->practice_id = $practice->id;
            if ($practice_contact_associate->save()){
                echo CJSON::encode(array(
                    'gp_id' => $practice_contact_associate->gp->getPrimaryKey(),
                ));
            }
        }

    }


    /**
     * @param Contact $contact
     * @param Practice $practice
     * @param Address $address
     * @param bool $isAjax
     * @throws CHttpException
     * @return array
     */
    public function performPracticeSave(Contact $contact, Practice $practice, Address $address, $isAjax = false)
    {
        $action = $practice->isNewRecord ? 'add' : 'edit';
        $transaction = Yii::app()->db->beginTransaction();
        try {
            if ($contact->save()) {
                $practice->contact_id = $contact->getPrimaryKey();
                $address->contact_id = $contact->id;
                if ($practice->save()) {
                    if (isset($address)) {
                        if ($address->save()) {
                            $transaction->commit();
                            Audit::add('Practice', $action . '-practice',
                                "Practice manually [id: $practice->id] {$action}ed.");
                            if (!$isAjax) {
                                $this->redirect(array('view', 'id' => $practice->id));
                            }

                        } else {
                            $address->validate();
                            $address->clearErrors('contact_id');
                            if ($isAjax) {
                                throw new CHttpException(400, CHtml::errorSummary($address));
                            }
                            $transaction->rollback();
                        }
                    } else {
                        $transaction->commit();
                        Audit::add('Practice', $action . '-practice',
                            "Practice manually [id: $practice->id] {$action}ed.");
                        if (!$isAjax) {
                            $this->redirect(array('view', 'id' => $practice->id));
                        }
                    }
                } else {
                    $address->validate();
                    $address->clearErrors('contact_id');
                    $practice->clearErrors('contact_id');
                    if ($isAjax) {
                        throw new CHttpException(400,CHtml::errorSummary(array($practice,$address)) );
                    }
                    $transaction->rollback();
                }
            } else {
                $address->validate();
                $address->clearErrors('contact_id');
                if ($isAjax) {
                    throw new CHttpException(400,CHtml::errorSummary(array($contact,$address)));
                }
                $transaction->rollback();
            }
        } catch (Exception $ex) {
            OELog::logException($ex);
            $transaction->rollback();
            if ($isAjax) {
                echo $ex->getMessage();
            }
        }

        return array($contact, $practice, $address);
    }

    /**
     * Updates a particular model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id the ID of the model to be updated
     */
    public function actionUpdate($id)
    {
        $model = $this->loadModel($id);
        $contact = $model->contact;
        $address = isset($contact->address) ? $contact->address : new Address();
        $contact->setScenario('manage_practice');
        $address->setScenario('manage_practice');
        $model->setScenario('manage_practice');

        $this->performAjaxValidation($contact);

        if (isset($_POST['Address']) || isset($_POST['Contact'])) {

            $contact->attributes = $_POST['Contact'];
            $address->attributes = $_POST['Address'];
            $model->attributes = $_POST['Practice'];
            list($contact, $model, $address) = $this->performPracticeSave($contact, $model, $address);
        }

        $this->render('update', array(
            'model' => $model,
            'address' => $address,
            'contact' => $contact,
        ));
    }

    /**
     * Lists all models.
     *
     * @param string $search_term
     */
    public function actionIndex($search_term = null)
    {
        $criteria = new CDbCriteria();
        $criteria->together = true;
        $criteria->with = array('contact');
        $criteria->order = 'first_name';

        if ($search_term !== null) {
            $search_term = strtolower($search_term);
            $criteria->addSearchCondition('LOWER(last_name)', $search_term, true, 'OR');
            $criteria->addSearchCondition('LOWER(first_name)', $search_term, true, 'OR');
            $criteria->addSearchCondition('LOWER(phone)', $search_term, true, 'OR');
            $criteria->addSearchCondition('LOWER(code)', $search_term, true, 'OR');
            $criteria->addSearchCondition('LOWER(address1)', $search_term, true, 'OR');
            $criteria->addSearchCondition('LOWER(address2)', $search_term, true, 'OR');
            $criteria->addSearchCondition('LOWER(city)', $search_term, true, 'OR');
            $criteria->addSearchCondition('LOWER(postcode)', $search_term, true, 'OR');

        }
        $dataProvider = new CActiveDataProvider('Practice', array(
            'criteria' => $criteria,
        ));
        $this->render('index', array(
            'dataProvider' => $dataProvider,
            'search_term' => $search_term,
        ));
    }

    /**
     * Returns the data model based on the primary key given in the GET variable.
     * If the data model is not found, an HTTP exception will be raised.
     * @param integer $id the ID of the model to be loaded
     * @return Practice|CActiveRecord the loaded model
     * @throws CHttpException
     */
    public function loadModel($id)
    {
        $model = Practice::model()->findByPk($id);
        if ($model === null) {
            throw new CHttpException(404, 'The requested page does not exist.');
        }

        return $model;
    }

    /**
     * Performs the AJAX validation.
     *
     * @param CModel|array $model the model to be validated
     */
    protected function performAjaxValidation($model)
    {
            if (isset($_POST['ajax']) && $_POST['ajax'] === 'practice-form') {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }
    }
}
