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

class DiagnosisSelection extends BaseFieldWidget
{
	public $selectedFirmId;
	public $dropdownOptions;
	public $class;
	public $form;
	public $label;
	public $restrict;
	//specialty code to restrict diagnosis list by
	public $code;
	public $default = true;
	public $layout = false;
	public $callback = false;
	public $filterCallback;
	public $selected = array();
	public $loader = false;
	public $nowrapper = false;
	public $options = array();
	public $secondary_to = array();
	// text in diagnosis search box
	public $placeholder = 'or type the first few characters of a diagnosis';

	public function run()
	{
		if ($this->element) {
			$this->class = CHTML::modelName($this->element);
		} else {
			$this->class = get_class($this);
		}

		if (empty($_POST) || !array_key_exists($this->class, $_POST)) {
			if (empty($this->element->event_id)) {
				if ($this->default) {
					// It's a new event so fetch the most recent element_diagnosis
					$firmId = Yii::app()->session['selected_firm_id'];
					$firm = Firm::model()->findByPk($firmId);
					if (isset(Yii::app()->getController()->patient)) {
						$patientId = Yii::app()->getController()->patient->id;
						$episode = Episode::getCurrentEpisodeByFirm($patientId, $firm, true);
						if ($episode && $disorder = $episode->diagnosis) {
							// There is a diagnosis for this episode
							$this->value = $disorder->id;
							$this->label = $disorder->term;
						}
					}
				}
			} else {
				if (isset($this->element->disorder)) {
					$this->value = $this->element->disorder->id;
					$this->label = $this->element->disorder->term;
				}
			}
		} elseif (array_key_exists($this->field, $_POST[$this->class])) {
			if (preg_match('/[^\d]/', $_POST[$this->class][$this->field])) {
				if ($disorder = Disorder::model()->find('term=? and specialty_id is not null',array($_POST[$this->class][$this->field]))) {
					$this->value = $disorder->id;
					$this->label = $disorder->term;
				}
			} else {
				$this->value = $_POST[$this->class][$this->field];
				if ($disorder = Disorder::model()->findByPk($this->value)) {
					$this->label = $disorder->term;
				}
			}
		}
		parent::run();
	}

	public function render($view, $data=null, $return=false)
	{
		if ($this->layout) {
			$view .= '_'.$this->layout;
		}
		if ($this->restrict == 'systemic') {
			$this->code = $this->restrict;
		}

		parent::render($view, $data, $return);
	}
}
