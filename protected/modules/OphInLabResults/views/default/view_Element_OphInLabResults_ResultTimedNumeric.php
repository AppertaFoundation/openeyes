<?php
?>
<table class="element-data label-values cols-6" style="empty-cells: show">
  <tbody class="cols-6">
  <tr class="cols-6">
    <div>
      <td class="cols-1"><div class="data-label">Time:</div></td>
      <td class="cols-1"><div class="data-value"><?= CHtml::encode($element->time) ?></div></td>
    </div>
    <div><td class="cols-4">&nbsp;</td></div>
  </tr>
  <tr class="cols-6">
    <div>
      <td class="cols-1"><div class="data-label">Result:</div></td>
      <td class="cols-1"><div class="data-value"><?= CHtml::encode($element->result) ?></div></td>
    </div>
    <div><td class="cols-4">&nbsp;</td></div>
  </tr>
  </tbody>
</table>
<table class="element-data label-values cols-6">
  <tbody>
  <tr/>
  <tr>
    <td><div class="data-label">Comment</div></td>
    <td><div class="data-value"><?= CHtml::encode($element->comment) ?></div></td>
  </tr>
  </tbody>
</table>