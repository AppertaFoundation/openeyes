<?php
/**
 * OpenEyes.
 *
 * (C) OpenEyes Foundation, 2019
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

class CommonSystemicDisorderGroupController extends BaseAdminController
{
    public $group = 'Disorders';

    public function actionList()
    {
        $current_institution = $this->request->getParam('institution_id')
            ? Institution::model()->find('id = ' . $this->request->getParam('institution_id'))
            : Institution::model()->getCurrent();

        $this->render('/admin/listcommonsystemicdisordergroup', [
            'model' => CommonSystemicDisorderGroup::model(),
            'model_list' => CommonSystemicDisorderGroup::model()->findAllAtLevel(ReferenceData::LEVEL_INSTITUTION, ['order' => 'display_order'], $current_institution),
            'current_institution_id' => $current_institution->id,
            'current_institution' => $current_institution
        ]);
    }

    public function actionCreate()
    {
        $model = new CommonSystemicDisorderGroup();
        $values = \Yii::app()->request->getPost('CommonSystemicDisorderGroup', []);
        if (!empty($values)) {
            $model->name = $values['name'];
            $last_item = $model::model()->find(['order'=>'display_order DESC']);
            $model->display_order = $last_item ? $last_item->display_order + 1 : '1';
            $result = $model->save();

            $institution_id = Institution::model()->getCurrent()->id;
            $needs_mapping = Yii::app()->request->getPost('assigned_institution');

            if ($model->hasMapping(ReferenceData::LEVEL_INSTITUTION, $institution_id)) {
                if (!$needs_mapping) {
                    $model->deleteMapping(ReferenceData::LEVEL_INSTITUTION, $institution_id);
                }
            } else {
                if ($needs_mapping) {
                    $model->createMapping(ReferenceData::LEVEL_INSTITUTION, $institution_id);
                }
            }

            if ($result) {
                Audit::add(
                    'admin',
                    'create',
                    serialize($model->attributes),
                    false,
                    ['model' => $model::getShortModelName()]
                );
                Yii::app()->user->setFlash('success', 'Common Systemic Disorder Group created');
                $this->redirect(['list']);
            }
        }
        $this->render('/admin/editcommonsystemicdisordergroup', [
            'model' => $model,
            'errors' => isset($errors) ? $errors : null,
        ]);
    }

    public function actionUpdate()
    {
        $request = Yii::app()->getRequest();
        $model = CommonSystemicDisorderGroup::model()->findByPk($request->getParam('id'));
        if (!$model) {
            \OELog::log('CommonSystemicDisorderGroup not found with id ' . $request->getParam('id'));
            $this->redirect(['list']);
        }

        $values = $request->getPost('CommonSystemicDisorderGroup', []);
        if (!empty($values)) {
            $model->name = $values['name'];
            if ($model->save()) {
                $institution_id = Institution::model()->getCurrent()->id;
                $needs_mapping =Yii::app()->request->getPost('assigned_institution');

                if ($model->hasMapping(ReferenceData::LEVEL_INSTITUTION, $institution_id)) {
                    if (!$needs_mapping) {
                        $model->deleteMapping(ReferenceData::LEVEL_INSTITUTION, $institution_id);
                    }
                } else {
                    if ($needs_mapping) {
                        $model->createMapping(ReferenceData::LEVEL_INSTITUTION, $institution_id);
                    }
                }

                Yii::app()->user->setFlash('success', 'Common Systemic Disorder Group saved');
                $this->redirect(['list']);
            } else {
                $errors = $model->getErrors();
            }
        }

        $this->render('/admin/editcommonsystemicdisordergroup', [
            'model' => $model,
            'errors' => isset($errors) ? $errors : null,
        ]);
    }

    public function actionDelete()
    {
        $delete_ids = \Yii::app()->request->getPost('select', []);
        $transaction = \Yii::app()->db->beginTransaction();
        $success = true;
        $result = [];
        $result['status'] = 1;
        $result['errors'] = "";
        try {
            foreach ($delete_ids as $group_id) {
                //check if there are disorders in the disorder group before deleting the group
                $disorderGroup = CommonSystemicDisorderGroup::model()->findByPk($group_id);
                $disorder = CommonSystemicDisorder::model()->findByAttributes(array('group_id' => $group_id));
                if (isset($disorder) && isset($disorderGroup->name)) {
                    $success = false;
                    $result['status'] = 0;
                    $result['errors']= array(
                        "There are disorders associated with the Group: '" . $disorderGroup->name . "', please remove disorder from the Group before deleting."
                    );
                    break;
                } else {
                    $deleteGroup = CommonSystemicDisorderGroup::model()->deleteByPk($group_id);
                    if ($deleteGroup) {
                        Audit::add('admin-common-systemic-disorder-group', 'delete', $deleteGroup);
                    }
                }
            }
        } catch (Exception $e) {
            \OELog::log($e->getMessage());
            $result['status'] = 0;
            $result['errors'][]= $e->getMessage();
            $success = false;
        }

        if ($success) {
            $transaction->commit();
        } else {
            $transaction->rollback();
        }

        $this->renderJSON($result);
    }

    public function actionSave()
    {
        $transaction = Yii::app()->db->beginTransaction();
        $disorder_groups = Yii::app()->request->getPost('CommonSystemicDisorderGroup') ?: [];
        $errors = [];

        foreach ($disorder_groups as $disorder_group) {
            $group = CommonSystemicDisorderGroup::model()->findByPk($disorder_group['id']);
            if ($group) {
                $group->id = $disorder_group['id'];
                $group->display_order = $disorder_group['display_order'];

                if (!$group->save()) {
                    $errors[] = $group->getErrors();
                }
            }
        }

        if (empty($errors)) {
            $transaction->commit();
            Yii::app()->user->setFlash('success', 'List updated.');
        } else {
            $transaction->rollback();
        }

        $this->render('/admin/listcommonsystemicdisordergroup', [
            'model' => CommonSystemicDisorderGroup::model(),
            'model_list' => CommonSystemicDisorderGroup::model()->findAll(['order' => 'display_order']),
            'errors' => $errors
        ]);
    }
}
