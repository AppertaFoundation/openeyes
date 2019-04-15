<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2019
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2018, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

class MedicationSetAutoRulesAdminController extends BaseAdminController
{
	public $group = "Drugs";

	public function actionList()
	{
		$admin = new Admin(MedicationSet::model(), $this);
		$admin->setListFields(array(
			'name',
			'hiddenString',
			'itemsCount',
			'adminListAction'
		));

		$admin->getSearch()->getCriteria()->addCondition("automatic = 1");
		$admin->getSearch()->setItemsPerPage(30);
		$admin->getSearch()->addSearchItem('name');

		$admin->setModelDisplayName("Automatic medication sets");
		$admin->setListTemplate('application.modules.OphDrPrescription.views.admin.auto_set_rule.list');
		$admin->listModel(true);
	}

	public function actionEdit($id = null)
	{
		$admin = $this->_getAdmin($id);
		$admin->editModel();
	}

	private function _getAdmin($id)
	{
		$admin = new Admin(MedicationSet::model(), $this);

		$admin->setModelDisplayName("Automatic Set Rules");
		if($id) {
			$admin->setModelId($id);
		}

		$admin->setEditFields(array(
			'name'=>'Name',
			'hidden' => array(
				'widget' => 'DropDownList',
				'options' => array(0 => 'Visible set', 1 => 'Hidden/system set'),
				'label' => 'Set is a',
				'htmlOptions' => array(),
				'hidden' => false,
				'layoutColumns' => []
			),
			'medicationSetAutoRuleAttributes' => array(
				'widget' => 'CustomView',
				'viewName' => 'application.modules.OphDrPrescription.views.admin.auto_set_rule.edit_attributes',
				'viewArguments' => array(
					'medicationSet' => $admin->getModel()
				)
			),
			'medicationSetAutoRuleSetMemberships' => array(
				'widget' => 'CustomView',
				'viewName' => 'application.modules.OphDrPrescription.views.admin.auto_set_rule.edit_set_membership',
				'viewArguments' => array(
					'medicationSet' => $admin->getModel()
				)
			),
			'medicationSetAutoRuleMedications' => array(
				'widget' => 'CustomView',
				'viewName' => 'application.modules.OphDrPrescription.views.admin.auto_set_rule.edit_individual_medications',
				'viewArguments' => array(
					'medicationSet' => $admin->getModel(),
				)
			)
		));

		$admin->setCustomSaveURL('/OphDrPrescription/medicationSetAutoRulesAdmin/save/'.$id);
		return $admin;
	}

	public function actionSave($id = -1)
	{
		if($id > 0) {
			$model = MedicationSet::model()->findByPk($id);
		}
		else {
			$model = new MedicationSet();
		}


		$data = Yii::app()->request->getPost('MedicationSet');

		$model->name = $data['name'];
		$model->hidden = $data['hidden'];
		$model->automatic = 1;

		$model->tmp_attrs = array();
		if(isset($data['attribute']['id']) && !empty($data['attribute']['id'])) {
			foreach ($data['attribute']['id'] as $key=>$attr_id) {
				$model->tmp_attrs[] = array(
					'id' => $attr_id,
					'medication_attribute_option_id' => $data['attribute']['medication_attribute_option_id'][$key]
				);
			}
		}

		$model->tmp_sets = array();
		if(isset($data['sets']['id']) && !empty($data['sets']['id'])) {
			foreach ($data['sets']['id'] as $key=>$s_id) {
				$model->tmp_sets[] = array(
					'id' => $s_id,
					'medication_set_id' => $data['sets']['medication_set_id'][$key]
				);
			}
		}

		$model->tmp_meds = array();
		if(isset($data['medications']['id']) && !empty($data['medications']['id'])) {
			foreach ($data['medications']['id'] as $key=>$m_id) {
				$model->tmp_meds[] = array(
					'id' => $m_id,
					'medication_id' => $data['medications']['medication_id'][$key],
					'include_parent' => $data['medications']['include_parent'][$key],
					'include_children' => $data['medications']['include_children'][$key],
				);
			}
		}

		$trans = Yii::app()->db->beginTransaction();

		if(!$model->validate() || !$model->save(false)) {
			$trans->rollback();
			$admin = $this->_getAdmin($id);
			$admin->setModel($model);
			$admin->editModel();
		}
		else {
			$trans->commit();
			$this->redirect("/OphDrPrescription/medicationSetAutoRulesAdmin/list");
		}
	}

	public function actionPopulateAll()
	{
		shell_exec("php ".Yii::app()->basePath."/yiic populateAutoMedicationSets >/dev/null 2>&1 &");
		Yii::app()->user->setFlash('success', "Rebuild process started at ".date('H:i').".");
		$this->redirect('/OphDrPrescription/medicationSetAutoRulesAdmin/list');
	}

	public function actionDelete()
	{
		$ids = Yii::app()->request->getPost("MedicationSet");
		$ids = $ids['id'];
		foreach ($ids as $id) {
			$set = MedicationSet::model()->findByPk($id);
			$trans = Yii::app()->db->trans_start();
			try{
				$set->delete();
			}
			catch (Exception $e) {
				$trans->rollback();
				echo 0;
				exit;
			}

			$trans->commit();
		}

		echo 1;
		exit;
	}
}