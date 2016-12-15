<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of BloodSampleAdminController
 *
 */
class BloodSampleAdminController extends BaseAdminController{
    
    protected $itemsPerPage = 100;
    
    public function actionList()
    {
        $admin = new Admin(OphInBloodsample_Sample_Type::model(), $this);
        $admin->setModelDisplayName('DNA sample type change');
        $admin->setListFields(array(
            'id',
            'name',
            'display_order',
        ));
        $admin->searchAll();
        $admin->getSearch()->setItemsPerPage($this->itemsPerPage);
        $admin->listModel();
    }
    
    public function actionEdit($id = false)
    {
        $admin = new Admin(OphInBloodsample_Sample_Type::model(), $this);
        if ($id) {
            $admin->setModelId($id);
        }
        $admin->setModelDisplayName('DNA sample type change');
        $admin->setEditFields(array(
            'name' => 'text',
        ));
        $admin->editModel();
    }
    
    public function actionDelete()
    {
        $admin = new Admin(OphInBloodsample_Sample_Type::model(), $this);
        $admin->deleteModel();
    }
    
    public function actionSort()
    {
        
    }
}
