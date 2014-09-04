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
				'actions' => array('GeneticPatients'),
				'roles' => array('OprnSearchPedigree'),
			));
	}

	protected function beforeAction($action)
	{
		Yii::app()->assetManager->registerCssFile('css/admin.css', null, 10);
		return parent::beforeAction($action);
	}


	public function actionGeneticPatients()
	{

		$pages = 1;
		$page = 1;
		$results = array();

		$assetPath = Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('application.modules.'.$this->getModule()->name.'.assets'));
		Yii::app()->clientScript->registerScriptFile($assetPath.'/js/module.js');

		$pagination = $this->initPagination(Pedigree::model());

		if(@$_GET['search']) {

			$count_command = $this->buildSearchCommand("count(patient.id) as count");
			$search_command =  $this->buildSearchCommand("patient.id,patient.hos_num,contact.first_name,contact.maiden_name,contact.last_name,contact.title,patient.gender,patient.dob,pedigree_id,pedigree_status.name,patient.yob,genetics_patient.comments");

			$total_items = $count_command->queryScalar();

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
				case 'title':
					$order = "title $dir";
					break;
				case 'gender':
					$order = "gender $dir";
					break;
				case 'patient_name':
					$order = "last_name $dir, first_name $dir";
					break;
				case 'dob':
					$order = "patient.dob $dir";
					break;
				case 'yob':
					$order = "patient.yob $dir";
					break;
				case 'status':
					$order = "pedigree_status.name $dir";
					break;
				case 'pedigree_id':
					$order = "pedigree.id $dir";
					break;
				default:
					$order = "last_name $dir, first_name $dir";
			}

			$search_command->order($order)
				->offset(($page-1) * $this->items_per_page)
				->limit($this->items_per_page);

			$results = $search_command->queryAll();

		}

		$this->render('geneticPatients',array(
			'results' => $results,
			'pagination' => $pagination,
			'page' => $page,
			'pages' => $pages,
		));
	}


	private function buildSearchCommand($select)
	{
		$pedigree_id = @$_GET['pedigree-id'];
		$first_name = @$_GET['first-name'];
		$last_name = @$_GET['last-name'];
		$dob = @$_GET['dob'];
		$disorder_id = @$_GET['disorder-id'];
		$comments = @$_GET['comments'];
		$part_first_name = @$_GET['part-first-name'];
		$part_last_name = @$_GET['part-last-name'];

		$command = Yii::app()->db->createCommand()
			->select($select)
			->from("patient")
			->join("patient_pedigree","patient_pedigree.patient_id = patient.id")
			->join("pedigree","patient_pedigree.pedigree_id = pedigree.id")
			->join("pedigree_status","patient_pedigree.status_id = pedigree_status.id")
			->join("contact","patient.contact_id = contact.id")
			->leftJoin("secondary_diagnosis","secondary_diagnosis.patient_id = patient.id")
			->leftJoin("episode","episode.patient_id = patient.id")
			->leftJoin("genetics_patient","genetics_patient.patient_id = patient.id");


		if ($first_name) {
			if($part_first_name=='true'){
				$command->andWhere((array('like', 'contact.first_name', '%'.$first_name.'%')));
			}else {
				$command->andWhere('contact.first_name=:first_name', array(':first_name'=>$first_name));
			}
		}

		if ($last_name) {
			if($part_last_name=='true'){
				$command->andWhere((array('like', 'contact.last_name', '%'.$last_name.'%')));
			}else {
				$command->andWhere('contact.last_name=:last_name', array(':last_name'=>$last_name));
			}
		}

		if ($dob) {
			if(strlen($dob) >3) {
				$command->andWhere('patient.dob=:dob', array(':dob'=>$dob));
			} else {
				$age = $dob; //dob is actually an age
				//calculate possible dob range or yob for entered age
				$possible_yob_1 = date('Y', strtotime($age . ' years ago'));
				$possible_yob_2 = $possible_yob_1 - 1;
				$start_dob = date('Y-m-d', strtotime($age+1 . ' years ago'));
				$end_dob = date('Y-m-d', strtotime($age . ' years ago'));
				$command->andWhere('(patient.yob=:yob_1 or patient.yob=:yob_2 and patient.dob is null) or (patient.dob>:start_dob and patient.dob < :end_dob)', array(':yob_1'=>$possible_yob_1,':yob_2'=>$possible_yob_2, ":start_dob"=>$start_dob, ":end_dob"=>$end_dob));
			}
		}

		if ($pedigree_id) {
			$command->andWhere('pedigree.id=:pedigree_id', array(':pedigree_id'=>$pedigree_id));
		}

		if ($disorder_id) {
			$command->andWhere('secondary_diagnosis.disorder_id=:disorder_id', array(':disorder_id'=>$disorder_id));
		}

		if ($comments) {
			$command->andWhere((array('like', 'genetics_patient.comments', '%'.$comments.'%')));
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
			if(!is_array($value)) {
				$uri .= $first ? '?' : '&';
				$first = false;
				$uri .= "$key=$value";
			}
		}

		return $uri;
	}

	private function initPagination($model, $criteria = null)
	{
		$criteria = is_null($criteria) ? new CDbCriteria() : $criteria;
		$itemsCount = $model->count($criteria);
		$pagination = new CPagination($itemsCount);
		$pagination->pageSize = $this->items_per_page;
		$pagination->applyLimit($criteria);
		return $pagination;
	}

}
