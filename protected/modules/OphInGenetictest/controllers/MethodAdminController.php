<?php

/**
 * Class MethodAdminController
 *
 * Admin controller class for MethodAdminController
 */
class MethodAdminController extends BaseAdminController
{
    /**
     * @var string
     */
    public $layout = '//layouts/admin';

    protected $itemsPerPage = 100;

    /**
     * Lists OphInGenetictest_Test_Method.
     *
     * @throws CHttpException
     */
    public function actionList()
    {
        $admin = new Admin(OphInGenetictest_Test_Method::model(), $this);
        $admin->setModelDisplayName('Genetic Test Method');
        $admin->setListFields(array(
            'id',
            'name',
        ));
        $admin->searchAll();
        $admin->getSearch()->setItemsPerPage($this->itemsPerPage);
        $admin->listModel();
    }

    /**
     * Edits or adds a Genetic Test Method.
     *
     * @param bool|int $id
     *
     * @throws CHttpException
     */
    public function actionEdit($id = false)
    {
        $admin = new Admin(OphInGenetictest_Test_Method::model(), $this);
        if ($id) {
            $admin->setModelId($id);
        }
        $admin->setModelDisplayName('Genetic Test Method');
        $admin->setEditFields(array(
            'name' => 'text',
        ));
        $admin->editModel();
    }

    /**
     * Deletes rows for the model.
     */
    public function actionDelete()
    {
        $admin = new Admin(OphInGenetictest_Test_Method::model(), $this);
        $admin->deleteModel();
    }
}