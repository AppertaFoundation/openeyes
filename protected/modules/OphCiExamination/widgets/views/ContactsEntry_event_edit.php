<?php



if (!isset($values)) {
    if(isset($entry) && isset($entry->contact)){
        $contact = $entry->contact;
    }
    $values = array(
        'id' => $contact->id,
        'label' => $contact->label ? $contact->label->name : "",
        'full_name' => $contact->getFullName(),
        'email' => $contact->address ? $contact->address->email : "",
        'phone' => $contact->primary_phone,
        'address' => $contact->address ? $contact->address->getLetterLine() : "",
        'comments' => isset($entry) && isset($entry->comments) ? $entry->comments : null
    );
}

?>

<tr>
    <td><?= $values['label'] ?></td>
    <td><?= $values['full_name'] ?></td>
    <td><?= $values['email'] ?></td>
    <td><?= $values['phone'] ?></td>
    <td><?= $values['address'] ?></td>
    <?php if($show_comments) {
        $comment = isset($entry->comment) ? $entry->comment : "";
        ?>
    <td>
        <textarea id="<?= $model_name ?>_comments"
                  name="<?= $model_name ?>[comments][]"
                  class="js-comment-field"
                  style="overflow: hidden; overflow-wrap: break-word;"><?= CHtml::encode($comment) ?></textarea>
    </td>
    <?php } ?>
    <?php if (isset($removable) && $removable) { ?>
        <input type="hidden" name="<?= $model_name ?>[contact_id][]" value="<?= $values['id'] ?>"/>
    <td>
        <i class="oe-i trash"></i>
    </td>
    <?php } ?>
</tr>

