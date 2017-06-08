<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of DnaTestsInvestigatorAdmin
 *
 * @author Irvine
 */
class BoxAdminController extends BaseAdminController
{
    protected $itemsPerPage = 100;
    
    public function actionList()
    {
        $admin = new Admin(OphInDnaextraction_DnaExtraction_Box::model(), $this);
        $admin->setModelDisplayName('DNA Boxes');
        $admin->setListFields(array(
            'id',
            'value',
        ));
        $admin->searchAll();
        $admin->getSearch()->setItemsPerPage($this->itemsPerPage);
        $admin->listModel();
    }
    
    public function actionEdit($id = false)
    {
        $admin = new Admin(OphInDnaextraction_DnaExtraction_Box::model(), $this);
        if ($id) {
            $admin->setModelId($id);
        }
        $admin->setModelDisplayName('DNA Boxes');
        $admin->setEditFields(array(
            'value' => 'text',
        ));
        $admin->editModel();
    }
    
    public function actionDelete()
    {
        $admin = new Admin(OphInDnaextraction_DnaExtraction_Box::model(), $this);
        $admin->deleteModel();
    }
}
