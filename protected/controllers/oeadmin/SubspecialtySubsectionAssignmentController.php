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

class SubspecialtySubsectionAssignmentController extends BaseAdminController
{
    public $layout = 'admin';
    public $group = 'Procedure Management';

    public function actionList()
    {
        $subspecialty_id = Yii::app()->request->getParam('subspecialty_id');
        $subsection_id = Yii::app()->request->getParam('subsection_id');
        if ($this->checkAccess('admin')) {
            $institution_id = Yii::app()->request->getParam('institution_id');
        } else {
            $institution_id = Institution::model()->getCurrent()->id;
        }

        $this->render('/oeadmin/subspecialty_subsection_assignment/index', [
            'subspecialty_id' => $subspecialty_id,
            'subsection_id' => $subsection_id,
            'institution_id' => $institution_id
        ]);
    }

    /**
     * @throws Exception
     */
    public function actionAdd()
    {
        /**
         * @var $request HttpRequest
         */
        $request = Yii::app()->request;
        $model = new ProcedureSubspecialtySubsectionAssignment();
        $subspecialty_id = $request->getParam('subspecialty_id');
        $institution_id = $request->getParam('institution_id');
        $attributes = [
            'subspecialty_subsection_id' => $request->getParam('subsection_id'),
            'proc_id' => $request->getParam('procedure_id'),
            'institution_id' => $institution_id ?? Institution::model()->getCurrent()->id,
        ];

        $model->setAttributes($attributes);

        if ($model->save()) {
            Audit::add(
                'admin',
                'add',
                serialize($model->attributes),
                false,
                ['model' => 'ProcedureSubspecialtySubsectionAssignment']
            );
            Yii::app()->user->setFlash('success', 'Assignment added');
            $this->redirect(['list?subspecialty_id=' . $subspecialty_id .
                '&subsection_id=' . $attributes['subspecialty_subsection_id'] .
                '&institution_id=' . $attributes['institution_id']]);
        } else {
            $this->render('/oeadmin/subspecialty_subsection_assignment/index', [
                'model' => $model,
                'subspecialty_id' => $subspecialty_id,
                'subsection_id' => $attributes['subspecialty_subsection_id'],
                'institution_id' => $institution_id
            ]);
        }
    }

    public function actionDelete()
    {
        $delete_ids = Yii::app()->request->getPost('select', []);
        $transaction = Yii::app()->db->beginTransaction();
        $success = true;

        try {
            foreach ($delete_ids as $id) {
                $assignment = ProcedureSubspecialtySubsectionAssignment::model()->findByPk($id);
                if ($assignment) {
                    if (!$assignment->delete()) {
                        $success = false;
                        break;
                    }

                    Audit::add('admin-procedureSubspecialtySubsectionAssignment', 'delete', serialize($assignment));
                }
            }
        } catch (Exception $e) {
            OELog::log($e->getMessage());
            $success = false;
        }

        if ($success) {
            $transaction->commit();
            echo '1';
        } else {
            $transaction->rollback();
            echo '0';
        }
    }
}
