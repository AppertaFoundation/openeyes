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
        $assetPath = Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('application.modules.' . $this->getModule()->name . '.assets'), true);
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

    public function actionGetNextLetterNumberRow()
    {
        $result = array();
        if ((int)$_POST['box_id'] > 0) {
            $storage = new OphInDnaextraction_DnaExtraction_Storage();

            $boxRanges = OphInDnaextraction_DnaExtraction_Box::boxMaxValues(Yii::app()->request->getPost('box_id'));
            $letterArray = $storage->generateLetterArrays(Yii::app()->request->getPost('box_id'), $boxRanges['maxletter'], $boxRanges['maxnumber']);
            $usedBoxRows = $storage->getAllLetterNumberToBox(Yii::app()->request->getPost('box_id'));


            $arrayDiff = array_filter($letterArray, function ($element) use ($usedBoxRows) {
                return !in_array($element, $usedBoxRows);
            });

            foreach ($arrayDiff as $key => $val) {
                if ($val['letter'] == "0") {
                    $result['letter'] = "You have not specified a maximum letter value.";
                    $result['number'] = "You have not specified a maximum number value.";
                } else {
                    $result['letter'] = $val['letter'];
                    $result['number'] = $val['number'];
                }

                break;
            }

            $this->renderJSON($result);
        }
    }
}
