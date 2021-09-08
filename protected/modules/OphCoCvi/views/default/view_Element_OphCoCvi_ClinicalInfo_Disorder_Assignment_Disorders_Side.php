<table>
    <thead>
        <td>Disorder</td>
        <td>Status</td>
        <td>Main Cause</td>
    </thead>
    <tbody>
    <?php
    foreach ($disorder_section->disorders as $disorder) {
        $affected = $element->hasCviDisorderForSide($disorder, $side);
        $main_cause = $element->isCviDisorderMainCauseForSide($disorder, $side);
        ?>
            <tr>
                    <td><?php echo CHtml::encode($disorder->name); ?></td>
                    <td><?php echo ($affected) ? 'Yes' : 'No'; ?></td>
                    <td><?php echo ($main_cause) ? 'Yes' : 'No';?></td>
            </tr>
        <?php
    }?>
    </tbody>
</table>