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
<div class="box admin">
	<form id="rulestest" class="panel">
		<h2>Test:</h2>
		<div class="row field-row">
			<div class="large-2 column">
				<?php echo CHtml::dropDownList('lcr_rule_type_id','',CHtml::listData(OphTrOperationbooking_Admission_Letter_Warning_Rule_Type::model()->findAll(array('order'=>'name')),'id','name'),array('empty'=>'- Rule -'))?>
			</div>
			<div class="large-2 column">
				<?php echo CHtml::dropDownList('lcr_site_id','',CHtml::listData(Site::model()->findAll(array('order'=>'name asc','condition'=>'institution_id = 1')),'id','name'),array('empty'=>'- Site -'))?>
			</div>
			<div class="large-2 column">
				<?php echo CHtml::dropDownList('lcr_subspecialty_id','',CHtml::listData(Subspecialty::model()->findAllByCurrentSpecialty(),'id','name'),array('empty'=>'- Subspecialty -'))?>
			</div>
			<div class="large-2 column">
				<?php echo CHtml::dropDownList('lcr_firm_id','',array(),array('empty'=>'- Firm -'))?>
			</div>
			<div class="large-2 column">
				<?php echo CHtml::dropDownList('lcr_theatre_id','',array(),array('empty'=>'- Theatre -'))?>
			</div>
			<div class="large-2 column">
				<?php echo CHtml::dropDownList('lcr_is_child','',array('' => '- Child/adult -','1' => 'Child','0' => 'Adult'))?>
			</div>
		</div>
		<div id="nomatch" style="display: none; color: #f00;">No match</div>
	</form>
	<div class="reportInputs">
		<h3 class="georgia">Letter warning rules</h3>
		<div>
			<form id="rules">
				<?php
				$this->widget('CTreeView',array(
						'data' => $data,
					))?>
			</form>
		</div>
	</div>
</div>

<?php echo EventAction::button('Add', 'add_letter_contact_rule', null, array('class' => 'button small'))->toHtml()?>

