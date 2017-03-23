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
    public $layout = '//../modules/genetics/views/layouts/genetics';

    protected $itemsPerPage = 100;

    public function accessRules()
    {
        return array(array('allow', 'roles' => array('Genetics Admin')));
    }

    /**
     * Lists OphInGeneticresults_Test_Method.
     *
     * @throws CHttpException
     */
    public function actionList()
    {
        $admin = new Admin(OphInGeneticresults_Test_Method::model(), $this);
        $admin->setModelDisplayName('Genetic Results Method');
        $admin->setListFields(array(
            'id',
            'name',
        ));
        $admin->searchAll();
        $admin->getSearch()->setItemsPerPage($this->itemsPerPage);
        $admin->listModel();
    }

    /**
     * Edits or adds a Genetic Results Method.
     *
     * @param bool|int $id
     *
     * @throws CHttpException
     */
    public function actionEdit($id = false)
    {
        $admin = new Admin(OphInGeneticresults_Test_Method::model(), $this);
        if ($id) {
            $admin->setModelId($id);
        }
        $admin->setModelDisplayName('Genetic Results Method');
        $admin->setEditFields(array(
            'referer' => 'referer',
            'name' => 'text',
        ));
        
        $admin->setCustomCancelURL(Yii::app()->request->getUrlReferrer());    

        $valid = $admin->editModel(false);

        if (Yii::app()->request->isPostRequest) {        
            if ($valid) {
                Yii::app()->user->setFlash('success', "Genetic Results Method Saved");
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
        $admin = new Admin(OphInGeneticresults_Test_Method::model(), $this);
        $admin->deleteModel();
    }
}
