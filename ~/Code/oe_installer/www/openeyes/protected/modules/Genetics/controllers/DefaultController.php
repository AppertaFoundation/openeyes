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

class DefaultController extends BaseEventTypeController
{
	public $items_per_page = 100;
	public $page = 1;
	public $total_items = 0;
	public $pages = 1;
	public $renderPatientPanel = false;
	public $layout = 'genetics';

	public function actionIndex()
	{
		$this->redirect(Yii::app()->createUrl('/Genetics/default/pedigrees'));
	}

	public function actionPedigrees()
	{
		$errors = array();

		if (isset($_POST['add'])) {
			$this->redirect(Yii::app()->createUrl('/Genetics/default/addPedigree'));
		}

		if (isset($_POST['delete'])) {
			$criteria = new CDbCriteria;
			$criteria->addInCondition('id',$_POST['pedigrees']);

			foreach (Pedigree::model()->findAll($criteria) as $pedigree) {
				try {
					$pedigree->delete();
				} catch (Exception $e) {
					if (!isset($errors['Error'])) {
						$errors['Error'] = array();
					}
					$errors['Error'][] = "unable to delete pedigree $pedigree->id: in use";
				}
			}
		}

		$pagination = $this->initPagination(Pedigree::model());

		$criteria = new CDbCriteria;

		if (@$_GET['family-id']) {
			$criteria->addCondition('t.id = :id');
			$criteria->params[':id'] = $_GET['family-id'];
		}

		if (@$_GET['gene-id']) {
			$criteria->addCondition('gene_id = :gene_id');
			$criteria->params[':gene_id'] = $_GET['gene-id'];
		}

		if (strlen(@$_GET['consanguineous']) >0) {
			$criteria->addCondition('consanguinity = :consanguineous');
			$criteria->params[':consanguineous'] = $_GET['consanguineous'];
		}

		if (@$_GET['disorder-id']) {
			$criteria->addCondition('disorder_id = :disorder_id');
			$criteria->params[':disorder_id'] = $_GET['disorder-id'];
		}

		$this->render('pedigrees',array(
			'pedigrees' => $this->getItems(array(
				'model' => 'Pedigree',
				'with' => array(
					'inheritance',
					'gene',
					'disorder',
				),
				'page' => (Integer)@$_GET['page'],
				'criteria' => $criteria,
			)),
			'pagination' => $pagination,
			'errors' => $errors,
		));
	}

	public function getItems($params)
	{
		if (isset($params['criteria'])) {
			$criteria = $params['criteria'];
		} else {
			$criteria = new CDbCriteria;
		}

		$model = $params['model'];
		$with = isset($params['with']) ? $params['with'] : array();

		$this->total_items = $model::model()->count($criteria);
		$this->pages = ceil($this->total_items / $this->items_per_page);
		$this->page = 1;

		if (isset($params['page'])) {
			if ($params['page'] >= 1 and $params['page'] <= $this->pages) {
				$this->page = $params['page'];
			}
		}

		$criteria->order = 't.id asc';
		$criteria->offset = ($this->page-1) * $this->items_per_page;
		$criteria->limit = $this->items_per_page;

		return $model::model()->with($with)->findAll($criteria);
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

	public function actionAddPedigree()
	{
		$pedigree = new Pedigree;

		$errors = array();

		if (!empty($_POST)) {
			if (isset($_POST['cancel'])) {
				return $this->redirect(array('/Genetics/default/index'));
			}

			$pedigree->attributes = $_POST['Pedigree'];

			if (!$pedigree->save()) {
				$errors = $pedigree->getErrors();
			} else {
				return $this->redirect(array('/Genetics/default/index'));
			}
		}

		$this->render('edit_pedigree',array(
			'pedigree' => $pedigree,
			'errors' => $errors,
		));
	}

	public function actionEditPedigree($id)
	{
		if (!$pedigree = Pedigree::model()->findByPk($id)) {
			throw new Exception("Pedigree not found: $id");
		}

		$errors = array();

		if (!empty($_POST)) {
			if (isset($_POST['cancel'])) {
				return $this->redirect(array('/Genetics/default/index'));
			}

			$pedigree->attributes = $_POST['Pedigree'];

			if (!$pedigree->save()) {
				$errors = $pedigree->getErrors();
			} else {
				return $this->redirect(array('/Genetics/default/index'));
			}
		}

		$this->render('edit_pedigree',array(
			'pedigree' => $pedigree,
			'errors' => $errors,
		));
	}

	public function actionInheritance()
	{
		$errors = array();

		if (isset($_POST['add'])) {
			$this->redirect(Yii::app()->createUrl('/Genetics/default/addInheritance'));
		}

		if (isset($_POST['delete'])) {
			$criteria = new CDbCriteria;
			$criteria->addInCondition('id',$_POST['inheritance']);

			foreach (PedigreeInheritance::model()->findAll($criteria) as $inheritance) {
				try {
					$inheritance->delete();
				} catch (Exception $e) {
					if (!isset($errors['Error'])) {
						$errors['Error'] = array();
					}
					$errors['Error'][] = "unable to delete inheritance $inheritance->id: in use";
				}
			}
		}

		$pagination = $this->initPagination(PedigreeInheritance::model());

		$this->render('inheritance',array(
			'inheritance' => $this->getItems(array(
				'model' => 'PedigreeInheritance',
				'page' => (Integer)@$_GET['page'],
			)),
			'pagination' => $pagination,
			'errors' => $errors,
		));
	}

	public function getInheritance()
	{
		$this->total_items = PedigreeInheritance::model()->count(array('order' => 't.id asc'));

		$this->pages = ceil($this->total_items / $this->items_per_page);

		return PedigreeInheritance::model()->findAll(array(
			'order' => 't.id asc',
			'limit' => $this->items_per_page,
		));
	}

	public function actionAddInheritance()
	{
		$inheritance = new PedigreeInheritance;

		$errors = array();

		if (!empty($_POST)) {
			if (isset($_POST['cancel'])) {
				return $this->redirect(array('/Genetics/default/inheritance'));
			}

			$inheritance->attributes = $_POST['PedigreeInheritance'];

			if (!$inheritance->save()) {
				$errors = $inheritance->getErrors();
			} else {
				return $this->redirect(array('/Genetics/default/inheritance'));
			}
		}

		$this->render('edit_inheritance',array(
			'inheritance' => $inheritance,
			'errors' => $errors,
		));
	}

	public function actionEditInheritance($id)
	{
		if (!$inheritance = PedigreeInheritance::model()->findByPk($id)) {
			throw new Exception("PedigreeInheritance not found: $id");
		}

		$errors = array();

		if (!empty($_POST)) {
			if (isset($_POST['cancel'])) {
				return $this->redirect(array('/Genetics/default/inheritance'));
			}

			$inheritance->attributes = $_POST['PedigreeInheritance'];

			if (!$inheritance->save()) {
				$errors = $inheritance->getErrors();
			} else {
				return $this->redirect(array('/Genetics/default/inheritance'));
			}
		}

		$this->render('edit_inheritance',array(
			'inheritance' => $inheritance,
			'errors' => $errors,
		));
	}

	public function actionGenes()
	{
		$errors = array();

		if (isset($_POST['add'])) {
			$this->redirect(Yii::app()->createUrl('/Genetics/default/addGene'));
		}

		if (isset($_POST['delete'])) {
			$criteria = new CDbCriteria;
			$criteria->addInCondition('id',$_POST['genes']);

			foreach (PedigreeGene::model()->findAll($criteria) as $gene) {
				try {
					$gene->delete();
				} catch (Exception $e) {
					if (!isset($errors['Error'])) {
						$errors['Error'] = array();
					}
					$errors['Error'][] = "unable to delete gene $gene->id: in use";
				}
			}
		}

		$pagination = $this->initPagination(PedigreeGene::model());

		$this->render('genes',array(
			'genes' => $this->getItems(array(
				'model' => 'PedigreeGene',
				'page' => (Integer)@$_GET['page'],
			)),
			'pagination' => $pagination,
			'errors' => $errors,
		));
	}

	public function getGenes()
	{
		$this->total_items = PedigreeGene::model()->count(array('order' => 't.asc'));

		$this->pages = ceil($this->total_items / $this->items_per_page);

		return PedigreeGene::model()->findAll(array(
			'order' => 't.id asc',
			'limit' => $this->items_per_page,
		));
	}

	public function getUriAppend()
	{
		$return = '';
		foreach (array('date_from', 'date_to', 'include_bookings' => 0, 'include_reschedules' => 0, 'include_cancellations' => 0) as $token) {
			if (isset($_GET[$token])) {
				$return .= '&'.$token.'='.$_GET[$token];
			}
		}
		return $return;
	}

	public function actionAddGene()
	{
		$gene = new PedigreeGene;

		$errors = array();

		if (!empty($_POST)) {
			if (isset($_POST['cancel'])) {
				return $this->redirect(array('/Genetics/default/genes'));
			}

			$gene->attributes = $_POST['PedigreeGene'];

			if (!$gene->save()) {
				$errors = $gene->getErrors();
			} else {
				return $this->redirect(array('/Genetics/default/genes'));
			}
		}

		$this->render('edit_gene',array(
			'gene' => $gene,
			'errors' => $errors,
		));
	}

	public function actionEditGene($id)
	{
		if (!$gene = PedigreeGene::model()->findByPk($id)) {
			throw new Exception("PedigreeGene not found: $id");
		}

		$errors = array();

		if (!empty($_POST)) {
			if (isset($_POST['cancel'])) {
				return $this->redirect(array('/Genetics/default/genes'));
			}

			$gene->attributes = $_POST['Pedigree'];

			if (!$gene->save()) {
				$errors = $gene->getErrors();
			} else {
				return $this->redirect(array('/Genetics/default/genes'));
			}
		}

		$this->render('edit_gene',array(
			'gene' => $gene,
			'errors' => $errors,
		));
	}
}
