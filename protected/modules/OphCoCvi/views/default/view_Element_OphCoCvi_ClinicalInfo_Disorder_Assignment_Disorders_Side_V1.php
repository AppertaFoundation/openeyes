<table>
    <thead>
        <td>Diagnosis</td>
        <td>Main Cause</td>
        <td>ICD 10 Code</td>
        <td>Right Eye</td>
        <td>Left Eye</td>
        <td>Both Eyes</td>
    </thead>
    <tbody>
    <?php
    foreach ($disorder_section->disorders as $disorder) {
        $main_cause = $element->isCviDisorderMainCauseForSide($disorder, 'right');
             ?>
            <tr>
                    <td><?php echo CHtml::encode($disorder->name); ?></td>
                    <td><?php echo ($main_cause) ? 'Yes' : 'No';?></td>
                    <td><?php echo CHtml::encode($disorder->code); ?></td>
                    <td><?php echo ($element->hasCviDisorderForSide($disorder, 'right')) ? 'Yes' : 'No'; ?></td>
                    <td><?php echo ($element->hasCviDisorderForSide($disorder, 'left')) ? 'Yes' : 'No'; ?></td>
                    <td><?php echo ($element->hasCviDisorderForSide($disorder, 'both')) ? 'Yes' : 'No'; ?></td>
            </tr>
        <?php
    }?>
    </tbody>
</table>