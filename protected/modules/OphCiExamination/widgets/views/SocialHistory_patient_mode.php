<?php if (!$element) { ?>
<p>No Social History recorded.</p>
<?php } else { ?>
    <table class="plain patient-data">
        <thead>
        <tr>
            <th>Social History</th>
            <th>Status</th>
        </tr>
        </thead>
        <tbody>
        <?php if ($element->occupation) { ?>
            <tr>
                <td><?= CHtml::encode($element->getAttributeLabel('occupation_id')) ?></td>
                <td><?php echo CHtml::encode($element->getDisplayOccupation())?></td>
            </tr>
        <?php }
         if ($element->driving_statuses) { ?>
            <tr>
                <td class="driving_statuses"><?= CHtml::encode($element->getAttributeLabel('driving_statuses')) ?></td>
                <td>
                    <?php foreach ($element->driving_statuses as $item) {?>
                        <?php echo $item->name?><br/>
                    <?php }?>
                </td>
            </tr>
        <?php }
        if ($element->smoking_status) { ?>
            <tr>
                <td><?= CHtml::encode($element->getAttributeLabel('smoking_status_id')) ?></td>
                <td><?php echo CHtml::encode($element->smoking_status->name)?></td>
            </tr>
        <?php }
        if ($element->accommodation) { ?>
            <tr>
                <td><?= CHtml::encode($element->getAttributeLabel('accommodation_id')) ?></td>
                <td><?php echo CHtml::encode($element->accommodation->name)?></td>
            </tr>
        <?php }
        if ($element->comments) { ?>
            <tr>
                <td><?= CHtml::encode($element->getAttributeLabel('comments')) ?></td>
                <td><?php echo CHtml::encode($element->comments)?></td>
            </tr>
        <?php }
        if (isset($element->carer)) { ?>
            <tr>
                <td><?= CHtml::encode($element->getAttributeLabel('carer_id')) ?></td>
                <td><?php echo CHtml::encode($element->carer->name)?></td>
            </tr>
        <?php }
        if (isset($element->alcohol_intake)) { ?>
            <tr>
                <td><?= CHtml::encode($element->getAttributeLabel('alcohol_intake')) ?></td>
                <td><?php echo CHtml::encode($element->alcohol_intake)?> units/week</td>
            </tr>
        <?php }
        if (isset($element->substance_misuse)) { ?>
            <tr>
                <td><?= CHtml::encode($element->getAttributeLabel('substance_misuse')) ?></td>
                <td><?php echo CHtml::encode($element->substance_misuse->name)?></td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
<?php } ?>
