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
            : (!$this->checkAccess('admin') ? Institution::model()->getCurrent() : null);

        // Get groups to list
        $disorder_groups_for_institution = $this->getCommonSystemicDisorderGroupsForInstitution($current_institution);

        // Get which groups are in use to ensure they can't be deleted
        $active_group_ids = CommonSystemicDisorder::getDisordersInGroup();

        $this->render('/admin/listcommonsystemicdisordergroup', array(
            'dataProvider' => $disorder_groups_for_institution,
            'active_group_ids' => $active_group_ids,
            'current_institution_id' => $current_institution->id ?? null,
            'current_institution' => $current_institution ?? null
        ));
    }

    public function actionSave()
    {
        $current_institution = $this->request->getParam('institution_id')
            ? Institution::model()->find('id = ' . $this->request->getParam('institution_id'))
            : (!$this->checkAccess('admin') ? Institution::model()->getCurrent() : null);

        $json = $this->parseCommonSystemicDisorderGroupsJson();

        if (!$json) {
            Yii::app()->user->setFlash('warning.failure-form', 'There has been an error in saving, please contact support.');
            $this->redirect(Yii::app()->request->urlReferrer);
        }

        $this->updateCommonSystemicDisorderGroups($current_institution, $json);
    }

    protected function getCommonSystemicDisorderGroupsForInstitution($institution)
    {
        // Get groups to list
        $criteria = new CDbCriteria();
        $criteria->order = 'display_order';
        return new CActiveDataProvider('CommonSystemicDisorderGroup', array(
            'criteria' => CommonSystemicDisorderGroup::model()->getCriteriaForLevels(
                $institution ? ReferenceData::LEVEL_INSTITUTION : ReferenceData::LEVEL_INSTALLATION,
                $criteria,
                $institution
            ),
            'pagination' => false,
        ));
    }

    protected function parseCommonSystemicDisorderGroupsJson()
    {
        $json_string = Yii::app()->request->getParam('CommonSystemicDisorderGroups');

        $json_error = false;
        if (!$json_string || !array_key_exists('JSON_string', $json_string)) {
            $json_error = true;
        }

        $json = json_decode(str_replace("'", '"', $json_string['JSON_string']), true);
        if (json_last_error() != 0) {
            $json_error = true;
        }

        return $json_error ? null : $json;
    }

    protected function updateCommonSystemicDisorderGroups($current_institution, $json)
    {
        $transaction = Yii::app()->db->beginTransaction();

        $display_orders = array_map(function ($entry) {
            return $entry['display_order'];
        }, $json);

        $groups = array_map(function ($entry) {
            return $entry['CommonSystemicDisorderGroup'];
        }, $json);

        list($saved_group_ids, $errors) = $this->saveCommonSystemicDisorderGroups($current_institution, $groups, $display_orders);

        if (!empty($errors)) {
            foreach ($errors as $error) {
                foreach ($error as $attribute => $error_array) {
                    $display_errors = '<strong>' . (new CommonSystemicDisorderGroup())->getAttributeLabel($attribute) . ':</strong> ' . implode(', ', $error_array);
                    Yii::app()->user->setFlash('warning.failure-' . $attribute, $display_errors);
                }
            }

            $transaction->rollback();
            $this->redirect(Yii::app()->request->urlReferrer);
        }

        try {
            $this->deleteOtherCommonSystemicDisorderGroups($current_institution, $saved_group_ids);
        } catch (Exception $e) {
            $transaction->rollback();
        }

        $transaction->commit();

        Yii::app()->user->setFlash('success', 'List updated.');

        $this->redirect(Yii::app()->request->urlReferrer);
    }

    protected function deleteOtherCommonSystemicDisorderGroups($institution, $saved_group_ids)
    {
        //Delete items
        $criteria = new CDbCriteria();

        if ($saved_group_ids) {
            $criteria->addNotInCondition('id', $saved_group_ids);
        }

        $to_delete = CommonSystemicDisorderGroup::model()->findAllAtLevels(
            $institution ? ReferenceData::LEVEL_INSTITUTION : ReferenceData::LEVEL_ALL,
            $criteria,
            $institution
        );

        foreach ($to_delete as $item) {
            // unmap deleted
            if ($institution) {
                // If an institution was selected, only delete the mapping and not the record itself.
                $item->deleteMapping(ReferenceData::LEVEL_INSTITUTION, $institution->id);
            } else {
                // If no institution selected, delete the entire record as well as the mappings at all applicable levels.
                $item->deleteMappings(ReferenceData::LEVEL_INSTALLATION);
                $item->deleteMappings(ReferenceData::LEVEL_INSTITUTION);
                if (!$item->delete()) {
                    throw new Exception("Unable to delete CommonSystemicDisorderGroup:{$item->primaryKey}");
                }

                Audit::add('admin', 'delete', $item->primaryKey, null, array(
                    'module' => (is_object($this->module)) ? $this->module->id : 'core',
                    'model' => CommonSystemicDisorderGroup::getShortModelName(),
                ));
            }
        }
    }

    protected function saveCommonSystemicDisorderGroups($institution, $groups, $display_orders)
    {
        $errors = [];
        $saved_group_ids = [];

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

            $saved_group_ids[] = $common_systemic_disorder_group->id;

            // map to institution if not already mapped
            if ($institution) {
                if (!$common_systemic_disorder_group->hasMapping(ReferenceData::LEVEL_INSTITUTION, $institution->id)) {
                    $common_systemic_disorder_group->createMapping(ReferenceData::LEVEL_INSTITUTION, $institution->id);
                }
            }
        }

        return [$saved_group_ids, $errors];
    }
}
