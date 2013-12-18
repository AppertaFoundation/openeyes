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

class FormLayout extends CActiveForm
{
	// The amount of columns for the labels and fields. These 'global' values should
	// be merged into the widget options.
	public $layoutColumns = array(
		'label' => 2,
		'field' => 10
	);

	/**
	 * Generates a CSS class that is used for the layout columns.
	 * @param  mixed $key The column key ('label', or 'field') or column value (integer).
	 * @return string      The CSS class.
	 */
	public function columns($key='label', $end=false) {

		if (is_int($key)) {
			$className = 'large-'.(string)$key.' column';
		} else {
			$className = 'large-'.$this->layoutColumns[$key].' column';
		}

		if ($key === 'field') {
			$end = true;
		}

		if ($end) {
			$className .= ' end';
		}

		return $className;
	}

	/**
	 * We override this method so we can pass through the layoutColumns from the 'form'
	 * into the widget.
	 */
	public function widget($className,$properties=array(),$captureOutput=false){

		// We don't want to override the default layoutColumns with an empty array.
		if (empty($properties['layoutColumns'])) {
			unset($properties['layoutColumns']);
		}

		//override form layoutColumns with any properties set by the form controls
		$properties['layoutColumns']=array_merge($this->layoutColumns,isset($properties['layoutColumns']) ? $properties['layoutColumns'] : array());

		return parent::widget($className,$properties,$captureOutput);
	}
}