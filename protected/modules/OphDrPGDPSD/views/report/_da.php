<?php foreach ($report->administrations as $administration) {?>
<div>
    <?=$administration['title']?> <?=$administration['status']?>
    <h3><?=$administration['creation']?></h3>
</div>
<table class="standard">
    <thead>
        <tr>
            <?php foreach ($report->getColumns() as $column) {?>
                <th><?= $column?></th>
            <?php }?>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($administration['assigned_meds'] as $med_info) {?>
        <tr>
            <?php foreach ($report->getColumns() as $column) {?>
                <td><?=$med_info[$column];?></td>
            <?php }?>

        </tr>
        <?php } ?>
    </tbody>
</table>
<hr/>
<?php }?>