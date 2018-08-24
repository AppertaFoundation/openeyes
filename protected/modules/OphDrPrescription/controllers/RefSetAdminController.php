<?php

/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2018
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2018, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

class RefSetAdminController extends BaseAdminController
{
    public function actionList()
    {
        $admin = new Admin(RefSet::model(), $this);
        $admin->setListFields(array(
            'id',
            'name',
            'itemsCount',
            'adminListAction'
        ));

        $admin->getSearch()->addSearchItem('name');
        $admin->getSearch()->setItemsPerPage(30);
        $crit = new CDbCriteria();
        $crit->order = 'id ASC';
        $admin->getSearch()->setCriteria($crit);

        $admin->setListFieldsAction('edit');


        $admin->setModelDisplayName("Medication sets");
        $admin->listModel();
    }

    public function actionToList($id)
    {
        $this->redirect('/OphDrPrescription/refMedicationAdmin/list?ref_set_id='.$id);
    }

    public function actionEdit($id = null)
    {
        $admin = new Admin(RefSet::model(), $this);

        $admin->setEditFields(array(
            'name'=>'Name',
            'rules' =>  array(
                'widget' => 'GenericAdmin',
                'options' => array(
                    'model' => RefSetRule::class,
                    'extra_fields' =>  array(
                        array(
                            'field' => 'site_id',
                            'type' => 'lookup',
                            'model' => Site::class,
                            'allow_null' => true
                        ),
                        array(
                            'field' => 'subspecialty_id',
                            'type' => 'lookup',
                            'model' => Subspecialty::class,
                            'allow_null' => true
                        )
                    ),
                    'label_extra_field' => true,
                    'items' => !is_null($id) ? RefSet::model()->findByPk($id)->refSetRules : array(),
                    'filters_ready' => true,
                    'cannot_save' => true,
                    'no_form' => true,
                ),
                'label' => 'Rules'
            ),

        ));
        $admin->setModelDisplayName("Medication set");
        if($id) {
            $admin->setModelId($id);
        }
        $admin->setCustomSaveURL('/OphDrPrescription/refSetAdmin/save/'.$id);

        $admin->editModel();
    }

    public function actionSave($id = null)
    {
        if(is_null($id)) {
            $model = new RefSet();
        }
        else {
            if(!$model = RefSet::model()->findByPk($id)) {
                throw new CHttpException(404, 'Page not found');
            }
        }

        /** @var RefSet $model */

        $data = Yii::app()->request->getPost('RefSet');
        $model->setAttributes($data);

        $model->save();

        $existing_ids = array();
        $updated_ids = array();
        foreach ($model->refSetRules as $rule) {
            $existing_ids[] = $rule->id;
        }

        $ids = Yii::app()->request->getPost('id');
        if(is_array($ids)) {
            foreach ($ids as $key => $rid) {
                if($rid === '') {
                    $refSetRule = new RefSetRule();
                }
                else {
                    $refSetRule = RefSetRule::model()->findByPk($rid);
                    $updated_ids[] = $rid;
                }

                $refSetRule->setAttributes(array(
                    'ref_set_id' => $model->id,
                    'site_id' => Yii::app()->request->getPost('site_id')[$key],
                    'subspecialty_id' => Yii::app()->request->getPost('subspecialty_id')[$key]
                ));

                $refSetRule->save();
            }
        }

        $deleted_ids = array_diff($existing_ids, $updated_ids);
        if(!empty($deleted_ids)) {
            RefSetRule::model()->deleteByPk($deleted_ids);
        }

        $this->redirect('/OphDrPrescription/refSetAdmin/list');
    }

    public function actionDelete()
    {
        $ids_to_delete = Yii::app()->request->getPost('RefSet')['id'];
        if(is_array($ids_to_delete)) {
            foreach ($ids_to_delete as $id) {
                $model = RefSet::model()->findByPk($id);
                /** @var RefSet $model */
                foreach ($model->refSetRules as $rule) {
                    $rule->delete();
                }
                foreach ($model->refMedicationSets as $med_set) {
                    $med_set->delete();
                }
                $model->delete();
            }
        }

        exit("1");
    }
}