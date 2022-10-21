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

        // Get groups to list
        $criteria = new CDbCriteria();
        $criteria->join = "JOIN common_systemic_disorder_group_institution codgi ON t.id = codgi.common_systemic_disorder_group_id";
        $criteria->compare('codgi.institution_id', $current_institution->id);
        $data = new CActiveDataProvider('CommonSystemicDisorderGroup', array(
            'criteria' => $criteria,
            'pagination' => false,
        ));

        // Get which groups are in use to ensure they can't be deleted
        $criteria = new CDbCriteria();
        $criteria->addCondition('group_id IS NOT NULL');
        $disorders_in_group = new CActiveDataProvider('CommonSystemicDisorder', array(
            'criteria' => $criteria,
            'pagination' => false,
        ));
        $active_group_ids = array_values(
            array_unique(
                array_map(function ($disorder) {
                        return $disorder->group_id;
                },
                    $disorders_in_group->getData()
                )
            )
        );

        $this->render('/admin/listcommonsystemicdisordergroup', array(
            'dataProvider' => $data,
            'active_group_ids' => $active_group_ids,
            'current_institution_id' => $current_institution->id,
            'current_institution' => $current_institution
        ));
    }

    public function actionSave()
    {
        $current_institution = $this->request->getParam('institution_id')
            ? Institution::model()->find('id = ' . $this->request->getParam('institution_id'))
            : Institution::model()->getCurrent();

        $transaction = Yii::app()->db->beginTransaction();
        $JSON_string = Yii::app()->request->getParam('CommonSystemicDisorderGroups');

        $json_error = false;
        if (!$JSON_string || !array_key_exists('JSON_string', $JSON_string)) {
            $json_error = true;
        }

        $JSON = json_decode(str_replace("'", '"', $JSON_string['JSON_string']), true);
        if (json_last_error() != 0) {
            $json_error = true;
        }

        $ids = array();

        if (!$json_error) {
            $display_orders = array_map(function ($entry) {
                return $entry['display_order'];
            }, $JSON);

            $groups = array_map(function ($entry) {
                return $entry['CommonSystemicDisorderGroup'];
            }, $JSON);
            foreach ($groups as $key => $group) {
                $common_systemic_disorder_group = CommonSystemicDisorderGroup::model()->findByPk($group['id']);
                if (!$common_systemic_disorder_group) {
                    $common_systemic_disorder_group = new CommonSystemicDisorderGroup();
                    $group['id'] = null;
                }

                $common_systemic_disorder_group->attributes = $group;
                $common_systemic_disorder_group->display_order = $display_orders[$key];

                if (!$common_systemic_disorder_group->save()) {
                    $errors[] = $common_systemic_disorder_group->getErrors();
                }

                $ids[$common_systemic_disorder_group->id] = $common_systemic_disorder_group->id;

                // map to institution if not already mapped
                if (!$common_systemic_disorder_group->hasMapping(ReferenceData::LEVEL_INSTITUTION, $current_institution->id)) {
                    $common_systemic_disorder_group->createMapping(ReferenceData::LEVEL_INSTITUTION, $current_institution->id);
                }
            }
        } else {
            $errors[] = ['Form Error' => ['There has been an error in saving, please contact support.']];
        }

        if (empty($errors)) {
            //Delete items
            $criteria = new CDbCriteria();

            if ($ids) {
                $criteria->addNotInCondition('id', array_map(function ($id) {
                    return $id;
                }, $ids));
            }

            $to_delete = CommonSystemicDisorderGroup::model()->findAllAtLevel(ReferenceData::LEVEL_INSTITUTION, $criteria, $current_institution);

            foreach ($to_delete as $item) {
                // unmap deleted
                $item->deleteMapping(ReferenceData::LEVEL_INSTITUTION, $current_institution->id);

                if (!$item->delete()) {
                    throw new Exception("Unable to delete CommonSystemicDisorderGroup:{$item->primaryKey}");
                }
                Audit::add('admin', 'delete', $item->primaryKey, null, array(
                    'module' => (is_object($this->module)) ? $this->module->id : 'core',
                    'model' => CommonSystemicDisorderGroup::getShortModelName(),
                ));
            }

            $transaction->commit();

            Yii::app()->user->setFlash('success', 'List updated.');
        } else {
            foreach ($errors as $error) {
                foreach ($error as $attribute => $error_array) {
                    $display_errors = '<strong>' . (new CommonSystemicDisorderGroup())->getAttributeLabel($attribute) . ':</strong> ' . implode(', ', $error_array);
                    Yii::app()->user->setFlash('warning.failure-' . $attribute, $display_errors);
                }
            }

            $transaction->rollback();
        }

        $this->redirect(Yii::app()->request->urlReferrer);
    }
}
