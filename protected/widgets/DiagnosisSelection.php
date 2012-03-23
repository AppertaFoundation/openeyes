<?php
/**
 * OpenEyes
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2012
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
 * @copyright Copyright (c) 2011-2012, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

class DiagnosisSelection extends BaseCWidget {
	public $model;
	public $selectedFirmId;
	public $field;
	public $options;
	public $class;
	public $form;
	public $value;

	public function run() {
		$this->class = get_class($this->model);

		if (empty($_POST)) {
			if (empty($this->model->event_id)) {
				// It's a new event so fetch the most recent element_diagnosis
				$diagnosis = $this->model->getNewestDiagnosis(Yii::app()->getController()->patient);

				if (!empty($diagnosis->disorder)) {
					// There is a diagnosis for this episode
					$this->value = $diagnosis->disorder->term;
				}
			} else {
				if (isset($this->model->disorder)) {
					$this->value = $this->model->disorder->term;
				}
			}
		} else {
			$this->value = $_POST[$this->class][$this->field];
		}

		parent::run();
	}
}
?>
