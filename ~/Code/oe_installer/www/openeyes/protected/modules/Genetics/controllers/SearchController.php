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

	public function actionGeneticPatients()
	{
		$assetPath = Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('application.modules.'.$this->getModule()->name.'.assets'), false, -1, YII_DEBUG);
		Yii::app()->clientScript->registerScriptFile($assetPath.'/js/module.js');

		$pagination = $this->initPagination(Pedigree::model());

		if (@$_GET['disorder-id']) {
			Yii::app()->event->dispatch('start_batch_mode');

			$total_items = Yii::app()->db->createCommand()
				->select("count(p.id) as count")
				->from("patient p")
				->join("patient_pedigree pp","pp.patient_id = p.id")
				->leftJoin("secondary_diagnosis sd","sd.patient_id = p.id")
				->leftJoin("episode ep","ep.patient_id = p.id")
				->where("sd.disorder_id = :disorder_id or ep.disorder_id = :disorder_id",array(
					":disorder_id" => $_GET['disorder-id'],
				))
				->queryScalar();

			$pages = ceil($total_items / $this->items_per_page);
			$page = 1;

			if (@$_GET['page'] && $_GET['page'] >= 1 and $_GET['page'] <= $pages) {
				$page = $_GET['page'];
			}

			$dir = @$_GET['order'] == 'desc' ? 'desc' : 'asc';

			switch (@$_GET['sortby']) {
				case 'hos_num':
				case 'title':
				case 'gender':
					$order = @$_GET['sortby'].' '.$dir;
					break;
				case 'patient_name':
					$order = "last_name $dir, first_name $dir";
					break;
				case 'gene':
					$order = "gene.name $dir";
					break;
				case 'diagnosis':
					$order = "disorder.term $dir";
					break;
				default:
					$order = "last_name $dir, first_name $dir";
			}

			$pp_ids = array();
			
			foreach (Yii::app()->db->createCommand()
				->select("pp.id")
				->from("patient p")
				->join("contact c","p.contact_id = c.id")
				->join("patient_pedigree pp","pp.patient_id = p.id")
				->leftJoin("secondary_diagnosis sd","sd.patient_id = p.id")
				->leftJoin("episode ep","ep.patient_id = p.id")
				->where("sd.disorder_id = :disorder_id or ep.disorder_id = :disorder_id",array(
					":disorder_id" => $_GET['disorder-id'],
				))
				->order($order)
				->offset(($page-1) * $this->items_per_page)
				->limit($this->items_per_page)
				->queryAll() as $row) {
				$pp_ids[] = $row['id'];
			}

			$pp_map = array();

			$criteria = new CDbCriteria;
			$criteria->addInCondition('t.id',$pp_ids);

			foreach (PatientPedigree::model()
				->with(array(
					'patient',
					'pedigree' => array(
						'gene',
						'disorder',
					),
				))
				->findAll($criteria) as $pp) {
				$pp_map[$pp->id] = $pp;
			}

			$patient_pedigrees = array();

			foreach ($pp_ids as $pp_id) {
				$patient_pedigrees[] = $pp_map[$pp_id];
			}

		} else {
			$total_items = 0;
			$pages = 1;
			$page = 1;

			$patient_pedigrees = array();
		}

		$this->render('index',array(
			'patient_pedigrees' => $patient_pedigrees,
			'pagination' => $pagination,
			'page' => $page,
			'pages' => $pages,
		));
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
		$criteria = is_null($criteria) ? new CDbCriteria() : $criteria;
		$itemsCount = $model->count($criteria);
		$pagination = new CPagination($itemsCount);
		$pagination->pageSize = $this->items_per_page;
		$pagination->applyLimit($criteria);
		return $pagination;
	}
}
