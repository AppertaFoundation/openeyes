<?php
/*
_____________________________________________________________________________
(C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
(C) OpenEyes Foundation, 2011
This file is part of OpenEyes.
OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
_____________________________________________________________________________
http://www.openeyes.org.uk   info@openeyes.org.uk
--
*/

class AdminSequenceController extends BaseController
{
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
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
		$model = $this->loadModel($id);
		$this->render('view',array(
			'model'=>$model,
		));
	}

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{
		$model=new Sequence;
		$firmAssociation = new SequenceFirmAssignment;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['Sequence']))
		{
			$model->attributes=$_POST['Sequence'];
			$firmAssociation->attributes=$_POST['SequenceFirmAssignment'];
			$modelValid = $model->validate();
			$firmValid = $firmAssociation->validate();
			if ($modelValid && $firmValid) {
				if ($model->save()) {
					if (!empty($firmAssociation->firm_id)) {
						$firmAssociation->sequence_id = $model->id;
						if ($firmAssociation->save()) {
							$this->redirect(array('view','id'=>$model->id));
						}
					} else {
						$this->redirect(array('view','id'=>$model->id));
					}
				}
			}
		}

		$this->render('create',array(
			'model'=>$model,
			'firm'=>$firmAssociation
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
		$firmAssignment = $model->sequenceFirmAssignment;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['Sequence']))
		{
			$model->attributes=$_POST['Sequence'];
			if (!empty($_POST['SequenceFirmAssignment']['firm_id'])) {
				$firmAssignment->attributes=$_POST['SequenceFirmAssignment'];
				$firmValid = $firmAssignment->save();
			} else {
				SequenceFirmAssignment::model()->deleteByPk(
					$model->sequenceFirmAssignment->id);
				$firmValid = true;
			}
			if ($model->save() && $firmValid) {

				$this->redirect(array('view','id'=>$model->id));
			}
		}

		$this->render('update',array(
			'model'=>$model,
			'firm'=>$firmAssignment
		));
	}

	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'admin' page.
	 * @param integer $id the ID of the model to be deleted
	 */
	public function actionDelete($id)
	{
		if(Yii::app()->request->isPostRequest)
		{
			// we only allow deletion via POST request

			// make really sure this thing has no bookings associated with it before we delete
			$sequence = $this->loadModel($id);
			if ($sequence->getAssociatedBookings() > 0) {
				throw new CHttpException(400, 'This sequence has bookings associated with it and cannot be deleted.');
			}

			// delete any sessions that are involved with this sequence first
			Session::model()->deleteAllByAttributes(array('sequence_id' => $sequence->id));

			// also delete any firm association(s)
			SequenceFirmAssignment::model()->deleteAllByAttributes(array('sequence_id' => $sequence->id));

			$sequence->delete();

			// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
			if(!isset($_GET['ajax']))
				$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
		}
		else
			throw new CHttpException(400,'Invalid request. Please do not repeat this request again.');
	}

	/**
	 * Lists all models.
	 */
	public function actionIndex()
	{
		$dataProvider=new CActiveDataProvider('Sequence', array(
			'criteria' => array('with' => array('sequenceFirmAssignment'))
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
		$model=new Sequence('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['Sequence']))
			$model->attributes=$_GET['Sequence'];
		if (isset($_GET['Firm']))
			$model->firm_id = $_GET['Firm']['id'];
		if (isset($_GET['Site']))
			$model->site_id = $_GET['Site']['id'];
		if (isset($_GET['Sequence']['repeat']) && $_GET['Sequence']['repeat'] != '') {
			if ($_GET['Sequence']['repeat'] <= 4) {
				$model->repeat_interval = $_GET['Sequence']['repeat'];
			} elseif ($_GET['Sequence']['repeat'] >= 5) {
				$model->week_selection = $_GET['Sequence']['repeat'] - Sequence::FREQUENCY_4WEEKS;
			}
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
		$model=Sequence::model()->findByPk((int)$id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param CModel the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='sequence-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
