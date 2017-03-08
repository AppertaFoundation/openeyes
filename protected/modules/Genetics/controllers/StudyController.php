<?php

/**
 * Class GeneController
 *
 * Contains the actions pertaining to genes
 */
class StudyController extends BaseModuleController
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
                'actions' => array('List', 'View'),
                'roles' => array('TaskViewGeneticStudy'),
            ),
            array('allow',
                'actions' => array('Edit'),
                'roles' => array('TaskEditGeneticStudy'),
            ),
        );
    }
    
    public function actionView($id)
    {
        $genetics_study = $this->loadModel($id);
        $this->render('view', array('model' => $genetics_study));
    }

    /**
     * @param bool $id
     *
     * @throws CHttpException
     * @throws Exception
     */
    public function actionEdit($id = false)
    {
        $admin = new Crud(GeneticsStudy::model(), $this);
        if ($id) {
            $admin->setModelId($id);
        }

        $admin->setModelDisplayName('Study');
        $admin->setEditFields(array(
            'name' => 'label',
            'end_date' => 'label',
            'subjects' => array(
                'widget' => 'CustomView',
                'viewName' => '/default/subjectList',
                'viewArguments' => array(
                    'subjects' => $admin->getModel()->subjects,
                ),
            ),
        ));

        $admin->editModel();
    }

    /**
     * List the Genetic Patients
     */
    public function actionList()
    {
        $admin = new Crud(GeneticsStudy::model(), $this);
        $admin->setListFieldsAction('view');
        $admin->setModelDisplayName('Genetic Studies');
        $admin->setListFields(array(
            'id',
            'name',
            'end_date',
        ));
        $admin->searchAll();
        $admin->getSearch()->setItemsPerPage($this->itemsPerPage);
        
        $display_buttons = $this->checkAccess('TaskEditGeneticStudy');
        $admin->listModel($display_buttons);
    }
    
    /**
     * Returns the data model based on the primary key given in the GET variable.
     * If the data model is not found, an HTTP exception will be raised.
     *
     * @param int $id the ID of the model to be loaded
     */
    public function loadModel($id)
    {
        $model = GeneticsStudy::model()->findByPk((int) $id);
        if ($model === null) {
            throw new CHttpException(404, 'The requested page does not exist.');
        }

        return $model;
    }

}