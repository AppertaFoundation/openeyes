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
    protected function renderManageLaserProcdures($laser_procs)
    {
        $this->render('list_OphTrLaser_Procedures', array(
            'laser_procs' => $laser_procs,
            'title' => 'Manage Laser Procedures',
        ));
    }
    protected function addNewLaserProcedure($proc_id)
    {
        $laser_proc = new OphTrLaser_LaserProcedure();
        $laser_proc->procedure_id = $proc_id;
        if (!$laser_proc->save()) {
            throw new Exception("Unable to save $proc_id to laser procedure list");
            return;
        }
        $this->logActivity("added procedure $proc_id to laser procedure list");
        Audit::add('admin', 'add-laser-procedure', "added procedure $proc_id to laser procedure list", null, false, array('module' => 'OphTrLaser', 'model' => 'OphTrLaser_LaserProcedure'));
    }
    protected function editLaserProcedures($id, $proc_id)
    {
        $laser_proc = OphTrLaser_LaserProcedure::model()->find('id = :id', array(':id' => $id));
        $procedure = Procedure::model()->find('id = :id', array(':id' => $proc_id));
        if (!$laser_proc) {
            throw new Exception("Unable to find laser procedure item with id $id");
            return;
        }
        if (!$procedure) {
            throw new Exception("Unable to find procedure item with id $proc_id");
            return;
        }
        $previous_proc = $laser_proc->procedure_id;
        $laser_proc->procedure_id = $proc_id;

        if (!$laser_proc->save()) {
            throw new Exception("Unable to update the procedure $previous_proc for laser procedure id $id to procedure $proc_id");
            return;
        }
        $this->logActivity("updated procedure $previous_proc to $proc_id for laser procedure with id $id");
        Audit::add('admin', 'edit-laser-procedure', "updated procedure $previous_proc to $proc_id for laser procedure with id $id", null, false, array('module' => 'OphTrLaser', 'model' => 'OphTrLaser_LaserProcedure'));
    }
    protected function deleteLaserProcedure($id)
    {
        $laser_proc = OphTrLaser_LaserProcedure::model()->find('id = :id', array(':id' => $id));
        if (!$laser_proc) {
            throw new Exception("Unable to find laser procedure item with id $id");
            return;
        }
        if (!$laser_proc->delete()) {
            throw new Exception("Unable to delete laser procedure item with id $id");
            return;
        }
        $this->logActivity("deleted item id $id from laser procedure list");
        Audit::add('admin', 'delete-laser-procedure', "deleted item id $id with procedure $laser_proc->procedure_id from laser procedure list", null, false, array('module' => 'OphTrLaser', 'model' => 'OphTrLaser_LaserProcedure'));
    }

    public function actionManageLaserProcedures()
    {
        $laser_procs = Yii::app()->db->createCommand()
            ->select('ol.id, ol.procedure_id, p.term')
            ->from('ophtrlaser_laserprocedure ol')
            ->join('proc p', 'p.id = ol.procedure_id')
            ->order('p.term')
            ->queryAll();
        $all_procs = Yii::app()->db->createCommand()
            ->select('p.id procedure_id, p.term')
            ->from('proc p')
            ->leftJoin('ophtrlaser_laserprocedure ol', 'p.id = ol.procedure_id')
            ->where('ol.id IS NULL')
            ->group('p.id, p.term')
            ->order('p.term')
            ->queryAll();
        $this->jsVars['laser_procs'] = $laser_procs;
        $this->jsVars['all_procs'] = $all_procs;
        $this->renderManageLaserProcdures($laser_procs);
    }

    public function actionProcessLaserProcedures()
    {
        if (isset($_POST['laser_proc'])) {
            $transaction = Yii::app()->db->beginTransaction();
            try {
                foreach ($_POST['laser_proc'] as $item) {
                    switch ($item['mode']) {
                        case 'create':
                            if (isset($item['proc_id']) && !empty($item['proc_id'])) {
                                $this->addNewLaserProcedure($item['proc_id']);
                            }
                            break;
                        case 'edit':
                            if (
                                (isset($item['id']) && !empty($item['id']))
                                && (isset($item['proc_id']) && !empty($item['proc_id']))
                            ) {
                                $this->editLaserProcedures($item['id'], $item['proc_id']);
                            }
                            break;
                        case 'delete':
                            if (isset($item['id']) && !empty($item['id'])) {
                                $this->deleteLaserProcedure($item['id']);
                            }
                            break;
                        default:
                            break;
                    }
                }
            } catch (Exception $e) {
                $transaction->rollback();
                throw $e;
            }
            $transaction->commit();
        }
        $this->redirect(array('/OphTrLaser/admin/managelaserprocedures'));
    }
}
