<?php
/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
Yii::import('application.controllers.*');

/**
 * Class GpController
 *
 * @property Episode $episode
 * @property Patient $patient
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
                'actions' => array('create', 'validateGpContact'),
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
            list($contact, $gp) = $gp->performGpSave($contact, $gp, $context === 'AJAX');
        }

        if ($context === 'AJAX') {
            if(isset($gp->contact)){
                echo CJSON::encode(array(
                    'label' => $contact->getFullName(),
                    'value' => $gp->getFullName(),
                    'id'    => $gp->getPrimaryKey(),
                ));
            }
        } else {
            $this->render('create', array(
                'model' => $contact,
                'context' => null
            ));
        }
    }

    /**
     * Just to validate the Contact model on the Add Patient Screen and show the error messages to the user (if any),
     * when user presses Next in the popup for adding a new contact or Referring Practitioner.
     */
    public function actionValidateGpContact()
    {
        // manage_gp is used for validation purposes.
        $contact = new Contact('manage_gp');

        if (isset($_POST['Contact'])) {
            if (strpos(CActiveForm::validate($contact), 'cannot be blank' ) !== false) {
                echo CHtml::errorSummary($contact);
            } else {
                Yii::app()->session->add('contactForm',$_POST['Contact']);
            }
        }
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

            list($contact, $model) = $model->performGpSave($contact, $model);
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
