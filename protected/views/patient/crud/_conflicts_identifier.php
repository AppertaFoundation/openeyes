<tr id="conflicts" class="cols-full alert-box error" style="font-style: italic; font-size: small;">
    <?php if (isset($patients)): ?>
        <td class="row field-row">
            <p>Duplicate patient detected.</p>
        </td>
        <td>
            <table class="last-left">
                <thead>
                <tr>
                    <th> <?= Yii::app()->params['patient_identifiers'][$identifier_code ]['label']; ?></th>
                    <th>Name</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <?php foreach ($patients as $patient):
                            foreach ($patient->identifiers as $pid):
                                if($pid->code == $identifier_code): ?>
                <tr>
                    <td><?php echo CHtml::link($pid->value,
                            Yii::app()->controller->createUrl('patient/view', array('id' => $patient->id)), array('target' => '_blank')); ?></td>
                    <td>
                        <?php echo CHtml::link($patient->getFullName(),
                            Yii::app()->controller->createUrl('patient/view', array('id' => $patient->id)), array('target' => '_blank')); ?>
                    </td>
                    <td></td>
                </tr>
                <?php endif; ?>
                <?php endforeach; ?>
                <?php endforeach; ?>

                </tr>
                </tbody>
            </table>
        </td>
    <?php else: ?>
        <p>No conflicts found.</p>
    <?php endif; ?>
</tr>