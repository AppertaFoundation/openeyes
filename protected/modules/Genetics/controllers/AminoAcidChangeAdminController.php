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
    public $layout = 'genetics';

    protected $itemsPerPage = 100;

    public function accessRules()
    {
        return array(array('allow', 'roles' => array('Genetics Admin')));
    }

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
            'referer' => 'referer',
            'change' => 'text',
        ));
        $admin->setCustomCancelURL(Yii::app()->request->getUrlReferrer());    

        $valid = $admin->editModel(false);

        if (Yii::app()->request->isPostRequest) {        
            if ($valid) {
                Yii::app()->user->setFlash('success', "Amino Acid Change Type Saved");
                $url = str_replace('/edit','/view',(Yii::app()->request->requestUri)).'/'.$admin->getModel()->id;
                $this->redirect($url);
            } else {
                $admin->render($admin->getEditTemplate(), array('admin' => $admin, 'errors' => $admin->getModel()->getErrors()));
            }
        }
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