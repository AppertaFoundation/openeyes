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

class SearchController extends BaseController
{
	public $layout = '//layouts/advanced_search';
	public $items_per_page = 30;

	public function accessRules()
	{
		return array(
			array('allow',
				'actions' => array('BloodSample'),
				'roles' => array('OprnSearchPedigree'),
			));
	}

	protected function beforeAction($action)
	{
		Yii::app()->assetManager->registerCssFile('css/admin.css', null, 10);
		return parent::beforeAction($action);
	}

	public function actionBloodSample()
	{

		$pages = 1;
		$page = 1;
		$results = array();


		if(@$_GET['search']) {


			$assetPath = Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('application.modules.'.$this->getModule()->name.'.assets'));
			Yii::app()->clientScript->registerScriptFile($assetPath.'/js/module.js');

			$count_command = $this->buildSearchCommand("count(et_ophinbloodsample_sample.id) as count");
			$search_command =  $this->buildSearchCommand("patient.id,patient.hos_num,contact.first_name,event.event_date,contact.maiden_name,contact.last_name,contact.title,patient.gender,patient.dob,patient.yob,ophinbloodsample_sample_type.name,et_ophinbloodsample_sample.volume,et_ophinbloodsample_sample.comments");

			$total_items = $count_command->queryScalar();

			$pagination = $this->initPagination($total_items);

			$pages = ceil($total_items / $this->items_per_page);
			$page = 1;

			if (@$_GET['page'] && $_GET['page'] >= 1 and $_GET['page'] <= $pages) {
				$page = $_GET['page'];
			}

			$dir = @$_GET['order'] == 'desc' ? 'desc' : 'asc';

			switch (@$_GET['sortby']) {
				case 'hos_num':
					$order = "hos_num $dir";
					break;
				case 'patient_name':
					$order = "last_name $dir, first_name $dir";
					break;
				case 'date_taken':
					$order = "blood_date $dir";
					break;
				case 'sample_type':
					$order = "ophinbloodsample_sample_type.name $dir";
					break;
				case 'volume':
					$order = "volume $dir";
					break;
				case 'comment':
					$order = "comments $dir";
					break;
				default:
					$order = "last_name $dir, first_name $dir";
			}

			$search_command->order($order)
				->offset(($page-1) * $this->items_per_page)
				->limit($this->items_per_page);

			$results = $search_command
				->queryAll();
		}



		$this->render('bloodSample',array(
			'patients' => $results,
			'pagination' => $pagination,
			'page' => $page,
			'pages' => $pages,
		));
	}


	private function buildSearchCommand($select)
	{
		$date_from = @$_GET['date-from'];
		$date_to = @$_GET['date-to'];
		$sample_type = @$_GET['sample-type'];
		$disorder_id = @$_GET['disorder-id'];
		$comment = @$_GET['comment'];

		$command = Yii::app()->db->createCommand()
			->select($select)
			->from("et_ophinbloodsample_sample")
			->join("event","et_ophinbloodsample_sample.event_id = event.id")
			->join("episode","event.episode_id = episode.id")
			->join("patient","episode.patient_id = patient.id")
			->join("ophinbloodsample_sample_type","et_ophinbloodsample_sample.type_id = ophinbloodsample_sample_type.id")
			->join("contact","patient.contact_id = contact.id")
			->leftJoin("secondary_diagnosis","secondary_diagnosis.patient_id = patient.id");

		if ($date_from) {
			$command->andWhere('blood_date <= :date_from', array(':date_from'=>Helper::convertNHS2MySQL($date_from)));
		}

		if ($date_to) {
			$command->andWhere('blood_date >= :date_to', array(':date_to'=>Helper::convertNHS2MySQL($date_to)));
		}

		if ($sample_type) {
			$command->andWhere('type_id = :type_id', array(':type_id'=>$sample_type));
		}

		if ($comment) {
			$command->andWhere((array('like', 'comments', '%'.$comment.'%')));
		}
		if ($disorder_id) {
			$command->andWhere('secondary_diagnosis.disorder_id = :disorder_id', array(':disorder_id'=>$disorder_id));
		}

		return $command;
	}


	public function getUri($elements)
	{
		$uri = preg_replace('/\?.*$/','',$_SERVER['REQUEST_URI']);

		$request = $_REQUEST;

		if (isset($elements['sortby']) && $elements['sortby'] == @$request['sortby']) {
			$request['order'] = (@$request['order'] == 'desc') ? 'asc' : 'desc';
		} elseif (isset($request['sortby']) && isset($elements['sortby']) && $request['sortby'] != $elements['sortby']) {
			$request['order'] = 'asc';
		}

		$first = true;
		foreach (array_merge($request,$elements) as $key => $value) {
			$uri .= $first ? '?' : '&';
			$first = false;
			$uri .= "$key=$value";
		}

		return $uri;
	}

	private function initPagination($model, $criteria = null)
	{
		$pagination = new CPagination($itemsCount);
		$pagination->pageSize = $this->items_per_page;
		$pagination->applyLimit($criteria);
		return $pagination;
	}

	public function actionDnaExtractions()
	{

	}
}

