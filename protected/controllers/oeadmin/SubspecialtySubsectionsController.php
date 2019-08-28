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

class SubspecialtySubsectionsController extends BaseAdminController {
    public $layout = 'admin';
    public $group = 'Core';

    public function actionList()
    {
        $model = SubspecialtySubsection::model();
        $id = Yii::app()->request->getParam('subspecialty_id');
        $model_list = isset($id) ? $model->findAll('subspecialty_id = :sid', [':sid' => $id]) : [];

        $this->render('/oeadmin/subspecialty_subsections/index', [
            'model' => $model,
            'model_list' => $model_list,
            'subspecialty_id' => $id,
        ]);
    }

    public function actionEdit()
    {
        $request = Yii::app()->request;
        $id = $request->getParam('id');
        $subspecialty_id = $request->getParam('subspecialty_id');
        $model = SubspecialtySubsection::model()->findByPk($id);

        if (!$model) {
            $this->redirect(['list?subspecialty_id=' . $subspecialty_id]);
        }

        if ($request->getPost('SubspecialtySubsection')) {
            $model->setAttributes($request->getPost('SubspecialtySubsection'));

            if ($model->save()) {
                Audit::add('admin', 'edit', serialize($model->attributes), false,
                    ['model' => 'SubspecialtySubsection']);
                Yii::app()->user->setFlash('success', 'Subsection edited');
                $this->redirect(['list?subspecialty_id=' . $subspecialty_id]);
            }
        }

        if (isset($subspecialty_id) && !empty($subspecialty_id)) {
            $this->render('/oeadmin/subspecialty_subsections/create', [
                'model' => $model,
                'subspecialty_id' => $subspecialty_id,
                'errors' => $model->getErrors(),
            ]);
        } else {
            $this->redirect(['list']);
        }
    }

    public function actionCreate()
    {
        $model = new SubspecialtySubsection();
        $request = Yii::app()->request;
        $subspecialty_id = $request->getParam('subspecialty_id');

        if ($request->getPost('SubspecialtySubsection')) {
            $model->setAttributes($request->getPost('SubspecialtySubsection'));

            if ($model->save()) {
                Audit::add('admin', 'create', serialize($model->attributes), false,
                    ['model' => 'SubspecialtySubsection']);
                Yii::app()->user->setFlash('success', 'Subsection created');
                $this->redirect(['list?subspecialty_id=' . $subspecialty_id]);
            }
        }

        if (isset($subspecialty_id) && !empty($subspecialty_id)) {
            $this->render('/oeadmin/subspecialty_subsections/create', [
                'model' => $model,
                'subspecialty_id' => $subspecialty_id,
                'errors' => $model->getErrors(),
            ]);
        } else {
            $this->redirect(['list']);
        }
    }

    public function actionDelete()
    {
        $id = Yii::app()->request->getParam('id');
        $subspecialty_id = Yii::app()->request->getParam('subspecialty_id');
        if (!isset($id) || empty($id)) {
            $this->redirect(['list?subspecialty_id=' . $subspecialty_id]);
        }

        $transaction = Yii::app()->db->beginTransaction();
        $success = true;
        $exception_message = null;

        try {
            $subsection = SubspecialtySubsection::model()->findByPk($id);
            if ($subsection) {
                if (!$subsection->delete()) {
                    $success = false;
                } else {
                    Audit::add('admin-subspecialtySubsection', 'delete', serialize($subsection));
                }
            }
        } catch (Exception $e) {
            \OELog::log($e->getMessage());
            $exception_message = $e->getMessage();
            $success = false;
        }

        if ($success) {
            $transaction->commit();
            Yii::app()->user->setFlash('success', 'Subsection deleted');
            $this->redirect(['list?subspecialty_id=' . $subspecialty_id]);
        } else {
            $transaction->rollback();
            $model = SubspecialtySubsection::model()->findByPk($id);
            if (strpos($exception_message, 'foreign key constraint fails') !== false) {
                $model->addError('In use error', 'This subsection could not be deleted as it is in use.');
            }
            $this->render('/oeadmin/subspecialty_subsections/create', [
                'model' => $model,
                'subspecialty_id' => $subspecialty_id,
            ]);
        }
    }
}
