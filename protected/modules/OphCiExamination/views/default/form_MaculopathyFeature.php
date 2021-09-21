<?php
?>
<tr data-key="<?= $key ?>">
    <td>
        <?= $maculopathy_feature->feature->grade ?>
    </td>
    <td>
        <?= $maculopathy_feature->feature->name ?>
        <input type="hidden" name="<?= $name_stub ?>[<?= $key ?>][id]" value="<?= $maculopathy_feature->id ?>"/>
        <input type="hidden" name="<?= $name_stub ?>[<?= $key ?>][feature_id]" value="<?= $maculopathy_feature->feature_id ?>"/>
    </td>
    <td>
        <i class="oe-i trash"></i>
    </td>
</tr>
