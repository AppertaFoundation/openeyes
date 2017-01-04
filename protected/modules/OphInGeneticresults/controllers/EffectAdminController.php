<?php

/**
 * Class EffectAdminController
 *
 * Admin controller class for EffectAdminController
 */
class EffectAdminController extends BaseAdminController
{
    /**
     * @var string
     */
    public $layout = '//layouts/admin';

    protected $itemsPerPage = 100;

    /**
     * Lists OphInGeneticresults_Test_Effect.
     *
     * @throws CHttpException
     */
    public function actionList()
    {
        $admin = new Admin(OphInGeneticresults_Test_Effect::model(), $this);
        $admin->setModelDisplayName('Genetic Results Effect');
        $admin->setListFields(array(
            'id',
            'name',
        ));
        $admin->searchAll();
        $admin->getSearch()->setItemsPerPage($this->itemsPerPage);
        $admin->listModel();
    }

    /**
     * Edits or adds a Genetic Results Effect.
     *
     * @param bool|int $id
     *
     * @throws CHttpException
     */
    public function actionEdit($id = false)
    {
        $admin = new Admin(OphInGeneticresults_Test_Effect::model(), $this);
        if ($id) {
            $admin->setModelId($id);
        }
        $admin->setModelDisplayName('Genetic Results Effect');
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
        $admin = new Admin(OphInGeneticresults_Test_Effect::model(), $this);
        $admin->deleteModel();
    }
}