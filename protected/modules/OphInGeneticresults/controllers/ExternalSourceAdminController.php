<?php

/**
 * Class ExternalSourceAdminController
 *
 * Admin controller class for ExternalSourceAdminController
 */
class ExternalSourceAdminController extends BaseAdminController
{
    /**
     * @var string
     */
    public $layout = '//../modules/genetics/views/layouts/genetics';

    protected $itemsPerPage = 100;

    public function accessRules()
    {
        return array(array('allow', 'roles' => array('Genetics Admin')));
    }

    /**
     * Lists OphInGeneticresults_External_Source.
     *
     * @throws CHttpException
     */
    public function actionList()
    {
        $admin = new Admin(OphInGeneticresults_External_Source::model(), $this);
        $admin->setModelDisplayName('External Source');
        $admin->setListFields(array(
            'id',
            'name',
        ));
        $admin->searchAll();
        $admin->getSearch()->setItemsPerPage($this->itemsPerPage);
        $admin->listModel();
    }

    /**
     * Edits or adds a Genetic Test Effect.
     *
     * @param bool|int $id
     *
     * @throws CHttpException
     */
    public function actionEdit($id = false)
    {
        $admin = new Admin(OphInGeneticresults_External_Source::model(), $this);
        if ($id) {
            $admin->setModelId($id);
        }
        $admin->setModelDisplayName('External Source');
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
        $admin = new Admin(OphInGeneticresults_External_Source::model(), $this);
        $admin->deleteModel();
    }
}
