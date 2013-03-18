<?php
/**
 * OpenEyes
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

class LetterTemplateController extends BaseController
{
	public $layout = '//layouts/patientMode/column2';
	public $firm;

	protected function beforeAction($action)
	{
		// Only internal consultants are allowed to use this page
		if (!User::isConsultant()) {
			throw new CHttpException(403, 'You are not permitted to administrate letter templates.');
		}

		$this->storeData();

		$this->firm = Firm::model()->findByPk($this->selectedFirmId);

		return parent::beforeAction($action);
	}

	/**
	 * Displays a particular model.
	 * @param integer $id the ID of the model to be displayed
	 */
	public function actionView($id)
	{
		$this->render('view',array(
			'model'=>$this->loadModel($id),
		));
	}

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{
		$model=new LetterTemplate;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['LetterTemplate']))
		{
			$model->attributes=$_POST['LetterTemplate'];
			$model->subspecialty_id = $this->firm->serviceSubspecialtyAssignment->subspecialty_id;
			if($model->save())
				$this->redirect(array('view','id'=>$model->id));
		}

		$this->render('create',array(
			'model'=>$model,
		));
	}

	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionUpdate($id)
	{
		$model=$this->loadModel($id);

		if ($model->subspecialty_id != $this->firm->serviceSubspecialtyAssignment->subspecialty_id) {
			throw new CHttpException(403, 'You are not permitted to alter this letter template.');
		}

		if(isset($_POST['LetterTemplate']))
		{
			$model->attributes=$_POST['LetterTemplate'];
			$model->subspecialty_id = $this->firm->serviceSubspecialtyAssignment->subspecialty_id;
			if($model->save())
				$this->redirect(array('view','id'=>$model->id));
		}

		$this->render('update',array(
			'model'=>$model,
		));
	}

	/**
	 * Lists all models.
	 */
	public function actionIndex()
	{
		$dataProvider=new CActiveDataProvider('LetterTemplate', array(
			'criteria'=>array(
				'condition'=>'subspecialty_id=' . $this->firm->serviceSubspecialtyAssignment->subspecialty_id
			)
		));

		$this->render('index',array(
			'dataProvider'=>$dataProvider,
		));
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the ID of the model to be loaded
	 */
	public function loadModel($id)
	{
		$model=LetterTemplate::model()->findByPk((int)$id);

		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');

		if ($model->subspecialty_id != $this->firm->serviceSubspecialtyAssignment->subspecialty_id) {
			throw new CHttpException(403, 'You are not permitted to view this letter template.');
		}

		return $model;
	}
}
