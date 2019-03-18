<div class="data-group">
  <table>
    <colgroup>
      <col>
      <col class="cols-6">
    </colgroup>
    <tbody>
    <?php foreach ($disorder_section->disorders as $disorder) {
        $field_base_name = CHtml::modelName($element) . "[{$side}_disorders][{$disorder->id}]"; ?>
      <tr>
        <td>
          <?php echo $disorder->name; ?>
        </td>
        <td>
          <label class="inline highlight">
              <?=\CHtml::radioButton(
                  $field_base_name . "[affected]",
                  $element->hasCviDisorderForSide($disorder, $side),
                  array(
                      'id' => $field_base_name . '_affected_1',
                      'value' => 1,
                      'class' => 'affected-selector',
                  )
              ) ?>
            Yes
          </label>
          <label class="inline highlight">
              <?=\CHtml::radioButton(
                  $field_base_name . "[affected]",
                  !$element->hasCviDisorderForSide($disorder, $side),
                  array(
                      'id' => $field_base_name . '_affected_0',
                      'value' => 0,
                      'class' => 'affected-selector',
                  )
              ) ?>
            No
          </label>
          <label class="inline">
              <?=\CHtml::checkBox(
                  $field_base_name . "[main_cause]",
                  $element->isCviDisorderMainCauseForSide($disorder, $side),
                  array('class' => 'disorder-main-cause')); ?>
            Main cause
          </label>
        </td>
      </tr>
    <?php } ?>
    </tbody>
  </table>
</div>
