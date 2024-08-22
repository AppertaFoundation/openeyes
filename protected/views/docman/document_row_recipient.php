<?php
if (!isset($element)) {
    $element = new ElementLetter();
}

    $is_mandatory = isset($is_mandatory) ? $is_mandatory : false;

?>
<tr class="valign-top new_entry_row rowindex-<?php echo $row_index ?>" data-rowindex="<?php echo $row_index ?>">
    <td>
        <b class="large-text"><?php echo ($row_index == 0 ? 'To:' : 'Cc:') ?></b>
        <?=\CHtml::hiddenField("DocumentTarget[" . $row_index . "][attributes][ToCc]", ($row_index == 0 ? 'To' : 'Cc')); ?>
    </td>
    <td>
        <?php $this->renderPartial('//docman/table/contact_name_type', array(
                        'address_targets' => $element->address_targets,
                        'contact_id' => $contact_id,
                        'contact_name' => $contact_name,
                        'contact_type' => isset($selected_contact_type) ? $selected_contact_type : null,
                        'contact_nickname' => $contact_nickname,
                        'row_index' => $row_index,
                        //contact_type is not editable as per requested, former validation left until the req finalized
                        'is_editable' => false, //!$element->isInternalReferral(),
                ));
?>
    </td>
        <td>
            <?php
            $contact_type = ( isset($selected_contact_type) ? $selected_contact_type : null );
            $this->renderPartial('//docman/table/contact_address', array(
                'contact_id' => $contact_id,
                'is_editable_address' => $contact_type != \SettingMetadata::model()->getSetting('gp_label'),
                'contact_type' => $contact_type,
                'row_index' => $row_index,
                'address' => $address,
                'email' => $email,
                'can_send_electronically' => $can_send_electronically,
            ));
            ?>
        </td>
    <td class="docman_delivery_method align-left" data-test="docman_delivery_method">
        <?php $this->renderPartial('//docman/table/delivery_methods', array(
                    'is_draft' => $element->draft,
                    'contact_type' => $selected_contact_type,
                    'row_index' => $row_index,
                    'can_send_electronically' => $can_send_electronically,
                    'email' => $email,
                    'patient_id' => $patient_id ?? null,
                ));
?>

    </td>
    <td>
        <?php if ($row_index > 0) : ?>
            <a class="remove_recipient removeItem <?php echo $is_mandatory ? 'hidden' : '' ?>" data-rowindex="<?php echo $row_index ?>"><i class="oe-i trash js-has-tooltip" data-tooltip-content="Remove recipient"></i></a>
        <?php endif; ?>
    </td>
</tr>
