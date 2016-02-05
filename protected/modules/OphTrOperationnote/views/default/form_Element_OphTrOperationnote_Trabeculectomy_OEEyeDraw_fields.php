<?php
/**
 * (C) OpenEyes Foundation, 2014
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2014, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */
?>
<div class="row">
	<div class="large-12 column">
		<?php echo $form->dropDownList($element, 'conjunctival_flap_type_id', 'OphTrOperationnote_Trabeculectomy_Conjunctival_Flap_Type', array('textAttribute'=>'data-value'), false, array('field' => 4))?>
		<?php echo $form->checkBox($element, 'stay_suture', array('text-align' => 'right'), array('field' => 4))?>
		<?php echo $form->dropDownList($element, 'site_id', 'OphTrOperationnote_Trabeculectomy_Site', array('textAttribute' => 'data-value'), false, array('field' => 4))?>
		<?php echo $form->dropDownList($element, 'size_id', 'OphTrOperationnote_Trabeculectomy_Size', array('textAttribute' => 'data-value'), false, array('field' => 4))?>
		<?php echo $form->dropDownList($element, 'sclerostomy_type_id', 'OphTrOperationnote_Trabeculectomy_Sclerostomy_Type', array('textAttribute' => 'data-value'), false, array('field' => 4))?>
		<?php echo $form->dropDownList($element, 'viscoelastic_type_id', 'OphTrOperationnote_Trabeculectomy_Viscoelastic_Type', array(), false, array('field' => 4))?>
		<?php echo $form->checkBox($element, 'viscoelastic_removed', array('text-align' => 'right'), array('field' => 4))?>
		<?php echo $form->dropDownList($element, 'viscoelastic_flow_id', 'OphTrOperationnote_Trabeculectomy_Viscoelastic_Flow', array(), false, array('field' => 4))?>
		<?php echo $form->textArea($element, 'report', array(), false, array(), array('field' => 9))?>
		<div class="row field-row">
			<div class="large-offset-3 large-9 column end">
				<button id="btn-trabeculectomy-report" class="secondary small ed_report">
					Report
				</button>
				<button id="btn-trabeculectomy-clear" class="secondary small ed_clear">
					Clear
				</button>
			</div>
		</div>
		<?php echo $form->multiSelectList($element, 'MultiSelect_Difficulties', 'difficulty_assignments', 'difficulty_id', CHtml::listData(OphTrOperationnote_Trabeculectomy_Difficulty::model()->findAll(array('order'=>'display_order asc')),'id','name'), array(), array('empty' => '- Select -','label' => 'Operative difficulties','class' => 'linked-fields','data-linked-fields' => 'difficulty_other', 'data-linked-values' => 'Other'), false, false, null, false, false, array('field' => 4))?>
		<?php echo $form->textArea($element, 'difficulty_other', array(), !$element->hasMultiSelectValue('difficulties','Other'), array(), array('field' => 6))?>
		<?php echo $form->multiSelectList($element, 'MultiSelect_Complications', 'complication_assignments', 'complication_id', CHtml::listData(OphTrOperationnote_Trabeculectomy_Complication::model()->findAll(array('order'=>'display_order asc')),'id','name'), array(), array('empty' => '- Select -','label' => 'Complications','class' => 'linked-fields','data-linked-fields' => 'complication_other', 'data-linked-values' => 'Other'), false, false, null, false, false, array('field' => 4))?>
		<?php echo $form->textArea($element, 'complication_other', array(), !$element->hasMultiSelectValue('complications','Other'), array(), array('field' => 6))?>
	</div>
</div>
