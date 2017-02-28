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
                'roles' => array('OprnViewGeneticStudy'),
            ),
            array('allow',
                'actions' => array('Edit'),
                'roles' => array('OprnViewGeneticStudy'),
            ),
        );
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
        $admin->setModelDisplayName('Genetic Study');
        $admin->setListFields(array(
            'id',
            'name',
            'end_date',
        ));
        $admin->searchAll();
        $admin->getSearch()->setItemsPerPage($this->itemsPerPage);
        $admin->listModel();
    }

}