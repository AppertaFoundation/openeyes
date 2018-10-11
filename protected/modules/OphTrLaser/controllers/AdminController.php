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
class AdminController extends ModuleAdminController
{
    //public $defaultAction = "manageLasers";
    public $group = 'Laser';

    public function actionManageLasers()
    {
        $model_list = OphTrLaser_Site_Laser::model()->with('type')->findAll(array('order' => 'display_order asc'));
        //$this->jsVars['OphTrIntravitrealinjection_sort_url'] = $this->createUrl('sortTreatmentDrugs');

        Audit::add('admin', 'list', null, false, array('module' => 'OphTrLaser', 'model' => 'OphTrLaser_Site_Laser'));

        $this->render('list_OphTrLaser_Manage_Lasers', array(
            'model_list' => $model_list,
            'title' => 'Manage Lasers',
            'model_class' => 'OphTrLaser_Site_Laser',
        ));
    }

    public function actionAddLaser()
    {
        $model = new OphTrLaser_Site_Laser();
        $request = Yii::app()->getRequest();

        if ($request->getPost('OphTrLaser_Site_Laser')) {
            $model->attributes = $request->getPost('OphTrLaser_Site_Laser');

            if ($bottom_laser = OphTrLaser_Site_Laser::model()->find(array('order' => 'display_order desc'))) {
                $display_order = $bottom_laser->display_order + 1;
            } else {
                $display_order = 1;
            }
            $model->display_order = $display_order;

            if ($model->save()) {
                Audit::add('admin', 'create', serialize($model->attributes), false, array('module' => 'OphTrLaser', 'model' => 'OphTrLaser_Site_Laser'));
                Yii::app()->user->setFlash('success', 'Laser created');

                $this->redirect(array('ManageLasers'));
            }
        }

        $this->render('edit', array(
            'model' => $model,
            'title' => 'Add Laser',
            'cancel_uri' => '/OphTrLaser/admin/manageLasers',
        ));
    }

    public function actionEditLaser()
    {
        $request = Yii::app()->getRequest();
        if (!$model = OphTrLaser_Site_Laser::model()->findByPk((int) $request->getParam('id'))) {
            throw new Exception('Laser not found with id '.$request->getParam('id'));
        }

        if ($request->getPost('OphTrLaser_Site_Laser')) {
            $model->attributes = $request->getPost('OphTrLaser_Site_Laser');
            if ($model->save()) {
                Audit::add('admin', 'edit_saved', serialize($model->attributes), false, array('module' => 'OphTrLaser', 'model' => 'OphTrLaser_Site_Laser'));
                Yii::app()->user->setFlash('success', 'Laser saved');

                $this->redirect(array('ManageLasers'));
            }
            Audit::add('admin', 'edit_error', serialize($model->attributes), false, array('module' => 'OphTrLaser', 'model' => 'OphTrLaser_Site_Laser'));
            Yii::app()->user->setFlash('success', 'Laser: error saving laser');
        }
        Audit::add('admin', 'edit', serialize($model->attributes), false, array('module' => 'OphTrLaser', 'model' => 'OphTrLaser_Site_Laser'));

        $this->render('edit', array(
            'model' => $model,
            'title' => 'Edit Laser',
            'cancel_uri' => '/OphTrLaser/admin/manageLasers',
        ));
    }

    public function actionViewLaserOperators()
    {
        Audit::add('admin', 'list', null, false, array('module' => 'OphTrLaser', 'model' => 'OphTrLaser_Laser_Operator'));

        $pagination = $this->initPagination(OphTrLaser_Laser_Operator::model());

        $this->render('laser_operators', array(
            'operators' => $this->getItems(array(
                'model' => 'OphTrLaser_Laser_Operator',
                'page' => $pagination->currentPage,
            )),
            'pagination' => $pagination,
        ));
    }

    public function getItems($params)
    {
        $model = $params['model']::model();
        $page = $params['page'];

        $criteria = new CDbCriteria();
        if (isset($params['order'])) {
            $criteria->order = $params['order'];
        } else {
            $criteria->order = 'id asc';
        }
        $criteria->offset = $page * $this->items_per_page;
        $criteria->limit = $this->items_per_page;

        if (!empty($_REQUEST['search'])) {
            $criteria->addSearchCondition('username', $_REQUEST['search'], true, 'OR');
            $criteria->addSearchCondition('first_name', $_REQUEST['search'], true, 'OR');
            $criteria->addSearchCondition('last_name', $_REQUEST['search'], true, 'OR');
        }

        return array(
            'items' => $params['model']::model()->findAll($criteria),
        );
    }

    public function actionAddLaserOperator()
    {
        $errors = array();

        $laser_operator = new OphTrLaser_Laser_Operator();

        if (!empty($_POST)) {
            if (OphTrLaser_Laser_Operator::model()->find('user_id = ?', array($_POST['OphTrLaser_Laser_Operator']['user_id']))) {
                $errors[] = array('This user is already in the list.');
            }

            if (empty($errors)) {
                $laser_operator->attributes = $_POST['OphTrLaser_Laser_Operator'];
                if (!$laser_operator->save()) {
                    $errors = $laser_operator->getErrors();
                } else {
                    Audit::add('admin', 'create', serialize($_POST), false, array('module' => 'OphTrLaser', 'model' => 'OphTrLaser_Laser_Operator'));
                    $this->redirect(array('/OphTrLaser/admin/viewLaserOperators'));
                }
            }
        }

        $this->render('/admin/edit_laser_operator', array(
            'laser_operator' => $laser_operator,
            'errors' => $errors,
        ));
    }

    public function actionEditLaserOperator($id)
    {
        if (!$laser_operator = OphTrLaser_Laser_Operator::model()->findByPk($id)) {
            throw new Exception("Laser operator not found: $id");
        }

        $errors = array();

        if (!empty($_POST)) {
            if ($laser_operator->id) {
                if (OphTrLaser_Laser_Operator::model()->find('id != ? and user_id = ?', array($laser_operator->id, $_POST['OphTrLaser_Laser_Operator']['user_id']))) {
                    $errors[] = array('This user is already in the list.');
                }
            }

            if (empty($errors)) {
                $laser_operator->attributes = $_POST['OphTrLaser_Laser_Operator'];

                if (!$laser_operator->save()) {
                    $errors = $laser_operator->getErrors();
                } else {
                    Audit::add('admin', 'update', serialize(array_merge(array('id' => $id), $_POST)), false, array('module' => 'OphTrLaser', 'model' => 'OphTrLaser_Laser_Operator'));

                    $this->redirect(array('/OphTrLaser/admin/viewLaserOperators'));
                }
            }
        }

        Audit::add('admin', 'view', $id, false, array('module' => 'OphTrLaser', 'model' => 'OphTrLaser_Laser_Operator'));

        $this->render('/admin/edit_laser_operator', array(
            'laser_operator' => $laser_operator,
            'errors' => $errors,
        ));
    }

    public function actionDeleteOperators()
    {
        $criteria = new CDbCriteria();
        $criteria->addInCondition('id', $_POST['operators']);

        OphTrLaser_Laser_Operator::model()->deleteAll($criteria);

        echo '1';
    }
}
