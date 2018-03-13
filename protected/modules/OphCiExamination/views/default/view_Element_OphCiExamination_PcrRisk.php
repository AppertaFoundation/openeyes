
<div class="element-data element-eyes element-fields">
    <?php
    $pcr = new PcrRisk();
    foreach (array('right', 'left') as $eye_side):
        $page_side = ($eye_side === 'right') ? 'left' : 'right';
        ?>
      <div class="element-eye <?= $eye_side ?>-eye column <?= $page_side ?> side "
           data-side="<?= $eye_side ?>">
    <?php
        if ($element->{$eye_side . '_glaucoma'}) { ?>
          <table>
            <tbody>
            <tr style="text-align: left;">
              <td>
                <div class="data-label"><?php echo $element->getAttributeLabel($eye_side . '_glaucoma') ?>:</div>
              </td>
              <td>
                <div class="data-value"><?php echo $pcr->displayValues($element->{$eye_side . '_glaucoma'},
                        'glaucoma') ?></div>
              </td>
            </tr>
            <tr>
              <td>
                <div class="data-label"><?php echo $element->getAttributeLabel($eye_side . '_pxf') ?>:</div>
              </td>
              <td>
                <div class="data-value"><?php echo $pcr->displayValues($element->{$eye_side . '_pxf'}) ?></div>
              </td>
            </tr>
            <tr>
              <td>
                <div class="data-label"><?php echo $element->getAttributeLabel($eye_side . '_diabetic') ?>:</div>
              </td>
              <td>
                <div class="data-value"><?php echo $pcr->displayValues($element->{$eye_side . '_diabetic'},
                        'diabetic') ?></div>
              </td>
            </tr>
            <tr>
              <td>
                <div class="data-label"><?php echo $element->getAttributeLabel($eye_side . '_pupil_size') ?>:</div>
              </td>
              <td>
                <div class="data-value"><?php echo $element->{$eye_side . '_pupil_size'} ?></div>
              </td>
            </tr>
            <tr>
              <td>
                <div class="data-label"><?php echo $element->getAttributeLabel($eye_side . '_no_fundal_view') ?>:</div>
              </td>
              <td>
                <div class="data-value"><?php echo $pcr->displayValues($element->{$eye_side . '_no_fundal_view'}) ?></div>
              </td>
            </tr>
            <tr>
              <td>
                <div class="data-label"><?php echo $element->getAttributeLabel($eye_side . '_axial_length_group') ?>:</div>
              </td>
              <td>
                <div class="data-value"><?php echo $pcr->displayValues($element->{$eye_side . '_axial_length_group'},
                        'axial') ?></div>
              </td>
            </tr>
            <tr>
              <td>
                <div class="data-label"><?php echo $element->getAttributeLabel($eye_side . '_brunescent_white_cataract') ?>:
                </div>
              </td>
              <td>
                <div
                    class="data-value"><?php echo $pcr->displayValues($element->{$eye_side . '_brunescent_white_cataract'}) ?></div>
              </td>
            </tr>
            <tr>
              <td>
                <div class="data-label"><?php echo $element->getAttributeLabel($eye_side . '_alpha_receptor_blocker') ?>:
                </div>
              </td>
              <td>
                <div
                    class="data-value"><?php echo $pcr->displayValues($element->{$eye_side . '_alpha_receptor_blocker'}) ?></div>
              </td>
            </tr>
            <tr>
              <td>
                <div class="data-label"><?php echo $element->getAttributeLabel($eye_side . '_doctor_grade_id') ?>:</div>
              </td>
              <td>
                <div class="data-value">
                    <?php if ($element->{$eye_side . '_doctor'}) {
                        echo $element->{$eye_side . '_doctor'}->grade;
                    } ?>
                </div>
              </td>
            </tr>
            <tr>
              <td>
                <div class="data-label"><?php echo $element->getAttributeLabel($eye_side . '_can_lie_flat') ?>:</div>
              </td>
              <td>
                <div class="data-value"><?php echo $pcr->displayValues($element->{$eye_side . '_can_lie_flat'}) ?></div>
              </td>
            </tr>
            <tr>
              <td class=" pcr-risk-div">
                <label class="<?php echo $element->pcrRiskColour($eye_side) ?>">
                  PCR Risk <span
                      class="pcr-span "><?php echo ($element->{$eye_side . '_pcr_risk'}) ? $element->{$eye_side . '_pcr_risk'} : 'N/A' ?></span>
                  %
                </label>
              </td>
              <td>
                <label>
                  Excess risk compared to average eye <span
                      class="pcr-erisk"><?php echo $element->{$eye_side . '_excess_risk'} ?></span> times
                </label>
              </td>
            </tr>
            </tbody>
          </table>
        <?php } else { ?>
            Not recorded
        <?php } ?>
      </div>
    <?php endforeach; ?>
</div>