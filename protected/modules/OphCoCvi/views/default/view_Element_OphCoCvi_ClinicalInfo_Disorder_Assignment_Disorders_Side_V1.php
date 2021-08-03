<table class="standard">
    <colgroup>
        <col class="cols-3">
        <col class="cols-2">
        <col class="cols-2">
        <col class="cols-2">
        <col class="cols-2">
        <col class="cols-2">
    </colgroup>
    <thead>
        <th>Diagnosis</th>
        <th>Main Cause</th>
        <th>ICD 10 Code</th>
        <th>Right Eye</th>
        <th>Left Eye</th>
        <th>Both Eyes</th>
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
    <tr><td colspan="10">
            <hr class="divider"></td></tr>
    </tbody>
</table>