<?php

/**
 * Class SubjectController
 *
 * Contains the actions pertaining to genetics subjects
 */
class SubjectController extends BaseModuleController
{
    public $layout = 'genetics';

    protected $itemsPerPage = 20;

    public $renderPatientPanel = false;

    public $patient;

    /**
     * Configure access rules
     *
     * @return array
     */
    public function accessRules()
    {
        return array(
            array(
                'allow',
                'actions' => array('Edit', 'EditStudyStatus'),
                'roles' => array('TaskEditGeneticPatient'),
            ),
            array(
                'allow',
                'actions' => array('List', 'View'),
                'roles' => array('TaskViewGeneticPatient'),
            ),
            array(
                'allow',
                'actions' => array('patientSearch'),
                'users' => array('@'),
            ),

        );
    }

    /**
     * @param CAction $action
     * @return bool
     */
    public function beforeAction($action)
    {
        if ($action->id === "edit") {
            $relations = CHtml::listData(GeneticsRelationship::model()->findAll(), 'id', 'relationship');
            $relationsForJson = array();
            foreach ($relations as $key => $relation) {
                $relationsForJson[] = array(
                    'id' => $key,
                    'name' => $relation,
                );
            }
            $this->jsVars['geneticsRelationships'] = $relationsForJson;
        }
        $assetPath = Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('application.modules.' . $this->getModule()->name . '.assets'), true);
        Yii::app()->clientScript->registerScriptFile($assetPath . '/js/subjects.js');

        return parent::beforeAction($action);
    }

    public function actionView($id)
    {
        $admin = new Crud(GeneticsPatient::model(), $this);
        $admin->setModelId($id);
        $this->renderPatientPanel = true;
        $this->patient = isset($admin->getModel()->patient) ? $admin->getModel()->patient : null;

        $genetics_patient = $this->loadModel($id);
        $this->render('view', array('model' => $genetics_patient));
    }
    /**
     * @param bool $id
     *
     * @throws CHttpException
     * @throws Exception
     */
    public function actionEdit($id = false)
    {
        $admin = new Crud(GeneticsPatient::model(), $this);
        $genetics_patient = GeneticsPatient::model()->findByPk($id);

        if (!$genetics_patient) {
            $genetics_patient = new GeneticsPatient();
        }
        if ($id) {
            $admin->setModelId($id);
            $this->renderPatientPanel = true;
            $this->patient = isset($admin->getModel()->patient) ? $admin->getModel()->patient : null;
            $htmlOptions = null;
        }

        if (isset($_GET['patient']) && ((int)$_GET['patient'] > 0) && ($this->patient == null)) {
            $this->patient = Patient::model()->findByPk((int)$_GET['patient']);

            $admin->getModel()->patient = $this->patient;
            $admin->getModel()->patient_id = $this->patient->id;

            switch ($this->patient->gender) {
                case 'M':
                    $genderValue = 1;
                    break;
                case 'F':
                    $genderValue = 2;
                    break;
                case 'U':
                    $genderValue = 3;
                    break;
                default:
                    $genderValue = 4;
            }
            $admin->getModel()->is_deceased = $this->patient->is_deceased;
            $htmlOptions = array('options' => array($genderValue => array('selected' => true)));
        }

        $admin->setModelDisplayName('Genetics Subject');

        $admin->setEditFields(array(
            'referer' => 'referer',
            'id' => 'label',
            'patient_id' => array(
                'widget' => 'PatientLookup',
                'extras' => true
            ),
            'gender_id' => array(
                'widget' => 'DropDownList',
                'options' => CHtml::listData(Gender::model()->findAll(), 'id', 'name'),
                'htmlOptions' => $htmlOptions,
                'hidden' => false,
                'layoutColumns' => null,
            ),

            //nope, just, nope, this must be stored in the patient table @TODO: remove this and from the genetics_patient database table
            //'is_deceased' => 'checkbox',

            'comments' => 'textarea',
            'family' => array(
                'widget' => 'CustomView',
                'viewName' => 'relationships',
                'viewArguments' => array(
                    'model' => $admin->getModel(),
                ),
            ),
            'diagnoses' => array(
                'widget' => 'DisorderLookup',
                'relation' => 'diagnoses',
                'options' => CommonOphthalmicDisorder::getList(Firm::model()->findByPk($this->selectedFirmId)),
                'empty_text' => 'Select a commonly used diagnosis'
            ),


            'pedigree' => array(
                'widget' => 'CustomView',
                'viewName' => 'application.modules.Genetics.views.subject._edit_pedigree',
                'viewArguments' => array(
                    'genetics_patient' => $genetics_patient,

                )
            ),

            'no_pedigree' => array(
                'widget' => 'CustomView',
                'viewName' => 'application.modules.Genetics.views.subject.nopedigree',
                'viewArguments' => array(
                    'genetics_patient' => $genetics_patient,
                )
            ),
            'previous_studies' => array(
                'widget' => 'CustomView',
                'viewName' => '//studies/list',
                'viewArguments' => array(
                    'model' => $admin->getModel(),
                    'list' => 'previous_studies',
                    'label' => 'Previous Studies',
                ),
            ),
            'rejected_studies' => array(
                'widget' => 'CustomView',
                'viewName' => '//studies/list',
                'viewArguments' => array(
                    'model' => $admin->getModel(),
                    'list' => 'rejected_studies',
                    'label' => 'Rejected Studies',
                ),
            ),
            'current_studies' => array(
                'widget' => 'CustomView',
                'viewName' => '//studies/list',
                'viewArguments' => array(
                    'model' => $admin->getModel(),
                    'list' => 'current_studies',
                    'label' => 'Current Studies',
                    'edit_status_url' => '/Genetics/subject/editStudyStatus/',
                ),
            ),
            'studies' => array(
                'widget' => 'MultiSelectList',
                'relation_field_id' => 'id',
                'label' => 'Study Proposal',
                'options' => CHtml::encodeArray(
                    CHtml::listData(
                        GeneticsStudy::model()->findAll(),
                        'id',
                        'name'
                    )
                ),
            ),
        ));

        $redirect = $id ? ('/Genetics/subject/view/' . $id) : '/patient/view/' . $_GET['patient'];
        $admin->setCustomCancelURL($redirect);
        try {
            $valid = $admin->editModel(false);

            if (Yii::app()->request->isPostRequest) {
                if ($valid) {
                    $post = Yii::app()->request->getPost('GeneticsPatient', array());
                    if (isset($post['pedigrees_through'])) {
                        foreach ($admin->getModel()->pedigrees as $pedigree) {
                            // NOTE that patient_id below is actually the genetic subject, the FK should be renamed at some point
                            // and this comment removed!
                            if (array_key_exists($pedigree->id, $post['pedigrees_through'])) {
                                $pedigreeStatus = GeneticsPatientPedigree::model()->findByAttributes(array(
                                    'patient_id' => $admin->getModel()->id,
                                    'pedigree_id' => $pedigree->id,
                                ));
                                if ($pedigreeStatus) {
                                    $pedigreeStatus->status_id = $post['pedigrees_through'][$pedigree->id]['status_id'];
                                    $pedigreeStatus->save();
                                }
                            }
                        }
                    }

                    Yii::app()->user->setFlash('success', "Patient Saved");
                    $url = '/Genetics/subject/view/' . $admin->getModel()->id;
                    $this->redirect($url);
                } else {
                    $admin->render($admin->getEditTemplate(), array('admin' => $admin, 'errors' => $admin->getModel()->getErrors()));
                }
            }
        } catch (Exception $e) {
            $bugreport = "=== GENETICS BUG REPORT ===\n";
            $bugreport .= "Subject id: {$genetics_patient->id}\n";
            $bugreport .= "Subject data:\n";
            $bugreport .= print_r($genetics_patient->getAttributes(), true) . "\n";
            $bugreport .= "POST data:\n";
            $bugreport .= print_r($_POST, true) . "\n";
            $filename = "genetics_bugreport_" . date("YmdHis") . ".log";
            file_put_contents(Yii::app()->basePath . "/runtime/logs/$filename", $bugreport);
            throw $e;
        }
    }

    /**
     * List the Genetic Patients
     */
    public function actionList()
    {
        if (empty($_GET)) {
            if (($data = YiiSession::get('genetics_patient_searchoptions'))) {
                $_GET = $data;
            }
            Audit::add('Genetics patient list', 'view');
        } else {
            Audit::add('Genetics patient list', 'search');
        }

        $path = Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('application.widgets'), true);
        Yii::app()->clientScript->registerScriptFile($path . '/js/DiagnosisSelection.js');

        $model = new GeneticsPatient('search');
        $model->unsetAttributes();  // clear any default values
        if (isset($_POST['GeneticsPatient'])) {
            //thanks for the awesome implementation of the //disorder/disorderAutoComplete.php
            //I cannot remove the 'search' from the name attribute without refactoring several things
            if (isset($_POST['search']['patient_disorder_id'])) {
                $_POST['GeneticsPatient']['patient_disorder_id'] = $_POST['search']['patient_disorder_id'];
            }

            $model->attributes = $_POST['GeneticsPatient'];

            YiiSession::set('genetics_patient_searchoptions', $_POST);
        }

        $this->render('list', array(
            'model' => $model,
        ));
    }


    /**
     * Edit the status of a study - subject relationship.
     *
     * @param $id
     * @throws CHttpException
     */
    public function actionEditStudyStatus($id)
    {
        $pivot = GeneticsStudySubject::model()->findByPk($id);
        if (!$pivot) {
            throw new CHttpException(404, 'No pivot found for relationship');
        }

        if (Yii::app()->request->isPostRequest) {
            $pivot->attributes = Yii::app()->request->getPost('GeneticsStudySubject');
            if ($pivot->is_consent_given) {
                $pivot->consent_given_on = date_create('now')->format('Y-m-d H:i:s');
            }
            if ($pivot->save() && Yii::app()->request->getPost('return')) {
                $this->redirect(Yii::app()->request->getPost('return'));
            }
        }

        $this->render('//studies/editStatus', array(
            'pivot' => $pivot,
        ));
    }

    /**
     * Returns the data model based on the primary key given in the GET variable.
     * If the data model is not found, an HTTP exception will be raised.
     *
     * @param int $id the ID of the model to be loaded
     */
    public function loadModel($id)
    {
        $model = GeneticsPatient::model()->findByPk((int) $id);
        if ($model === null) {
            throw new CHttpException(404, 'The requested page does not exist.');
        }

        return $model;
    }

    /**
     * Ajax search.
     */
    public function actionPatientSearch()
    {
        $term = trim(\Yii::app()->request->getParam('term', ''));
        $result = array();
        $patientSearch = new PatientSearch();

        //no PAS sync required at this stage
        $patientSearch->use_pas = false;

        if ($patientSearch->getValidSearchTerm($term)) {
            $dataProvider = $patientSearch->search($term);

            $criteria = $dataProvider->getCriteria();

            // only genetics patient can be searched and added as a relative
            $criteria->join .= ' JOIN genetics_patient ON t.id = genetics_patient.patient_id';
            $dataProvider->setCriteria($criteria);
            $dataProvider->setPagination(false);

            foreach ($dataProvider->getData() as $patient) {
                $pi = [];
                foreach ($patient->identifiers as $identifier) {
                    $pi[] = [
                        'title' => $identifier->patientIdentifierType->long_title ?? $identifier->patientIdentifierType->short_title,
                        'value' => $identifier->value
                    ];
                }

                $primary_identifier = PatientIdentifierHelper::getIdentifierForPatient(
                    Yii::app()->params['display_primary_number_usage_code'],
                    $patient->id,
                    \Institution::model()->getCurrent()->id,
                    Yii::app()->session['selected_site_id']
                );

                $result[] = array(
                    'id' => $patient->id,
                    'genetics_patient_id' => $patient->geneticsPatient->id,
                    'first_name' => $patient->first_name,
                    'last_name' => $patient->last_name,
                    'age' => ($patient->isDeceased() ? 'Deceased' : $patient->getAge()),
                    'gender' => $patient->getGenderString(),
                    'genderletter' => $patient->gender,
                    'dob' => ($patient->dob) ? $patient->NHSDate('dob') : 'Unknown',
                    // in script.js we override the behaviour for showing search results and its require the label key to be present
                    'label' => $patient->first_name . ' ' . $patient->last_name . ' (' . PatientIdentifierHelper::getIdentifierPrompt($primary_identifier) . PatientIdentifierHelper::getIdentifierValue($primary_identifier) . ')',
                    'is_deceased' => $patient->is_deceased,
                    'patient_identifiers' => $pi,
                    'primary_patient_identifiers' => [
                        'title' => PatientIdentifierHelper::getIdentifierPrompt($primary_identifier),
                        'value' => PatientIdentifierHelper::getIdentifierValue($primary_identifier)
                    ]

                );
            }
        }
        echo CJavaScript::jsonEncode($result);
        Yii::app()->end();
    }
}
