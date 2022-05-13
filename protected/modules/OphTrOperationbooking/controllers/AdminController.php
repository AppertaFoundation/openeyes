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
    public $sequences_items_per_page = 20;
    public $sessions_items_per_page = 20;

    public $group = 'Operation booking';

    public function actionViewERODRules()
    {
        Audit::add('admin', 'list', null, null, array('module' => 'OphTrOperationbooking', 'model' => 'OphTrOperationbooking_Operation_EROD_Rule'));

        $this->render('/admin/erod_rule/index');
    }

    public function actionEditERODRule($id)
    {
        if (!$erod = OphTrOperationbooking_Operation_EROD_Rule::model()->findByPk($id)) {
            throw new Exception("EROD rule not found: $id");
        }

        $errors = array();

        //NOTE at the moment we only have rules relating to firms, so the items are all assumed to be firms in the code below
        // this will need to change if we introduce other item types to the EROD rules.
        if (!empty($_POST)) {
            $erod->subspecialty_id = $_POST['OphTrOperationbooking_Operation_EROD_Rule']['subspecialty_id'];
            $current_items = $erod->items;
            $posted_items = array();
            $posted_by_firm = array();
            if (@$_POST['Firms']) {
                foreach ($_POST['Firms'] as $posted_firm_id) {
                    if (!Firm::model()->findByPk($posted_firm_id)) {
                        throw new CHttpException('invalid firm id posted');
                    }
                    $item = new OphTrOperationbooking_Operation_EROD_Rule_Item();
                    $item->item_type = 'firm';
                    $item->item_id = $posted_firm_id;
                    $posted_items[] = $item;
                    $posted_by_firm[$posted_firm_id] = $item;
                }
            }
            $erod->items = $posted_items;

            $transaction = Yii::app()->db->beginTransaction();
            try {
                if (!$erod->save()) {
                    $errors = $erod->getErrors();
                } else {
                    $firm_ids = array();
                    foreach ($current_items as $curr_item) {
                        $curr_firm_ids[] = $curr_item->item_id;
                        $curr_by_firm_id[$curr_item->item_id] = $curr_item;
                    }

                    foreach ($posted_items as $posted_item) {
                        if (!in_array($posted_item->item_id, $curr_firm_ids)) {
                            $posted_item->erod_rule_id = $erod->id;
                            if (!$posted_item->save()) {
                                $errors = array_merge($errors, $item->getErrors());
                                throw new Exception();
                            }
                        } else {
                            unset($curr_by_firm_id[$posted_item->item_id]);
                        }
                    }

                    foreach ($curr_by_firm_id as $curr_item) {
                        if (!$curr_item->delete()) {
                            throw new Exception('Rule item delete failed: '.print_r($item->getErrors(), true));
                        }
                    }

                    if (empty($errors)) {
                        $transaction->commit();
                        Audit::add('admin', 'update', $id, null, array('module' => 'OphTrOperationbooking', 'model' => 'OphTrOperationbooking_Operation_EROD_Rule'));
                        $this->redirect(array('/OphTrOperationbooking/admin/viewERODRules'));
                    }
                }
            } catch (Exception $e) {
                $transaction->rollback();
            }
        }

        Audit::add('admin', 'view', $id, null, array('module' => 'OphTrOperationbooking', 'model' => 'OphTrOperationbooking_Operation_EROD_Rule'));

        $this->render('/admin/erod_rule/edit', array(
            'erod' => $erod,
            'errors' => $errors,
        ));
    }

    public function actionAddERODRule()
    {
        $errors = array();

        $erod = new OphTrOperationbooking_Operation_EROD_Rule();

        if (!empty($_POST)) {
            $erod->subspecialty_id = @$_POST['OphTrOperationbooking_Operation_EROD_Rule']['subspecialty_id'];
            $posted_items = array();
            $posted_by_firm = array();
            if (@$_POST['Firms']) {
                foreach ($_POST['Firms'] as $posted_firm_id) {
                    if (!Firm::model()->findByPk($posted_firm_id)) {
                        throw new CHttpException('invalid firm id posted');
                    }
                    $item = new OphTrOperationbooking_Operation_EROD_Rule_Item();
                    $item->item_type = 'firm';
                    $item->item_id = $posted_firm_id;
                    $posted_items[] = $item;
                    $posted_by_firm[$posted_firm_id] = $item;
                }
            }
            $erod->items = $posted_items;

            $transaction = Yii::app()->db->beginTransaction();

            try {
                if (!$erod->save()) {
                    $errors = $erod->getErrors();
                } else {
                    foreach ($posted_items as $posted_item) {
                        $posted_item->erod_rule_id = $erod->id;
                        if (!$posted_item->save()) {
                            $errors = array_merge($errors, $item->getErrors());
                            throw new Exception();
                        }
                    }

                    if (empty($errors)) {
                        $transaction->commit();
                        Audit::add('admin', 'create', $erod->id, null, array('module' => 'OphTrOperationbooking', 'model' => 'OphTrOperationbooking_Operation_EROD_Rule'));
                        $this->redirect(array('/OphTrOperationbooking/admin/viewERODRules'));
                    }
                }
            } catch (Exception $e) {
                $transaction->rollback();
                throw $e;
            }
        }

        $this->render('/admin/erod_rule/edit', array(
            'erod' => $erod,
            'errors' => $errors,
        ));
    }

    public function actionDeleteERODRules()
    {
        if (!empty($_POST['erod'])) {
            $transaction = Yii::app()->db->beginTransaction();
            try {
                foreach ($_POST['erod'] as $erod_id) {
                    if ($_erod = OphTrOperationbooking_Operation_EROD_Rule::model()->findByPk($erod_id)) {
                        foreach ($_erod->items as $item) {
                            if (!$item->delete()) {
                                throw new Exception('Unable to delete rule item: '.print_r($item->getErrors(), true));
                            }
                        }
                        if (!$_erod->delete()) {
                            throw new Exception('Unable to delete erod rule: '.print_r($_erod->getErrors(), true));
                        }
                    } else {
                        throw new Exception('EROD Rule not found for id '.$erod_id);
                    }
                }

                Audit::add('admin', 'delete', null, null, array('module' => 'OphTrOperationbooking', 'model' => 'OphTrOperationbooking_Operation_EROD_Rule'));

                $transaction->commit();
            } catch (Exception $e) {
                $transaction->rollback();
                echo $e->getMessage();
                Yii::app()->end();
            }
        }

        echo '1';
    }

    public function actionViewLetterContactRules()
    {
        $this->jsVars['OE_rule_model'] = 'LetterContactRule';

        Audit::add('admin', 'list', null, null, array('module' => 'OphTrOperationbooking', 'model' => 'OphTrOperationbooking_Letter_Contact_Rule'));

        $this->render('/admin/letter_contact_rule/index', array(
            'data' => OphTrOperationbooking_Letter_Contact_Rule::model()->findAllAsTree(),
        ));
    }

    public function actionTestLetterContactRules()
    {
        $site_id = @$_POST['lcr_site_id'];
        $subspecialty_id = @$_POST['lcr_subspecialty_id'];
        $theatre_id = @$_POST['lcr_theatre_id'];
        $firm_id = @$_POST['lcr_firm_id'];

        $criteria = new CDbCriteria();
        $criteria->addCondition('parent_rule_id is null');
        $criteria->order = 'rule_order asc';

        foreach (OphTrOperationbooking_Letter_Contact_Rule::model()->findAll($criteria) as $rule) {
            if ($rule->applies($site_id, $subspecialty_id, $theatre_id, $firm_id, false)) {
                $final = $rule->parse($site_id, $subspecialty_id, $theatre_id, $firm_id, false);
                $this->renderJSON(array($final->id));

                return;
            }
        }

        echo $this->renderJSON(array());
    }

    public function actionEditLetterContactRule($id)
    {
        if (!$rule = OphTrOperationbooking_Letter_Contact_Rule::model()->findByPk($id)) {
            throw new Exception("Letter contact rule not found: $id");
        }

        $errors = array();

        if (!empty($_POST)) {
            $rule->attributes = $_POST['OphTrOperationbooking_Letter_Contact_Rule'];

            if (!$rule->save()) {
                $errors = $rule->getErrors();
            } else {
                Audit::add('admin', 'update', $id, null, array('module' => 'OphTrOperationbooking', 'model' => 'OphTrOperationbooking_Letter_Contact_Rule'));
                $this->redirect(array('/OphTrOperationbooking/admin/viewLetterContactRules'));
            }
        }

        $this->jsVars['OE_rule_model'] = 'LetterContactRule';

        Audit::add('admin', 'view', $id, null, array('module' => 'OphTrOperationbooking', 'model' => 'OphTrOperationbooking_Letter_Contact_Rule'));

        $this->render('/admin/letter_contact_rule/edit', array(
            'rule' => $rule,
            'errors' => $errors,
        ));
    }

    public function actionDeleteLetterContactRule($id)
    {
        if (!$rule = OphTrOperationbooking_Letter_Contact_Rule::model()->findByPk($id)) {
            throw new Exception("Letter contact rule not found: $id");
        }

        $errors = array();

        if (!empty($_POST)) {
            if (@$_POST['delete']) {
                if (!$rule->delete()) {
                    $errors = $rule->getErrors();
                } else {
                    Audit::add('admin', 'delete', $id, null, array('module' => 'OphTrOperationbooking', 'model' => 'OphTrOperationbooking_Letter_Contact_Rule'));
                    $this->redirect(array('/OphTrOperationbooking/admin/viewLetterContactRules'));
                }
            }
        }

        $this->jsVars['OE_rule_model'] = 'LetterContactRule';

        $this->render('/admin/letter_contact_rule/delete', array(
            'rule' => $rule,
            'errors' => $errors,
        ));
    }

    public function actionAddLetterContactRule()
    {
        $rule = new OphTrOperationbooking_Letter_Contact_Rule();

        $errors = array();

        if (!empty($_POST)) {
            $rule->attributes = $_POST['OphTrOperationbooking_Letter_Contact_Rule'];

            if (!$rule->save()) {
                $errors = $rule->getErrors();
            } else {
                Audit::add('admin', 'create', $rule->id, null, array('module' => 'OphTrOperationbooking', 'model' => 'OphTrOperationbooking_Letter_Contact_Rule'));
                $this->redirect(array('/OphTrOperationbooking/admin/viewLetterContactRules'));
            }
        } else {
            if (isset($_GET['parent_rule_id'])) {
                $rule->parent_rule_id = $_GET['parent_rule_id'];
            }
        }

        $this->jsVars['OE_rule_model'] = 'LetterContactRule';

        $this->render('/admin/letter_contact_rule/edit', array(
            'rule' => $rule,
            'errors' => $errors,
        ));
    }

    public function actionViewLetterWarningRules()
    {
        $this->jsVars['OE_rule_model'] = 'LetterWarningRule';

        Audit::add('admin', 'list', null, null, array('module' => 'OphTrOperationbooking', 'model' => 'OphTrOperationbooking_Admission_Letter_Warning_Rule'));

        $this->render('/admin/letter_warning_rules/index', array(
            'data' => OphTrOperationbooking_Admission_Letter_Warning_Rule::model()->findAllAsTree(),
        ));
    }

    public function actionTestLetterWarningRules()
    {
        $site_id = @$_POST['lcr_site_id'];
        $subspecialty_id = @$_POST['lcr_subspecialty_id'];
        $theatre_id = @$_POST['lcr_theatre_id'];
        $firm_id = @$_POST['lcr_firm_id'];
        $is_child = @$_POST['lcr_is_child'];

        $criteria = new CDbCriteria();
        $criteria->addCondition('parent_rule_id is null');
        $criteria->addCondition('rule_type_id = :rule_type_id');
        $criteria->params[':rule_type_id'] = @$_POST['lcr_rule_type_id'];
        $criteria->order = 'rule_order asc';

        $rule_ids = array();

        foreach (OphTrOperationbooking_Admission_Letter_Warning_Rule::model()->findAll($criteria) as $rule) {
            if ($rule->applies($site_id, $is_child, $theatre_id, $subspecialty_id, $firm_id)) {
                $final = $rule->parse($site_id, $is_child, $theatre_id, $subspecialty_id, $firm_id);
                $this->renderJSON(array($final->id));

                return;
            }
        }

        $this->renderJSON(array());
    }

    public function actionEditLetterWarningRule($id)
    {
        if (!$rule = OphTrOperationbooking_Admission_Letter_Warning_Rule::model()->findByPk($id)) {
            throw new Exception("Letter warning rule not found: $id");
        }

        $errors = array();

        if (!empty($_POST)) {
            $rule->attributes = $_POST['OphTrOperationbooking_Admission_Letter_Warning_Rule'];

            if (!$rule->save()) {
                $errors = $rule->getErrors();
            } else {
                Audit::add('admin', 'update', $id, null, array('module' => 'OphTrOperationbooking', 'model' => 'OphTrOperationbooking_Admission_Letter_Warning_Rule'));
                $this->redirect(array('/OphTrOperationbooking/admin/viewLetterWarningRules'));
            }
        }

        Audit::add('admin', 'view', $id, null, array('module' => 'OphTrOperationbooking', 'model' => 'OphTrOperationbooking_Admission_Letter_Warning_Rule'));

        $this->jsVars['OE_rule_model'] = 'LetterWarningRule';

        $this->render('/admin/letter_warning_rules/edit', array(
            'rule' => $rule,
            'errors' => $errors,
        ));
    }

    public function actionAddLetterWarningRule()
    {
        $rule = new OphTrOperationbooking_Admission_Letter_Warning_Rule();

        $errors = array();

        if (!empty($_POST)) {
            $rule->attributes = $_POST['OphTrOperationbooking_Admission_Letter_Warning_Rule'];

            if (!$rule->save()) {
                $errors = $rule->getErrors();
            } else {
                Audit::add('admin', 'create', $rule->id, null, array('module' => 'OphTrOperationbooking', 'model' => 'OphTrOperationbooking_Admission_Letter_Warning_Rule'));
                $this->redirect(array('/OphTrOperationbooking/admin/viewLetterWarningRules'));
            }
        } else {
            if (isset($_GET['parent_rule_id'])) {
                $rule->parent_rule_id = $_GET['parent_rule_id'];
            }
        }

        $this->jsVars['OE_rule_model'] = 'LetterWarningRule';

        $this->render('/admin/letter_warning_rules/edit', array(
            'rule' => $rule,
            'errors' => $errors,
        ));
    }

    public function actionDeleteLetterWarningRule($id)
    {
        if (!$rule = OphTrOperationbooking_Admission_Letter_Warning_Rule::model()->findByPk($id)) {
            throw new Exception("Letter warning rule not found: $id");
        }

        $errors = array();

        if (!empty($_POST)) {
            if (@$_POST['delete']) {
                if (!$rule->delete()) {
                    $errors = $rule->getErrors();
                } else {
                    Audit::add('admin', 'delete', $id, null, array('module' => 'OphTrOperationbooking', 'model' => 'OphTrOperationbooking_Admission_Letter_Warning_Rule'));
                    $this->redirect(array('/OphTrOperationbooking/admin/viewLetterWarningRules'));
                }
            }
        }

        $this->jsVars['OE_rule_model'] = 'LetterWarningRule';

        $this->render('/admin/letter_warning_rules/delete', array(
            'rule' => $rule,
            'errors' => $errors,
        ));
    }

    public function actionViewWaitingListContactRules()
    {
        $this->jsVars['OE_rule_model'] = 'WaitingListContactRule';

        Audit::add('admin', 'list', null, null, array('module' => 'OphTrOperationbooking', 'model' => 'OphTrOperationbooking_Waiting_List_Contact_Rule'));

        $this->render('/admin/waiting_list_contact_rules/index', array(
            'data' => OphTrOperationbooking_Waiting_List_Contact_Rule::model()->findAllAsTree(null, true, 'text', Institution::model()->getCurrent()->id),
        ));
    }

    public function actionTestWaitingListContactRules()
    {
        $site_id = @$_POST['lcr_site_id'];
        $service_id = @$_POST['lcr_service_id'];
        $firm_id = @$_POST['lcr_firm_id'];
        $is_child = @$_POST['lcr_is_child'];

        $criteria = new CDbCriteria();
        $criteria->addCondition('parent_rule_id is null');
        $criteria->order = 'rule_order asc';

        $rule_ids = array();

        foreach (OphTrOperationbooking_Waiting_List_Contact_Rule::model()->findAll($criteria) as $rule) {
            if ($rule->applies($site_id, $service_id, $firm_id, $is_child)) {
                $final = $rule->parse($site_id, $service_id, $firm_id, $is_child);
                $this->renderJSON(array($final->id));

                return;
            }
        }

        $this->renderJSON(array());
    }

    public function actionAddWaitingListContactRule()
    {
        $rule = new OphTrOperationbooking_Waiting_List_Contact_Rule();

        $errors = array();

        if (!empty($_POST)) {
            $transaction = Yii::app()->db->beginTransaction();
            $rule->attributes = $_POST['OphTrOperationbooking_Waiting_List_Contact_Rule'];

            if (!$rule->save()) {
                $errors = $rule->getErrors();
            } else {
                try {
                    $rule->createMapping(ReferenceData::LEVEL_INSTITUTION, Yii::app()->session['selected_institution_id']);
                } catch (Exception $e) {
                    $errors[] = $e->getMessage();
                }

                if (empty($errors)) {
                    $transaction->commit();
                    Audit::add('admin', 'update', serialize($_POST), false, array('module' => 'OphTrOperationbooking', 'model' => 'OphTrOperationbooking_Waiting_List_Contact_Rule'));
                    $this->redirect(array('/OphTrOperationbooking/admin/viewWaitingListContactRules'));
                } else {
                    $transaction->rollback();
                }
            }
        }

        $this->jsVars['OE_rule_model'] = 'WaitingListContactRule';

        Audit::add('admin', 'list', null, false, array('module' => 'OphTrOperationbooking', 'model' => 'OphTrOperationbooking_Waiting_List_Contact_Rule'));

        $this->render('/admin/waiting_list_contact_rules/edit', array(
            'rule' => $rule,
            'errors' => $errors,
        ));
    }

    public function actionEditWaitingListContactRule($id)
    {
        if (!$rule = OphTrOperationbooking_Waiting_List_Contact_Rule::model()->findByPk($id)) {
            throw new Exception("Waiting list contact rule not found: $id");
        }

        $errors = array();

        if (!empty($_POST)) {
            $transaction = Yii::app()->db->beginTransaction();
            $rule->attributes = $_POST['OphTrOperationbooking_Waiting_List_Contact_Rule'];

            if (!$rule->save()) {
                $errors = $rule->getErrors();
            } else {
                try {
                    if (!$rule->hasMapping(ReferenceData::LEVEL_INSTITUTION, Yii::app()->session['selected_institution_id'])) {
                        $rule->createMapping(ReferenceData::LEVEL_INSTITUTION, Yii::app()->session['selected_institution_id']);
                    }
                } catch (Exception $e) {
                    $errors[] = $e->getMessage();
                }

                if (empty($errors)) {
                    $transaction->commit();
                    Audit::add('admin', 'update', $rule->id, null, array('module' => 'OphTrOperationbooking', 'model' => 'OphTrOperationbooking_Waiting_List_Contact_Rule'));
                    $this->redirect(array('/OphTrOperationbooking/admin/viewWaitingListContactRules'));
                } else {
                    $transaction->rollback();
                }
            }
        }

        $this->jsVars['OE_rule_model'] = 'WaitingListContactRule';

        Audit::add('admin', 'list', null, null, array('module' => 'OphTrOperationbooking', 'model' => 'OphTrOperationbooking_Waiting_List_Contact_Rule'));

        $this->render('/admin/waiting_list_contact_rules/edit', array(
            'rule' => $rule,
            'errors' => $errors,
        ));
    }

    public function actionDeleteWaitingListContactRule($id)
    {
        if (!$rule = OphTrOperationbooking_Waiting_List_Contact_Rule::model()->findByPk($id)) {
            throw new Exception("Waiting list contact rule not found: $id");
        }

        $errors = array();

        if (!empty($_POST)) {
            if (@$_POST['delete']) {
                $transaction = Yii::app()->db->beginTransaction();
                try {
                    $rule->deleteMapping(ReferenceData::LEVEL_INSTITUTION, Yii::app()->session['selected_institution_id']);
                } catch (Exception $e) {
                    $errors[] = $e->getMessage();
                }
                if (!$rule->delete()) {
                    $errors = $rule->getErrors();
                }
                if (empty($errors)) {
                    $transaction->commit();
                    Audit::add('admin', 'delete', null, null, array('module' => 'OphTrOperationbooking', 'model' => 'OphTrOperationbooking_Waiting_List_Contact_Rule'));
                    $this->redirect(array('/OphTrOperationbooking/admin/viewWaitingListContactRules'));
                } else {
                    $transaction->rollback();
                }
            }
        }

        $this->jsVars['OE_rule_model'] = 'WaitingListContactRule';

        $this->render('/admin/waiting_list_contact_rules/delete', array(
            'rule' => $rule,
            'errors' => $errors,
            'data' => OphTrOperationbooking_Waiting_List_Contact_Rule::model()->findAllAsTree($rule, true, 'textPlain'),
        ));
    }

    public function actionViewOperationNameRules()
    {
        $this->jsVars['OE_rule_model'] = 'OperationNameRule';

        Audit::add('admin', 'list', null, null, array('module' => 'OphTrOperationbooking', 'model' => 'OphTrOperationbooking_Operation_Name_Rule'));

        $this->render('/admin/name_rule/index');
    }

    public function actionAddOperationNameRule()
    {
        $errors = array();

        $rule = new OphTrOperationbooking_Operation_Name_Rule();

        if (!empty($_POST)) {
            $rule->attributes = $_POST['OphTrOperationbooking_Operation_Name_Rule'];

            if (!$rule->save()) {
                $errors = $rule->getErrors();
            } else {
                Audit::add('admin', 'create', $rule->id, null, array('module' => 'OphTrOperationbooking', 'model' => 'OphTrOperationbooking_Operation_Name_Rule'));
                $this->redirect(array('/OphTrOperationbooking/admin/viewOperationNameRules'));
            }
        }

        $this->render('/admin/name_rule/edit', array(
            'rule' => $rule,
            'errors' => $errors,
        ));
    }

    public function actionAddInstitutionMapping()
    {
        $ids = Yii::app()->request->getPost('select');
        $model = Yii::app()->request->getPost('model');
        $redirect_url = Yii::app()->request->getPost('redirect-url');

        $model .= '::model';
        $instances = $model()->findAllByPk($ids);
        $institution_id = Institution::model()->getCurrent()->id;
        $errors = array();
        $status = 1;

        /**
         * @var $instances MappedReferenceData[]|BaseActiveRecordVersioned[]
         */
        foreach ($instances as $instance) {
            if (!$instance->createMapping(ReferenceData::LEVEL_INSTITUTION, $institution_id)) {
                $errors[] = $instance->getErrors();
            }
        }

        if (!empty($errors)) {
            $status = 0;
        }
        $this->redirect($redirect_url);
    }

    public function actionDeleteInstitutionMapping()
    {
        $ids = $_POST['select'];
        $model = $_POST['model'];
        $redirect_url = Yii::app()->request->getPost('redirect-url');

        $model .= '::model';
        $instances = $model()->findAllByPk($ids);
        $institution_id = Institution::model()->getCurrent()->id;
        $errors = array();
        $status = 1;

        /**
         * @var $instances MappedReferenceData[]|BaseActiveRecordVersioned[]
         */
        foreach ($instances as $instance) {
            if (!$instance->deleteMapping(ReferenceData::LEVEL_INSTITUTION, $institution_id)) {
                $errors[] = $instance->getErrors();
            }
        }

        if (!empty($errors)) {
            $status = 0;
        }
        $this->redirect($redirect_url);
    }

    /**
     * @param $id
     * @throws Exception
     */
    public function actionEditOperationNameRule($id)
    {
        if (!$rule = OphTrOperationbooking_Operation_Name_Rule::model()->findByPk($id)) {
            throw new Exception("Operation name rule not found: $id");
        }

        $errors = array();

        if (!empty($_POST)) {
            $rule->attributes = $_POST['OphTrOperationbooking_Operation_Name_Rule'];

            if (!$rule->save()) {
                $errors = $rule->getErrors();
            } else {
                Audit::add('admin', 'update', $id, null, array('module' => 'OphTrOperationbooking', 'model' => 'OphTrOperationbooking_Operation_Name_Rule'));
                $this->redirect(array('/OphTrOperationbooking/admin/viewOperationNameRules'));
            }
        }

        Audit::add('admin', 'view', $id, null, array('module' => 'OphTrOperationbooking', 'model' => 'OphTrOperationbooking_Operation_Name_Rule'));

        $this->render('/admin/name_rule/edit', array(
            'rule' => $rule,
            'errors' => $errors,
        ));
    }

    public function actionDeleteOperationNameRules()
    {
        if (!empty($_POST['operation_name'])) {
            foreach ($_POST['operation_name'] as $rule_id) {
                if ($_rule = OphTrOperationbooking_Operation_Name_Rule::model()->findByPk($rule_id)) {
                    if (!$_rule->delete()) {
                        throw new Exception('Unable to delete rule rule: '.print_r($_rule->getErrors(), true));
                    }
                }
            }

            Audit::add('admin', 'delete', null, null, array('module' => 'OphTrOperationbooking', 'model' => 'OphTrOperationbooking_Operation_Name_Rule'));
        }

        echo '1';
    }

    public function actionViewSequences()
    {
        if (@$_GET['reset'] == 1) {
            unset($_GET['reset']);
            unset(Yii::app()->session['admin_sequences']);
            $this->redirectWith($_GET);
        }

        if (empty($_GET) && empty($_POST) && !empty(Yii::app()->session['admin_sequences'])) {
            $this->redirectWith(Yii::app()->session['admin_sequences']);
        }

        $save_from_GET = $_GET;
        unset($save_from_GET['page']);
        if (!empty($save_from_GET)) {
            Yii::app()->session['admin_sequences'] = $save_from_GET;
        }

        if (@$_POST['generateSessions']) {
            $api = Yii::app()->moduleAPI->get('OphTrOperationbooking');
            $api->generateSessions();
            Yii::app()->user->setFlash('success', 'Sessions have been generated.');
            echo '1';

            return;
        }

        Audit::add('admin', 'list', null, null, array('module' => 'OphTrOperationbooking', 'model' => 'OphTrOperationbooking_Operation_Sequence'));

        $this->render('/admin/sequences', array(
            'sequences' => $this->getSequences(),
        ));
    }

    public function redirectWith($params)
    {
        $uri = preg_replace('/\?.*$/', '', $_SERVER['REQUEST_URI']);

        $first = true;
        foreach ($params as $key => $value) {
            $uri .= $first ? '?' : '&';
            $first = false;
            $uri .= "$key=$value";
        }

        $this->redirect(array($uri));
    }

    public function getSequences($all = false)
    {
        $criteria = new CDbCriteria();

        if ($firm = Firm::model()->findByPk(@$_REQUEST['firm_id'])) {
            $criteria->addCondition('firm_id=:firm_id');
            $criteria->params[':firm_id'] = $firm->id;
        } elseif (@$_REQUEST['firm_id'] == 'NULL') {
            $criteria->addCondition('firm_id is null');
        }

        if ($theatre = OphTrOperationbooking_Operation_Theatre::model()->findByPk(@$_REQUEST['theatre_id'])) {
            $criteria->addCondition('theatre_id=:theatre_id');
            $criteria->params[':theatre_id'] = $theatre->id;
        }

        if (@$_REQUEST['date_from'] && strtotime(@$_REQUEST['date_from'])) {
            $criteria->addCondition('start_date >= :start_date');
            $criteria->params[':start_date'] = date('Y-m-d', strtotime(@$_REQUEST['date_from']));
        }

        if (@$_REQUEST['date_to'] && strtotime(@$_REQUEST['date_to'])) {
            $criteria->addCondition('end_date <= :end_date');
            $criteria->params[':end_date'] = date('Y-m-d', strtotime(@$_REQUEST['date_to']));
        }

        if (@$_REQUEST['interval_id'] != '') {
            $criteria->addCondition('interval_id = :interval_id');
            $criteria->params[':interval_id'] = @$_REQUEST['interval_id'];
        }

        if (@$_REQUEST['weekday'] != '') {
            $criteria->addCondition('weekday = :weekday');
            $criteria->params[':weekday'] = @$_REQUEST['weekday'];
        }

        if (@$_REQUEST['consultant'] != '') {
            $criteria->addCondition('consultant = :consultant');
            $criteria->params[':consultant'] = @$_REQUEST['consultant'];
        }

        if (@$_REQUEST['paediatric'] != '') {
            $criteria->addCondition('paediatric = :paediatric');
            $criteria->params[':paediatric'] = @$_REQUEST['paediatric'];
        }

        if (@$_REQUEST['anaesthetist'] != '') {
            $criteria->addCondition('anaesthetist = :anaesthetist');
            $criteria->params[':anaesthetist'] = @$_REQUEST['anaesthetist'];
        }

        if (@$_REQUEST['general_anaesthetic'] != '') {
            $criteria->addCondition('general_anaesthetic = :general_anaesthetic');
            $criteria->params[':general_anaesthetic'] = @$_REQUEST['general_anaesthetic'];
        }

        $page = @$_REQUEST['page'] ? $_REQUEST['page'] : 1;

        if ($all) {
            if (!$this->checkAccess('admin')) {
                $criteria->with = 'sequence.theatre.site';
                $criteria->addCondition('site.institution_id = :institution_id');
                $criteria->params[':institution_id'] = Institution::model()->getCurrent()->id;
            }

            return OphTrOperationbooking_Operation_Sequence::model()->findAll($criteria);
        }

        $count = OphTrOperationbooking_Operation_Sequence::model()->count($criteria);
        $pages = ceil($count / $this->sequences_items_per_page);

        if ($page < 1) {
            $page = 1;
        }
        if ($page > $pages) {
            $page = $pages;
        }

        $criteria->limit = $this->sequences_items_per_page;
        $criteria->offset = ($page - 1) * $this->sequences_items_per_page;

        $order = @$_REQUEST['order'] == 'desc' ? 'desc' : 'asc';

        switch (@$_REQUEST['sortby']) {
            case 'firm':
                $criteria->order = "firm.name $order, subspecialty.name $order";
                break;
            case 'theatre':
                $criteria->order = "theatre.name $order";
                break;
            case 'dates':
                $criteria->order = "start_date $order, end_date $order, start_time $order, end_time $order";
                break;
            case 'time':
                $criteria->order = "start_time $order, end_time $order";
                break;
            case 'interval':
                $criteria->order = "interval.name $order";
                break;
            case 'weekday':
                $criteria->order = "weekday $order";
                break;
            default:
                $criteria->order = "firm.name $order, subspecialty.name $order";
        }

        $with = array(
            'firm' => array(
                'with' => array(
                    'serviceSubspecialtyAssignment' => array(
                        'with' => 'subspecialty',
                    ),
                ),
            ),
            'theatre',
            'theatre.site',
            'interval',
        );

        if (!$this->checkAccess('admin')) {
            $criteria->addCondition('site.institution_id = :institution_id');
            $criteria->params[':institution_id'] = Institution::model()->getCurrent()->id;
        }

        $this->items_per_page = $this->sessions_items_per_page;
        $pagination = $this->initPagination(OphTrOperationbooking_Operation_Sequence::model()->with($with), $criteria);
        $data = OphTrOperationbooking_Operation_Sequence::model()->with($with)->findAll($criteria);

        return array(
            'data' => $data,
            'pagination' => $pagination,
        );
    }

    public function getUri($elements)
    {
        $uri = preg_replace('/\?.*$/', '', $_SERVER['REQUEST_URI']);

        $request = $_REQUEST;

        if (isset($elements['sortby']) && $elements['sortby'] == @$request['sortby']) {
            $request['order'] = (@$request['order'] == 'desc') ? 'asc' : 'desc';
        } elseif (isset($request['sortby']) && isset($elements['sortby']) && $request['sortby'] != $elements['sortby']) {
            $request['order'] = 'asc';
        }

        $first = true;
        foreach (array_merge($request, $elements) as $key => $value) {
            $uri .= $first ? '?' : '&';
            $first = false;
            $uri .= "$key=$value";
        }

        return $uri;
    }

    public function actionSequenceInlineEdit()
    {
        $errors = array();

        if (!empty($_POST['sequence'])) {
            $criteria = new CDbCriteria();
            $criteria->addInCondition('id', $_POST['sequence']);
            $sequences = OphTrOperationbooking_Operation_Sequence::model()->findAll($criteria);
        } elseif (@$_POST['use_filters']) {
            $sequences = $this->getSequences(true);
        }

        foreach ($sequences as $sequence) {
            $changed = false;

            foreach (array('firm_id', 'theatre_id', 'start_time', 'end_time', 'interval_id', 'weekday', 'consultant', 'paediatric', 'anaesthetist', 'general_anaesthetic') as $field) {
                if ($_POST['inline_'.$field] != '') {
                    if ($sequence->$field != $_POST['inline_'.$field]) {
                        $sequence->$field = $_POST['inline_'.$field];
                        $changed = true;
                    }
                }
            }
            if ($_POST['inline_start_date'] != '') {
                if (!strtotime($_POST['inline_start_date'])) {
                    $errors['start_date'] = 'Invalid start date';
                }
                if ($sequence->start_date != date('Y-m-d', strtotime($_POST['inline_start_date']))) {
                    $sequence->start_date = date('Y-m-d', strtotime($_POST['inline_start_date']));
                    $changed = true;
                }
            }
            if ($_POST['inline_end_date'] != '') {
                if (!strtotime($_POST['inline_end_date'])) {
                    $errors['end_date'] = 'Invalid end date';
                }
                if ($sequence->end_date != date('Y-m-d', strtotime($_POST['inline_end_date']))) {
                    $sequence->end_date = date('Y-m-d', strtotime($_POST['inline_end_date']));
                    $changed = true;
                }
            }
            if ($_POST['inline_update_weeks']) {
                $weeks = 0;
                $_POST['inline_week1'] && $weeks += 1;
                $_POST['inline_week2'] && $weeks += 2;
                $_POST['inline_week3'] && $weeks += 4;
                $_POST['inline_week4'] && $weeks += 8;
                $_POST['inline_week5'] && $weeks += 16;

                if ($sequence->week_selection != $weeks) {
                    $sequence->week_selection = $weeks;
                    $changed = true;
                }
            }

            if ($changed) {
                if (!empty($errors)) {
                    $sequence->validate();
                    $this->renderJSON(array_merge($errors, $sequence->getErrors()));

                    return;
                }

                if (!$sequence->save()) {
                    $this->renderJSON($sequence->getErrors());

                    return;
                }

                Audit::add('admin', 'update', $sequence->id, null, array('module' => 'OphTrOperationbooking', 'model' => 'OphTrOperationbooking_Operation_Sequence'));
            }
        }

        $this->renderJSON($errors);
    }

    public function actionEditSequence($id)
    {
        if (!$sequence = OphTrOperationbooking_Operation_Sequence::model()->findByPk($id)) {
            throw new Exception("Sequence not found: $id");
        }

        $errors = array();

        // check for conflicts with other sessions

        if (!empty($_POST)) {
            $sequence->attributes = $_POST['OphTrOperationbooking_Operation_Sequence'];

            $weeks = 0;

            if ($_POST['OphTrOperationbooking_Operation_Sequence']['week_selection_week1']) {
                $weeks += 1;
            }
            if ($_POST['OphTrOperationbooking_Operation_Sequence']['week_selection_week2']) {
                $weeks += 2;
            }
            if ($_POST['OphTrOperationbooking_Operation_Sequence']['week_selection_week3']) {
                $weeks += 4;
            }
            if ($_POST['OphTrOperationbooking_Operation_Sequence']['week_selection_week4']) {
                $weeks += 8;
            }
            if ($_POST['OphTrOperationbooking_Operation_Sequence']['week_selection_week5']) {
                $weeks += 16;
            }

            $sequence->week_selection = $weeks;

            if (!$sequence->end_date) {
                $sequence->end_date = null;
            }
            if (!$sequence->week_selection) {
                $sequence->week_selection = null;
            }

            if (!$sequence->save()) {
                $errors = $sequence->getErrors();
            } else {
                if (empty($errors)) {
                    Audit::add('admin', 'update', $id, null, array('module' => 'OphTrOperationbooking', 'model' => 'OphTrOperationbooking_Operation_Sequence'));
                    $this->redirect(array('/OphTrOperationbooking/admin/viewSequences'));
                }
            }
        }

        Audit::add('admin', 'view', $id, null, array('module' => 'OphTrOperationbooking', 'model' => 'OphTrOperationbooking_Operation_Sequence'));

        $this->render('/admin/editsequence', array(
            'sequence' => $sequence,
            'errors' => $errors,
        ));
    }

    public function actionAddSequence()
    {
        $sequence = new OphTrOperationbooking_Operation_Sequence();

        $errors = array();

        if (!empty($_POST)) {
            $sequence->attributes = $_POST['OphTrOperationbooking_Operation_Sequence'];

            $weeks = 0;

            if ($_POST['OphTrOperationbooking_Operation_Sequence']['week_selection_week1']) {
                $weeks += 1;
            }
            if ($_POST['OphTrOperationbooking_Operation_Sequence']['week_selection_week2']) {
                $weeks += 2;
            }
            if ($_POST['OphTrOperationbooking_Operation_Sequence']['week_selection_week3']) {
                $weeks += 4;
            }
            if ($_POST['OphTrOperationbooking_Operation_Sequence']['week_selection_week4']) {
                $weeks += 8;
            }
            if ($_POST['OphTrOperationbooking_Operation_Sequence']['week_selection_week5']) {
                $weeks += 16;
            }

            $sequence->week_selection = $weeks;

            if (!$sequence->end_date) {
                $sequence->end_date = null;
            }
            if (!$sequence->week_selection) {
                $sequence->week_selection = null;
            }

            if (!$sequence->save()) {
                $errors = $sequence->getErrors();
            } else {
                if (empty($errors)) {
                    Audit::add('admin', 'create', $sequence->id, null, array('module' => 'OphTrOperationbooking', 'model' => 'OphTrOperationbooking_Operation_Sequence'));
                    $this->redirect(array('/OphTrOperationbooking/admin/viewSequences'));
                }
            }
        }

        $this->render('/admin/editsequence', array(
            'sequence' => $sequence,
            'errors' => $errors,
        ));
    }

    public function actionViewSessions()
    {
        if (@$_GET['reset'] == 1) {
            unset($_GET['reset']);
            unset(Yii::app()->session['admin_sessions']);
            $this->redirectWith($_GET);
        }

        if (empty($_GET) && !empty(Yii::app()->session['admin_sessions'])) {
            $this->redirectWith(Yii::app()->session['admin_sessions']);
        }

        $save_from_GET = $_GET;
        unset($save_from_GET['page']);
        if (!empty($save_from_GET)) {
            Yii::app()->session['admin_sessions'] = $save_from_GET;
        }

        Audit::add('admin', 'list', null, null, array('module' => 'OphTrOperationbooking', 'model' => 'OphTrOperationbooking_Operation_Session'));

        $this->render('/admin/sessions', array(
            'sessions' => $this->getSessions(),
        ));
    }

    /**
     * @param bool $all
     * @return array
     * @throws Exception
     */
    public function getSessions($all = false)
    {
        $criteria = new CDbCriteria();

        if ($firm = Firm::model()->findByPk(@$_REQUEST['firm_id'])) {
            $criteria->addCondition('t.firm_id=:firm_id');
            $criteria->params[':firm_id'] = $firm->id;
        } elseif (@$_REQUEST['firm_id'] === 'NULL') {
            $criteria->addCondition('t.firm_id is null');
        }

        if ($theatre = OphTrOperationbooking_Operation_Theatre::model()->findByPk(@$_REQUEST['theatre_id'])) {
            $criteria->addCondition('t.theatre_id=:theatre_id');
            $criteria->params[':theatre_id'] = $theatre->id;
        }

        if (@$_REQUEST['date_from'] && strtotime(@$_REQUEST['date_from'])) {
            $criteria->addCondition('date >= :start_date');
            $criteria->params[':start_date'] = date('Y-m-d', strtotime(@$_REQUEST['date_from']));
        }

        if (@$_REQUEST['date_to'] && strtotime(@$_REQUEST['date_to'])) {
            $criteria->addCondition('date <= :end_date');
            $criteria->params[':end_date'] = date('Y-m-d', strtotime(@$_REQUEST['date_to']));
        }

        if (@$_REQUEST['weekday'] !== '') {
            $criteria->addCondition('sequence.weekday = :weekday');
            $criteria->params[':weekday'] = @$_REQUEST['weekday'];
        }

        if (@$_REQUEST['consultant'] !== '') {
            $criteria->addCondition('t.consultant = :consultant');
            $criteria->params[':consultant'] = @$_REQUEST['consultant'];
        }

        if (@$_REQUEST['paediatric'] !== '') {
            $criteria->addCondition('t.paediatric = :paediatric');
            $criteria->params[':paediatric'] = @$_REQUEST['paediatric'];
        }

        if (@$_REQUEST['anaesthetist'] !== '') {
            $criteria->addCondition('t.anaesthetist = :anaesthetist');
            $criteria->params[':anaesthetist'] = @$_REQUEST['anaesthetist'];
        }

        if (@$_REQUEST['general_anaesthetic'] !== '') {
            $criteria->addCondition('t.general_anaesthetic = :general_anaesthetic');
            $criteria->params[':general_anaesthetic'] = @$_REQUEST['general_anaesthetic'];
        }

        if (@$_REQUEST['available'] !== '') {
            $criteria->addCondition('t.available = :available');
            $criteria->params[':available'] = @$_REQUEST['available'];
        }

        if (@$_REQUEST['sequence_id'] !== '') {
            $criteria->addCondition('t.sequence_id = :sequence_id');
            $criteria->params[':sequence_id'] = @$_REQUEST['sequence_id'];
        }

        $page = @$_REQUEST['page'] ? $_REQUEST['page'] : 1;

        if ($all) {
            $criteria->addCondition('site.institution_id = :institution_id');
            $criteria->params[':institution_id'] = Institution::model()->getCurrent()->id;
            return OphTrOperationbooking_Operation_Session::model()->with(array('sequence', 'sequence.theatre.site'))->findAll($criteria);
        }

        $count = OphTrOperationbooking_Operation_Session::model()->with('sequence')->count($criteria);
        $pages = ceil($count / $this->sessions_items_per_page);

        if ($page < 1) {
            $page = 1;
        }
        if ($page > $pages) {
            $page = $pages;
        }

        $criteria->limit = $this->sessions_items_per_page;
        $criteria->offset = ($page - 1) * $this->sessions_items_per_page;

        $order = @$_REQUEST['order'] === 'desc' ? 'desc' : 'asc';

        switch (@$_REQUEST['sortby']) {
            case 'firm':
                $criteria->order = "firm.name $order, subspecialty.name $order";
                break;
            case 'theatre':
                $criteria->order = "theatre.name $order";
                break;
            case 'dates':
                $criteria->order = "date $order, t.start_time $order, t.end_time $order";
                break;
            case 'time':
                $criteria->order = "t.start_time $order, t.end_time $order";
                break;
            case 'interval':
                $criteria->order = "interval.name $order";
                break;
            case 'weekday':
                $criteria->order = "sequence.weekday $order";
                break;
            default:
                $criteria->order = "firm.name $order, subspecialty.name $order";
        }

        $with = array(
            'sequence',
            'firm' => array(
                'with' => array(
                    'serviceSubspecialtyAssignment' => array(
                        'with' => 'subspecialty',
                    ),
                ),
            ),
            'theatre',
            'theatre.site'
        );

        if (!$this->checkAccess('admin')) {
            $criteria->addCondition('site.institution_id = :institution_id');
            $criteria->params[':institution_id'] = Institution::model()->getCurrent()->id;
        }

        $this->items_per_page = $this->sessions_items_per_page;
        $pagination = $this->initPagination(OphTrOperationbooking_Operation_Session::model()->with($with), $criteria);
        $data = OphTrOperationbooking_Operation_Session::model()->with($with)->findAll($criteria);

        return array(
            'data' => $data,
            'pagination' => $pagination,
        );
    }

    public function actionSessionInlineEdit()
    {
        $errors = array();

        if (!empty($_POST['session'])) {
            $criteria = new CDbCriteria();
            $criteria->addInCondition('id', $_POST['session']);
            $sessions = OphTrOperationbooking_Operation_Session::model()->findAll($criteria);
        } elseif (@$_POST['use_filters']) {
            $sessions = $this->getSessions(true);
        }

        $result = $this->saveSessions($sessions);

        if (empty($result['errors'])) {
            foreach ($result['sessions'] as $session) {
                if (!$session->save()) {
                    $this->renderJSON($session->getErrors());

                    return;
                }
                Audit::add('admin', 'update', $session->id, null, array('module' => 'OphTrOperationbooking', 'model' => 'OphTrOperationbooking_Operation_Session'));
            }
            $this->renderJSON(array());
        } else {
            $this->renderJSON($result['errors']);
        }
    }

    public function saveSessions($sessions)
    {
        $errors = array();
        $_sessions = array();

        foreach ($sessions as $session) {
            $changed = false;

            foreach (array('firm_id', 'theatre_id', 'start_time', 'end_time', 'consultant', 'paediatric', 'anaesthetist', 'general_anaesthetic', 'comments', 'available') as $field) {
                if ($_POST['inline_'.$field] != '') {
                    if ($session->$field != $_POST['inline_'.$field]) {
                        $session->$field = $_POST['inline_'.$field];
                        $changed = true;
                    }
                }
            }
            if ($_POST['inline_date'] != '') {
                if (!strtotime($_POST['inline_date'])) {
                    $errors['date'] = 'Invalid start date';
                }
                if ($session->date != date('Y-m-d', strtotime($_POST['inline_date']))) {
                    $session->date = date('Y-m-d', strtotime($_POST['inline_date']));
                    $changed = true;
                }
            }

            if ($changed) {
                if (!$session->validate()) {
                    $errors = array_merge($errors, $session->getErrors());
                } else {
                    $_sessions[] = $session;
                }
            }
        }

        return array(
            'sessions' => $_sessions,
            'errors' => $errors,
        );
    }

    /**
     * @param $id
     * @throws Exception
     */
    public function actionEditSession($id)
    {
        if (!$session = OphTrOperationbooking_Operation_Session::model()->findByPk($id)) {
            throw new Exception("Session not found: $id");
        }

        $errors = array();

        if (!empty($_POST)) {
            $session->attributes = $_POST['OphTrOperationbooking_Operation_Session'];

            if (!$session->save()) {
                $errors = $session->getErrors();
            } elseif (empty($errors)) {
                Audit::add('admin', 'update', $id, null, array('module' => 'OphTrOperationbooking', 'model' => 'OphTrOperationbooking_Operation_Session'));
                $this->redirect(array('/OphTrOperationbooking/admin/viewSessions'));
            }
        }

        Audit::add('admin', 'view', $id, null, array('module' => 'OphTrOperationbooking', 'model' => 'OphTrOperationbooking_Operation_Session'));

        $this->render('/admin/editsession', array(
            'session' => $session,
            'errors' => $errors,
        ));
    }

    public function actionAddSession()
    {
        $session = new OphTrOperationbooking_Operation_Session();

        $errors = array();

        if (!empty($_POST)) {
            $session->attributes = $_POST['OphTrOperationbooking_Operation_Session'];

            if (!$session->save()) {
                $errors = $session->getErrors();
            } elseif (empty($errors)) {
                Audit::add('admin', 'create', $session->id, null, array('module' => 'OphTrOperationbooking', 'model' => 'OphTrOperationbooking_Operation_Session'));
                $this->redirect(array('/OphTrOperationbooking/admin/viewSessions'));
            }
        } elseif (isset($_GET['sequence_id'])) {
            $session->sequence_id = $_GET['sequence_id'];
        }

        $this->render('/admin/editsession', array(
            'session' => $session,
            'errors' => $errors,
        ));
    }

    public function actionVerifyDeleteSessions()
    {
        if (!empty($_POST['session'])) {
            $session_ids = $_POST['session'];
        } elseif (@$_POST['use_filters']) {
            $session_ids = array();
            foreach ($this->getSessions(true) as $session) {
                $session_ids[] = $session->id;
            }
        }

        $criteria = new CDbCriteria();
        $criteria->addInCondition('t.session_id', $session_ids);
        $criteria->addCondition('booking_cancellation_date is null');

        if (OphTrOperationbooking_Operation_Booking::model()
            ->with(array(
                'operation' => array(
                    'with' => array(
                        'event' => array(
                            'with' => 'episode',
                        ),
                    ),
                ),
            ))
            ->find($criteria)) {
            echo '0';
        } else {
            echo '1';
        }
    }

    public function actionDeleteSessions()
    {
        if (!empty($_POST['session'])) {
            $criteria = new CDbCriteria();
            $criteria->addInCondition('id', $_POST['session']);
            $sessions = OphTrOperationbooking_Operation_Session::model()->findAll($criteria);
        } elseif (@$_POST['use_filters']) {
            $sessions = $this->getSessions(true);
        }

        foreach ($sessions as $session) {
            if (!$session->delete()) {
                throw new Exception('Unable to delete session: '.print_r($session->getErrors(), true));
            }
            Audit::add('admin', 'delete', $session->id, null, array('module' => 'OphTrOperationbooking', 'model' => 'OphTrOperationbooking_Operation_Session'));
        }

        echo '1';
    }

    public function actionVerifyDeleteSequences()
    {
        if (!empty($_POST['sequence'])) {
            $sequence_ids = $_POST['sequence'];
        } elseif (@$_POST['use_filters']) {
            $sequence_ids = array();
            foreach ($this->getSequences(true) as $sequence) {
                $sequence_ids[] = $sequence->id;
            }
        }

        $criteria = new CDbCriteria();
        $criteria->addInCondition('session.sequence_id', $sequence_ids);
        $criteria->addCondition('booking_cancellation_date is null');

        if (OphTrOperationbooking_Operation_Booking::model()
            ->with(array(
                'session',
                'operation' => array(
                    'with' => array(
                        'event' => array(
                            'with' => 'episode',
                        ),
                    ),
                ),
            ))
            ->find($criteria)) {
            echo '0';
        } else {
            echo '1';
        }
    }

    public function actionDeleteSequences()
    {
        if (!empty($_POST['sequence'])) {
            $criteria = new CDbCriteria();
            $criteria->addInCondition('id', $_POST['sequence']);
            $sequences = OphTrOperationbooking_Operation_Sequence::model()->findAll($criteria);
        } elseif (@$_POST['use_filters']) {
            $sequences = $this->getSequences(true);
        }

        foreach ($sequences as $sequence) {
            foreach ($sequence->sessions as $session) {
                if (!$session->delete()) {
                    throw new Exception('Unable to delete session: '.print_r($session->getErrors(), true));
                }
            }

            if (!$sequence->delete()) {
                throw new Exception('Unable to delete sequence: '.print_r($sequence->getErrors(), true));
            }

            Audit::add('admin', 'delete', $sequence->id, null, array('module' => 'OphTrOperationbooking', 'model' => 'OphTrOperationbooking_Operation_Sequence'));
        }

        echo '1';
    }

    public function actionViewTheatres()
    {
        Audit::add('admin', 'list', null, null, array('module' => 'OphTrOperationbooking', 'model' => 'OphTrOperationbooking_Operation_Theatre'));

        $this->render('/admin/theatre/index');
    }

    public function actionAddTheatre()
    {
        $errors = array();

        $theatre = new OphTrOperationbooking_Operation_Theatre();

        if (!empty($_POST)) {
            $theatre->attributes = $_POST['OphTrOperationbooking_Operation_Theatre'];
            if (!$theatre->save()) {
                $errors = $theatre->getErrors();
            } else {
                Audit::add('admin', 'create', $theatre->id, null, array('module' => 'OphTrOperationbooking', 'model' => 'OphTrOperationbooking_Operation_Theatre'));
                $this->redirect(array('/OphTrOperationbooking/admin/viewTheatres'));
            }
        }

        $this->render('/admin/theatre/edit', array(
            'theatre' => $theatre,
            'errors' => $errors,
        ));
    }

    public function actionEditTheatre($id)
    {
        if (!$theatre = OphTrOperationbooking_Operation_Theatre::model()->findByPk($id)) {
            throw new Exception("Theatre not found: $id");
        }

        $errors = array();

        if (!empty($_POST)) {
            $theatre->attributes = $_POST['OphTrOperationbooking_Operation_Theatre'];
            if (!$theatre->save()) {
                $errors = $theatre->getErrors();
            } else {
                Audit::add('admin', 'update', $id, null, array('module' => 'OphTrOperationbooking', 'model' => 'OphTrOperationbooking_Operation_Theatre'));

                $this->redirect(array('/OphTrOperationbooking/admin/viewTheatres'));
            }
        }

        Audit::add('admin', 'view', $id, null, array('module' => 'OphTrOperationbooking', 'model' => 'OphTrOperationbooking_Operation_Theatre'));

        $this->render('/admin/theatre/edit', array(
            'theatre' => $theatre,
            'errors' => $errors,
        ));
    }

    public function actionGetWardOptions($siteId, $wardId = null)
    {
        if ($siteId > 0) {
            $optionValues = OphTrOperationbooking_Operation_Ward::model()->findAll(array(
                'condition' => 'active=1 and site_id='.$siteId,
                'order' => 'name',
            ));
        } else {
            $optionValues = array();
        }
        echo CHtml::dropDownList(
            'OphTrOperationbooking_Operation_Theatre[ward_id]',
            $wardId,
            CHtml::listData($optionValues, 'id', 'name'),
            array(
                'empty' => '- None -', )
        );
    }

    public function actionVerifyDeleteTheatres()
    {
        $criteria = new CDbCriteria();
        $criteria->addInCondition('session.theatre_id', $_POST['theatre']);
        $criteria->addCondition('booking_cancellation_date is null');
        $criteria->addCondition('session_date >= :today');
        $criteria->params[':today'] = date('Y-m-d');

        if (OphTrOperationbooking_Operation_Booking::model()
            ->with(array(
                'session',
                'operation' => array(
                    'with' => array(
                        'event' => array(
                            'with' => 'episode',
                        ),
                    ),
                ),
            ))
            ->find($criteria)) {
            echo '0';
        } else {
            echo '1';
        }
    }

    public function actionDeleteTheatres()
    {
        $criteria = new CDbCriteria();
        $criteria->addInCondition('id', $_POST['theatre']);
        $theatres = OphTrOperationbooking_Operation_Theatre::model()->findAll($criteria);

        foreach ($theatres as $theatre) {
            $theatre->active = false;
            if (!$theatre->save()) {
                throw new Exception('Unable to mark theatre deleted: '.print_r($theatre->getErrors(), true));
            }
            Audit::add('admin', 'delete', $_POST['theatre'], null, array('module' => 'OphTrOperationbooking', 'model' => 'OphTrOperationbooking_Operation_Theatre'));
        }

        echo '1';
    }

    public function actionViewWards()
    {
        Audit::add('admin', 'list', null, null, array('module' => 'OphTrOperationbooking', 'model' => 'OphTrOperationbooking_Operation_Ward'));

        $criteria = new CDbCriteria();
        if (!$this->checkAccess('admin')) {
            $criteria->with = 'site';
            $criteria->addCondition('site.institution_id = :institution_id');
            $criteria->params[':institution_id'] = Institution::model()->getCurrent()->id;
        }

        $this->render(
            '/admin/ward/index',
            [
                'wards' => OphTrOperationbooking_Operation_Ward::model()->findAll($criteria),
            ]
        );
    }

    /**
     * @param int|null $id
     * @throws Exception
     */
    public function actionEditWard($id = null)
    {
        if (is_null($id)) {
            $ward = new OphTrOperationbooking_Operation_Ward();
        } elseif (!$ward = OphTrOperationbooking_Operation_Ward::model()->findByPk($id)) {
            throw new Exception("Ward not found: $id");
        }

        $errors = [];

        if (!empty($_POST)) {
            $attributes = \Yii::app()->request->getPost('OphTrOperationbooking_Operation_Ward');
            $ward->attributes = $attributes;

            $ward->restriction = 0;

            $ward->active = $attributes['active'] ?? null;

            if (isset($attributes['restriction_male']) && (int)$attributes['restriction_male'] === 1) {
                $ward->restriction += OphTrOperationbooking_Operation_Ward::RESTRICTION_MALE;
            }
            if (isset($attributes['restriction_female']) && (int)$attributes['restriction_female'] === 1) {
                $ward->restriction += OphTrOperationbooking_Operation_Ward::RESTRICTION_FEMALE;
            }
            if (isset($attributes['restriction_child']) && (int)$attributes['restriction_child'] === 1) {
                $ward->restriction += OphTrOperationbooking_Operation_Ward::RESTRICTION_CHILD;
            }
            if (isset($attributes['restriction_adult']) && (int)$attributes['restriction_adult'] === 1) {
                $ward->restriction += OphTrOperationbooking_Operation_Ward::RESTRICTION_ADULT;
            }
            if (isset($attributes['restriction_observation']) && (int)$attributes['restriction_observation'] === 1) {
                $ward->restriction += OphTrOperationbooking_Operation_Ward::RESTRICTION_OBSERVATION;
            }
            $action = $ward->isNewRecord ? 'create' : 'update';
            if (!$ward->save()) {
                $errors = $ward->getErrors();
            } else {
                Audit::add('admin', $action, 'ward id is: ' . $ward->id, null, array('module' => 'OphTrOperationbooking', 'model' => 'OphTrOperationbooking_Operation_Ward'));

                $this->redirect(array('/OphTrOperationbooking/admin/viewWards'));
            }
        }

        Audit::add('admin', 'view', $id, null, array('module' => 'OphTrOperationbooking', 'model' => 'OphTrOperationbooking_Operation_Ward'));

        $this->render('/admin/ward/edit', [
            'ward' => $ward,
            'errors' => $errors,
        ]);
    }

    public function actionAddWard()
    {
        $this->actionEditWard();
    }

    /**
     * Reorder the OphTrOperationbooking_Operation_Ward objects.
     *
     * @throws Exception
     */
    public function actionSortWards()
    {
        if (!empty($_POST['order'])) {
            $transaction = Yii::app()->db->beginTransaction();
            try {
                foreach ($_POST['order'] as $i => $id) {
                    if ($ward = OphTrOperationbooking_Operation_Ward::model()->findByPk($id)) {
                        $ward->display_order = $i + 1;
                        if (!$ward->save()) {
                            throw new Exception('Unable to save patient unavailable reason: '.print_r($ward->getErrors(), true));
                        }
                    }
                }
                Audit::add('admin', 'sort', serialize($_POST), false, array('module' => 'OphTrOperationbooking', 'model' => 'OphTrOperationbooking_Operation_Ward'));
                $transaction->commit();
            } catch (Exception $e) {
                $transaction->rollback();
                throw $e;
            }
        }
    }

    public function actionOperationPriorities()
    {
        $this->genericAdmin('Operation priorities', 'OphTrOperationbooking_Operation_Priority', array(
            'extra_fields' => array(
                array(
                    'field' => 'schedule_authitem',
                    'type' => 'authitem_roles',
                    'authitem_type' => 'operations',
                    'empty' => '- Edit rights -',
                ),
            ),
            'div_wrapper_class' => 'cols-5',
        ));
    }

    public function actionScheduleOptions()
    {
        $this->genericAdmin('Operation scheduling options', 'OphTrOperationbooking_ScheduleOperation_Options', ['div_wrapper_class' => 'cols-5']);
    }

    /**
     * List all the OphTrOperationbooking_ScheduleOperation_PatientUnavailableReason objects.
     */
    public function actionViewPatientUnavailableReasons()
    {
        Audit::add('admin', 'list', null, false, array('module' => 'OphTrOperationbooking', 'model' => 'OphTrOperationbooking_ScheduleOperation_PatientUnavailableReason'));

        $this->render('/admin/patient_unavailable_reasons/index');
    }

    /**
     * Edit the OphTrOperationbooking_ScheduleOperation_PatientUnavailableReason specified by $id.
     *
     * @param $id
     *
     * @throws Exception
     */
    public function actionEditPatientUnavailableReason($id)
    {
        if (!$reason = OphTrOperationbooking_ScheduleOperation_PatientUnavailableReason::model()->findByPk($id)) {
            throw new Exception("Patient Unavailable Reason not found: $id");
        }

        $errors = array();

        if (!empty($_POST)) {
            $reason->attributes = $_POST['OphTrOperationbooking_ScheduleOperation_PatientUnavailableReason'];

            if (!$reason->save()) {
                $errors = $reason->getErrors();
            } else {
                Audit::add('admin', 'update', serialize(array_merge(array('id' => $id), $_POST)), false, array('module' => 'OphTrOperationbooking', 'model' => 'OphTrOperationbooking_ScheduleOperation_PatientUnavailableReason'));

                $this->redirect(array('/OphTrOperationbooking/admin/viewPatientUnavailableReasons'));
            }
        }

        Audit::add('admin', 'view', $id, false, array('module' => 'OphTrOperationbooking', 'model' => 'OphTrOperationbooking_ScheduleOperation_PatientUnavailableReason'));

        $this->render('/admin/patient_unavailable_reasons/edit', array(
            'reason' => $reason,
            'errors' => $errors,
        ));
    }

    /**
     * Add a OphTrOperationbooking_ScheduleOperation_PatientUnavailableReason.
     */
    public function actionAddPatientUnavailableReason()
    {
        $errors = array();

        $reason = new OphTrOperationbooking_ScheduleOperation_PatientUnavailableReason();

        if (!empty($_POST)) {
            $reason->attributes = $_POST['OphTrOperationbooking_ScheduleOperation_PatientUnavailableReason'];
            //$reason->institution_id = Institution::model()->getCurrent()->id;
            if ($this->checkAccess('admin')) {
                $saved = $reason->save();
            } else {
                throw new Exception('User is not an installation level admin');
            }
            if (!$saved) {
                $errors = $reason->getErrors();
            } else {
                Audit::add('admin', 'create', serialize($_POST), false, array('module' => 'OphTrOperationbooking', 'model' => 'OphTrOperationbooking_ScheduleOperation_PatientUnavailableReason'));
                $this->redirect(array('admin/viewPatientUnavailableReasons'));
            }
        }

        $this->render('/admin/patient_unavailable_reasons/edit', array(
            'reason' => $reason,
            'errors' => $errors,
        ));
    }

    /**
     * Reorder the OphTrOperationbooking_ScheduleOperation_PatientUnavailableReason objects.
     *
     * @throws Exception
     */
    public function actionSortPatientUnavailableReasons()
    {
        if (!empty($_POST['order'])) {
            foreach ($_POST['order'] as $i => $id) {
                if ($reason = OphTrOperationbooking_ScheduleOperation_PatientUnavailableReason::model()->findByPk($id)) {
                    $reason->display_order = $i + 1;
                    if (!$reason->save()) {
                        throw new Exception('Unable to save patient unavailable reason: '.print_r($reason->getErrors(), true));
                    }
                }
            }
            Audit::add('admin', 'sort', serialize($_POST), false, array('module' => 'OphTrOperationbooking', 'model' => 'OphTrOperationbooking_ScheduleOperation_PatientUnavailableReason'));
        }
    }

    /**
     * Disable or enable a OphTrOperationbooking_ScheduleOperation_PatientUnavailableReason.
     *
     * @throws Exception
     */
    public function actionSwitchEnabledPatientUnavailableReason()
    {
        if (!$reason = OphTrOperationbooking_ScheduleOperation_PatientUnavailableReason::model()->findByPk(@$_POST['id'])) {
            throw new Exception('Patient Unavailable Reason not found: '.@$_POST['id']);
        }

        if ($reason->enabled) {
            $reason->enabled = 0;
            $action = 'disabled';
        } else {
            $reason->enabled = 1;
            $action = 'enabled';
        }
        if (!$reason->save()) {
            throw new Exception('Unexpected error changing enabled status for Patient Unavailable Reason '.print_r($reason->getErrors(), true));
        }

        Audit::add('admin', $action, serialize($_POST), false, array('module' => 'OphTrOperationbooking', 'model' => 'OphTrOperationbooking_ScheduleOperation_PatientUnavailableReason'));
    }

    /**
     * List all the OphTrOperationbooking_Operation_Session_UnavailableReason objects.
     */
    public function actionViewSessionUnavailableReasons()
    {
        Audit::add('admin', 'list', null, false, array('module' => 'OphTrOperationbooking', 'model' => 'OphTrOperationbooking_Operation_Session_UnavailableReason'));

        $this->render('sessionunavailablereasons');
    }

    /**
     * Edit the OphTrOperationbooking_Operation_Session_UnavailableReason specified by $id.
     *
     * @param $id
     *
     * @throws Exception
     */
    public function actionEditSessionUnavailableReason($id)
    {
        if (!$reason = OphTrOperationbooking_Operation_Session_UnavailableReason::model()->findByPk($id)) {
            throw new Exception("Session Unavailable Reason not found: $id");
        }

        $errors = array();

        if (!empty($_POST)) {
            $transaction = Yii::app()->db->beginTransaction();
            try {
                $reason->attributes = $_POST['OphTrOperationbooking_Operation_Session_UnavailableReason'];
                if (!$reason->save()) {
                    $errors = $reason->getErrors();
                    $transaction->rollback();
                } else {
                    Audit::add('admin', 'update', serialize(array_merge(array('id' => $id), $_POST)), false, array('module' => 'OphTrOperationbooking', 'model' => 'OphTrOperationbooking_Operation_Session_UnavailableReason'));
                    $transaction->commit();
                }
            } catch (Exception $e) {
                $transaction->rollback();
                throw $e;
            }
            if (empty($errors)) {
                $this->redirect(array('/OphTrOperationbooking/admin/viewSessionUnavailableReasons'));
            }
        }

        Audit::add('admin', 'view', $id, false, array('module' => 'OphTrOperationbooking', 'model' => 'OphTrOperationbooking_Operation_Session_UnavailableReason'));

        $this->render('/admin/editsessionunavailablereason', array(
                        'reason' => $reason,
                        'errors' => $errors,
                ));
    }

    /**
     * Add a OphTrOperationbooking_Operation_Session_UnavailableReason.
     */
    public function actionAddSessionUnavailableReason()
    {
        $errors = array();

        $reason = new OphTrOperationbooking_Operation_Session_UnavailableReason();

        if (!empty($_POST)) {
            $transaction = Yii::app()->db->beginTransaction();
            try {
                $reason->attributes = $_POST['OphTrOperationbooking_Operation_Session_UnavailableReason'];
                if (!$reason->save()) {
                    $errors = $reason->getErrors();
                    $transaction->rollback();
                } else {
                    Audit::add('admin', 'create', serialize($_POST), false, array('module' => 'OphTrOperationbooking', 'model' => 'OphTrOperationbooking_Operation_Session_UnavailableReason'));
                    $transaction->commit();
                }
            } catch (Exception $e) {
                $transaction->rollback();
                throw $e;
            }
            if (empty($errors)) {
                $this->redirect(array('admin/viewSessionUnavailableReasons'));
            }
        }

        $this->render('/admin/editsessionunavailablereason', array(
                        'reason' => $reason,
                        'errors' => $errors,
                ));
    }

    /**
     * Reorder the OphTrOperationbooking_Operation_Session_UnavailableReason objects.
     *
     * @throws Exception
     */
    public function actionSortSessionUnavailableReasons()
    {
        if (!empty($_POST['order'])) {
            $transaction = Yii::app()->db->beginTransaction();
            try {
                foreach ($_POST['order'] as $i => $id) {
                    if ($reason = OphTrOperationbooking_Operation_Session_UnavailableReason::model()->findByPk($id)) {
                        $reason->display_order = $i + 1;
                        if (!$reason->save()) {
                            throw new Exception('Unable to save sessiion unavailable reason: '.print_r($reason->getErrors(), true));
                        }
                    }
                }
                Audit::add('admin', 'sort', serialize($_POST), false, array('module' => 'OphTrOperationbooking', 'model' => 'OphTrOperationbooking_ScheduleOperation_PatientUnavailableReason'));
                $transaction->commit();
            } catch (Exception $e) {
                $transaction->rollback();
                throw $e;
            }
        }
    }

    /**
     * Disable or enable a OphTrOperationbooking_ScheduleOperation_PatientUnavailableReason.
     *
     * @throws Exception
     */
    public function actionSwitchEnabledSessionUnavailableReason()
    {
        if (!$reason = OphTrOperationbooking_Operation_Session_UnavailableReason::model()->findByPk(@$_POST['id'])) {
            throw new Exception('Session Unavailable Reason not found: '.@$_POST['id']);
        }

        if ($reason->enabled) {
            $reason->enabled = 0;
            $action = 'disabled';
        } else {
            $reason->enabled = 1;
            $action = 'enabled';
        }
        if (!$reason->save()) {
            throw new Exception('Unexpected error changing enabled status for Session Unavailable Reason '.print_r($reason->getErrors(), true));
        }

        Audit::add('admin', $action, serialize($_POST), false, array('module' => 'OphTrOperationbooking', 'model' => 'OphTrOperationbooking_Operation_Session_UnavailableReason'));
    }
    public function beforeAction($action)
    {
        $assetPath = Yii::app()->assetManager->publish(Yii::getPathOfAlias('application.assets'), true);
        Yii::app()->clientScript->registerCssFile($assetPath . '/components/jt.timepicker/jquery.timepicker.css');

        return parent::beforeAction($action);
    }

    public function actionPreassessmentType()
    {
        $this->genericAdmin('Pre-assessment Types', 'OphTrOperationbooking_PreAssessment_Type', array(
            'extra_fields' => array(
                array(
                    'field' => 'use_location',
                    'type' => 'boolean',
                )
            ),
            'div_wrapper_class' => 'cols-5',
        ));
    }

    public function actionPreassessmentLocation()
    {
        $this->genericAdmin('Pre-assessment Locations', 'OphTrOperationbooking_PreAssessment_Location', array(
            'extra_fields' => array(
                array(
                    'field' => 'site_id',
                    'type' => 'lookup',
                    'model' => 'Site',
                )
            ),
            'div_wrapper_class' => 'cols-5',
        ));
    }
}
