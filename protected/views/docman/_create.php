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
<div id="docman_block">
    <table id="dm_table" data-macro_id="<?php echo $macro_id; ?>">
        <tbody>
            <tr id="dm_0">
                <th>To/CC</th>
                <th>Recipient/Address</th>
                <th>Role</th>
                <th>Delivery Method(s)</th>
                <th> </th>
            </tr>

            <?php /* Generate recipients by macro */ ?>
            <?php if($macro_id && !empty($macro_data)): ?>
                <tr class="rowindex-<?php echo $row_index ?>" data-rowindex="<?php echo $row_index ?>">
                    <td> To <?php echo CHtml::hiddenField("DocumentTarget[" . $row_index . "][DocumentOutput][0][ToCc]", 'To'); ?> </td>
                    <td>
                        <?php echo CHtml::dropDownList('DocumentTarget['.$row_index.'][attributes][contact_id]', $macro_data["to"]["contact_id"], $element->address_targets,  array('empty' => '- Recipient -', 'nowrapper' => true, 'class' => 'full-width docman_recipient', 'data-rowindex'=>$row_index, 'data-previous' => $macro_data["to"]["contact_id"]))?>

                        <br>
                        <textarea rows="4" cols="10" name="DocumentTarget[<?php echo $row_index;?>][attributes][address]" id="Document_Target_Address_<?php echo $row_index;?>" data-rowindex="<?php echo $row_index ?>"><?php echo $macro_data["to"]["address"]?></textarea>
                    </td>
                    <td>
                        <?php echo CHtml::dropDownList('DocumentTarget['.$row_index.'][attributes][contact_type]', $macro_data["to"]["contact_type"], array(
                            'Gp' => 'Gp',
                            'Patient' => 'Patient',
                            'DRSS' => 'DRSS',
                            'Legacy' => 'Legacy',
                            'Other' => 'Other'
                            ), 
                            array(  'empty' => '- Type -',
                                    'nowrapper' => true, 
                                    'class' => 'full-width docman_contact_type',
                                    'id'=>'contact_type_'.$row_index,
                                    'data-rowindex' => $row_index
                                )
                        );?>
                    </td>
                    <td class="docman_delivery_method">
                        <?php if($macro_data["to"]["contact_type"] == 'Gp'){ ?>
                            <label>
                                <input type="checkbox" class="docman_delivery" name="DocumentTarget[<?php echo $row_index; ?>][DocumentOutput][0][output_type]" data-rowindex="<?php echo $row_index?>" value="docman" checked>DocMan
                            </label>
                            <br>
                        <?php }?>
                        <label>
                            <input type="checkbox" value="print" name="DocumentTarget[<?php echo $row_index; ?>]DocumentOutput[][output_type]" <?php if($macro_data["to"]["contact_type"] != 'Gp'){ echo 'checked';}?>>Print
                        </label>
                        <?php echo CHtml::hiddenField("DocumentTarget[" . $row_index . "][DocumentOutput][1][ToCc]", 'To'); ?> <?php /* well, thanks for the design */ ?>
                    </td>
                    <td> </td>
                </tr>
                <?php $row_index++; ?>

                <?php foreach($macro_data['cc'] as $cc_index => $macro): ?>
                    <?php $index = $row_index + $cc_index ?>
                    <tr class="rowindex-<?php echo $index ?>" data-rowindex="<?php echo $index ?>">
                        <td> Cc <?php echo CHtml::hiddenField("DocumentTarget[" . $row_index . "][DocumentOutput][0][ToCc]", 'Cc'); ?> </td>
                        <td>
                            <?php echo CHtml::dropDownList('DocumentTarget['.$row_index.'][attributes][contact_id]', $macro["contact_id"], $element->address_targets,  array('empty' => '- Recipient -', 'nowrapper' => true, 'class' => 'full-width docman_recipient', 'data-rowindex'=>$row_index, 'data-previous' => $macro["contact_id"]))?>
                        <br>
                        <textarea rows="4" cols="10" name="DocumentTarget[<?php echo $row_index;?>][attributes][address]" id="Document_Target_Address_<?php echo $row_index;?>" data-rowindex="<?php echo $row_index ?>"><?php echo $macro["address"]?></textarea>
                    </td>
                    <td>
                        <?php echo CHtml::dropDownList('DocumentTarget['.$row_index.'][attributes][contact_type]', $macro["contact_type"], array(
                            'Gp' => 'Gp',
                            'Patient' => 'Patient',
                            'DRSS' => 'DRSS',
                            'Legacy' => 'Legacy',
                            'Other' => 'Other'
                            ), 
                            array(  'empty' => '- Type -',
                                    'nowrapper' => true, 
                                    'class' => 'full-width docman_contact_type',
                                    'id'=>'contact_type_'.$row_index,
                                    'data-rowindex' => $row_index
                                )
                        );?>
                    </td>
                    <td class="docman_delivery_method">
                        <?php if($macro["contact_type"] == 'Gp'){ ?>
                            <label>
                                <input type="checkbox" value="Docman" class="docman_delivery" name="DocumentTarget[<?php echo $row_index; ?>][DocumentOutput][0][output_type]" data-rowindex="<?php echo $row_index?>" checked>DocMan
                            </label>
                            <br>
                        <?php }?>
                        <label>
                            <input type="checkbox" value="Print" name="DocumentTarget[<?php echo $row_index; ?>][DocumentOutput][1][output_type]" <?php if($macro["contact_type"] != 'Gp'){ echo 'checked';}?>>Print
                        </label>
                        <?php echo CHtml::hiddenField("DocumentTarget[" . $row_index . "][DocumentOutput][1][ToCc]", 'Cc'); ?>
                    </td>
                    <td> </td>

                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>

            <tr class="new_entry_row">
                <td colspan="6">
                    <button class="button small secondary" id="docman_add_new_recipient">Add new recipient</button>
                </td>
            </tr>
        </tbody>
    </table>
</div>    