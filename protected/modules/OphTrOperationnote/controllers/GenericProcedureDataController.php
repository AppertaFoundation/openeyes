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
class GenericProcedureDataController extends ModuleAdminController
{
    public $group = 'Operation Note';

    public function actionList()
    {
        Audit::add('admin', 'list', null, false,
            array('module' => 'OphTrOperationnote',
                'model' => 'OphTrOperationNote_Generic_Procedure_Data'));

        $search = \Yii::app()->request->getPost('search', ['query' => '']);
        $criteria = new \CDbCriteria();
        $criteria->with = 'procedure';
        $criteria->order = 'term asc';

        if (Yii::app()->request->isPostRequest) {
            if ($search['query']) {
                if (is_numeric($search['query'])) {
                    $criteria->addCondition('id = :id');
                    $criteria->params[':id'] = $search['query'];
                } else {

                    $criteria->addSearchCondition('procedure.term', $search['query'], true, 'OR');
                    $criteria->addSearchCondition('proc_id', $search['query'], true, 'OR');
                }
            }
        }

        $this->render('/admin/list_OphTrOperationNote_Generic_Procedure_Data', array(
            'pagination' => $this->initPagination(OphTrOperationNote_Generic_Procedure_Data::model(), $criteria),
            'model_list' => OphTrOperationNote_Generic_Procedure_Data::model()->findAll($criteria),
            'title' => 'Manage Generic Procedure Data',
            'model_class' => 'OphTrOperationNote_Generic_Procedure_Data',
            'search' => $search
        ));
    }

    public function actionEdit()
    {
        $request = Yii::app()->getRequest();
        $model = OphTrOperationNote_Generic_Procedure_Data::model()->findByPk((int)$request->getParam('id'));
        if (!$model) {
            throw new Exception('Generic Procedure Data not found with id ' . $request->getParam('id'));
        }

        if ($request->getPost('OphTrOperationNote_Generic_Procedure_Data')) {
            $model->attributes = $request->getPost('OphTrOperationNote_Generic_Procedure_Data');
            if ($model->save()) {
                Audit::add('admin', 'edit_saved',
                    serialize($model->attributes),
                    false,
                    array('module' => 'OphTrOperationnote', 'model' => 'OphTrOperationNote_Generic_Procedure_Data')
                );
                Yii::app()->user->setFlash('success', 'Generic Operation Data saved');

                $this->redirect(array('List'));
            }
            Audit::add('admin', 'edit_error', serialize($model->attributes),
                false,
                array('module' => 'OphTrOperationnote', 'model' => 'OphTrOperationNote_Generic_Procedure_Data')
            );
            Yii::app()->user->setFlash('success', 'Generic Operation data: error saving');
        }
        Audit::add('admin', 'edit', serialize($model->attributes),
            false,
            array('module' => 'OphTrOperationnote', 'model' => 'OphTrOperationNote_Generic_Procedure_Data')
        );

        $this->render('/admin/edit', array(
            'model' => $model,
            'title' => 'Edit Generic Operation Data',
            'cancel_uri' => '/OphTrOperationnote/GenericProcedureData/list',
        ));
    }

    public function actionAdd()
    {
        $model = new OphTrOperationNote_Generic_Procedure_Data();
        $request = Yii::app()->getRequest();

        if ($request->getPost('OphTrOperationNote_Generic_Procedure_Data')) {
            $model->attributes = $request->getPost('OphTrOperationNote_Generic_Procedure_Data');


            if ($model->save()) {
                Audit::add('admin', 'create', serialize($model->attributes), false, array('module' => 'OphTrOperationnote', 'model' => 'OphTrOperationNote_Generic_Procedure_Data'));
                Yii::app()->user->setFlash('success', 'Operation Generic Data created');

                $this->redirect(array('List'));
            }
        }

        $this->render('edit', array(
            'model' => $model,
            'title' => 'Add Generic Procedure Data',
            'cancel_uri' => '/OphTrOperationnote/GenericProcedureData/list',
        ));
    }

    public function actionDelete()
    {
        $criteria = new CDbCriteria();
        $criteria->addInCondition('id', $_POST['genericProcedures']);

        OphTrOperationNote_Generic_Procedure_Data::model()->deleteAll($criteria);
        echo '1';
    }


}