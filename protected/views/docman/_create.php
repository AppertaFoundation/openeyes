<?php
/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
?>

<?php
    $is_mandatory = isset($is_mandatory) ? $is_mandatory : false;
?>

<table id="dm_table" data-macro_id="<?php echo $macro_id; ?>">
    <thead>
        <tr id="dm_0">
            <th>To/CC</th>
            <th>Recipient/Address</th>
            <th>Role</th>
            <th>Delivery Method(s)</th>
            <th class="actions"><img class="docman_loader right" src="<?php echo Yii::app()->assetManager->createUrl('img/ajax-loader.gif') ?>" alt="loading..." style="display: none;"></th>
        </tr>
    </thead>

    <tbody>
    <?php /* Generate recipients by macro */ ?>
    <?php if (!empty($macro_data)):?>
        <?php if (array_key_exists('to', $macro_data)):?>
       
            <tr class="rowindex-<?php echo $row_index ?>" data-rowindex="<?php echo $row_index ?>">
                <td> To <?php echo CHtml::hiddenField("DocumentTarget[" . $row_index . "][attributes][ToCc]", 'To'); ?> </td>
                <td>
                    <?php

                        $contact_type = isset($macro_data["to"]["contact_type"]) ? $macro_data["to"]["contact_type"] : null;

                        $this->renderPartial('//docman/table/contact_name_address', array(
                                    'contact_id' => $macro_data["to"]["contact_id"],
                                    'contact_name' => $macro_data["to"]["contact_name"],
                                    'address_targets' => $element->address_targets,
                                    'contact_type' => $contact_type,
                                    'row_index' => $row_index,
                                    'address' => $macro_data["to"]["address"],

                                    'is_editable_contact_targets' => $contact_type != 'INTERNALREFERRAL',
                                    'is_editable_contact_name' => ($contact_type != 'INTERNALREFERRAL'),
                                    'is_editable_address' => (ucfirst(strtolower($contact_type)) != 'Gp') && ($contact_type != 'INTERNALREFERRAL') && ($contact_type != 'Practice'),
                                ));
                    ?>


                </td>
                <td>
                    <?php

                    $contact_type = strtoupper($macro_data["to"]["contact_type"]);
                    $contact_type = $contact_type == 'PRACTICE' ? 'GP' : $contact_type;

                    $this->renderPartial('//docman/table/contact_type', array(
                        'contact_type' => $contact_type,
                        'row_index' => $row_index,
                        // Internal referral will always be the first row - indexed 0
                        'contact_types' => Document::getContactTypes() + ((strtoupper($macro_data["to"]["contact_type"]) == 'INTERNALREFERRAL' && $row_index == 0) ? Document::getInternalReferralContactType() : []),

                        //contact_type is not editable as per requested, former validation left until the req finalized
                        'is_editable' => false, //strtoupper($macro_data["to"]["contact_type"]) != 'INTERNALREFERRAL',
                    ));
                    ?>
                </td>
                <td class="docman_delivery_method">
                    <?php $this->renderPartial('//docman/table/delivery_methods', array(
                        'is_draft' => $element->draft,
                        'contact_type' => $contact_type,
                        'row_index' => $row_index,
                        'can_send_electronically' => $can_send_electronically
                    ));
                    ?>
                </td>
                <td></td>
            </tr>
        <?php else: ?>
            <?php
                // if no To was set in the macro we just display an empty row
                $this->renderPartial(
                        '//docman/document_row_recipient',
                        array(
                            'contact_id' => null,
                            'address' => null,
                            'row_index' => 0,
                            'selected_contact_type' => null,
                            'contact_name' => null,
                            'can_send_electronically' => $can_send_electronically,
                        )
                    );
                ?>
        <?php endif; ?>
        <?php $row_index++; ?>

        <?php if( isset($macro_data['cc']) ): ?>
            <?php foreach ($macro_data['cc'] as $cc_index => $macro): ?>
                <?php $index = $row_index + $cc_index ?>
                <tr class="rowindex-<?php echo $index ?>" data-rowindex="<?php echo $index ?>">
                    <td> Cc <?php echo CHtml::hiddenField("DocumentTarget[" . $index . "][attributes][ToCc]", 'Cc'); ?> </td>
                    <td>
                        <?php 
                            $contact_name = isset($macro["contact_name"]) ? $macro["contact_name"] : null;
                            $contact_type = isset($macro["contact_type"]) ? $macro["contact_type"] : null;

                            $this->renderPartial('//docman/table/contact_name_address', array(
                                        'contact_id' => $macro["contact_id"],
                                        'contact_name' => $contact_name,
                                        'address_targets' => $element->address_targets,
                                        'is_editable_address' => ucfirst(strtolower($contact_type)) != 'Gp',
                                        'contact_type' => $contact_type,
                                        'row_index' => $index,
                                        'address' => $macro["address"],
                                    ));
                        ?>
                    </td>
                    <td>
                        <?php $this->renderPartial('//docman/table/contact_type', array(
                            'contact_type' => strtoupper($macro["contact_type"]),
                            'row_index' => $index,

                            //contact_type is not editable as per requested, former validation left until the req finalized
                            'is_editable' => false, //strtoupper($macro["contact_type"]) != 'INTERNALREFERRAL',
                            'contact_types' => Document::getContactTypes() + ((strtoupper($macro["contact_type"]) == 'INTERNALREFERRAL' && $row_index == 0) ? Document::getInternalReferralContactType() : []),
                        ));
                        ?>
                    </td>
                    <td class="docman_delivery_method">           
                        <?php $this->renderPartial('//docman/table/delivery_methods', array(
                                'is_draft' => $element->draft,
                                'contact_type' => strtoupper($macro["contact_type"]),
                                'row_index' => $index,
                                'can_send_electronically' => $can_send_electronically,
                            ));
                        ?>


                    </td>
                    <td>
                        <a class="remove_recipient removeItem <?php echo (isset($macro['is_mandatory']) && $macro['is_mandatory'])? 'hidden' : '' ?>" data-rowindex="<?php echo $index ?>">Remove</a>
                    </td>

                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    <?php endif; ?>


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
                    array(
                        'contact_id' => $contact_id, 
                        'address' => $address, 'row_index' => 0, 
                        'selected_contact_type' => $contact_type, 
                        'contact_name' => $contact_name, 
                        'can_send_electronically' => $can_send_electronically,
                    )
                );
            }
        ?>

    

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
                    array(
                        'contact_id' => $contact_id, 
                        'address' => $address, 
                        'row_index' => 1, 
                        'selected_contact_type' => $contact_type, 
                        'contact_name' => $contact_name,
                        'can_send_electronically' => $can_send_electronically,
                        'is_internal_referral' => $element->isInternalReferralEnabled(),
                    )
                );
            }
        ?>

        <tr class="new_entry_row">
            <td colspan="5">
                <button class="button small secondary" id="docman_add_new_recipient">Add new recipient</button>
            </td>
        </tr>

    
    </tbody>
</table>