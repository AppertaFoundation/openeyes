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

class AdminSessionController extends Controller
{
	public $layout='column2';

	protected function beforeAction($action)
	{
		// Sample code to be used when RBAC is fully implemented.
		if (!Yii::app()->user->checkAccess('admin')) {
			throw new CHttpException(403, 'You are not authorised to perform this action.');
		}

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
	 * Bulk creates sessions based on sequence template
	 */
	public function actionMassCreate()
	{
		$runner = new CConsoleCommandRunner;
		$command = new GenerateSessionsCommand('generateSessions',$runner);

		$endDate = date('Y-m-d', strtotime('+13 months'));
		$returnOutput = true;

		$output = $command->run(array($endDate, $returnOutput));

		Yii::app()->user->setFlash('notice',"The command has run with the following results:\n" . $output);

		$this->forward('index');
	}

	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionUpdate($id)
	{
		$model = $this->loadModel($id);

		if (!$model->firmAssignment) {
			$model->firmAssignment = new SessionFirmAssignment();
			$model->firmAssignment->session_id = $model->id;
		}

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if (isset($_POST['Session'])) {
			// TODO: Add validation to check collisions etc.
			$model->attributes = $_POST['Session'];
			if (!empty($_POST['SessionFirmAssignment']['firm_id'])) {
				$model->firmAssignment->attributes = $_POST['SessionFirmAssignment'];
				$firmValid = $model->firmAssignment->save();
			} else {
				if ($model->firmAssignment->id) {
					$model->firmAssignment->delete();
				}
				$firmValid = true;
			}
			if ($firmValid && $model->save()) {
				$this->redirect(array('view','id' => $model->id));
			}
		}

		$this->render('update',array(
			'model' => $model,
		));
	}

	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'index' page.
	 * @param integer $id the ID of the model to be deleted
	 */
	public function actionDelete($id)
	{
		// Deleting not allowed until RBAC properly implemented
		throw new CHttpException(403, 'You are not authorised to perform this action.');

		/*
		if (Yii::app()->request->isPostRequest) {
			// we only allow deletion via POST request
			$this->loadModel($id)->delete();

			// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
			if(!isset($_GET['ajax']))
				$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
		} else {
			throw new CHttpException(400,'Invalid request. Please do not repeat this request again.');
		}
		*/
	}

	/**
	 * Lists all models.
	 */
	public function actionIndex()
	{
		$dataProvider=new CActiveDataProvider('Session', array(
			'criteria' => array('order' => 'date ASC')
		));

		$this->render('index',array(
			'dataProvider'=>$dataProvider,
		));
	}

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		$model=new Session('search');
		$model->unsetAttributes();
		if (isset($_GET['Session'])) {
			$model->attributes = $_GET['Session'];
		}
		if (isset($_GET['Firm'])) {
			$model->firm_id = $_GET['Firm']['id'];
		}
		if (isset($_GET['Site'])) {
			$model->site_id = $_GET['Site']['id'];
		}
		$this->render('admin',array(
			'model'=>$model,
		));
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the ID of the model to be loaded
	 */
	public function loadModel($id)
	{
		$model=Session::model()->findByPk((int) $id);
		if ($model===null) {
			throw new CHttpException(404,'The requested page does not exist.');
		}
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param CModel the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if (isset($_POST['ajax']) && $_POST['ajax']==='session-form') {
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
