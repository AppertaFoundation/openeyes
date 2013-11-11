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
		$this->render('pedigrees',array(
			'pedigrees' => $this->pedigrees,
			'pagination' => '',
		));
	}

	public function getPedigrees()
	{
		$this->total_items = Pedigree::model()->with(array(
				'inheritance',
				'gene',
				'disorder',
			))
			->count(array(
				'order' => 't.id asc',
			));

		$this->pages = ceil($this->total_items / $this->items_per_page);

		return Pedigree::model()->with(array(
				'inheritance',
				'gene',
				'disorder',
			))
			->findAll(array(
				'order' => 't.id asc',
				'limit' => $this->items_per_page,
			));
	}

	public function actionInheritance()
	{
		$this->render('inheritance',array(
			'inheritance' => $this->inheritance,
			'pagination' => '',
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

	public function actionGenes()
	{
		$this->render('genes',array(
			'genes' => $this->genes,
			'pagination' => '',
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
}
