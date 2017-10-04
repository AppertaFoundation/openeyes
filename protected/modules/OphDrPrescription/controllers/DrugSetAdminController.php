<?php

/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2015
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2015, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
class DrugSetAdminController extends BaseAdminController
{
    /**
     * @var int
     */
    public $itemsPerPage = 100;

    /**
     * Init the edit admin page, because we have a custom save URL, so we need to use
     * Admin in more then 1 function.
     *
     * @param bool $id
     *
     * @return Admin
     */
    protected function initAdmin($id = false)
    {
        $admin = new Admin(DrugSet::model(), $this);
        if ($id) {
            $admin->setModelId($id);
        }
        $element = Element_OphDrPrescription_Details::model();
        $admin->setCustomSaveURL('/OphDrPrescription/DrugSetAdmin/SaveDrugSet');
        $admin->setCustomCancelURL('/OphDrPrescription/DrugSetAdmin/list');

        $admin->setEditFields(array(
            'active' => 'checkbox',
            'name' => 'text',
            'subspecialty' => array(
                'widget' => 'DropDownList',
                'options' => CHtml::listData(Subspecialty::model()->findAll(), 'id', 'name'),
                'htmlOptions' => null,
                'hidden' => false,
                'layoutColumns' => null,
            ),
            'setItems' => array(
                'widget' => 'CustomView',
                'viewName' => '/default/form_Element_OphDrPrescription_Details',
                'viewArguments' => array('element' => $element),
            ),
        ));

        return $admin;
    }

    /**
     * Render the basic drug set admin page.
     */
    public function actionList()
    {
        $admin = new Admin(DrugSet::model(), $this);
        $admin->setListFields(array(
            'id',
            'name',
            'subspecialty.name',
            'active',
        ));
        $admin->searchAll();
        $admin->getSearch()->setItemsPerPage($this->itemsPerPage);
        $admin->listModel();
    }

    /**
     * Edits or adds drug sets.
     *
     * @param bool|int $id
     *
     * @throws CHttpException
     */
    public function actionEdit($id = false)
    {
        $admin = $this->initAdmin($id);
        $admin->editModel();
    }

    /**
     * Deletes rows for the model.
     */
    public function actionDelete()
    {
        // instead of delete we just set the active field to false
        if (Yii::app()->request->isPostRequest) {
            $ids = Yii::app()->request->getPost('DrugSet');
            foreach ($ids as $id) {
                $model = DrugSet::model()->findByPk($id);
                if ($model) {
                    $model->active = 0;
                    $model->save();
                }
            }
        }
        echo 1;
    }

    /**
     * Save drug set data from the admin interface.
     */
    public function actionSaveDrugSet()
    {
        // we need to decide if it's a new set or modification
        $drugSet = Yii::app()->request->getParam('DrugSet');
        $prescriptionItem = Yii::app()->request->getParam('prescription_item');

        if (isset($drugSet['id'])) {
            $drugSetId = $drugSet['id'];
        }
        if ($drugSetId > 0) {
            $drugset = DrugSet::model()->findByPk($drugSetId);
        } else {
            $drugset = new DrugSet();
        }
        $drugset->name = $drugSet['name'];
        $drugset->subspecialty_id = $drugSet['subspecialty'];
        $drugset->active = $drugSet['active'];

        if ($drugset->save()) {

            // we delete previous tapers and items, and insert the new ones

            $currentDrugRows = DrugSetItem::model()->findAll(new CDbCriteria(array('condition' => "drug_set_id = '".$drugset->id."'")));
            foreach ($currentDrugRows as $currentDrugRow) {
                DrugSetItemTaper::model()->deleteAll(new CDbCriteria(array('condition' => "item_id = '".$currentDrugRow->id."'")));
                $currentDrugRow->delete();
            }

            if (isset($prescriptionItem) && is_array($prescriptionItem)) {
                foreach ($prescriptionItem as $item) {
                    $item_model = new DrugSetItem();
                    $item_model->drug_set_id = $drugset->id;
                    $item_model->attributes = $item;
                    $item_model->save(); // we need an id to save tapers
                    if (isset($item['taper'])) {
                        $tapers = array();
                        foreach ($item['taper'] as $taper) {
                            $taper_model = new DrugSetItemTaper();
                            $taper_model->attributes = $taper;
                            $taper_model->item_id = $item_model->id;
                            $taper_model->save();
                            $tapers[] = $taper_model;
                        }
                        //$item_model->tapers = $tapers;
                    }
                    //$items[] = $item_model;
                    //$item_model->save();
                }
                Yii::app()->user->setFlash('info.save_message', 'Save successful.');
            } else {
                Yii::app()->user->setFlash('info.save_message',
                    'Unable to save drugs, please add at least one drug to the set. Set name and subspecialty saved.');
            }
            $this->redirect('/OphDrPrescription/DrugSetAdmin/list');
        } else {
            // TODO: maybe more error handling need to be added here!!
            if ($drugSetId > 0) {
                $admin = $this->initAdmin($drugSetId);
            } else {
                $admin = $this->initAdmin(false);
            }
            $this->render('//admin/generic/edit', array('admin' => $admin, 'errors' => $drugset->getErrors()));
        }
    }
}
