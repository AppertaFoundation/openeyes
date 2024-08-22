<?php

/**
 * (C) OpenEyes Foundation, 2019
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

class ProcedureSubspecialtyAssignmentController extends \BaseAdminController
{
    public $group = 'Procedure Management';

    public function actionEdit()
    {
        $institution = Institution::model()->getCurrent();
        $institution_id = $institution->id;
        $subspecialty_id = Yii::app()->getRequest()->getParam('subspecialty_id');

        // make sure only admin can accept institution_id from param
        if ($this->checkAccess('admin')) {
            $institution_id = \Yii::app()->request->getParam(
                'institution_id',
                $institution_id
            );
        }

        $procedures = Procedure::model()->findAll(['order' => 'term']);
        $procedure_options = array_map(function ($procedure) {
            return $procedure->getAttributes(["id", "term"]);
        }, $procedures);

        $this->jsVars['procedure_options'] = $procedure_options;

        $institutions = Institution::model()->getTenanted();
        if ($this->checkAccess('admin')) {
            $institution_options = array_map(
                static function ($institution) {
                    return $institution->getAttributes(["id", "name"]);
                },
                $institutions
            );
        } else {
            $institution_options = [$institution->getAttributes(["id", "name"])];
        }

        $this->jsVars['institution_options'] = $institution_options;

        if (Yii::app()->request->isPostRequest) {
            $transaction = Yii::app()->db->beginTransaction();
            try {
                $display_orders = Yii::app()->request->getParam('display_order', []);
                $assignments = Yii::app()->request->getParam('ProcedureSubspecialtyAssignment', []);

                $ids = [];
                foreach ($assignments as $key => $assignment_id) {
                    $assignment = ProcedureSubspecialtyAssignment::model()->findByPk($assignment_id['id']);
                    if (!$assignment) {
                        $assignment = new ProcedureSubspecialtyAssignment();
                    }

                    $assignment->proc_id = $assignment_id['procedure_id'];
                    $assignment->display_order = $display_orders[$key];
                    $assignment->subspecialty_id = $subspecialty_id;
                    $assignment->need_eur = $assignment_id['need_eur'] ?? 0;

                    if ($this->checkAccess('admin')) {
                        $assignment->institution_id = $assignment_id['institution_id'];
                    } else {
                        $assignment->institution_id = $institution_id;
                    }
                    if (!$assignment->save()) {
                        $errors[] = $assignment->getErrors();
                    }

                    $ids[] = $assignment->id;
                }

                // Delete items
                $criteria = new CDbCriteria();

                if ($ids) {
                    $criteria->addNotInCondition('id', $ids);
                }

                $criteria->compare('subspecialty_id', $subspecialty_id);
                $criteria->compare('institution_id', $institution_id);

                $to_delete = ProcedureSubspecialtyAssignment::model()->findAll($criteria);

                foreach ($to_delete as $item) {
                    if (!$item->delete()) {
                        $errorMessage = "Model ProcedureSubspecialtyAssignment could not be deleted";
                        $errors[] = ['id' => [$errorMessage]];
                        \OELog::log($errorMessage . " (ID = $item->id )");
                    } else {
                        Audit::add('admin', 'delete', $item->primaryKey, null, [
                        'module' => (is_object($this->module)) ? $this->module->id : 'core',
                        'model' => ProcedureSubspecialtyAssignment::getShortModelName(),
                        ]);
                    }
                }
            } catch (Exception $e) {
                $errorMessage = $e->getMessage();
                \OELog::log($errorMessage);
                $errors[] = ['id' => [$errorMessage]];
            }

            if (empty($errors)) {
                $transaction->commit();
                Yii::app()->user->setFlash('success', 'List updated.');
            } else {
                $transaction->rollback();
                foreach ($errors as $error) {
                    foreach ($error as $attribute => $error_array) {
                        $display_errors = '<strong>' . $assignment->getAttributeLabel($attribute) .
                          ':</strong> ' . implode(', ', $error_array);
                        Yii::app()->user->setFlash('warning.failure-' . $attribute, $display_errors);
                    }
                }
            }
            $this->redirect(Yii::app()->request->url);
        }

        $generic_admin = Yii::app()->assetManager->publish(Yii::getPathOfAlias('application.widgets.js') . '/GenericAdmin.js', true);
        Yii::app()->getClientScript()->registerScriptFile($generic_admin);

        $criteria = new CDbCriteria();
        $criteria->order = 'display_order';
        $criteria->compare('subspecialty_id', $subspecialty_id);
        $criteria->compare('institution_id', $institution_id);

        $this->render('/edit_ProcedureSubspecialtyAssignment', [
        'dataProvider' => new CActiveDataProvider('ProcedureSubspecialtyAssignment', [
        'criteria' => $criteria,
        'pagination' => false,
        ]),
        'subspecialty_id' => $subspecialty_id,
        'subspecialities' => Subspecialty::model()->findAll(),
        'procedure_list' => $procedures,
        'institutions' => $institutions,
        'institution_id' => $institution_id
        ]);
    }
}
