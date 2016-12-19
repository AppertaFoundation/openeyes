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
class GeneticEffectAdminController extends BaseAdminController{
    
    protected $itemsPerPage = 100;
    
    public function actionList()
    {
        
        $admin = new Admin(OphInGenetictest_Test_Effect::model(), $this);
        $admin->setModelDisplayName('Genetic Effects');
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
        $admin = new Admin(OphInGenetictest_Test_Effect::model(), $this);
        if ($id) {
            $admin->setModelId($id);
        }
        $admin->setModelDisplayName('Genetic Effects');
        $admin->setEditFields(array(
            'name' => 'text',
        ));
        $admin->editModel();
    }
    
    public function actionDelete()
    {
        $admin = new Admin(OphInGenetictest_Test_Effect::model(), $this);
        $admin->deleteModel();
    }
}
