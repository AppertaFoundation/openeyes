<?php

/**
 * Class GeneController
 *
 * Contains the actions pertaining to genes
 */
class GeneController extends BaseAdminController
{
    public $layout = '//layouts/admin';

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
                'actions' => array('Edit', 'Delete'),
                'roles' => array('OprnEditGene'),
            ),
            array('allow',
                'actions' => array('List'),
                'roles' => array('OprnViewGene'),
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
        $admin = new Admin(PedigreeGene::model(), $this);
        if ($id) {
            $admin->setModelId($id);
        }

        $admin->setModelDisplayName('Gene');
        $admin->setEditFields(array(
            'name' => 'text',
            'location' => 'text',
            'priority' => 'checkbox',
            'description' => 'text',
            'details' => 'text',
            'refs' => 'text',
        ));

        $admin->editModel();
    }

    /**
     * List the Genetic Patients
     */
    public function actionList()
    {
        $admin = new Crud(PedigreeGene::model(), $this);
        $admin->setModelDisplayName('Gene');
        $admin->setListFields(array(
            'id',
            'name',
            'location',
            'description',
        ));
        $admin->searchAll();
        $admin->getSearch()->setItemsPerPage($this->itemsPerPage);
        $admin->listModel();
    }

    /**
     * Deletes rows for the model.
     */
    public function actionDelete()
    {
        $admin = new Crud(PedigreeGene::model(), $this);
        $admin->deleteModel();
    }
}