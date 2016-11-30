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
    table {
        border: 1px solid black;
        cell-spacing: 0;
        cell-padding: 0;
    }

    td {
        border: 1px solid black;
        border-collapse: 1;
        vertical-align: top;
    }

    tr {
        background-color: #eee;
        color: black;
        padding: 2px 5px;
    }

    th.actions {
        padding: 0 10px
    }

    select.addr {
        width: 200px !important;
        max-width: 200px;
    }

    div#docman_block select.macro {
        max-width: 220px;
    }
    .event-content div#docman_block textarea{
        font-size: 13px;
    }

    table.docman tbody tr td img {
        vertical-align: text-top;
        height: 13px;
        width: 13px;
    }

    table.docman > tbody > tr > td:first-child {
        width: 200px;
        max-width: 200px;
    }

    button.docman {
        width: 80px;
        background: none;
        font-size: 13px;
        line-height: 20px;
        height: 20px;
        margin: 5px 0;
        padding: 0;
        text-align: center;
    }

    button.red {
        background-color: red;
        color: white;
    }

    button.green {
        background-color: green;
        color: white;
    }
    th.actions{
        min-width: 113px;
    }
</style>

<table id="dm_table" data-macro_id="<?php echo $macro_id; ?>">
    <tbody>
    <tr id="dm_0">
        <th>To/CC</th>
        <th>Recipient/Address</th>
        <th>Role</th>
        <th>Delivery Method(s)</th>
        <th class="actions">
            <img class="docman_loader right" src="<?php echo Yii::app()->assetManager->createUrl('img/ajax-loader.gif') ?>" alt="loading..." style="display: none;">
        </th>
    </tr>

    <?php /* Generate recipients by macro */ ?>
    <?php if (!empty($macro_data)):?>
      <?php if (array_key_exists('to', $macro_data)):?>
       
        <tr class="rowindex-<?php echo $row_index ?>" data-rowindex="<?php echo $row_index ?>">
            <td> To <?php echo CHtml::hiddenField("DocumentTarget[" . $row_index . "][attributes][ToCc]", 'To'); ?> </td>
            <td>
                <?php 
                    $target = new DocumentTarget();
                    $target->contact_id = $macro_data["to"]["contact_id"];
                    $target->contact_name = $macro_data["to"]["contact_name"];
                    $target->address = $macro_data["to"]["address"];
                  
                    $this->renderPartial('//docman/table/contact_name_address', array(
                                'contact_id' => $target->contact_id,
                                'contact_name' => $target->contact_name,
                                'address_targets' => $element->address_targets,
                                'target' => $target,
                                'contact_type' => $target->contact_type,
                                'row_index' => $row_index,
                                'address' => $target->address,
                                'is_editable' => true));
                ?>
                
                
            </td>
            <td>
                <?php $this->renderPartial('//docman/table/contact_type', array(
                    'contact_type' => strtoupper($macro_data["to"]["contact_type"]),
                    'row_index' => $row_index,
                ));
                ?>
            </td>
            <td class="docman_delivery_method">
                <?php $this->renderPartial('//docman/table/delivery_methods', array(
                    'is_draft' => $element->draft,
                    'contact_type' => strtoupper($macro_data["to"]["contact_type"]),
                    'row_index' => $row_index,
                ));
                ?>
            </td>
            <td></td>
        </tr>
        <?php
            $row_index++;
            endif;
        ?>

        <?php if( isset($macro_data['cc']) ): ?>
            <?php foreach ($macro_data['cc'] as $cc_index => $macro): ?>
                <?php $index = $row_index + $cc_index ?>
                <tr class="rowindex-<?php echo $index ?>" data-rowindex="<?php echo $index ?>">
                    <td> Cc <?php echo CHtml::hiddenField("DocumentTarget[" . $index . "][attributes][ToCc]", 'Cc'); ?> </td>
                    <td>
                        <?php 
                            $target = new DocumentTarget();
                            $target->contact_id = $macro["contact_id"];
                            $target->contact_name = isset($macro["contact_name"]) ? $macro["contact_name"] : null;
                            $target->address = $macro["address"];
                            
                            $this->renderPartial('//docman/table/contact_name_address', array(
                                        'contact_id' => $target->contact_id,
                                        'contact_name' => $target->contact_name,
                                        'address_targets' => $element->address_targets,
                                        'target' => $target,
                                        'contact_type' => $target->contact_type,
                                        'row_index' => $index,
                                        'address' => $target->address,
                                        'is_editable' => true));
                        ?>
                    </td>
                    <td>
                        <?php $this->renderPartial('//docman/table/contact_type', array(
                            'contact_type' => strtoupper($macro["contact_type"]),
                            'row_index' => $index,
                        ));
                        ?>
                    </td>
                    <td class="docman_delivery_method">
                        <?php $output_index = 0; ?>
                        <?php if (strtoupper($macro["contact_type"]) == 'GP'): ?>
                            <label>
                                <input type="checkbox" value="Docman" class="docman_delivery" name="DocumentTarget_<?php echo $index; ?>_DocumentOutput_0_[output_type"
                                       data-rowindex="<?php echo $index ?>" checked disabled>DocMan
                                <input type="hidden" value="Docman" class="docman_delivery hidden" name="DocumentTarget[<?php echo $index; ?>][DocumentOutput][0][output_type]"
                                       data-rowindex="<?php echo $index ?>">
                            </label>
                            <?php $output_index++; ?>
                            <br>
                        <?php endif; ?>
                        <label>
                            <?php
                            $is_checked = $macro["contact_type"] == 'Gp' ? '' : 'checked';
                            if (Yii::app()->request->isPostRequest) {
                                $is_checked = isset($_POST["DocumentTarget"][$index]['DocumentOutput'][$output_index]['output_type']) ? 'checked' : '';
                            }
                            ?>
                            <input type="checkbox" value="Print"
                                   name="DocumentTarget[<?php echo $index; ?>][DocumentOutput][<?php echo $output_index ?>][output_type]" <?php echo $is_checked; ?>>Print
                        </label>
                    </td>
                    <td>
                        <a class="remove_recipient removeItem" data-rowindex="<?php echo $index ?>">Remove</a>
                    </td>

                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    <?php endif; ?>

    <tr class="rowindex-0" data-indexrow="0">
        <?php 
            /* generates a default 'To' and 'Cc' rows */
            if(empty($macro_data)){
                $contact_id = null;
                if(isset($defaults['To']['contact_id'])){
                    $contact_id = $defaults['To']['contact_id'];
                }
                $contact_type = null;
                if(isset($defaults['To']['contact_type'])){
                    $contact_type = $defaults['To']['contact_type'];
                }
                $address = null;
                if(isset($defaults['To']['address'])){
                    $address = $defaults['To']['address'];
                }
                $contact_name = null;
                if(isset($defaults['To']['contact_name'])){
                    $contact_name = $defaults['To']['contact_name'];
                }
                
                echo $this->renderPartial(
                    '//docman/document_row_recipient',
                    array('contact_id' => $contact_id, 'address' => $address, 'row_index' => 0, 'selected_contact_type' => $contact_type, 'contact_name' => $contact_name)
                );
            }
        ?>
    </tr>
    
    <tr class="rowindex-1" data-indexrow="1">
        <?php 
            if(empty($macro_data)){
                $contact_id = null;
                if(isset($defaults['Cc']['contact_id'])){
                    $contact_id = $defaults['Cc']['contact_id'];
                }
                $contact_type = null;
                if(isset($defaults['Cc']['contact_type'])){
                    $contact_type = $defaults['Cc']['contact_type'];
                }
                $address = null;
                if(isset($defaults['Cc']['address'])){
                    $address = $defaults['Cc']['address'];
                }
                $contact_name = null;
                if(isset($defaults['Cc']['contact_name'])){
                    $contact_name = $defaults['Cc']['contact_name'];
                }
                
                /* generates a default 'To' and 'Cc' rows */
                echo $this->renderPartial(
                    '//docman/document_row_recipient',
                    array('contact_id' => $contact_id, 'address' => $address, 'row_index' => 1, 'selected_contact_type' => $contact_type, 'contact_name' => $contact_name)
                );
            }
        ?>
        
        <td colspan="6">
            <button class="button small secondary" id="docman_add_new_recipient">Add new recipient</button>
        </td>
    </tr>
    
    </tbody>
</table>