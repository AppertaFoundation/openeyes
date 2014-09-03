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
		$assetPath = Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('application.modules.'.$this->getModule()->name.'.assets'));
		Yii::app()->clientScript->registerScriptFile($assetPath.'/js/module.js');

		$pagination = $this->initPagination(Pedigree::model());

		$count_command = $this->buildSearchCommand("count(patient.id) as count");
		$search_command =  $this->buildSearchCommand("patient.id,patient.hos_num,contact.first_name,contact.maiden_name,contact.last_name,contact.title,patient.gender,patient.dob,pedigree_id,pedigree_status.name,patient.yob");

		$total_items = $count_command->queryScalar();

		//	->where("sd.disorder_id = :disorder_id or ep.disorder_id = :disorder_id",array(
//					":disorder_id" => $_GET['disorder-id'],
//				))
//				->queryScalar();

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

		//				->where("sd.disorder_id = :disorder_id or ep.disorder_id = :disorder_id",array(
//				":disorder_id" => $_GET['disorder-id'],
//			))

		$search_command->order($order)
			->offset(($page-1) * $this->items_per_page)
			->limit($this->items_per_page);


		$patients = $search_command->queryAll();

		/*
				} else {
					$total_items = 0;
					$pages = 1;
					$page = 1;

					$patient_pedigrees = array();
				}*/

		$this->render('geneticPatients',array(
			'patients' => $patients,
			'pagination' => $pagination,
			'page' => $page,
			'pages' => $pages,
		));
	}


	private function buildSearchCommand($select)
	{
		$subject_id = @$_GET['subject-id'];
		$meh_number = @$_GET['meh-number'];
		$pedigree_id = @$_GET['pedigree-id'];
		$first_name = @$_GET['first-name'];
		$last_name = @$_GET['last-name'];
		$dob = @$_GET['dob'];
		$disorder_id = @$_GET['disorder_id'];

		$command = Yii::app()->db->createCommand()
			->select($select)
			->from("patient")
			->join("patient_pedigree","patient_pedigree.patient_id = patient.id")
			->join("pedigree","patient_pedigree.pedigree_id = pedigree.id")
			->join("pedigree_status","patient_pedigree.status_id = pedigree_status.id")
			->join("contact","patient.contact_id = contact.id")
			->leftJoin("secondary_diagnosis","secondary_diagnosis.patient_id = patient.id")
			->leftJoin("episode","episode.patient_id = patient.id");

		if ($meh_number) {
			$command->andWhere('patient.hos_num=:meh_number', array(':meh_number'=>$meh_number));
		}

		if ($first_name) {
			$command->andWhere('contact.first_name=:first_name', array(':first_name'=>$first_name));
		}

		if ($last_name) {
			$command->andWhere('contact.last_name=:last_name', array(':first_name'=>$last_name));
		}

		if ($dob) {
			$command->andWhere('patient.dob=:dob', array(':dob'=>$dob));
		}

		if ($pedigree_id) {
			$command->andWhere('pedigree.id=:pedigree_id', array(':pedigree_id'=>$pedigree_id));
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
