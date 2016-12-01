<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2016
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2016, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */
?>

<?php
    $pre_output_key = 0;
    // check if the it is a GP and if the GP has a Docman or Print
    $docnam_output = null;
    $print_output = null;
    if( isset($target->document_output)){
        foreach($target->document_output as $output_key => $doc_output){
            if($doc_output->output_type == 'Docman'){
                $docnam_output = $doc_output;
            } else if($doc_output->output_type == 'Print'){
                $print_output = $doc_output;
            }
        }
    }
    ?>
    <?php if($contact_type == 'GP'): ?>
        <label>
            <?php 
                $is_checked = $is_draft == 1 ? 'checked disabled' : ''; 
                
                //now, docman cannot be unchecked when the recipient is GP
                // if we want to allow users to tick/untick DocMan chcbox remove the following line
                $is_checked = 'checked disabled';
            ?>
            <input type="checkbox" value="Docman" name="DocumentTarget_<?php echo $row_index; ?>_DocumentOutput_<?php echo $pre_output_key; ?>_output_type" 
                <?php echo $is_checked; ?>>  Electronic (DocMan)
            <input type="hidden" value="Docman" name="DocumentTarget[<?php echo $row_index; ?>][DocumentOutput][<?php echo $pre_output_key; ?>][output_type]" >
        </label>
        <?php if($docnam_output):?>
            <?php echo CHtml::hiddenField("DocumentTarget[$row_index][DocumentOutput][" . $pre_output_key . "][id]", $docnam_output->id, array('class'=>'document_target_' . $row_index . '_document_output_id')); ?>
        <?php endif; ?>
        <?php $pre_output_key++; ?>
    <?php endif; ?>    

    <label>
        <?php 
            $is_checked = 'checked';
            
            $is_post_checked = isset($_POST['DocumentTarget'][$row_index]['DocumentOutput'][$pre_output_key]['output_type']);
            if( $contact_type == 'GP' ){
                $is_checked = $is_post_checked ? 'checked' : '';
            } else {
                $is_checked = (Yii::app()->request->isPostRequest && !$is_post_checked) ? '' : 'checked';
            }
        ?>
        <input type="checkbox" value="Print" 
               name="DocumentTarget[<?php echo $row_index; ?>][DocumentOutput][<?php echo $pre_output_key; ?>][output_type]" <?php echo $is_checked?>>  Print
    </label>
    <?php if($print_output): ?>
        <?php echo CHtml::hiddenField("DocumentTarget[$row_index][DocumentOutput][" . $pre_output_key . "][id]", $print_output->id); ?>
    <?php endif; ?>