<?php
?>
<table class="element-data label-values last-left cols-6">
  <tbody>
  <tr>
      <td><div class="data-label">Time:</div></td>
      <td><div class="data-value"><?= CHtml::encode($element->time) ?></div></td>
  </tr>
  <tr>
      <td><div class="data-label">Result:</div></td>
      <td><div class="data-value"><?= CHtml::encode($element->result) . " " .
            ($element->resultType->show_units ? CHtml::encode($element->unit) : "") ?></div></td>
      <td>
          <span class="large-text highlighter orange js-lab-result-warning"
                style="<?php
              if (isset($element->result)&& $element->resultType->normal_min && $element->resultType->normal_min &&
                 ($element->result > $element->resultType->normal_max || $element->result < $element->resultType->normal_min)) {
                      echo "display:block";
                  } else {
                      echo "display:none";
                  } ?>">
          <?php if ($element->resultType->custom_warning_message) {
              echo $element->resultType->custom_warning_message;
           } else { ?>
              The value is outside the normal range. Normal min: <?= $element->resultType->normal_min ?> Normal max: <?= $element->resultType->normal_max ?>
          <?php } ?>
          </span>
      </td>
  </tr>
  <tr>
      <td><div class="data-label">Comment</div></td>
      <td><div class="data-value"><?= CHtml::encode($element->comment) ?></div></td>
  </tr>
  </tbody>
</table>
