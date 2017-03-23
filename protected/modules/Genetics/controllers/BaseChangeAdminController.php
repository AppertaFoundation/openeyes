<?php

/**
 * Class BaseChangeAdminController
 *
 * Admin controller class for PedigreeBaseChangeType
 */
class BaseChangeAdminController extends BaseAdminController
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
     * Lists procedures.
     *
     * @throws CHttpException
     */
    public function actionList()
    {
        $admin = new Admin(PedigreeBaseChangeType::model(), $this);
        $admin->setModelDisplayName('Base Change Type');
        $admin->setListFields(array(
            'id',
            'change',
        ));
        $admin->searchAll();
        $admin->getSearch()->setItemsPerPage($this->itemsPerPage);
        $admin->getSearch()->setDefaultResults(false);
        $admin->listModel();
    }

    /**
     * Edits or adds a Base Change Type.
     *
     * @param bool|int $id
     *
     * @throws CHttpException
     */
    public function actionEdit($id = false)
    {
        $admin = new Admin(PedigreeBaseChangeType::model(), $this);
        if ($id) {
            $admin->setModelId($id);
        }
        $admin->setModelDisplayName('Base Change Type');
        $admin->setEditFields(array(
            'referer' => 'referer',
            'change' => 'text',
        ));
        $admin->setCustomCancelURL(Yii::app()->request->getUrlReferrer());    

        $valid = $admin->editModel(false);

        if (Yii::app()->request->isPostRequest) {        
            if ($valid) {
                Yii::app()->user->setFlash('success', "Base Change Type Saved");
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
        $admin = new Admin(PedigreeBaseChangeType::model(), $this);
        $admin->deleteModel();
    }
}