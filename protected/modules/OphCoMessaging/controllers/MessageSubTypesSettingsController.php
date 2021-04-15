<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2020
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2020, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

namespace OEModule\OphCoMessaging\controllers;

use OEModule\OphCoMessaging\models\OphCoMessaging_Message_MessageType;

class MessageSubTypesSettingsController extends \ModuleAdminController
{
    public $group = 'Message';

    /**
     * Renders the index page
     * @throws \CHttpException
     */
    public function actionIndex()
    {
        if (!$this->checkAccess('admin')) {
            throw new \CHttpException(403, 'Only system admins may access these settings.');
        }
        $OphCoMessaging_message_types = OphCoMessaging_Message_MessageType::model();
        $path = \Yii::getPathOfAlias('application.widgets.js');
        $generic_admin = \Yii::app()->assetManager->publish($path . '/GenericAdmin.js', true);
        \Yii::app()->getClientScript()->registerScriptFile($generic_admin);
        if (\Yii::app()->request->isPostRequest) {
            $sub_types = \Yii::app()->request->getPost('OEModule_OphCoMessaging_models_OphCoMessaging_Message_MessageType', []);
            foreach ($sub_types as $sub_type) {
                $model = $OphCoMessaging_message_types->findByPk($sub_type['id']);
                $model->display_order = $sub_type['display_order'];
                $model->save();
            }
        }
        $criteria = new \CDbCriteria();
        $criteria->order = 'display_order';

        $sub_types = $OphCoMessaging_message_types->findAll($criteria);

        $this->render(
            '/admin/index',
            [
                'sub_types' => $sub_types,
                'pagination' => $this->initPagination($OphCoMessaging_message_types),
            ]
        );
    }

    /**
     * Renders the create page
     * @throws \CHttpException
     */
    public function actionCreate()
    {
        if (!$this->checkAccess('admin')) {
            throw new \CHttpException(403, 'Only system admins may access these settings.');
        }
        $model = new OphCoMessaging_Message_MessageType();

        $errors = array();

        if (\Yii::app()->request->isPostRequest) {
            $model->attributes = $_POST['OEModule_OphCoMessaging_models_OphCoMessaging_Message_MessageType'];
            if ($model->save()) {
                $this->redirect(array('/OphCoMessaging/MessageSubTypesSettings'));
            } else {
                $errors = $model->getErrors();
            }
        }
        $this->render('/admin/edit', array(
            'errors' => $errors,
            'model' => $model,
        ));
    }

    /**
     * Renders the edit page
     * @throws \CHttpException
     */
    public function actionEdit()
    {
        if (!$this->checkAccess('admin')) {
            throw new \CHttpException(403, 'Only system admins may access these settings.');
        }
        if (!$model = OphCoMessaging_Message_MessageType::model()->find('`id`=?', array(@$_GET['id']))) {
            $this->redirect(array('/OphCoMessaging/MessageSubTypesSettings'));
        }

        $errors = array();

        if (\Yii::app()->request->isPostRequest) {
            $model->attributes = $_POST['OEModule_OphCoMessaging_models_OphCoMessaging_Message_MessageType'];
            if ($model->save()) {
                $this->redirect(array('/OphCoMessaging/MessageSubTypesSettings'));
            } else {
                $errors = $model->errors;
            }
        }

        $this->render('/admin/edit', array(
            'model' => $model,
            'errors' => $errors,
        ));
    }

    /**
     * Returns the data model based on the primary key given in the GET variable.
     * If the data model is not found, an HTTP exception will be raised.
     *
     * @param int $id the ID of the model to be loaded
     * @return OphCoMessaging_Message_MessageType
     * @throws \CHttpException
     */
    public function loadModel($id)
    {
        $model = OphCoMessaging_Message_MessageType::model()->findByPk((int)$id);
        if ($model === null) {
            throw new \CHttpException(404, 'The requested page does not exist.');
        }

        return $model;
    }
}
