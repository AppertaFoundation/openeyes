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
        $criteria = new CDbCriteria();
        $criteria->with = 'site';
        $criteria->condition = 't.institution_id = :institution_id OR (t.institution_id is null AND site.institution_id = :institution_id)';
        $criteria->params = [':institution_id' => Yii::app()->session['selected_institution_id']];
        $criteria->order = 'display_order asc';
        $model_list = OphTrLaser_Site_Laser::model()->with('type')->findAll($criteria);

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

    public function actionManageLaserProcedures()
    {
        if (Yii::app()->user->checkAccess('admin')) {
            // Show all procedures
            $model_list = OphTrLaser_LaserProcedure::model()->findAll();
        } else {
            // Show procedures only at institution level
            $model_list = OphTrLaser_LaserProcedure::model()
                ->with(['institutions' => [
                    'condition' => 'institutions_institutions.institution_id = :institution_id',
                    'params' => [':institution_id' => Yii::app()->session['selected_institution_id']],
                ]])
                ->findAll();
        }
        $this->render('list_OphTrLaser_Procedure', [
            'model_list' => $model_list
        ]);
    }

    public function actionDeleteLaserProcedure($id)
    {
        $laser_procedure = OphTrLaser_LaserProcedure::model()->findByPk($id);
        $institution = Institution::model()->getCurrent();
        if (!$laser_procedure) {
            throw new Exception("Unable to find laser procedure item with id $id");
        }
        $transaction = Yii::app()->db->beginTransaction();
        try {
            if (Yii::app()->user->checkAccess('admin')) {
                // Only admins can delete instance at installation level
                $laser_procedure->deleteMappings(ReferenceData::LEVEL_INSTITUTION);
                $laser_procedure->delete();
                $this->logActivity("deleted item id $id from laser procedure list");
                Audit::add('admin', 'delete-laser-procedure', "deleted item id $id with procedure $laser_procedure->procedure_id from laser procedure list", null, false, array('module' => 'OphTrLaser', 'model' => 'OphTrLaser_LaserProcedure'));
            } else {
                // Delete only institution level instance
                $laser_procedure->deleteMapping(ReferenceData::LEVEL_INSTITUTION, $institution->id);
                $this->logActivity("deleted item id $id from laser procedure list for institution $institution->name");
                Audit::add('admin', 'delete-laser-procedure', "deleted item id $id with procedure $laser_procedure->procedure_id from laser procedure list for institution $institution->name",
                    null,
                    false,
                    array('module' => 'OphTrLaser', 'model' => 'OphTrLaser_LaserProcedure'));
            }
            $transaction->commit();
            $this->redirect(['/OphTrLaser/admin/manageLaserProcedures']);
        } catch (Exception $e) {
            $transaction->rollback();
            throw new CHttpException(500, $e->getMessage(), true);
        }
    }

    public function actionAddLaserProcedure()
    {
        if (Yii::app()->request->isPostRequest) {
            $procedure = $_POST['Procedure'];
            if (Yii::app()->user->checkAccess('admin')) {
                $institutions = $_POST['OphTrLaser_LaserProcedure']['institutions'];
            } else {
                $institutions[] = Institution::model()->getCurrent()->id;
            }

            try {
                $laser_procedure = new OphTrLaser_LaserProcedure();
                $laser_procedure->procedure_id = $procedure['proc_id'];
                $laser_procedure->save();

                if (!empty($institutions)) {
                        $laser_procedure->createMappings(ReferenceData::LEVEL_INSTITUTION, $institutions);
                }
            } catch (Exception $e) {
                throw new CHttpException(500, $e->getMessage(), true);
            }
            $this->redirect(['/OphTrLaser/admin/manageLaserProcedures']);
        }

        $this->setJSVars();
        $model = new OphTrLaser_LaserProcedure();
        $this->render('edit_OphTrLaser_Procedure', [
            'laser_procedure' => $model,
        ]);
    }

    public function actionEditLaserProcedure($id = null)
    {
        if (Yii::app()->request->isPostRequest) {
            $procedure = $_POST['Procedure'];
            $institutions = $_POST['OphTrLaser_LaserProcedure']['institutions'];

            try {
                $laser_procedure = OphTrLaser_LaserProcedure::model()->findByPk($id);
                $laser_procedure->procedure_id = $procedure['proc_id'];
                $laser_procedure->save();

                OphTrLaser_LaserProcedure_Institution::model()->deleteAll('laserprocedure_id = :procedure_id', array(':procedure_id' => $id));
                if (!empty($institutions)) {
                    $laser_procedure->createMappings(ReferenceData::LEVEL_INSTITUTION, $institutions);
                }
            } catch (Exception $e) {
                throw new CHttpException(500, $e->getMessage(), true);
            }
            $this->redirect(['/OphTrLaser/admin/manageLaserProcedures']);
        }

        $this->setJSVars();

        $this->render('edit_OphTrLaser_Procedure', [
            'laser_procedure' => OphTrLaser_LaserProcedure::model()->findByPk($id),
        ]);
    }

    public function setJSVars()
    {
        $laser_procs = Yii::app()->db->createCommand()
            ->select('ol.id, ol.procedure_id, p.term')
            ->from('ophtrlaser_laserprocedure ol')
            ->join('proc p', 'p.id = ol.procedure_id')
            ->group('p.id, p.term')
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
    }
}
