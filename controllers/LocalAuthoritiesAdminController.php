<?php
/**
 * Created by PhpStorm.
 * User: veta
 * Date: 06/05/15
 * Time: 11:30
 */
namespace OEModule\OphCoCvi\controllers;


class LocalAuthoritiesAdminController extends \BaseAdminController
{

    /**
     * @var int
     */
    public $itemsPerPage = 100;

    /**
     * Lists lens types
     *
     * @throws CHttpException
     */
    public function actionList()
    {
        $admin = new \Admin(\CommissioningBodyService::model(), $this);
        $admin->setListFields(array(
            'id',
            'name',
            'code'
        ));
        $admin->setModelDisplayName('Local Authorities');
        $admin->searchAll();
        $admin->getSearch()->addActiveFilter();
        $admin->getSearch()->setItemsPerPage($this->itemsPerPage);
        $admin->listModel();
    }

    /**
     * Edits or adds a lens type
     *
     * @param bool|int $id
     * @throws CHttpException
     */
    public function actionEdit($id = false)
    {
        $admin = new \Admin(\CommissioningBodyService::model(), $this);
        if($id){
            $admin->setModelId($id);
        }
        $admin->setModelDisplayName('Local Authorities');
        $admin->setEditFields(array(
            'name' => 'text',
            'code' => 'text',
            'address' => 'text'
        ));
        $admin->editModel();
    }

    /**
     * Deletes rows for the model
     */
    public function actionDelete()
    {
        $admin = new \Admin(\CommissioningBodyService::model(), $this);
        $admin->deleteModel();
    }

}

