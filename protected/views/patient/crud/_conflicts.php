<tr id="conflicts" class="cols-full alert-box error" style="font-style: italic; font-size: small;">
  <?php if (isset($patients)) : ?>
    <td class="row field-row">
      <p>Duplicate patient detected.</p>
    </td>
    <td>
      <table class="last-left">
        <thead>
        <tr>
          <th> <?php echo Patient::model()->getAttributeLabel('hos_num'); ?></th>
          <th>Name</th>
          <th>Born</th>
        </tr>
        </thead>
        <tbody>
        <tr>
          <?php foreach ($patients as $patient) : ?>
            <tr>
              <td><?php echo CHtml::link(
                  $patient->hos_num,
                  Yii::app()->controller->createUrl('patient/view', array('id' => $patient->id)),
                  array('target' => '_blank')
              ); ?></td>
              <td>
                <?php echo CHtml::link(
                    $patient->getFullName(),
                    Yii::app()->controller->createUrl('patient/view', array('id' => $patient->id)),
                    array('target' => '_blank')
                ); ?>
              </td>
              <td>
                  <?php echo CHtml::link(
                      date('d/m/Y', strtotime($patient->getDOB())),
                      Yii::app()->controller->createUrl('patient/view', array('id' => $patient->id)),
                      array('target' => '_blank')
                  ); ?>
              </td>
            </tr>
            <?php endforeach; ?>
        </tr>
        </tbody>
      </table>
    </td>
    <?php else : ?>
    <p>No conflicts found.</p>
    <?php endif; ?>
</tr>
