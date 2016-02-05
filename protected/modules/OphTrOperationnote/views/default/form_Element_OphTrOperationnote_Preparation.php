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

<?php if ($element->getSetting('fife')) {?>
	<div class="element-fields">
		<?php echo $form->slider($element, 'spo2', array('min'=>0,'max'=>100,'step'=>1,'append'=>'%'))?>
		<?php echo $form->slider($element, 'oxygen', array('min'=>0,'max'=>100,'step'=>1,'append'=>'%'))?>
		<?php echo $form->slider($element, 'pulse', array('min'=>1, 'max'=>300,'step'=>1))?>
		<?php echo $form->dropDownList($element, 'skin_preparation_id', 'OphTrOperationnote_PreparationSkinPreparation', array('empty' => '- Please select -'))?>
		<?php echo $form->dropDownList($element, 'intraocular_solution_id', 'OphTrOperationnote_PreparationIntraocularSolution',array('empty' => '- Please select -'))?>
	</div>
<?php }?>
