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
 * @copyright Copyright (c) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

namespace OEModule\OphCiExamination\controllers;

use OEModule\OphCiExamination\models\OphCiExaminationRisk;

class RisksAdminController extends \ModuleAdminController
{
	public $group = 'Examination';

	private $_display_name = "Risks";

	public function actionList()
	{
		$admin = $this->_getAdmin();
		$admin->setListFields(array(
				'id',
				'name',
				'active'
			)
		);

		$admin->listModel(true);
	}

	public function actionEdit($id = null)
	{
		$admin = $this->_getEditAdmin($id);
		$admin->editModel();
	}

	public function actionSave($id = null)
	{
		if (is_null($id)) {
			$model = new OphCiExaminationRisk();
		}
		else {
			if (!$model = OphCiExaminationRisk::model()->findByPk($id)) {
				throw new \CHttpException(404);
			}
		}
		/** @var OphCiExaminationRisk $model */

		$data = \Yii::app()->request->getPost('OEModule_OphCiExamination_models_OphCiExaminationRisk');
		$this->_setModelData($model, $data);

		if ($model->hasErrors()) {
			$admin = $this->_getEditAdmin($model);
			$this->render($admin->getEditTemplate(), array('admin' => $admin, 'errors' => $model->getErrors() ));
			exit;
		}

		/** @var CDbTransaction $trans */
		$trans = \Yii::app()->db->beginTransaction();

		if ($model->save(false)) {
			\Yii::app()->db->createCommand("DELETE FROM ophciexamination_risk_tag WHERE risk_id = {$model->id}")->execute();
			if (array_key_exists('medicationSets', $data) && !empty($data['medicationSets'])) {
				foreach ($data['medicationSets'] as $id) {
					$id = filter_var($id, FILTER_SANITIZE_NUMBER_INT);
					\Yii::app()->db->createCommand("INSERT INTO ophciexamination_risk_tag (risk_id, medication_set_id) VALUES ({$model->id}, $id)")->execute();
				}
			}
			$trans->commit();
		}
		else {
			$trans->rollback();
		}

		$this->redirect('/'.$this->getModule()->id.'/'.$this->id.'/list');
	}

	public function actionDelete()
	{
		foreach ($_POST['OEModule\OphCiExamination\models\OphCiExaminationRisk'] as $item) {
			foreach ($item as $id) {
				if (!$model = OphCiExaminationRisk::model()->findByPk($id)) {
					throw new \CHttpException(404);
				}
				
				$model->delete();
			}
		}

		echo "1";
	}

	/**
	 * @return \Admin
	 */

	private function _getAdmin($id = null)
	{
		if (is_null($id)) {
			$model = OphCiExaminationRisk::model();
		}
		else {
			$model = OphCiExaminationRisk::model()->findByPk($id);
		}
		$admin = new \Admin($model, $this);
		$admin->setModelDisplayName($this->_display_name);
		return $admin;
	}

	/**
	 * @param null $id
	 * @return \Admin
	 */

	private function _getEditAdmin($id = null)
	{
		$admin = $this->_getAdmin($id);
		$admin->setEditFields(array(
			'name' => 'text',
			'active' => 'checkbox',
			'medicationSets' => array(
				'widget' => 'CustomView',
				'viewName' => 'application.modules.OphCiExamination.views.admin.edit_risk_set_assignment',
				'viewArguments' => array(
					'model' => $admin->getModel()
				)
			),
		));
		$admin->setCustomSaveURL("/OphCiExamination/risksAdmin/save/$id");
		return $admin;
	}

	private function _setModelData(OphCiExaminationRisk $model, array $data)
	{
		$model->name = $data['name'];
		$model->active = $data['active'];
		$model->validate();
	}

    public function actionSearch()
    {
        if (\Yii::app()->request->isAjaxRequest) {
            $criteria = new \CDbCriteria();
            if (isset($_GET['term']) && strlen($term = $_GET['term']) > 0) {
                $criteria->addCondition(array('LOWER(name) LIKE :term'),
                    'OR');
                $params[':term'] = '%'.strtolower(strtr($term, array('%' => '\%'))).'%';
            }

            $criteria->order = 'name';
            $criteria->select = 'id, name';
            $criteria->params = $params;

            $medicationsets = \MedicationSet::model()->findAll($criteria);

            $return = array();
            foreach ($medicationsets as $medicationset) {
                $return[] = array(
                    'label' => $medicationset->name,
                    'value' => $medicationset->name,
                    'id' => $medicationset->id,
                );
            }
            echo \CJSON::encode($return);
        }
    }
}