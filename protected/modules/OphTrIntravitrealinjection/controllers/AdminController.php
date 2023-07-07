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
    public $defaultAction = 'ViewAllOphTrIntravitrealinjection_Treatment_Drug';
    public $group = 'Intravitreal injection';

    public function actionViewTreatmentDrugs()
    {
        $model_list = OphTrIntravitrealinjection_Treatment_Drug::model()->findAll(array('order' => 'display_order asc'));
        $this->jsVars['OphTrIntravitrealinjection_sort_url'] = $this->createUrl('sortTreatmentDrugs');

        Audit::add('admin', 'list', null, null, array('module' => 'OphTrIntravitrealinjection', 'model' => 'OphTrIntravitrealinjection_Treatment_Drug'));

        $this->render('list_OphTrIntravitrealinjection_Treatment_Drug', array(
                'model_list' => $model_list,
                'title' => 'Treatment Drugs',
                'model_class' => 'OphTrIntravitrealinjection_Treatment_Drug',
        ));
    }

    public function actionAddTreatmentDrug()
    {
        $model = new OphTrIntravitrealinjection_Treatment_Drug();

        if (isset($_POST['OphTrIntravitrealinjection_Treatment_Drug'])) {
            $model->attributes = $_POST['OphTrIntravitrealinjection_Treatment_Drug'];

            if ($bottom_drug = OphTrIntravitrealinjection_Treatment_Drug::model()->find(array('order' => 'display_order desc'))) {
                $display_order = $bottom_drug->display_order + 1;
            } else {
                $display_order = 1;
            }
            $model->display_order = $display_order;

            if ($model->save()) {
                Audit::add('admin', 'create', $model->id, null, array('module' => 'OphTrIntravitrealinjection', 'model' => 'OphTrIntravitrealinjection_Treatment_Drug'));
                Yii::app()->user->setFlash('success', 'Treatment drug created');

                $this->redirect(array('ViewTreatmentDrugs'));
            }
        }

        $this->render('update', array(
            'model' => $model,
            'title' => 'Add Treatment Drug',
            'cancel_uri' => '/OphTrIntravitrealinjection/admin/viewTreatmentDrugs',
        ));
    }

    public function actionEditTreatmentDrug($id)
    {
        if (!$model = OphTrIntravitrealinjection_Treatment_Drug::model()->findByPk((int) $id)) {
            throw new Exception('Treatment drug not found with id ' . $id);
        }

        if (isset($_POST['OphTrIntravitrealinjection_Treatment_Drug'])) {
            $model->attributes = $_POST['OphTrIntravitrealinjection_Treatment_Drug'];

            if ($model->save()) {
                Audit::add('admin', 'update', $model->id, null, array('module' => 'OphTrIntravitrealinjection', 'model' => 'OphTrIntravitrealinjection_Treatment_Drug'));
                Yii::app()->user->setFlash('success', 'Treatment drug updated');

                $this->redirect(array('ViewTreatmentDrugs'));
            }
        }

        $this->render('update', array(
            'model' => $model,
            'title' => 'Edit Treatment Drug',
            'cancel_uri' => '/OphTrIntravitrealinjection/admin/viewTreatmentDrugs',
        ));
    }

    /*
     * sorts the drugs into the provided order (NOTE does not support a paginated list of drugs)
     */
    public function actionSortTreatmentDrugs()
    {
        if (!empty($_POST['order'])) {
            foreach ($_POST['order'] as $i => $id) {
                if ($drug = OphTrIntravitrealinjection_Treatment_Drug::model()->findByPk($id)) {
                    $drug->display_order = $i + 1;
                    if (!$drug->save()) {
                        throw new Exception('Unable to save drug: ' . print_r($drug->getErrors(), true));
                    }
                }
            }
        }
    }

    public function actionDeleteTreatmentDrugs()
    {
        $result = 1;

        foreach (OphTrIntravitrealinjection_Treatment_Drug::model()->findAllByPk($_POST['treatment_drugs']) as $drug) {
            if (!$drug->delete()) {
                $result = 0;
            }
        }

        echo $result;
    }

    public function actionManageIOPLoweringDrugs()
    {
        $this->genericAdmin('Edit IOP Lowering Drugs', 'OphTrIntravitrealinjection_IOPLoweringDrug', ['div_wrapper_class' => 'cols-5']);
    }

    public function actionManageSkinDrugs()
    {
        $this->genericAdmin('Edit Skin cleansing drugs', 'OphTrIntravitrealinjection_SkinDrug', ['div_wrapper_class' => 'cols-5']);
    }

    public function actionManageAntisepticDrugs()
    {
        $this->genericAdmin('Edit Antiseptic drugs', 'OphTrIntravitrealinjection_AntiSepticDrug', ['div_wrapper_class' => 'cols-5']);
    }

    public function actionInjectionUsers()
    {
        $injection_users = OphTrIntravitrealinjection_InjectionUser::model()->with(array('user'))->findAll(array('order' => 'user.last_name, user.first_name'));
        $user_ids = CHtml::listData($injection_users, 'id', 'user.id');

        $criteria = new CDbCriteria();
        $criteria->order = 'first_name asc, last_name asc';

        if (!empty($user_ids)) {
            $criteria->addNotInCondition('id', $user_ids);
        }

        $user_list = User::model()->findAll($criteria);

        $this->render('injection_users', array(
            'injection_users' => $injection_users,
            'user_list' => $user_list,
        ));
    }

    public function actionAddInjectionUser()
    {
        if (!$user = User::model()->findByPk(@$_POST['user_id'])) {
            throw new Exception('User not found: ' . @$_POST['user_id']);
        }

        $injection_user = new OphTrIntravitrealinjection_InjectionUser();
        $injection_user->user_id = $user->id;

        if (!$injection_user->save()) {
            throw new Exception('Unable to save injection user: ' . print_r($injection_user->errors, true));
        }

        echo '1';
    }

    public function actionDeleteInjectionUsers()
    {
        $injection_users_ids = Yii::app()->request->getPost('injection_users', []);

        foreach ($injection_users_ids as $injection_user_id) {
            if (!OphTrIntravitrealinjection_InjectionUser::model()->deleteByPk($injection_user_id)) {
                throw new Exception('Unable to delete injection user: ', true);
            }
        }

        echo 1;
    }
}
