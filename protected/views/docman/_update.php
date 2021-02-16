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

    <?=\CHtml::activeHiddenField($document_set, 'id') ?>
    <?=\CHtml::activeHiddenField($document_set->document_instance[0], 'id') ?>
    <?=\CHtml::activeHiddenField($document_set->document_instance[0]->document_instance_data[0], 'id') ?>

<?php $element->draft = 1; ?>
<?php $is_mandatory = false; ?>

    <table class= "cols-full" id="dm_table" data-macro_id="<?php echo $macro_id; ?>">
            <colgroup>
                <col>
                <col class="cols-3">
                <col class="cols-4">
            </colgroup>
        <thead>
            <tr id="dm_0">
                <th colspan="4"></th>
                <th class="actions"><img class="docman_loader right" src="<?php echo Yii::app()->assetManager->createUrl('img/ajax-loader.gif')?>" alt="loading..." style="display: none;"></th>
            </tr>
        </thead>
        <tbody>
            <?php
                    $document_targets = $document_set->document_instance[0]->document_target;

            if ( Yii::app()->request->isPostRequest ) {
                $document_targets = array();
                $post_targets = Yii::app()->request->getPost('DocumentTarget');

                if ($post_targets) {
                    foreach ($post_targets as $post_target) {
                        if (isset($post_target['attributes']['id'])) {
                            $target = DocumentTarget::model()->findByPk($post_target['attributes']['id']);
                            $document_targets[] = $target;
                        }
                    }
                }
            }
            ?>

            <?php foreach ($document_targets as $row_index => $target) :?>
                <tr class="valign-top rowindex-<?php echo $row_index ?>" data-rowindex="<?php echo $row_index ?>">
                    <td>
                        <?php echo $target->ToCc; ?>
                        <?=\CHtml::hiddenField("DocumentTarget[" . $row_index . "][attributes][id]", $target->id); ?>
                        <?=\CHtml::hiddenField("DocumentTarget[" . $row_index . "][attributes][ToCc]", $target->ToCc); ?>
                    </td>
                                    <td>
                                        <?php if ($element->draft) : ?>
                                            <?php
                                            $contact_type = strtoupper($target->contact_type);
                                            $contact_type = $contact_type == 'PRACTICE' ? \SettingMetadata::model()->getSetting('gp_label') : $contact_type;
                                            $contact_nick_name = $contact_type === 'GP' ? (isset($element['event']['episode']['patient']['gp']) ? $element['event']['episode']['patient']['gp']['contact']->nick_name : '') : $element['event']['episode']['patient']['contact']->nick_name;
                                            $email = (isset($contact_id) ? ( isset(Contact::model()->findByPk($target->contact_id)->id) ? Contact::model()->findByPk($target->contact_id)->email : null ) : ( isset($target->email) ? $target->email : null) );

                                            $this->renderPartial('//docman/table/contact_name_type', array(
                                                'address_targets' => $element->address_targets,
                                                'contact_id' => $target->contact_id,
                                                'contact_name' => $target->contact_name,
                                                'contact_nickname' =>$contact_nick_name ,
                                                'contact_type' => $contact_type,
                                                // Internal referral will always be the first row - indexed 0
                                                'contact_types' => Document::getContactTypes() + (($element->isInternalReferral() && $row_index == 0) ? Document::getInternalReferralContactType() : []),

                                                //contact_type is not editable as per requested, former validation left until the req finalized
                                                'is_editable' => false, //$target->contact_type != 'INTERNALREFERRAL',
                                                'is_editable_contact_name' => ($target->contact_type != 'INTERNALREFERRAL'),
                                                'is_editable_contact_targets' => $target->contact_type != 'INTERNALREFERRAL',
                                                'row_index' => $row_index));
                                            ?>
                                        <?php else : ?>
                                            <?php echo $target->contact_type != \SettingMetadata::model()->getSetting('gp_label') ? (ucfirst(strtolower($target->contact_type))) : $target->contact_type; ?>
                                            <?php if ($target->contact_modified) {
                                                echo "<br>(Modified)";
                                            }?>
                                            <?php echo  CHtml::hiddenField('DocumentTarget['.$row_index.'][attributes][contact_type]', $target->contact_type, array('data-rowindex' => $row_index)); ?>
                                        <?php endif; ?>
                                    </td>
                    <td>
                        <?php
                        $this->renderPartial('//docman/table/contact_address', array(
                                    'contact_id' => $target->contact_id,
                                    'target' => $target,
                                    'contact_type' => $target->contact_type,
                                    'row_index' => $row_index,
                                    'address' => $target->address,
                                    'email' => $email,
                                    'is_editable_address' => ($target->contact_type != \SettingMetadata::model()->getSetting('gp_label')) && ($target->contact_type != 'INTERNALREFERRAL') && ($target->contact_type != 'Practice'),
                                    'can_send_electronically' => $can_send_electronically,
                        ));
                        ?>
                    </td>
                    <td class="docman_delivery_method">
                        <?php $this->renderPartial('//docman/table/delivery_methods', array(
                                        'is_draft' => $element->draft,
                                        'contact_type' => $contact_type,
                                        'target' => $target,
                                        'can_send_electronically' => $can_send_electronically,
                                        'row_index' => $row_index,
                                        'email' => $email));
                        ?>
                    </td>
                    <td>
                        <?php if ($element->draft == "1" && $target->ToCc != 'To') : ?>
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