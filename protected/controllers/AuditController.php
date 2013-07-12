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

class AuditController extends BaseController
{
	/**
		* @var string the default layout for the views. Defaults to '//layouts/column2', meaning
		* using two-column layout. See 'protected/views/layouts/column2.php'.
		*/
	public $layout='//layouts/main';
	public $items_per_page = 100;

	public function accessRules()
	{
		return array(
			// Level 2 or above can do anything
			array('allow',
				'expression' => 'BaseController::checkUserLevel(2)',
			),
			// Deny anything else (default rule allows authenticated users)
			array('deny'),
		);
	}

	public function beforeAction($action)
	{
		$userid = Yii::app()->session['user']->id;
		if (($userid != 2103)and($userid != 122)and($userid != 613)and($userid != 1330)and($userid != 1)) return false;
		return parent::beforeAction($action);
	}

	public function actionIndex()
	{
		$actions = array();

		foreach (array('add-allergy','associate-contact','change-firm','change-status','create','delete','login-failed','login-successful','logout','print','remove-allergy','reschedule','search-error','search-results','unassociate-contact','update','view') as $field) {
			$actions[$field] = $field;
		}

		$targets = array();

		foreach (array('booking','diary','episode','episode summary','event','login','logout','patient','patient summary','search','session','user','waiting list') as $field) {
			$targets[$field] = $field;
		}

		$this->render('index',array('actions'=>$actions,'targets'=>$targets));
	}

	public function actionSearch()
	{
		if (isset($_POST['page'])) {
			$data = $this->getData($_POST['page']);
		} else {
			$data = $this->getData();
		}

		Yii::app()->clientScript->registerScriptFile(Yii::app()->createUrl('js/audit.js'));
		$this->renderPartial('_list', array('data' => $data), false, true);
		echo "<!-------------------------->";
		$this->renderPartial('_pagination', array('data' => $data), false, true);
	}

	public function criteria($count=false)
	{
		$criteria = new CDbCriteria;

		if ($count) {
			$criteria->select = 'count(*) as count';
		}

		if (@$_REQUEST['site_id']) {
			$criteria->addCondition('site_id='.$_REQUEST['site_id']);
		}

		if (@$_REQUEST['firm_id']) {
			$firm = Firm::model()->findByPk($_REQUEST['firm_id']);
			$firm_ids = array();
			foreach (Firm::model()->findAll('name=?',array($firm->name)) as $firm) {
				$firm_ids[] = $firm->id;
			}
			if (!empty($firm_ids)) {
				$criteria->addInCondition('firm_id',$firm_ids);
			}
		}

		if (@$_REQUEST['user']) {
			$user_ids = array();

			$criteria2 = new CDbCriteria;
			$criteria2->addCondition(array("active = :active"));
			$criteria2->addCondition(array("LOWER(concat_ws(' ',first_name,last_name)) = :term"));

			$params[':active'] = 1;
			$params[':term'] = strtolower($_REQUEST['user']);

			$criteria2->params = $params;

			foreach (User::model()->findAll($criteria2) as $user) {
				$user_ids[] = $user->id;
			}

			$criteria->addInCondition('user_id',$user_ids);
		}

		if (@$_REQUEST['action']) {
			$criteria->addCondition("action='".$_REQUEST['action']."'");
		}

		if (@$_REQUEST['target_type']) {
			$criteria->addCondition("target_type='".$_REQUEST['target_type']."'");
		}

		if (@$_REQUEST['event_type']) {
			$criteria->addCondition('event_type_id='.$_REQUEST['event_type']);
		}

		if (@$_REQUEST['date_from']) {
			$date_from = Helper::convertNHS2MySQL($_REQUEST['date_from']).' 00:00:00';
			$criteria->addCondition("`t`.created_date >= '$date_from'");
		}

		if (@$_REQUEST['date_to']) {
			$date_to = Helper::convertNHS2MySQL($_REQUEST['date_to']).' 23:59:59';
			$criteria->addCondition("`t`.created_date <= '$date_to'");
		}

		if (@$_REQUEST['hos_num']) {
			if ($patient = Patient::model()->find('hos_num=?',array($_REQUEST['hos_num']))) {
				$criteria->addCondition('patient_id='.$patient->id);
			} else {
				if ($patient = Patient::model()->find('hos_num=?',array(str_pad($_REQUEST['hos_num'],7,'0',STR_PAD_LEFT)))) {
					$criteria->addCondition('patient_id='.$patient->id);
				} else {
					$criteria->addCondition('patient_id=0');
				}
			}
		}

		if (@$_REQUEST['event_type_id']) {
			$criteria->addCondition('event_type.id = '.$_REQUEST['event_type_id']);
		}

		!($count) && $criteria->join = 'left join event on t.event_id = event.id left join event_type on event.event_type_id = event_type.id';

		return $criteria;
	}

	public function getData($page=1, $id=false)
	{
		$data = array();

		$data['total_items'] = Audit::model()->find($this->criteria(true))->count;

		$criteria = $this->criteria();

		$criteria->order = 't.id desc';
		$criteria->limit = $this->items_per_page;
		if ($id) {
			$criteria->addCondition('t.id > '.(integer) $id);
		} else {
			$criteria->offset = (($page-1) * $this->items_per_page);
		}

		$data['items'] = Audit::model()->findAll($criteria);
		$data['pages'] = ceil($data['total_items'] / $this->items_per_page);
		if ($data['pages'] <1) {
			$data['pages'] = 1;
		}
		if ($page > $data['pages']) {
			$page = $data['pages'];
		}
		if (!$id) {
			$data['page'] = $page;
		}

		return $data;
	}

	public function actionUpdateList()
	{
		if (!$audit = Audit::model()->findByPk(@$_GET['last_id'])) {
			throw new Exception('Log entry not found: '.@$_GET['last_id']);
		}

		$this->renderPartial('_list_update', array('data' => $this->getData(null,$audit->id)), false, true);
	}

	public function actionUsers()
	{
		$users = array();

		$criteria = new CDbCriteria;

		$criteria->addCondition(array("active = :active"));
		$criteria->addCondition(array("LOWER(concat_ws(' ',first_name,last_name)) LIKE :term"));

		$params[':active'] = 1;
		$params[':term'] = '%' . strtolower(strtr($_GET['term'], array('%' => '\%'))) . '%';

		$criteria->params = $params;
		$criteria->order = 'first_name, last_name';

		foreach (User::model()->findAll($criteria) as $user) {
			if ($contact = $user->contact) {
				if (!in_array(trim($contact->first_name.' '.$contact->last_name),$users)) {
					$users[] = trim($contact->first_name.' '.$contact->last_name);
				}
			}
		}

		echo json_encode($users);
	}
}
