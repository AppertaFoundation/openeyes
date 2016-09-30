<?php
    if(!isset($element))
    {
        $element = new ElementLetter();
    }
?>
<tr class="new_entry_row" data-rowindex="<?php echo $row_index ?>">
    <td>
    </td>
    <td>
        <?php echo CHtml::dropDownList('target_type[]', ($row_index > 0) ? 'CC' : 'To', array('To'=>'To','CC'=>'CC'), array('empty' => '- To/CC -', 'nowrapper' => true, 'class' => 'full-width', 'data-rowindex'=>$row_index));?>
    </td>
    <td>
        <?php echo CHtml::dropDownList('contact_id[]', '', $element->address_targets, array('empty' => '- Recipient -', 'nowrapper' => true, 'class' => 'full-width docman_recipient', 'data-rowindex'=>$row_index));?>
        <br>
        <textarea rows="4" cols="10" name="address[]" id="address_<?php echo $row_index ?>" data-rowindex="<?php echo $row_index ?>"></textarea>
    </td>
    <td>
        <?php echo CHtml::dropDownList('contact_type[]', '', array('Gp'=>'Gp','Patient'=>'Patient', 'DRSS'=>'DRSS', 'Legacy'=>'Legacy', 'Other'=>'Other'), array('empty' => '- Type -', 'nowrapper' => true, 'class' => 'full-width docman_contact_type', 'id'=>'contact_type_'.$row_index, 'data-rowindex'=>$row_index));?>
    </td>
    <td class="docman_delivery_method">
        <label><input type="checkbox" name="print[]" data-rowindex="<?php echo $row_index ?>" checked>Print</label><br>
        <label><input type="checkbox" class="docman_delivery" name="docman[]" data-rowindex="<?php echo $row_index ?>">DocMan</label><br>
        <!--<label><input type="checkbox" name="cc_email[]" disabled>Email</label>!-->
    </td>
    <td>
        <a class="remove_recipient removeItem" data-rowindex="<?php echo $row_index ?>">Remove</a>
    </td>
</tr>