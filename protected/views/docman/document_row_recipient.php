<?php
    if(!isset($element))
    {
        $element = new ElementLetter();
    }
?>
<tr class="new_entry_row rowindex-<?php echo $row_index ?>" data-rowindex="<?php echo $row_index ?>">
    <td>
        <?php echo ($row_index == 0 ? 'To' : 'Cc') ?>
        <?php echo CHtml::hiddenField('target_type['.$row_index.']', ($row_index == 0 ? 'To' : 'Cc'), array('data-rowindex' => $row_index)); ?>
    </td>
    <td>
        <?php echo CHtml::dropDownList('contact_id[' . $row_index . ']', '', $element->address_targets, array('empty' => '- Recipient -', 'nowrapper' => true, 'class' => 'full-width docman_recipient', 'data-rowindex'=>$row_index));?>
        <br>
        <textarea rows="4" cols="10" name="address[<?php echo $row_index ?>]" id="address_<?php echo $row_index ?>" data-rowindex="<?php echo $row_index ?>"></textarea>
    </td>
    <td>
        <?php echo CHtml::dropDownList('contact_type[' . $row_index . ']', '', array('Gp'=>'Gp','Patient'=>'Patient', 'DRSS'=>'DRSS', 'Legacy'=>'Legacy', 'Other'=>'Other'), array('empty' => '- Type -', 'nowrapper' => true, 'class' => 'full-width docman_contact_type', 'id'=>'contact_type_'.$row_index, 'data-rowindex'=>$row_index));?>
    </td>
    <td class="docman_delivery_method">
        <label><input type="checkbox" name="print[<?php echo $row_index ?>]" data-rowindex="<?php echo $row_index ?>" checked>Print</label><br>
        <label><input type="checkbox" class="docman_delivery" name="docman[<?php echo $row_index ?>]" data-rowindex="<?php echo $row_index ?>">DocMan</label><br>
        <!--<label><input type="checkbox" name="cc_email[]" disabled>Email</label>!-->
    </td>
    <td>
        <a class="remove_recipient removeItem" data-rowindex="<?php echo $row_index ?>">Remove</a>
    </td>
</tr>