<tr id="conflicts" class="cols-full error" style="font-style: italic; font-size: small;">
  <?php if (isset($patients)): ?>
    <td class="row field-row">
      <p>Duplicate patient detected.</p>
    </td>
    <td>
          <div class="row field-row">
            <div> <?php echo Patient::model()->getAttributeLabel('hos_num'); ?></div>
            <div>Name</div>
          </div>
        <?php foreach ($patients as $patient): ?>
          <div class="row field-row">
            <div><?php echo CHtml::link($patient->hos_num,
                Yii::app()->controller->createUrl('patient/view', array('id' => $patient->id)), array('target' => '_blank')); ?></div>
            <div>
              <?php echo CHtml::link($patient->getFullName(),
                Yii::app()->controller->createUrl('patient/view', array('id' => $patient->id)), array('target' => '_blank')); ?>
            </div>
          </div>
        <?php endforeach; ?>
      </ul>
    </td>
  <?php else: ?>
    <p>No conflicts found.</p>
  <?php endif; ?>
</tr>
