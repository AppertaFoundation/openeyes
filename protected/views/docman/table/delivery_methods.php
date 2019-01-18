<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2016
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2016, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
?>

<?php
    $pre_output_key = 0;
    // check if it is a GP and if the GP has a Docman or Print
    $document_output = null;
    $internalreferral_output = null;
    $print_output = null;
    $is_new_record = isset($target) && $target->isNewRecord ? true : false;
    if( isset($target->document_output)){
        foreach($target->document_output as $output_key => $doc_output){
            if($doc_output->output_type == 'Docman'){
                $document_output = $doc_output;
            } else if($doc_output->output_type == 'Print'){
                $print_output = $doc_output;
            } else if($doc_output->output_type == 'Internalreferral'){
                $internalreferral_output = $doc_output;
            }
        }
    }
    ?>
    <?php if ($contact_type == Yii::app()->params['gp_label']): ?>
        <?php if($can_send_electronically): ?>
            <div>
							<label class="inline highlight electronic-label docman">
                <?php
                    $is_checked = $is_draft == 1 ? 'checked disabled' : '';

                    //now, docman cannot be unchecked when the recipient is GP
                    // if we want to allow users to tick/untick DocMan checkbox remove the following line
                    $is_checked = 'checked disabled';
                ?>
                <input type="checkbox" value="Docman" name="DocumentTarget_<?php echo $row_index; ?>_DocumentOutput_<?php echo $pre_output_key; ?>_output_type"
                    <?php echo $is_checked; ?>> <?php echo (isset(Yii::app()->params['electronic_sending_method_label']) ? Yii::app()->params['electronic_sending_method_label'] : 'Electronic'); ?>
                <input type="hidden" value="Docman" name="DocumentTarget[<?php echo $row_index; ?>][DocumentOutput][<?php echo $pre_output_key; ?>][output_type]" >
            	</label>
						</div>

            <?php if($document_output):?>
                <?=\CHtml::hiddenField("DocumentTarget[$row_index][DocumentOutput][" . $pre_output_key . "][id]", $document_output->id, array('class'=>'document_target_' . $row_index . '_document_output_id')); ?>
            <?php endif; ?>
            <?php $pre_output_key++; ?>
        <?php endif; ?>

    <?php endif; ?>

    <?php if($contact_type == 'INTERNALREFERRAL'): ?>
        <?php $label = ElementLetter::model()->getInternalReferralSettings('internal_referral_method_label', 'Electronic'); ?>
        <div>
					<label class="inline highlight electronic-label internal-referral">
            <?php

            //now, WinDip cannot be unchecked
            // if we want to allow users to tick/untick the checkbox remove the following line
            $is_checked = 'checked disabled';
            ?>
            <input type="checkbox" value="Internalreferral" name="DocumentTarget_<?php echo $row_index; ?>_DocumentOutput_<?php echo $pre_output_key; ?>_output_type"
                <?php echo $is_checked; ?>> <?php echo $label; ?>
            <input type="hidden" value="Internalreferral" name="DocumentTarget[<?php echo $row_index; ?>][DocumentOutput][<?php echo $pre_output_key; ?>][output_type]" >
        	</label>
				</div>
        <?php if($internalreferral_output):?>
            <?=\CHtml::hiddenField("DocumentTarget[$row_index][DocumentOutput][" . $pre_output_key . "][id]", $internalreferral_output->id, array('class'=>'document_target_' . $row_index . '_document_output_id')); ?>
        <?php endif; ?>
        <?php $pre_output_key++; ?>
    <?php endif; ?>

   <div>
		 <label class="inline highlight">
        <?php
            $is_checked = $print_output || $is_new_record ? 'checked' : '';

            $is_post_checked = isset($_POST['DocumentTarget'][$row_index]['DocumentOutput'][$pre_output_key]['output_type']);
            if( $contact_type == Yii::app()->params['gp_label'] || $contact_type == 'INTERNALREFERRAL'){
                $is_checked = $is_post_checked ? 'checked' : ($print_output ? 'checked' : '');
            } else {
                $is_checked = (Yii::app()->request->isPostRequest && !$is_post_checked) ? '' : $is_checked;
            }
        ?>
        <?php
        if( isset(Yii::app()->params['OphCoCorrespondence_event_actions']['create']['saveprint']) && Yii::app()->params['OphCoCorrespondence_event_actions']['create']['saveprint'] ): ?>
        <input type="checkbox" value="Print"
               name="DocumentTarget[<?php echo $row_index; ?>][DocumentOutput][<?php echo $pre_output_key; ?>][output_type]" <?php echo $is_checked?>>  Print
        <?php endif; ?>
    </label>
	 </div>
    <?php if($print_output): ?>
        <?=\CHtml::hiddenField("DocumentTarget[$row_index][DocumentOutput][" . $pre_output_key . "][id]", $print_output->id); ?>
    <?php endif; ?>