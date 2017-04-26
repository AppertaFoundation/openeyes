<?php
/**
 * Created by Mike Smith <mike.smith@camc-ltd.co.uk>.
 */

?>
<p class="family-history-status-none" <?php if (!$element->no_family_history_date) { echo 'style="display: none;"'; }?>>Patient has no known family history</p>

<p class="family-history-status-unknown"  <?php if (!empty($element->entries) || $element->no_family_history_date) { echo 'style="display: none;"'; }?>>Patient family history is unknown</p>

<table id="currentFamilyHistory" class="plain patient-data" <?php if (empty($element->entries)) { echo 'style="display: none;"'; }?>>
    <thead>
    <tr>
        <th>Relative</th>
        <th>Side</th>
        <th>Condition</th>
        <th>Comments</th>
        <?php if ($this->checkAccess('OprnEditFamilyHistory')) { ?><th>Actions</th><?php } ?>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($element->entries as $history) {?>
        <tr>
            <td class="relative" data-relativeId="<?= $history->relative_id ?>"><?= $history->displayrelative; ?></td>
            <td class="side"><?= $history->side ?></td>
            <td class="condition" data-conditionId="<?= $history->condition_id ?>"><?= $history->displaycondition; ?></td>
            <td class="comments"><?= CHtml::encode($history->comments)?></td>
            <?php if ($this->checkAccess('OprnEditFamilyHistory')): ?>
                <td>
                    <a href="#" class="editFamilyHistory" rel="<?= $history->id ?>">Edit</a>&nbsp;&nbsp;
                    <a href="#" class="removeFamilyHistory" rel="<?= $history->id ?>">Remove</a>
                </td>
            <?php endif ?>
        </tr>
    <?php }?>
    </tbody>
</table>
