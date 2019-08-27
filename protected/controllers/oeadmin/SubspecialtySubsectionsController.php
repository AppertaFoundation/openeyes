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

    public function actionView() {
        $model = SubspecialtySubsection::model();
        $id = Yii::app()->request->getParam('subspecialty_id');
        $model_list = isset($id) ? $model->findAll('subspecialty_id = :sid', [':sid' => $id]) : [];

        $this->render('/oeadmin/subspecialty_subsections/index', [
            'model' => $model,
            'model_list' => $model_list,
            's_id' => $id,
        ]);
    }

    public function actionEdit() {
        $request = Yii::app()->request; 
        $id = $request->getParam('id');
        $s_id = $request->getParam('subspecialty_id');
        $model = SubspecialtySubsection::model()->findByPk($id);

        if (!$model) {
            $this->redirect(['view?subspecialty_id=' . $s_id]);
        }

        if ($request->getPost('SubspecialtySubsection')) {
            $model->setAttributes($request->getPost('SubspecialtySubsection'));

            if ($model->save()) {
                Audit::add('admin', 'edit', serialize($model->attributes), false,
                    ['model' => 'SubspecialtySubsection']);
                Yii::app()->user->setFlash('success', 'Subsection edited');
                $this->redirect(['view?subspecialty_id=' . $s_id]);
            }
        }

        if (isset($s_id) && !empty($s_id)) {
            $this->render('/oeadmin/subspecialty_subsections/create', [
                'model' => $model,
                's_id' => $s_id,
                'errors' => $model->getErrors(),
            ]);
        } else {
            $this->redirect(['view']);
        }
    }

    public function actionCreate() {
        $model = new SubspecialtySubsection();
        $request = Yii::app()->request; 
        $s_id = $request->getParam('subspecialty_id');

        if ($request->getPost('SubspecialtySubsection')) {
            $model->setAttributes($request->getPost('SubspecialtySubsection'));

            if ($model->save()) {
                Audit::add('admin', 'create', serialize($model->attributes), false,
                    ['model' => 'SubspecialtySubsection']);
                Yii::app()->user->setFlash('success', 'Subsection created');
                $this->redirect(['view?subspecialty_id=' . $s_id]);
            }
        }

        if (isset($s_id) && !empty($s_id)) {
            $this->render('/oeadmin/subspecialty_subsections/create', [
                'model' => $model,
                's_id' => $s_id,
                'errors' => $model->getErrors(),
            ]);
        } else {
            $this->redirect(['view']);
        }
    }

    public function actionDelete() {
        $id = Yii::app()->request->getParam('id');
        $s_id = Yii::app()->request->getParam('subspecialty_id');
        if (!isset($id) || empty($id)) {
            $this->redirect(['view?subspecialty_id=' . $s_id]);
        }

        $transaction = Yii::app()->db->beginTransaction();
        $success = true;

        try {
            $subsection = SubspecialtySubsection::model()->findByPk($id);
            if ($subsection) {
                if (!$subsection->delete()) {
                    $success = false;
                } else {
                    Audit::add('admin-subspecialtySubsection', 'delete', serialize($subsection));
                }
            }
        } catch (Excpetion $e) {
            \OELog::log($e->getMessage());
            $success = false;
        }

        if ($success) {
            $transaction->commit();
        } else {
            $transaction->rollback();
        }

        $this->redirect(['view?subspecialty_id=' . $s_id]);
    }
}
