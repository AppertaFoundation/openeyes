<?php



if (!isset($values)) {
    if (isset($entry) && isset($entry->contact)) {
        $contact = $entry->contact;
    }
    $values = array(
        'id' => $contact->id,
        'label' => $contact->label ? $contact->label->name : "",
        'full_name' => $contact->getFullName(),
        'email' => $contact ? $contact->email : "",
        'phone' => $contact->primary_phone,
        'address' => $contact->address ? $contact->address->getLetterLine() : "",
        'comments' => isset($entry) && isset($entry->comments) ? $entry->comments : null
    );
}

?>

<tr data-key="<?= $row_count; ?>">
    <td class="js-contact-label"><?= $values['label'] ?></td>
    <td><?= $values['full_name'] ?></td>
    <td><?= $values['email'] ?></td>
    <td><?= $values['phone'] ?></td>
    <td><?= $values['address'] ?></td>
    <?php if ($show_comments) {
        $comment = isset($entry->comment) ? $entry->comment : "";
        ?>
        <td>
    <span class="comment-group js-comment-container"
          id="<?= CHtml::getIdByName($field_prefix . '[comment_container]') ?>"
          style="<?php if (!$comment) :
                ?>display: none;<?php
                 endif; ?>"
          data-comment-button="#<?= CHtml::getIdByName($field_prefix . '[comments]') ?>_button">
                   <textarea id="<?= $model_name ?>_comments[]"
                             name="<?= $model_name ?>[comments][]"
                             class="cols-9 autosize js-comment-field"
                             style="overflow: hidden; overflow-wrap: break-word;"><?= CHtml::encode($comment) ?></textarea>
            <i class="oe-i remove-circle small-icon pad-left js-remove-add-comments"></i>
        </span>
            <button id="<?= CHtml::getIdByName($field_prefix . '[comments]') ?>_button"
                    class="button js-add-comments"
                    data-comment-container="#<?= CHtml::getIdByName($field_prefix . '[comment_container]') ?>"
                    type="button"
                    style="<?php if ($comment) :
                        ?>visibility: hidden;<?php
                           endif; ?>"
            >
                <i class="oe-i comments small-icon"></i>
            </button>
    </td>
    <?php } ?>
    <?php if (isset($removable) && $removable) { ?>
        <input type="hidden" name="<?= $model_name ?>[contact_id][]" value="<?= $values['id'] ?>"/>
    <td>
        <i class="oe-i trash"></i>
    </td>
    <?php } ?>
</tr>

