<?php
    $date = date_create_from_format('Y-m-d H:i:s', $data->last_modified_date);
?>
<div class="oe-hd-title">
    <ul class="dot-list">
        <li><?= $data->patient_name ?></li>
        <li><?= $data->hos_num ?></li>
    </ul>
</div>
<div class="oe-hd-actions">
    <small>updated at: &nbsp;</small>
    <b><?=$date->format('H:i')?></b>
    &nbsp;&nbsp;<?=$date->format('j M Y')?>
</div>
