<?php


if (!isset($values)) {
    $values = array(
        'id' => $entry->id,
        'label' => $entry->label ? $entry->label->name : "",
        'full_name' => $entry->getFullName(),
        'email' => $entry->address ? $entry->address->email : "",
        'phone' => $entry->primary_phone,
        'address' => $entry->address ? $entry->address->getLetterLine() : "",
    );
} 

?>


<tr>
    <input type="hidden" name="<?= $model_name ?>[contact_id][]" value="<?= $values['id'] ?>" />
    <td><?= $values['label'] ?></td>
    <td><?= $values['full_name'] ?></td>
    <td><?= $values['email'] ?></td>
    <td><?= $values['phone'] ?></td>
    <td><?= $values['address'] ?></td>
</tr>

