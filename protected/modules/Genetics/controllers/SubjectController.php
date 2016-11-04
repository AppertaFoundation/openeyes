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
        Yii::app()->assetManager->registerScriptFile('/js/handleButtons.js');
        Yii::app()->assetManager->registerCssFile('/components/font-awesome/css/font-awesome.css', null, 10);

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
        $admin->setModelDisplayName('Genetics Subject');
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
            $relationshipPost = Yii::app()->request->getPost('GeneticsPatient', array());
            if(isset($relationshipPost['relationships'])){
                foreach ($admin->getModel()->relationships as $relationship) {
                    if(array_key_exists($relationship->related_to_id, $relationshipPost['relationships'])){
                        $relationship->relationship_id = $relationshipPost['relationships'][$relationship->related_to_id]['relationship_id'];
                        $relationship->save();
                    }
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