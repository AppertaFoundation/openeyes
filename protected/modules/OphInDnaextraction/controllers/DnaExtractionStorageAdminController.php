<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of DnaExtractionStorageAddress
 *
 * @author Irvine
 */

class DnaExtractionStorageAdminController extends \ModuleAdminController
{
    protected $itemsPerPage = 100;
    
    public function actionList()
    {
       
        $admin = new Admin(OphInDnaextraction_DnaExtraction_Storage::model(), $this);
      
        $admin->setModelDisplayName('Dna Extraction Storage');
        $admin->setListFields(array(
            'box.value',
            'letter',
            'number'
        ));
        $admin->searchAll();
        
        $admin->getSearch()->addSearchItem('box_id', array(
            'type' => 'dropdown',
            'options' => CHtml::listData(OphInDnaextraction_DnaExtraction_Box::model()->findAll(), 'id', 'value'),
            'empty' => '- Box -',
        ));
        
        $admin->getSearch()->setItemsPerPage($this->itemsPerPage);
        $admin->listModel();
    }
    
    public function actionEdit($id = false)
    {
       
        $admin = new Admin(OphInDnaextraction_DnaExtraction_Storage::model(), $this);
        if ($id) {
            $admin->setModelId($id);
        }

        $admin->setModelDisplayName('Dna Extraction Storage');
        $admin->setEditFields(array(
           'box_id' => array(
                'widget' => 'DropDownList',
                'options' => CHtml::listData(OphInDnaextraction_DnaExtraction_Box::model()->findAll(), 'id', 'value'),
                'htmlOptions' => array('empty' => '- Box -'),
                'hidden' => false,
                'layoutColumns' => null,
            ),
            'letter' => 'text',
            'number' => 'text',
        ));
        $admin->editModel();
    }
    
    public function actionDelete()
    {
        $admin = new Admin(OphInDnaextraction_DnaExtraction_Storage::model(), $this);
        $admin->deleteModel();
    }
}
