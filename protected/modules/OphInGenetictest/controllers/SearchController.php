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
				'actions' => array('GeneticTests'),
				'roles' => array('OprnSearchPedigree'),
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

	public function actionGeneticTests()
	{
		$assetPath = Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('application.modules.'.$this->getModule()->name.'.assets'));
		Yii::app()->clientScript->registerScriptFile($assetPath.'/js/module.js');
		Yii::app()->assetManager->registerCssFile('css/admin.css', null, 10);

		$where = "e.deleted = :zero and ep.deleted = :zero";
		$whereParams = array(':zero' => 0);

		if (@$_GET['gene-id']) {
			$where .= " and gene_id = :gene_id";
			$whereParams[':gene_id'] = $_GET['gene-id'];
		}

		if (@$_GET['method-id']) {
			$where .= " and method_id = :method_id";
			$whereParams[':method_id'] = $_GET['method-id'];
		}

		if (strlen(@$_GET['homo']) >0) {
			$where .= " and homo = :homo";
			$whereParams[':homo'] = $_GET['homo'];
		}

		if (@$_GET['effect-id']) {
			$where .= " and effect_id = :effect_id";
			$whereParams[':effect_id'] = $_GET['effect-id'];
		}

		if (@$_GET['date-from'] && strtotime($_GET['date-from'])) {
			$where .= " and result_date >= :date_from";
			$whereParams[':date_from'] = $_GET['date-from'];
		}

		if (@$_GET['date-to'] && strtotime($_GET['date-to'])) {
			$where .= " and result_date <= :date_to";
			$whereParams[':date_to'] = $_GET['date-to'];
		}

		if (strlen(@$_GET['query']) >0) {
			$where .= " and ( comments like :query or exon like :query or prime_rf like :query or prime_rr like :query or base_change like :query or amino_acid_change like :query or assay like :query or result like :query)";
			$whereParams[':query'] = '%'.$_GET['query'].'%';
		}

		$total_items = Yii::app()->db->createCommand()
			->select("count(gt.id) as count")
			->from("et_ophingenetictest_test gt")
			->join("event e","gt.event_id = e.id")
			->join("episode ep","e.episode_id = ep.id")
			->join("patient p","ep.patient_id = p.id")
			->where($where, $whereParams)
			->queryScalar();

		$pages = ceil($total_items / $this->items_per_page);
		$page = 1;

		if (@$_GET['page'] && $_GET['page'] >= 1 and $_GET['page'] <= $pages) {
			$page = $_GET['page'];
		}

		$dir = @$_GET['order'] == 'desc' ? 'desc' : 'asc';

		switch (@$_GET['sortby']) {
			case 'date':
				$order = "result_date $dir";
				break;
			case 'hos_num':
				$order = "hos_num $dir";
				break;
			case 'gene':
				$order = "g.name $dir";
				break;
			case 'method':
				$order = "m.name $dir";
				break;
			case 'homo':
				$order = "homo $dir";
				break;
			case 'base_change':
				$order = "base_change $dir";
				break;
			case 'amino_acid_change':
				$order = "amino_acid_change $dir";
				break;
			case 'result':
				$order = "result $dir";
				break;
			case 'patient_name':
			default:
				$order = "last_name $dir, first_name $dir";
				break;
		}

		$test_ids = array();

		foreach (Yii::app()->db->createCommand()
			->select("gt.id")
			->from("et_ophingenetictest_test gt")
			->join("event e","gt.event_id = e.id")
			->join("episode ep","e.episode_id = ep.id")
			->join("patient p","ep.patient_id = p.id")
			->join("contact c","p.contact_id = c.id")
			->leftJoin("ophingenetictest_test_method m","gt.method_id = m.id")
			->join("pedigree_gene g","gt.gene_id = g.id")
			->leftJoin("ophingenetictest_test_effect ef","gt.effect_id = ef.id")
			->where($where, $whereParams)
			->order($order)
			->offset(($page-1) * $this->items_per_page)
			->limit($this->items_per_page)
			->queryAll() as $row) {
			$test_ids[] = $row['id'];
		}

		$test_map = array();

		$criteria = new CDbCriteria;
		$criteria->addInCondition('t.id',$test_ids);

		foreach (Element_OphInGenetictest_Test::model()
			->with(array(
				'event' => array(
					'with' => array(
						'episode' => array(
							'with' => array(
								'patient' => array(
									'with' => array('contact'),
								),
							),
						),
					),
				),
			))
			->findAll($criteria) as $test) {
			$test_map[$test->id] = $test;
		}

		$tests = array();

		foreach ($test_ids as $test_id) {
			$tests[] = $test_map[$test_id];
		}

		$this->render('geneticTests',array(
			'genetic_tests' => $tests,
			'page' => $page,
			'pages' => $pages,
		));
	}
}
