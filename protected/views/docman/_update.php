<?php
/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */
?>
<style type='text/css'>
table { border:1px solid black; cell-spacing:0; cell-padding:0; }
td { border:1px solid black; border-collapse: 1; vertical-align:top; }
tr { background-color: #eee; color: black; padding:2px 5px; }
select.addr { width:200px !important; max-width:200px; }
div#docman_block select.macro { max-width:220px; }
table.docman tbody tr td img { vertical-align: text-top; height:13px; width:13px; }
table.docman > tbody > tr > td:first-child { width:200px; max-width:200px; }
button.docman { width:80px; background: none; font-size:13px; line-height:20px; height:20px; margin:5px 0; padding:0; text-align:center; }
button.red { background-color:red; color: white; }
button.green { background-color:green; color: white; }
</style>

    <?php echo CHtml::activeHiddenField($document_set, 'id') ?>
    <?php echo CHtml::activeHiddenField($document_set->document_instance[0], 'id') ?>
    <table id="dm_table" data-macro_id="<?php echo $macro_id; ?>">
        <tbody>
            <tr id="dm_0">
                <th>To/CC</th>
                <th>Recipient/Address</th>
                <th>Role</th>
                <th>Delivery Method(s)</th>
                <th> </th>
            </tr>
            
            <?php foreach($document_set->document_instance[0]->document_target as $row_index => $target):?>
                <tr class="rowindex-<?php echo $row_index ?>" data-rowindex="<?php echo $row_index ?>">
                    <td> 
                        <?php echo (isset($target->document_output[0]->ToCc) ? $target->document_output[0]->ToCc : ''); ?>
                        
                        <?php echo CHtml::hiddenField("DocumentTarget[" . $row_index . "][attributes][id]", $target->id); ?>
                    </td>
                    
                    <td>
                         <?php echo $target->contact_name; ?>
                        <?php echo CHtml::hiddenField('DocumentTarget['.$row_index.'][attributes][contact_id]', $target->contact_id); ?>
                        <br>                        
                        <div id="docman_address_<?php echo $target->id; ?>">
                            <?php echo $target->address; ?>
                            <?php echo  CHtml::hiddenField("DocumentTarget[$row_index][attributes][address]", $target->address, array('data-rowindex' => $row_index)); ?>
                        </div>
                    </td>
                    <td>
                        <?php echo ucfirst(strtolower($target->contact_type));?>
                        <?php if($target->contact_modified){ echo "<br>(Modified)";}?>
                        <?php echo  CHtml::hiddenField('DocumentTarget['.$row_index.'][attributes][contact_type]', $target->contact_type, array('data-rowindex' => $row_index)); ?>
                    </td>
                    <td>
                        <?php
                            $pre_output_key = 0;
                            // check if the it is a GP and if the GP has a Docman or Print
                            $docnam_output = null;
                            $print_output = null;
                            foreach($target->document_output as $output_key => $doc_output){
                                if($doc_output->output_type == 'Docman'){
                                    $docnam_output = $doc_output;
                                } else if($doc_output->output_type == 'Print'){
                                    $print_output = $doc_output->output_type;
                                }
                            }
                        ?>
                        <?php if(!$docnam_output && $target->contact_type == 'GP'): /* if no docman set for the GP we still need to display the chk box */?>
                            <label>
                                <input type="checkbox" value="Docmam" name="DocumentTarget[<?php echo $row_index; ?>][DocumentOutput][<?php echo $pre_output_key++; ?>][output_type]" checked>DocMan
                            </label>
                        <?php endif; ?>
                        
                        <?php if(!$print_output): /* if no Print output saved before, we still need to display the option */ ?>
                            <label>
                                <input type="checkbox" value="Print" name="DocumentTarget[<?php echo $row_index; ?>][DocumentOutput][<?php echo $pre_output_key++; ?>][output_type]">Print
                            </label>
                        <?php endif; ?>
               
                        <?php foreach($target->document_output as $key => $doc_output): ?>
                            
                            <?php $output_key = $pre_output_key + $key; ?>
                        
                            <?php
                                // on error we check what had been submited and restore
                                $is_checked = ($doc_output->output_type == 'Docman' && $target->contact_type == 'GP') ? 'checked disabled' : '';
                                if (isset($_POST['DocumentTarget'][$row_index]['DocumentOutput'][$output_key]['output_type'])){
                                    
                                    if($_POST['DocumentTarget'][$row_index]['DocumentOutput'][$output_key]['output_type'] == 'Docnman'){
                                        $is_checked = 'checked disabled'; // disabled, we will resend it
                                    } else {
                                        $is_checked = 'checked';
                                    }
                                    
                                } else if( Yii::app()->request->isPostRequest && !isset($_POST['DocumentTarget'][$row_index]['DocumentOutput'][$output_key]['output_type']) && $target->contact_type != 'GP') {
                                    $is_checked = '';
                                }
                            ?>
                            
                            <label>
                                <input type="checkbox" value="<?php echo $doc_output->output_type;?>" name="DocumentTarget[<?php echo $row_index; ?>][DocumentOutput][<?php echo $output_key; ?>][output_type]" <?php echo $is_checked; ?>><?php echo $doc_output->output_type; ?>
                                <?php echo CHtml::hiddenField("DocumentTarget[$row_index][DocumentOutput][$output_key][id]", $doc_output->id); ?>
                                <?php echo CHtml::hiddenField("DocumentTarget[$row_index][DocumentOutput][$output_key][ToCc]", $doc_output->ToCc); ?>
                            </label>
                                
                        <?php endforeach; ?>
                    </td>
                    <td></td>
                </tr>
            <?php endforeach; ?>
    </table>         
            
            
            
            
            
            
            
            
            
            
            
            
            
            
            
            
            
            
            
            
            
            
            
            
            
            
            
            
            