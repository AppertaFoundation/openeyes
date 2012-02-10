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

class AdminAuditTrailController extends Controller {
	public $defaultAction = "admin";
	public $layout='column1';
	
	protected function beforeAction($action) {
		// Sample code to be used when RBAC is fully implemented.
		if (!Yii::app()->user->checkAccess('admin')) {
			throw new CHttpException(403, 'You are not authorised to perform this action.');
		}

		return parent::beforeAction($action);
	}
	
	public function actionAdmin() {
		$model = new AuditTrail('search');
		$model->unsetAttributes();
		if(isset($_GET['AuditTrail'])) {
			$model->attributes=$_GET['AuditTrail'];
		}
		$this->render('admin',array(
				'model'=>$model,
		));
	}
}
