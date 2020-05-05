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

class DefaultController extends \AdminController
{
    public $layout = '//layouts/admin';
    public $group = 'API';

    public function accessRules()
    {
        // Allow logged in users - the main authorisation check happens later in verifyActionAccess
        return array(array('allow',  'roles' => array('admin')));
    }

    public function actionIndex()
    {
        $this->render('index');
    }

    public function actionManualupload()
    {
        $request_type = \Yii::app()->request->getParam('request_type');
        $system_message = \Yii::app()->request->getParam('system_message');

        $errors = [];
        if ($this->request->isPostRequest) {
            $handler = new FormDataHandler($request_type, $system_message, 'multipart/form-data');
            $handler->save();
            $errors = $handler->save_handler->errorSummary();
        }

        $data_provider = new CActiveDataProvider('Request', array(
            'pagination' => array('pageSize' => 20),
            'criteria' => array(
                'order' => 'created_date DESC',
            ),
        ));

        $this->render('manual_upload', [
            'data_provider' => $data_provider,
            'errors' => $errors
        ]);
    }
}
