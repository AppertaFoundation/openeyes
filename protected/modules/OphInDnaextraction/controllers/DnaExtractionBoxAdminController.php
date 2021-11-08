<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of DnaExtractionBoxAdminController
 *
 * @author Irvine
 */
class DnaExtractionBoxAdminController extends \ModuleAdminController
{
    protected $itemsPerPage = 100;

    public function actionList()
    {

        $admin = new Admin(OphInDnaextraction_DnaExtraction_Box::model(), $this);

        $admin->setModelDisplayName('Dna Storage Box');
        $admin->setListFields(array(
            'value',
            'maxletter',
            'maxnumber',
            'display_order',
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
        $admin->setModelDisplayName('Dna Storage Box');
        $admin->setEditFields(array(
           'value'     => 'text',
           'maxletter' => 'text',
           'maxnumber' => 'text',
        ));
        $admin->editModel();
    }

    public function actionDelete()
    {
        $admin = new Admin(OphInDnaextraction_DnaExtraction_Box::model(), $this);
        $admin->deleteModel();
    }

    public function actionSort()
    {
        if (!empty($_POST['OphInDnaextraction_DnaExtraction_Box']['display_order'])) {
            foreach ($_POST['OphInDnaextraction_DnaExtraction_Box']['display_order'] as $i => $id) {
                if ($box = OphInDnaextraction_DnaExtraction_Box::model()->findByPk($id)) {
                    $box->display_order = $i + 1;
                    if (!$box->save()) {
                        throw new Exception('Unable to save storage box: '.print_r($dnaName->getErrors(), true));
                    }
                }
            }
        }
    }
}
