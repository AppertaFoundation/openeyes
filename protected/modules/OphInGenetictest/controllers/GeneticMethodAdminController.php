<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of DnaSampleAdminController
 *
 */
class GeneticMethodAdminController extends BaseAdminController{
    
    protected $itemsPerPage = 100;
    
    public function actionList()
    {
        
        $admin = new Admin(OphInGenetictest_Test_Method::model(), $this);
        $admin->setModelDisplayName('Genetic Method');
        $admin->setListFields(array(
            'id',
            'name'
        ));
        $admin->searchAll();
        $admin->getSearch()->setItemsPerPage($this->itemsPerPage);
        $admin->listModel();

    }
    
    public function actionEdit($id = false)
    {
        $admin = new Admin(OphInGenetictest_Test_Method::model(), $this);
        if ($id) {
            $admin->setModelId($id);
        }
        $admin->setModelDisplayName('Genetic Method');
        $admin->setEditFields(array(
            'name' => 'text',
        ));
        $admin->editModel();
    }
    
    public function actionDelete()
    {
        $admin = new Admin(OphInGenetictest_Test_Method::model(), $this);
        $admin->deleteModel();
    }
}
