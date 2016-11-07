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
    <table id="dm_table">
    <tbody>
    <tr id="dm_0">
        <th>To/CC</th>
        <th>Recipient/Address</th>
        <th>Role</th>
        <th>Delivery Method(s)</th>
        <th>Actions</th>
    </tr>
    <?php    
        if(isset($data["document_set_id"]) ){
            echo  CHtml::hiddenField('document_set_id', $data["document_set_id"]);
            echo  CHtml::hiddenField('document_instance_id', $data['doctargets'][0]['document_instance_id']);
            echo  CHtml::hiddenField('document_instance_data_id', $data['docoutputs'][0]['document_instance_data_id']);
        }
        
        if(isset($data["doctargets"])) {
            $row_index = 0;
            foreach($data["doctargets"] as $doc_target) {
                foreach($data["docoutputs"] as $k => $doc_output){ ?>
                    <?php if ($doc_output["document_target_id"] == $doc_target["id"]): ?>
                        
                        <tr data-rowindex="<?php echo $row_index ?>">
                            <td>
                                <?php echo $doc_output["ToCc"] ." - " . $doc_output["id"]; ?>
                                <?php echo  CHtml::hiddenField('target_type['.$row_index.']', $doc_output["ToCc"], array('data-rowindex' => $row_index)); ?>                            
                                <?php echo  CHtml::hiddenField('document_target_id['.$row_index.']', $doc_target["id"], array('data-rowindex' => $row_index)); ?>
                                <?php echo  CHtml::hiddenField('id['.$row_index.']', $doc_output["id"], array('data-rowindex' => $row_index)); ?>
                            </td>
                            <td>
                                <?php echo $doc_target["contact_name"] ?>
                                <?php echo  CHtml::hiddenField('contact_id['.$row_index.']', $doc_target["contact_id"], array('data-rowindex' => $row_index)); ?>
                                <br>
                                <div id="docman_address_<?php echo $doc_target["id"]?>">
                                    <?php echo $doc_target["address"]; ?>
                                    <?php echo  CHtml::hiddenField('address['.$row_index.']', $doc_target["address"], array('data-rowindex' => $row_index)); ?>
                                </div>
                                <br>
                                
                                <a id="docman_edit_button_<?php echo $doc_target["id"]?>" onClick="docman2.editAddress(<?php echo $doc_target["id"]?>);">Edit Address</a>
                            
                            </td>
                            <td>
                                <?php echo ucfirst($doc_target["contact_type"]);?>
                                <?php if($doc_target["contact_modified"]){ echo "<br>(Modified)";}?>
                                <?php echo  CHtml::hiddenField('contact_type['.$row_index.']', $doc_target["contact_type"], array('data-rowindex' => $row_index)); ?>
                            </td>
                            <td>
                                <?php echo ucfirst($doc_output["output_type"]).' - '.ucfirst($doc_output["output_status"]);?>
                                <?php echo  CHtml::hiddenField(lcfirst($doc_output["output_type"]).'['.$row_index.']', lcfirst($doc_output["output_type"]), array('data-rowindex' => $row_index)); ?>
                            </td>
                            <td>
                                <?php
                                if($doc_output["ToCc"] != "To")
                                {?>
                                
                                    <a class="remove_recipient removeItem" data-rowindex="<?php echo $row_index ?>">Remove</a>
                                
                                <?php
                                }
                                ?>
                            </td>
                        </tr>
                        <?php $row_index++; ?>
                    <?php  endif;
                }
            }
        }
    // TODO: need to check if the user has proper rights to add new document!
    if(isset($data["correspondence_mode"]) && $data["correspondence_mode"]){
        $this->renderPartial('/docman/document_row_edit', array('data'=>$data));
    ?>
    <?php
    }else{
    ?>
    <tr><td colspan="5"><button class="docman green" id="docman_add_new">Add New</button></td></tr>
    <?php
        }?>
    </tbody>
    </table>