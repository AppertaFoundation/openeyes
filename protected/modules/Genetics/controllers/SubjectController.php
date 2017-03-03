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
                'roles' => array('OprnEditGeneticPatient'),
            ),
            array(
                'allow',
                'actions' => array('List', 'View'),
                'roles' => array('OprnViewGeneticPatient'),
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
        $assetPath = Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('application.modules.' . $this->getModule()->name . '.assets'));
        Yii::app()->clientScript->registerScriptFile($assetPath . '/js/subjects.js');
        Yii::app()->assetManager->registerCssFile('/components/font-awesome/css/font-awesome.css', null, 10);

        return parent::beforeAction($action);
    }

    public function actionView($id)
    {
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
        if ($id) {
            $admin->setModelId($id);
            $this->renderPatientPanel = true;
            $this->patient = $admin->getModel()->patient;
        }

        $admin->setModelDisplayName('Genetics Subject');
        $admin->setEditFields(array(
            'id' => 'label',
            'patient_id' => array(
                'widget' => 'PatientLookup',
                'extras' => true
            ),
            'gender_id' => array(
                'widget' => 'DropDownList',
                'options' => CHtml::listData(Gender::model()->findAll(), 'id', 'name'),
                'htmlOptions' => null,
                'hidden' => false,
                'layoutColumns' => null,
            ),
            'is_deceased' => 'checkbox',
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
            ),
            'pedigrees' => array(
                'widget' => 'MultiSelectList',
                'relation_field_id' => 'id',
                'label' => 'Pedigree',
                'options' => CHtml::encodeArray(
                    CHtml::listData(
                        Pedigree::model()->getAllIdAndText(),
                        'id',
                        'text'
                    )
                ),
                'through' => array(
                    'current' => GeneticsPatientPedigree::model()->findAllByAttributes(array('patient_id' => $id)),
                    'related_by' => 'pedigree_id',
                    'field' => 'status_id',
                    'options' => CHtml::encodeArray(
                        CHtml::listData(
                            PedigreeStatus::model()->findAll(),
                            'id',
                            'name'
                        )
                    ),
                ),
                'link' => '/Genetics/pedigree/edit/%s'
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

        $valid = $admin->editModel(false);

        if (Yii::app()->request->isPostRequest) {
            if ($valid) {
                $post = Yii::app()->request->getPost('GeneticsPatient', array());
                if (isset($post['pedigrees_through'])) {
                    foreach ($admin->getModel()->pedigrees as $pedigree) {
                        if (array_key_exists($pedigree->id, $post['pedigrees_through'])) {
                            $pedigreeStatus = GeneticsPatientPedigree::model()->findByAttributes(array(
                                'patient_id' => $id,
                                'pedigree_id' => $pedigree->id,
                            ));
                            if ($pedigreeStatus) {
                                $pedigreeStatus->status_id = $post['pedigrees_through'][$pedigree->id]['status_id'];
                                $pedigreeStatus->save();
                            }
                        }
                    }
                }

                $admin->redirect();
            } else {
                $admin->render($admin->getEditTemplate(), array('admin' => $admin, 'errors' => $admin->getModel()->getErrors()));
            }
        }
    }

    /**
     * List the Genetic Patients
     */
    public function actionList()
    {
        $admin = new Crud(GeneticsPatient::model(), $this);
        $admin->setModelDisplayName('Patients');
        $admin->setListFieldsAction('view');
        $admin->setListFields(array(
            'id',
            'patient.fullName',
        ));
        $admin->getSearch()->addSearchItem('patient.contact.first_name');
        $admin->getSearch()->addSearchItem('patient.contact.last_name');
        $admin->getSearch()->addSearchItem('patient.dob');
        $admin->getSearch()->addSearchItem('comments');
        $admin->getSearch()->addSearchItem('diagnoses.id', array('type' => 'disorder'));
        $admin->getSearch()->setItemsPerPage($this->itemsPerPage);
        $admin->getSearch()->setDefaultResults(false);
        $display_buttons = $this->checkAccess('OprnEditGeneticPatient');
        $admin->listModel($display_buttons);
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
}