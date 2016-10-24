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

    /**
     * Configure access rules
     *
     * @return array
     */
    public function accessRules()
    {
        return array(
            array('allow',
                'actions' => array('Edit'),
                'roles' => array('OprnEditGeneticPatient'),
            ),
            array('allow',
                'actions' => array('List'),
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
        if($action->id === "edit") {
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
        return parent::beforeAction($action);
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
        }
        $admin->setModelDisplayName('Patient');
        $admin->setEditFields(array(
            'patient_id' => array(
                'widget' => 'PatientLookup',
                'options' => CHtml::listData(Patient::model()->findAll(), 'id', 'fullName'),
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
            )
        ));
        $admin->editModel(false);
        if(Yii::app()->request->isPostRequest){
            foreach ($admin->getModel()->relationships as $relationship) {
                if(isset(Yii::app()->request->getPost('GeneticsPatient')['relationships'])){
                    $relationship->relationship_id = Yii::app()->request->getPost('GeneticsPatient')['relationships'][$relationship->related_to_id]['relationship_id'];
                    $relationship->save();
                }
            }
            $admin->redirect();
        }
    }

    /**
     * List the Genetic Patients
     */
    public function actionList()
    {
        $admin = new Crud(GeneticsPatient::model(), $this);
        $admin->setListFields(array(
            'id',
            'patient.fullName',
        ));
        $admin->getSearch()->addSearchItem('patient.contact.first_name', array('type' => 'compare', 'compare_to' => array('patient.contact.last_name')));
        $admin->getSearch()->setItemsPerPage($this->itemsPerPage);
        $admin->listModel();
    }
}