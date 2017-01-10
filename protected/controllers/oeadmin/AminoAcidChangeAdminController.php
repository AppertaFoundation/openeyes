<?php

/**
 * Class AminoAcidChangeAdminController
 *
 * Admin controller class for PedigreeAminoAcidChangeType
 */
class AminoAcidChangeAdminController extends BaseAdminController
{
    /**
     * @var string
     */
    public $layout = '//layouts/admin';

    protected $itemsPerPage = 100;

    /**
     * Lists PedigreeAminoAcidChangeType.
     *
     * @throws CHttpException
     */
    public function actionList()
    {
        $admin = new Admin(PedigreeAminoAcidChangeType::model(), $this);
        $admin->setModelDisplayName('Amino Acid Change Type');
        $admin->setListFields(array(
            'id',
            'change',
        ));
        $admin->searchAll();
        $admin->getSearch()->setItemsPerPage($this->itemsPerPage);
        $admin->listModel();
    }

    /**
     * Edits or adds a Amino Acid Change Type.
     *
     * @param bool|int $id
     *
     * @throws CHttpException
     */
    public function actionEdit($id = false)
    {
        $admin = new Admin(PedigreeAminoAcidChangeType::model(), $this);
        if ($id) {
            $admin->setModelId($id);
        }
        $admin->setModelDisplayName('Amino Acid Change Type');
        $admin->setEditFields(array(
            'change' => 'text',
        ));
        $admin->editModel();
    }

    /**
     * Deletes rows for the model.
     */
    public function actionDelete()
    {
        $admin = new Admin(PedigreeAminoAcidChangeType::model(), $this);
        $admin->deleteModel();
    }
}