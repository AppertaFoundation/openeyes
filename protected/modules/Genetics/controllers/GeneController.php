<?php

/**
 * Class GeneController
 *
 * Contains the actions pertaining to genes
 */
class GeneController extends BaseModuleController
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
                'actions' => array('Edit', 'Delete'),
                'roles' => array('OprnEditGene'),
            ),
            array('allow',
                'actions' => array('List', 'View'),
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
        $admin = new Crud(PedigreeGene::model(), $this);
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
    
    public function actionView($id)
    {
        $gene = $this->loadModel($id);
        $this->render('view', array('model' => $gene));
    }

    /**
     * List the Genetic Patients
     */
    public function actionList()
    {
        $admin = new Crud(PedigreeGene::model(), $this);
        $admin->setModelDisplayName('Gene');
        $admin->setListFieldsAction('view');
        $admin->setListFields(array(
            'id',
            'name',
            'location',
            'description',
        ));
        $admin->searchAll();
        $admin->getSearch()->setItemsPerPage($this->itemsPerPage);
        $display_buttons = $this->checkAccess('OprnEditGene');
        $admin->listModel($display_buttons);
    }

    /**
     * Deletes rows for the model.
     */
    public function actionDelete()
    {
        $admin = new Crud(PedigreeGene::model(), $this);
        $admin->deleteModel();
    }
    
    /**
     * Returns the data model based on the primary key given in the GET variable.
     * If the data model is not found, an HTTP exception will be raised.
     *
     * @param int $id the ID of the model to be loaded
     */
    public function loadModel($id)
    {
        $model = PedigreeGene::model()->findByPk((int) $id);
        if ($model === null) {
            throw new CHttpException(404, 'The requested page does not exist.');
        }

        return $model;
    }
}