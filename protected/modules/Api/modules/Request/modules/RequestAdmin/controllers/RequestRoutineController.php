<?php
/**
 * (C) Copyright Apperta Foundation 2020
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2017, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

class RequestRoutineController extends \AdminController
{
    public $layout = '//layouts/admin';
    public $group = 'API';

    public function actionEdit($id)
    {
        $request = Yii::app()->getRequest();
        $model = RequestRoutine::model()->findByPk($id);
        if (!$model) {
            throw new Exception('Request Routine  not found with id ' . $id);
        }
        if ($request->getPost('RequestRoutine')) {
            $model->attributes = $request->getPost('RequestRoutine');

            if ($model->next_try_date_time === "") {
                $model->next_try_date_time = null;
            }
            if ($model->save()) {
                Yii::app()->user->setFlash('success', 'Request Routine saved');
                $this->redirect(array('/Api/Request/admin/request/index'));
            } else {
                $errors = $model->getErrors();
            }
        }

        $this->render('/requestRoutine/edit', array(
            'model' => $model,
            'title' => 'Edit Request Routine',
            'errors' => isset($errors) ? $errors : null,
            'cancel_uri' => '/Api/Request/admin/request/index',
        ));
    }
}
