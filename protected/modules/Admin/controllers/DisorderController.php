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
class DisorderController extends BaseAdminController
{
    public function actionList()
    {
        Audit::add('admin', 'list', null, false,
            array('module' => 'OphTrOperationnote',
                'model' => 'Disorder'));
        $search = \Yii::app()->request->getPost('search', ['query' => '']);
        $criteria = new \CDbCriteria();
        if (Yii::app()->request->isPostRequest) {
            if ($search['query']) {
                if (is_numeric($search['query'])) {
                    $criteria->addCondition('id = :id');
                    $criteria->params[':id'] = $search['query'];
                } else {
                    $criteria->addSearchCondition('fully_specified_name', $search['query'], true, 'OR');
                    $criteria->addSearchCondition('term', $search['query'], true, 'OR');
                    $criteria->addSearchCondition('aliases', $search['query'], true, 'OR');
                }
            }
        }
        $this->render('/list_disorder', array(
            'pagination' => $this->initPagination(Disorder::model(), $criteria),
            'model_list' => Disorder::model()->findAll($criteria),
            'title' => 'Manage Disorder',
            'model_class' => 'Disorder',
            'search' => $search
        ));
    }

    public function actionEdit()
    {
        $request = Yii::app()->getRequest();
        $model = Disorder::model()->findByPk((int)$request->getParam('id'));
        if (!$model) {
            throw new Exception('Disorder not found with id ' . $request->getParam('id'));
        }
        if ($request->getPost('Disorder')) {
            $model->attributes = $request->getPost('Disorder');
            if ($model->save()) {
                Yii::app()->user->setFlash('success', 'Disorder saved');
                $this->redirect(array('List'));
            } else {
                $errors = $model->getErrors();
            }
        }

        $this->render('/edit', array(
            'model' => $model,
            'title' => 'Edit Disorder',
            'errors' => isset($errors) ? $errors : null,
            'cancel_uri' => '/Admin/disorder/list',
        ));
    }

    public function actionAdd()
    {
        $model = new Disorder();
        $request = Yii::app()->getRequest();
        if ($request->getPost('Disorder')) {
            $model->attributes = $request->getPost('Disorder');

            if ($model->save()) {
                Audit::add('admin', 'create', serialize($model->attributes), false, array('model' => 'Disorder'));
                Yii::app()->user->setFlash('success', 'Disorder created');
                $this->redirect(array('List'));
            } else {
                $errors = $model->getErrors();
            }
        }
        $this->render('/edit', array(
            'model' => $model,
            'title' => 'Add Disorder',
            'cancel_uri' => '/Admin/Disorder/list',
            'errors' => isset($errors) ? $errors : null,
        ));
    }

    public function actionDelete()
    {
        $result = [];
        $result['status'] = 1;
        $result['errors'] = "";

        if (!empty($_POST['disorders'])) {
            foreach (Disorder::model()->findAllByPk($_POST['disorders']) as $disorder) {
                try {
                    if (!$disorder->delete()) {
                        $result['status'] = 0;
                        $result['errors'][]= $disorder->getErrors();
                    } else {
                        Audit::add('admin-disorder', 'delete', $disorder);
                    }
                } catch (Exception $e) {
                    $result['status'] = 0;
                    $result['errors'][]= "Disorder: " . $disorder->term . " is in use";
                }
            }
        }

        echo json_encode($result);
    }
}