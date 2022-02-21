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

use OEModule\OphCiExamination\models\OphCiExamination_ClinicOutcome_Role;

class ClinicOutcomeRolesController extends \ModuleAdminController
{

    public function actions()
    {
        return [
        'sortClinicOutcomeRoles' => [
        'class' => 'SaveDisplayOrderAction',
        'model' => OphCiExamination_ClinicOutcome_Role::model(),
        'modelName' => 'OphCiExamination_ClinicOutcome_Role',
        ],
        ];
    }

  /**
   * Lists all models.
   */
    public function actionIndex()
    {
        $this->group = 'Examination';
        $assetManager = Yii::app()->getAssetManager();
        $assetManager->registerScriptFile('/js/oeadmin/OpenEyes.admin.js');
        $assetManager->registerScriptFile('/js/oeadmin/list.js');

        $this->render('/clinicoutcomeroles/index', [
        'model' => OphCiExamination_ClinicOutcome_Role::model(),
        'model_list' => OphCiExamination_ClinicOutcome_Role::model()->findAll([
            'condition' => 'institution_id = :institution_id',
            'params' => [':institution_id' => Yii::app()->session['selected_institution_id']],
            'order' => 'display_order']),
        ]);
    }

  /**
   * Updates a particular model.
   * If update is successful, the browser will be redirected to the 'view' page.
   * @throws Exception
   */
    public function actionUpdate()
    {
        $request = Yii::app()->getRequest();
        $model = OphCiExamination_ClinicOutcome_Role::model()->findByPk((int)$request->getParam('id'));
        if (!$model) {
            throw new Exception('OphCiExamination_ClinicOutcome_Role not found with id ' . $request->getParam('id'));
        }
        if ($request->getPost('OEModule_OphCiExamination_models_OphCiExamination_ClinicOutcome_Role')) {
            $model->attributes = $request->getPost('OEModule_OphCiExamination_models_OphCiExamination_ClinicOutcome_Role');
            if ($model->save()) {
                Yii::app()->user->setFlash('success', 'Follow-up Role saved');
                $this->redirect(['index']);
            } else {
                $errors = $model->getErrors();
            }
        }

        $this->render('/clinicoutcomeroles/edit', [
        'model' => $model,
        'errors' => isset($errors) ? $errors : null,
        ]);
    }

  /**
   * Creates a new model.
   * If creation is successful, the browser will be redirected to the 'view' page.
   */
    public function actionCreate()
    {
        $model = new OphCiExamination_ClinicOutcome_Role();
        $request = Yii::app()->getRequest();
        if ($request->getPost('OEModule_OphCiExamination_models_OphCiExamination_ClinicOutcome_Role')) {
            $model->attributes = $request->getPost('OEModule_OphCiExamination_models_OphCiExamination_ClinicOutcome_Role');

            if ($model->save()) {
                Audit::add(
                    'admin',
                    'create',
                    serialize($model->attributes),
                    false,
                    ['model' => 'OEModule_OphCiExamination_models_OphCiExamination_ClinicOutcome_Role']
                );
                Yii::app()->user->setFlash('success', 'Follow-up Role created');
                $this->redirect(['index']);
            } else {
                $errors = $model->getErrors();
            }
        }
        $this->render('/clinicoutcomeroles/edit', [
        'model' => $model,
        'errors' => isset($errors) ? $errors : null,
        ]);
    }

  /**
   * Deletes the selected models
   */
    public function actionDelete()
    {
        $delete_ids = isset($_POST['select']) ? $_POST['select'] : [];
        $transaction = Yii::app()->db->beginTransaction();
        $success = true;
        try {
            foreach ($delete_ids as $role_id) {
                $role = OphCiExamination_ClinicOutcome_Role::model()->findByPk($role_id);
                if ($role) {
                    if (!$role->delete()) {
                        $success = false;
                        break;
                    } else {
                        Audit::add('admin-clinic-outcome-role', 'delete', $role);
                    }
                }
            }
        } catch (Exception $e) {
            \OELog::log($e->getMessage());
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
