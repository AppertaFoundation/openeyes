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

class EyeDrawService
{
	public $identifiers = Array();
	public $active = '';

	/**
	 * Returns HTML for an EyeDraw field
	 *
	 * @param object  $controller	the controller calling this
	 * @param object  $model		the element model
	 * @param integer $side			usually 'left' or 'right' - the eye we're rendering a field for
	 * @param boolean $writeable		whether the field should be writeable
	 *
	 * @return array
	 */
	public static function activeEyeDrawField($controller, $model, $side, $writeable = true)
	{
		global $active;
		global $identifiers;
		$active = true;
		$identifier = get_class($model) . '_' . $side;
		$identifiers[]= $identifier;

		$html = '';

		// create hidden form field to contain the EyeDraw data string
		// $html .= CHtml::activeHiddenField($model, $side);
		// eyedraw javascript
		$html .= $controller->renderPartial('/eyedraw/js',array('model' => $model, 'side' => $side, 'writeable' => $writeable),true);

		// eyedraw html
		$html .= $controller->renderPartial('/eyedraw/ed',array('model' => $model, 'side' => $side, 'writeable' => $writeable),true);

		return $html;
	}

	/**
	 * Initialises EyeDraw.
	 *
	 * @param object $controller
	 */
	public static function activeEyeDrawInit($controller)
	{
		$init = ''; $submit = '';
		global $identifiers;

		if (is_array($identifiers)) {
			foreach ($identifiers as $identifier) {
				$init .= 'init' . $identifier . '();';
				$submit .= 'submit' . $identifier . '();';
			}

			$return = $controller->renderPartial('/eyedraw/init',array('init' => $init, 'submit' => $submit),true);
			return $return;
		} else {
			return '<script type="text/javascript">function eyedraw_init() {return true;}</script>';
		}
	}

	public static function getActive()
	{
		global $active;
		return $active;
	}
}
