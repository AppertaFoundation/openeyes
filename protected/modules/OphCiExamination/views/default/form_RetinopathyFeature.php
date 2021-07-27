<?php
?>
<tr data-key="<?= $key ?>">
    <td>
        <?= $retinopathy_feature->feature->grade ?>
    </td>
    <td>
        <?= $retinopathy_feature->feature->name ?>
        <input type="hidden" name="<?= $name_stub ?>[<?= $key ?>][id]" value="<?= $retinopathy_feature->id ?>"/>
        <input type="hidden" name="<?= $name_stub ?>[<?= $key ?>][feature_id]" value="<?= $retinopathy_feature->feature_id ?>"/>
        <input type="hidden" name="<?= $name_stub ?>[<?= $key ?>][feature_count]" value="<?= $retinopathy_feature->feature_count ?>"/>
    </td>
    <td>
        <i class="oe-i trash"></i>
    </td>
</tr>
