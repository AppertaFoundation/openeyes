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

class EventAction {
	
	public $name;
	public $type;
	public $htmlOptions;
	public $label;
	public $href;
	
	public static function button($label, $name, $htmlOptions = null) {
		$action = new self($label, 'button');
		$action->htmlOptions = $htmlOptions;
		$action->htmlOptions['name'] = $name;
		$action->htmlOptions['type'] = 'submit';
		$action->htmlOptions['class'] = (isset($action->htmlOptions['class'])) ? $action->htmlOptions['class'] . ' venti classy' : 'blue venti classy';
		if(!isset($htmlOptions['id'])) {
			$htmlOptions['id'] = 'et_'.strtolower($name);
		}
		return $action;
	}
	
	public static function link($label, $href = '#', $htmlOptions = null) {
		$action = new self($label, 'link');
		$action->htmlOptions = $htmlOptions;
		$action->htmlOptions['class'] = (isset($action->htmlOptions['class'])) ? $action->htmlOptions['class'] . ' venti classy' : 'blue venti classy';
		$action->href = $href;
		return $action;
	}
	
	public function __construct($label, $type) {
		$this->label = $label;
		$this->type = $type;
	}
	
	public function toHtml() {
		$label = '<span class="btn">'.CHtml::encode($this->label).'</span>';
		if($this->type == 'button') {
			return CHtml::htmlButton($label, $this->htmlOptions);
		} else if($this->type == 'link') {
			return CHtml::link($label, $this->href, $this->htmlOptions);
		}
	}
	
}
