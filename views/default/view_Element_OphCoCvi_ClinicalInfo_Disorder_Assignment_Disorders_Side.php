<table>
    <thead>
        <td>Disorder</td>
        <td>Status</td>
        <td>Main Cause</td>
    </thead>
    <tbody>
    <?php
    foreach (OEModule\OphCoCvi\models\OphCoCvi_ClinicalInfo_Disorder::model()
                 ->findAll('`active` = ? and section_id = ?', array(1, $disorder_section->id)) as $disorder) {
        $value = OEModule\OphCoCvi\models\Element_OphCoCvi_ClinicalInfo_Disorder_Assignment::model()->
        getDisorderAffectedStatus($disorder->id, $element->id, $side);
            $checkbox_value = OEModule\OphCoCvi\models\Element_OphCoCvi_ClinicalInfo_Disorder_Assignment::model()->
            getDisorderMainCause($disorder->id, $element->id, $side);
             ?>
            <tr>
                    <td><?php echo $disorder->name; ?></td>
                    <td><?php echo ($value == 1) ? 'Yes' : 'No'; ?></td>
                    <td><?php echo ($value == 1 && $checkbox_value == 1) ? 'Yes' : 'No';?></td>
            </tr>
        <?php
    }?>
    </tbody>
</table>