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
			$specialtyId = !empty($_POST['specialty-id']) ? $_POST['specialty-id'] : null;
			$firmId = !empty($_POST['firm-id']) ? $_POST['firm-id'] : null;
			$status = !empty($_POST['status']) ? $_POST['status'] : null;
			$hos_num = !empty($_POST['hos_num']) && ctype_digit($_POST['hos_num']) ? $_POST['hos_num'] : false;

			$service = new WaitingListService;
			$operations = $service->getWaitingList($firmId, $specialtyId, $status, $hos_num);
		}

		$this->renderPartial('_list', array('operations' => $operations), false, true);
	}

	/**
	 * Generates a firm list based on a specialty id provided via POST
	 * echoes form option tags for display
	 */
	public function actionFilterFirms()
	{
		echo CHtml::tag('option', array('value'=>''),
			CHtml::encode('All firms'), true);
		if (!empty($_POST['specialty_id'])) {
			$firms = $this->getFilteredFirms($_POST['specialty_id']);

			foreach ($firms as $id => $name) {
				echo CHtml::tag('option', array('value'=>$id),
					CHtml::encode($name), true);
			}
		}
	}

	/**
	 * Helper method to fetch firms by specialty ID
	 *
	 * @param integer $specialtyId
	 *
	 * @return array
	 */
	protected function getFilteredFirms($specialtyId)
	{
		$data = Yii::app()->db->createCommand()
			->select('f.id, f.name')
			->from('firm f')
			->join('service_specialty_assignment ssa', 'f.service_specialty_assignment_id = ssa.id')
			->join('specialty s', 'ssa.specialty_id = s.id')
			->where('ssa.specialty_id=:id',
				array(':id'=>$specialtyId))
			->queryAll();

		$firms = array();
		foreach ($data as $values) {
			$firms[$values['id']] = $values['name'];
		}

		return $firms;
	}
}
