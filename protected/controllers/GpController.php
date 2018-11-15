<?php
/**
 * Created by PhpStorm.
 * User: Fivium
 * Date: 6/11/2018
 * Time: 1:59 PM
 */

class GpController extends BaseController
{
    /**
     * @return array action filters
     */
    public function filters()
    {
        return array(
            'accessControl', // perform access control for CRUD operations
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
                'allow',  // allow users with either the TaskViewGp or TaskCreateGp roles to view GP data
                'actions' => array('index', 'view'),
                'roles' => array('TaskViewGp', 'TaskCreateGp')
            ),
            array(
                'allow', // allow users with either the TaskCreateGp or TaskAddPatient roles to perform 'create' actions
                'actions' => array('create'),
                'roles' => array('TaskCreateGp', 'TaskAddPatient'),
            ),
            array(
                'allow', // allow users with the TaskCreateGp role to perform 'update' actions
                'actions' => array('update'),
                'roles' => array('TaskCreateGp'),
            ),
            array(
                'allow', // allow anyone to search for contact labels
                'actions' => array('contactLabelList'),
                'users' => array('*')
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

        Yii::app()->assetManager->RegisterScriptFile('js/Gp.js');
        $gp = new Gp();
        $contact = new Contact('manage_gp');

        if (isset($_POST['Contact'])) {
            $contact->attributes = $_POST['Contact'];
            $this->performAjaxValidation($contact, $context);
            list($contact, $gp) = $this->performGpSave($contact, $gp, $context === 'AJAX');
        }

        if ($context === 'AJAX') {
            echo CJSON::encode(array(
                'label' => $contact->getFullName(),
                'value' => $gp->getFullName(),
                'id'    => $gp->getPrimaryKey(),
            ));
        } else {
            $this->renderpartial('_form', array(
                'model' => $contact,
                'context' => null
            ));
        }
    }

    public function performGpSave(Contact $contact, Gp $gp, $isAjax = false)
    {
        $action = $gp->isNewRecord ? 'add' : 'edit';
        $transaction = Yii::app()->db->beginTransaction();

        try {
            if ($contact->save()) {
                // No need to re-set these values if they already exist.
                if ($gp->contact_id === null) {
                    $gp->contact_id = $contact->getPrimaryKey();
                }

                if ($gp->nat_id === null) {
                    $gp->nat_id = 0;
                }

                if ($gp->obj_prof === null) {
                    $gp->obj_prof = 0;
                }

                if ($gp->save()) {
                    $transaction->commit();
                    Audit::add('Gp', $action . '-gp', "Practitioner manually [id: $gp->id] {$action}ed.");
                    if (!$isAjax) {
                        $this->redirect(array('view', 'id' => $gp->id));
                    }
                } else {
                    if ($isAjax) {
                        throw new CHttpException(400,"Unable to save Practitioner contact");
                    }
                    $transaction->rollback();
                }
            } else {
                if ($isAjax) {
                    throw new CHttpException(400,"Unable to save Practitioner contact");
                }
                $transaction->rollback();
            }
        } catch (Exception $ex) {
            OELog::logException($ex);
            $transaction->rollback();
            if ($isAjax) {
                throw new CHttpException(400,"Unable to save Practitioner contact");
            }
        }

        return array($contact, $gp);
    }

    /**
     * Updates a particular model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id the ID of the model to be updated
     */
    public function actionUpdate($id)
    {
        Yii::app()->assetManager->RegisterScriptFile('js/Gp.js');
        $model = $this->loadModel($id);
        $contact = $model->contact;
        $contact->setScenario('manage_gp');

        $this->performAjaxValidation($contact);

        if (isset($_POST['Contact'])) {

            $contact->attributes = $_POST['Contact'];
            if ($_POST['Contact']['contact_label_id'] == -1)
            {
                $contact->contact_label_id = null;
            }

            list($contact, $model) = $this->performGpSave($contact, $model);
        }

        $this->render('update', array(
            'model' => $contact,
        ));
    }

    /**
     * List all contact labels that contain the $term
     * @param string $term what to search on
     */
    public function actionContactLabelList($term)
    {
        $criteria = new CDbCriteria;
        $criteria->addSearchCondition('LOWER(name)', strtolower($term), true, 'OR');
        $labels = ContactLabel::model()->findAll($criteria);

        $output = array();
        foreach($labels as $label){
            $output[] = array(
                'label' => $label->name,
                'value' => $label->name,
                'id' => $label->id
            );
        }

        echo CJSON::encode($output);

        Yii::app()->end();
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
        $criteria->order = 'last_name';

        if ($search_term !== null) {
            $criteria->addSearchCondition('LOWER(last_name)', strtolower($search_term), true, 'OR');
            $criteria->addSearchCondition('LOWER(first_name)', strtolower($search_term), true, 'OR');
            $criteria->addSearchCondition('LOWER(primary_phone)', strtolower($search_term), true, 'OR');
        }
        $dataProvider = new CActiveDataProvider('Gp', array(
            'criteria' => $criteria
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
     * @return Gp the loaded model
     * @throws CHttpException
     */
    public function loadModel($id)
    {
        $model = Gp::model()->findByPk($id);
        if ($model === null) {
            throw new CHttpException(404, 'The requested page does not exist.');
        }

        return $model;
    }

    /**
     * Performs the AJAX validation.
     *
     * @param CModel $model the model to be validated
     */
    protected function performAjaxValidation($model)
    {
        if (isset($_POST['ajax']) && $_POST['ajax'] === 'gp-form') {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }
    }
}
