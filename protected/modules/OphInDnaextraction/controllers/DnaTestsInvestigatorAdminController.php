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
class DnaTestsInvestigatorAdminController extends BaseAdminController
{
    protected $itemsPerPage = 100;

    public function actionList()
    {
        $admin = new Admin(OphInDnaextraction_DnaTests_Investigator::model(), $this);
        $admin->setModelDisplayName('DNA Investigators');
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
        $admin = new Admin(OphInDnaextraction_DnaTests_Investigator::model(), $this);
        if ($id) {
            $admin->setModelId($id);
        }
        $admin->setModelDisplayName('DNA Investigators');
        $admin->setEditFields(array(
            'name' => 'text',
        ));
        $admin->editModel();
    }
    
    public function actionDelete()
    {
        $admin = new Admin(OphInDnaextraction_DnaTests_Investigator::model(), $this);
        $admin->deleteModel();
    }

    public function actionSort()
    {
        if (!empty($_POST['OphInDnaextraction_DnaTests_Investigator']['display_order'])) {
            foreach ($_POST['OphInDnaextraction_DnaTests_Investigator']['display_order'] as $i => $id) {
                if ($investigator = OphInDnaextraction_DnaTests_Investigator::model()->findByPk($id)) {
                    $investigator->display_order = $i + 1;
                    if (!$investigator->save()) {
                        throw new Exception('Unable to save investigator: '.print_r($dnaName->getErrors(), true));
                    }
                }
            }
        }
    }
}
