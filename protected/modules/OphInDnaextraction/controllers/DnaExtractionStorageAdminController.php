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
        $assetPath = Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('application.modules.'.$this->getModule()->name.'.assets'));
        Yii::app()->clientScript->registerScriptFile($assetPath.'/js/admin.js');
        
        $admin = new Admin(OphInDnaextraction_DnaExtraction_Storage::model(), $this);
        if ($id) {
            $admin->setModelId($id);
        }

        $admin->setModelDisplayName('Dna Extraction Storage');
        $admin->setEditFields(array(
           'box_id' => array(
                'widget' => 'DropDownList',
                'options' => CHtml::listData(OphInDnaextraction_DnaExtraction_Box::model()->findAll(), 'id', 'value'),
                'htmlOptions' => array('empty' => '- Box -', 'onchange' => 'getExtractionStorageLetterNumber(this)'),
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
    
     public function actionGetNextLetterNumberRow( )
    {
        if((int)$_POST['box_id'] > 0){
            
            $usedBoxRows = OphInDnaextraction_DnaExtraction_Storage::getBoxLastRow($_POST['box_id']);
            $boxRanges = OphInDnaextraction_DnaExtraction_Box::availableBoxes($_POST['box_id']);
            
            $letterRange = range('A', $boxRanges['maxletter']);
            $numberRange = range('1' , $boxRanges['maxnumber']);
 
            if(in_array($usedBoxRows['letter'], $letterRange) && 
              in_array($usedBoxRows['number'] + 1, $numberRange))
            {
                $result['letter'] = $usedBoxRows['letter'];
                $result['number'] = $usedBoxRows['number'] + 1;
            } else {
                
                $key = array_search($usedBoxRows['letter'], $letterRange); 
                if(isset($letterRange[$key+1])){
                    $result['letter'] = $letterRange[$key+1];
                }
                
                $result['number'] = '1';
            }
        
            echo json_encode($result);
        }
    }
}
