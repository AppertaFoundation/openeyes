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

    <?php echo CHtml::activeHiddenField($document_set, 'id') ?>
    <?php echo CHtml::activeHiddenField($document_set->document_instance[0], 'id') ?>
    <?php echo CHtml::activeHiddenField($document_set->document_instance[0]->document_instance_data[0], 'id') ?>

<?php $element->draft = 1; ?>
<?php $is_mandatory = false; ?>

    <table id="dm_table" data-macro_id="<?php echo $macro_id; ?>">
        <thead>
            <tr id="dm_0">
                <th>To/CC</th>
                <th>Recipient/Address</th>
                <th>Role</th>
                <th>Delivery Method(s)</th>
                <th class="actions"><img class="docman_loader right" src="<?php echo Yii::app()->assetManager->createUrl('img/ajax-loader.gif')?>" alt="loading..." style="display: none;"></th>
            </tr>
        </thead>
        <tbody>
            <?php 
                    $document_targets = $document_set->document_instance[0]->document_target;

                    if( Yii::app()->request->isPostRequest ){
                        $document_targets = array();
                        $post_targets = Yii::app()->request->getPost('DocumentTarget');

                        if($post_targets){
                            foreach($post_targets as $post_target){
                                $target = new DocumentTarget();
                                $target->attributes = $post_target['attributes'];
                                $document_targets[] = $target;
                            }
                        }
                    }
                ?>

            <?php foreach($document_targets as $row_index => $target):?>

                <tr class="rowindex-<?php echo $row_index ?>" data-rowindex="<?php echo $row_index ?>">
                    <td> 
                        <?php echo $target->ToCc; ?>
                        <?php echo CHtml::hiddenField("DocumentTarget[" . $row_index . "][attributes][id]", $target->id); ?>
                        <?php echo CHtml::hiddenField("DocumentTarget[" . $row_index . "][attributes][ToCc]", $target->ToCc); ?>
                    </td>
                    <td>
                        <?php $this->renderPartial('//docman/table/contact_name_address', array(
                                    'contact_id' => $target->contact_id,
                                    'contact_name' => $target->contact_name,
                                    'address_targets' => $element->address_targets,
                                    'target' => $target,
                                    'contact_type' => $target->contact_type,
                                    'row_index' => $row_index,
                                    'address' => $target->address,
                                    'is_editable_contact_targets' => $target->contact_type != 'INTERNALREFERRAL',
                                    'is_editable_contact_name' => ($target->contact_type != 'INTERNALREFERRAL'),
                                    'is_editable_address' => ($target->contact_type != 'GP') && ($target->contact_type != 'INTERNALREFERRAL') && ($target->contact_type != 'Practice'),
                                ));
                        ?>
                    </td>
                    <td>
                        <?php if($element->draft): ?>
                            <?php
                                    $contact_type = strtoupper($target->contact_type);
                                    $contact_type = $contact_type == 'PRACTICE' ? 'GP' : $contact_type;

                                    $this->renderPartial('//docman/table/contact_type', array(
                                        'contact_type' => $contact_type,
                                        // Internal referral will always be the first row - indexed 0
                                        'contact_types' => Document::getContactTypes() + (($element->isInternalReferral() && $row_index == 0) ? Document::getInternalReferralContactType() : []),

                                        //contact_type is not editable as per requested, former validation left until the req finalized
                                        'is_editable' => false, //$target->contact_type != 'INTERNALREFERRAL',
                                        'row_index' => $row_index));
                            ?>
                        <?php else: ?>
                            <?php echo $target->contact_type != 'GP' ? (ucfirst(strtolower($target->contact_type))) : $target->contact_type; ?>
                            <?php if($target->contact_modified){ echo "<br>(Modified)";}?>
                            <?php echo  CHtml::hiddenField('DocumentTarget['.$row_index.'][attributes][contact_type]', $target->contact_type, array('data-rowindex' => $row_index)); ?>
                        <?php endif; ?>
                    </td>
                    <td class="docman_delivery_method">
                        <?php $this->renderPartial('//docman/table/delivery_methods', array(
                                        'is_draft' => $element->draft,
                                        'contact_type' => $contact_type,
                                        'target' => $target,
                                        'can_send_electronically' => $can_send_electronically,
                                        'row_index' => $row_index));
                        ?>
                    </td>
                    <td>
                        <?php if($element->draft == "1" && $target->ToCc != 'To'): ?>
                            <a class="remove_recipient removeItem <?php echo $is_mandatory ? 'hidden' : '' ?>" data-rowindex="<?php echo $row_index ?>">Remove</a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            <tr class="new_entry_row">
                <td colspan="5">
                    <button class="button small secondary" id="docman_add_new_recipient">Add new recipient</button>
                </td>
            </tr>
    </table>