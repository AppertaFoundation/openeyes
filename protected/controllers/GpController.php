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
     * @var string to display if user has not selected a role while saving the Gp or Contact.
     */
    public static $errorRoleNotSelected='Please select a Role.';

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
                'actions' => array('contactLabelList', 'gpList'),
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
        $gp = $this->loadModel($id);

        $criteria = new CDbCriteria();
        $criteria->addCondition('gp_id='.$id);
        $dataProvider = new CActiveDataProvider('ContactPracticeAssociate', array(
            'criteria' => $criteria,
        ));

        $this->render('view', array(
            'model' => $gp,
            'dataProvider' => $dataProvider,
        ));
    }

    /**
     * Creates a new model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @param $context string The context through which create action is invoked.
     */
    public function actionCreate()
    {
        Yii::app()->assetManager->RegisterScriptFile('js/Gp.js');
        $gp = new Gp();

        // manage_gp_role_req is used for CERA for validating roles as well.
        $contact = new Contact(Yii::app()->params['use_contact_practice_associate_model'] === true ? 'manage_gp_role_req' : 'manage_gp');

        if (isset($_POST['Contact'])) {
            $contact->attributes = $_POST['Contact'];
            $contact->created_institution_id = Yii::app()->session['selected_institution_id'];
            $gp->is_active = $_POST['Gp']['is_active'];
            $this->performAjaxValidation($contact);
            list($contact, $gp) = $this->performGpSave($contact, $gp);

            if ($gp->id) {
                $this->redirect(array('view', 'id' => $gp->id));
            }
        }

        $this->render('create', array(
           'model' => $contact,
           'gp' => $gp,
           'context' => null
        ));
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

        $valid=true;

        $contact = $model->contact;
        $cpas = $model->contactPracticeAssociate;
        $contact->setScenario(Yii::app()->params['use_contact_practice_associate_model'] === true ? 'manage_gp_role_req' : 'manage_gp');
        $this->performAjaxValidation($contact);
        $this->performAjaxValidation($model);

        if (isset($_POST['Contact']) && isset($_POST['Gp']['is_active'])) {
            $contact->attributes = $_POST['Contact'];
            $model->is_active = $_POST['Gp']['is_active'];
            $this->performAjaxValidation($contact);
            $this->performAjaxValidation($model);

            if (isset($_POST['ContactPracticeAssociate'])) {
                $index = 0;
                foreach ($_POST['ContactPracticeAssociate'] as $cpa) {
                    $cpas[$index]->provider_no = $cpa['provider_no'];
                    $valid=$cpas[$index]->validate() && $valid;
                    for ($i=0; $i<$index; $i++) {
                        if ($cpas[$index]->provider_no == $cpas[$i]->provider_no && $cpas[$index]->provider_no != '') {
                            $valid = false;
                            $cpas[$index]->addError('provider_no', 'Duplicate provider number.');
                        }
                    }
                    $index++;
                }
            }

            if ($contact->validate() && $valid) {
                foreach ($cpas as $cpa) {
                    $update = Yii::app()->db->createCommand()
                        ->update('contact_practice_associate', array('provider_no' => !empty($cpa->provider_no) ? $cpa->provider_no : null), 'id=:id', array(':id'=>$cpa->id));
                }
                list($contact, $model) = $this->performGpSave($contact, $model);
            }
        }

        $this->render('update', array(
            'model' => $contact,
            'gp' => $model,
            'cpas' => $cpas,
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
        foreach ($labels as $label) {
            $output[] = array(
                'label' => $label->name,
                'value' => $label->name,
                'id' => $label->id
            );
        }

        $this->renderJSON($output);

        Yii::app()->end();
    }

    /**
     * List all gp's that contain the $term
     * @param string $term what to search on
     */
    public function actionGpList($term)
    {
        $labels= Yii::app()->db->createCommand()
            ->select('g.id, c.first_name, c.last_name, cl.name as role')
            ->from('gp g')
            ->join('contact c', 'c.id = g.contact_id')
            ->join('contact_label cl', 'cl.id = c.contact_label_id')
            ->where(
                '(LOWER(c.first_name) LIKE LOWER(:first_name)) OR (LOWER(c.last_name) LIKE LOWER(:last_name))',
                array(':first_name' => "%{$term}%", ':last_name' => "%{$term}%")
            )
            ->queryAll();

        $output = array();
        foreach ($labels as $label) {
            if ($this->loadModel($label['id'])->is_active) {
                $output[] = array(
                  'id' => $label['id'],
                  'label' => $label['first_name'].' '. $label['last_name'].' - '.$label['role'],
                  'value' => $label['first_name'].' '. $label['last_name'].' - '.$label['role']
                );
            }
        }

        $this->renderJSON($output);

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
     * @param Contact $contact
     * @param Gp $gp
     * @param bool $isAjax
     * @return array
     * @throws CException
     */
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
                        $this->redirect(array('view','id'=>$gp->id));
                    }
                } else {
                    if ($isAjax) {
                        throw new CHttpException(400, "Unable to save Practitioner contact");
                    }
                    $transaction->rollback();
                }
            } else {
                if ($isAjax) {
                    throw new CHttpException(400, CHtml::errorSummary($contact));
                }
                $transaction->rollback();
            }
        } catch (Exception $ex) {
            OELog::logException($ex);
            $transaction->rollback();
            if ($isAjax) {
                if (strpos($ex->getMessage(), 'errorSummary')) {
                    echo $ex->getMessage();
                } else {
                    echo "<div class=\"errorSummary\"><p>Unable to save Practitioner information, please contact your support.</p></div>";
                }
            }
        }
        return array($contact, $gp);
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
