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
?>
<?php
$expert = $element->getSetting('expert');
if ($expert) {
	$doodleToolBarArray = array('AngleNV', 'AntSynech', 'AngleRecession');
} else {
	$doodleToolBarArray = array('AngleNV', 'AntSynech');
}
$bindingArray = array();
$onReadyCommandArray = array(
	array('addDoodle', array('Gonioscopy'))
);
foreach (array('AngleGradeNorth' => 'sup','AngleGradeEast' => 'nas','AngleGradeSouth' => 'inf', 'AngleGradeWest' => 'tem') as $doodleClass => $position) {
	$bindingArray[$doodleClass]['grade'] = array('id' => 'Element_OphCiExamination_Gonioscopy_'.$side.'_gonio_'.$position.'_id', 'attribute' => 'data-value');
	if (!$expert) {
		$bindingArray[$doodleClass]['seen'] = array('id' => $side.'_gonio_'.$position.'_basic', 'attribute' => 'data-value');
	}
	$onReadyCommandArray[] = array('addDoodle', array($doodleClass));
}
$onReadyCommandArray[] = array('deselectDoodles', array());
$this->widget('application.modules.eyedraw.OEEyeDrawWidget', array(
		'doodleToolBarArray' => $doodleToolBarArray,
		'onReadyCommandArray' => $onReadyCommandArray,
		'bindingArray' => $bindingArray,
		'idSuffix' => $side.'_'.$element->elementType->id,
		'side' => ($side == 'right') ? 'R' : 'L',
		'mode' => 'edit',
		'model' => $element,
		'attribute' => $side.'_eyedraw',
))?>
