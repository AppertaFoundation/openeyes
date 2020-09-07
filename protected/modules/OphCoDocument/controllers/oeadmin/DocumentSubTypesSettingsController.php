<?php

/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
class DocumentSubTypesSettingsController extends \ModuleAdminController
{
    public $group = 'Document';

    /**
     * Renders the index page
     */
    public function actionIndex()
    {
        $OphCoDocument_Sub_Types = OphCoDocument_Sub_Types::model();
        $path = Yii::getPathOfAlias('application.widgets.js');
        $generic_admin = Yii::app()->assetManager->publish($path . '/GenericAdmin.js', true);
        Yii::app()->getClientScript()->registerScriptFile($generic_admin);
        //reorder and save
        if (Yii::app()->request->isPostRequest) {
            $sub_types = \Yii::app()->request->getPost('OphCoDocument_Sub_Types', []);
            foreach ($sub_types as $sub_type) {
                $model = $OphCoDocument_Sub_Types->findByPk($sub_type['id']);
                $model->display_order = $sub_type['display_order'];
                $model->save();
            }
        }
        $criteria = new \CDbCriteria();
        $criteria->order = 'display_order';
        $sub_types = $OphCoDocument_Sub_Types->findAll($criteria);

        $this->render(
            '/admin/sub_types/index',
            [
                'sub_types' => $sub_types,
                'pagination' => $this->initPagination($OphCoDocument_Sub_Types),
            ]
        );
    }

    /**
     * Renders the create page
     */
    public function actionCreate()
    {
        $model = new OphCoDocument_Sub_Types;

        $errors = array();

        if (Yii::app()->request->isPostRequest) {
            $model->attributes = $_POST['OphCoDocument_Sub_Types'];
            if ($model->save()) {
                $this->redirect(array('/OphCoDocument/oeadmin/DocumentSubTypesSettings'));
            } else {
                $errors = $model->getErrors();
            }
        }
        $this->render('/admin/sub_types/create', array(
            'errors' => $errors,
            'model' => $model,
        ));
    }

    /**
     * Renders the edit page
     * @param model id $id
     */
    public function actionEdit($id)
    {
        if (!$model = OphCoDocument_Sub_Types::model()->find('`id`=?', array(@$_GET['id']))) {
            $this->redirect(array('/OphCoDocument/oeadmin/DocumentSubTypesSettings'));
        }

        $errors = array();

        if (Yii::app()->request->isPostRequest) {
            $model->attributes = $_POST['OphCoDocument_Sub_Types'];
            if ($model->save()) {
                $this->redirect(array('/OphCoDocument/oeadmin/DocumentSubTypesSettings'));
            } else {
                $errors = $model->errors;
            }
        }

        $this->render('/admin/sub_types/edit', array('model' => $model,
            'errors' => $errors));
    }

    /**
     * Returns the data model based on the primary key given in the GET variable.
     * If the data model is not found, an HTTP exception will be raised.
     *
     * @param int $id the ID of the model to be loaded
     */
    public function loadModel($id)
    {
        $model = OphCoDocument_Sub_Types::model()->findByPk((int)$id);
        if ($model === null) {
            throw new CHttpException(404, 'The requested page does not exist.');
        }

        return $model;
    }
}
