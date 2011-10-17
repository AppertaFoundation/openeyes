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

class WaitingListController extends BaseController
{
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	public $layout='//layouts/main';

	public function filters()
	{
		return array('accessControl');
	}

	public function accessRules()
	{
		return array(
			array('allow',
				'users'=>array('@')
			),
			// non-logged in can't view anything
			array('deny',
				'users'=>array('?')
			),
		);
	}

	/**
	 * Lists all models.
	 */
	public function actionIndex()
	{
		$this->render('index');
	}

	public function actionSearch()
	{
		if (empty($_POST)) {
			$operations = array();
		} else {
			$serviceId = !empty($_POST['service-id']) ? $_POST['service-id'] : null;
			$firmId = !empty($_POST['firm-id']) ? $_POST['firm-id'] : null;
			$status = !empty($_POST['status']) ? $_POST['status'] : null;

			$service = new WaitingListService;
			$operations = $service->getWaitingList($firmId, $serviceId, $status);
		}

		$this->renderPartial('_list', array('operations' => $operations), false, true);
	}

	/**
	 * Generates a firm list based on a service id provided via POST
	 * echoes form option tags for display
	 */
	public function actionFilterFirms()
	{
		echo CHtml::tag('option', array('value'=>''),
			CHtml::encode('All firms'), true);
		if (!empty($_POST['service_id'])) {
			$firms = $this->getFilteredFirms($_POST['service_id']);

			foreach ($firms as $id => $name) {
				echo CHtml::tag('option', array('value'=>$id),
					CHtml::encode($name), true);
			}
		}
	}

	public function actionUpdateSessionComments()
	{
		if (Yii::app()->getRequest()->getIsAjaxRequest()) {
			if (!empty($_POST['sessionId']) && !empty($_POST['comments'])) {
				$session = Session::model()->findByPk($_POST['sessionId']);

				if (!empty($session)) {
					$session->comments = $_POST['comments'];
					$session->save();
				}
			}
			return true;
		}
	}

	/**
	 * Helper method to fetch firms by service ID
	 *
	 * @param integer $serviceId
	 *
	 * @return array
	 */
	protected function getFilteredFirms($serviceId)
	{
		$data = Yii::app()->db->createCommand()
			->select('f.id, f.name')
			->from('firm f')
			->join('service_specialty_assignment ssa', 'f.service_specialty_assignment_id = ssa.id')
			->join('service s', 'ssa.service_id = s.id')
			->where('ssa.service_id=:id',
				array(':id'=>$serviceId))
			->queryAll();

		$firms = array();
		foreach ($data as $values) {
			$firms[$values['id']] = $values['name'];
		}

		return $firms;
	}
}
