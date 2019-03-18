<?php
$form = $this->beginWidget(
    'CActiveForm',
    array(
        'id' => 'import-form',
        'action' => Yii::app()->createURL('csv/import', array('context' => $context)),
        'enableAjaxValidation' => false,
        'htmlOptions' => array('enctype' => 'multipart/form-data'),
    )
);
if (!empty($table)): ?>
  <div style="overflow: auto">
    <table>
      <tr>
          <?php foreach (array_keys($table[0]) as $header): ?>
            <th>
                <?php echo $header; ?>
            </th>
          <?php endforeach; ?>
      </tr>
        <?php foreach ($table as $row): ?>
          <tr>
              <?php foreach ($row as $column): ?>
                <td>
                    <?php echo $column ?>
                </td>
              <?php endforeach; ?>
          </tr>
        <?php endforeach; ?>
    </table>
  </div>
<?php endif;
echo CHtml::submitButton('Import');
$this->endWidget();
?>
