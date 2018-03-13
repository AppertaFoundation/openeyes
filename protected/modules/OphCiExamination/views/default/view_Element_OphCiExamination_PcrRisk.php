
<div class="element-data element-eyes element-fields">
    <?php
    $pcr = new PcrRisk();
    foreach (array('right', 'left') as $side):
        $opposite = ($side === 'right') ? 'left' : 'right';
        $activeClass = ($element->{'has' . ucfirst($side)}()) ? 'active' : 'inactive';
        if (!$element->{$side . '_glaucoma'}) {
            continue;
        }
        ?>
      <div class="element-eye <?= $side ?>-eye column <?= $opposite ?> side <?= $activeClass ?>"
           data-side="<?= $side ?>">
        <table>
          <tbody>
          <tr style="text-align: left;">
            <td>
              <div class="data-label"><?php echo $element->getAttributeLabel($side . '_glaucoma') ?>:</div>
            </td>
            <td>
              <div class="data-value"><?php echo $pcr->displayValues($element->{$side . '_glaucoma'},
                      'glaucoma') ?></div>
            </td>
          </tr>
          <tr>
            <td>
              <div class="data-label"><?php echo $element->getAttributeLabel($side . '_pxf') ?>:</div>
            </td>
            <td>
              <div class="data-value"><?php echo $pcr->displayValues($element->{$side . '_pxf'}) ?></div>
            </td>
          </tr>
          <tr>
            <td>
              <div class="data-label"><?php echo $element->getAttributeLabel($side . '_diabetic') ?>:</div>
            </td>
            <td>
              <div class="data-value"><?php echo $pcr->displayValues($element->{$side . '_diabetic'},
                      'diabetic') ?></div>
            </td>
          </tr>
          <tr>
            <td>
              <div class="data-label"><?php echo $element->getAttributeLabel($side . '_pupil_size') ?>:</div>
            </td>
            <td>
              <div class="data-value"><?php echo $element->{$side . '_pupil_size'} ?></div>
            </td>
          </tr>
          <tr>
            <td>
              <div class="data-label"><?php echo $element->getAttributeLabel($side . '_no_fundal_view') ?>:</div>
            </td>
            <td>
              <div class="data-value"><?php echo $pcr->displayValues($element->{$side . '_no_fundal_view'}) ?></div>
            </td>
          </tr>
          <tr>
            <td>
              <div class="data-label"><?php echo $element->getAttributeLabel($side . '_axial_length_group') ?>:</div>
            </td>
            <td>
              <div class="data-value"><?php echo $pcr->displayValues($element->{$side . '_axial_length_group'},
                      'axial') ?></div>
            </td>
          </tr>
          <tr>
            <td>
              <div class="data-label"><?php echo $element->getAttributeLabel($side . '_brunescent_white_cataract') ?>:
              </div>
            </td>
            <td>
              <div
                  class="data-value"><?php echo $pcr->displayValues($element->{$side . '_brunescent_white_cataract'}) ?></div>
            </td>
          </tr>
          <tr>
            <td>
              <div class="data-label"><?php echo $element->getAttributeLabel($side . '_alpha_receptor_blocker') ?>:
              </div>
            </td>
            <td>
              <div
                  class="data-value"><?php echo $pcr->displayValues($element->{$side . '_alpha_receptor_blocker'}) ?></div>
            </td>
          </tr>
          <tr>
            <td>
              <div class="data-label"><?php echo $element->getAttributeLabel($side . '_doctor_grade_id') ?>:</div>
            </td>
            <td>
              <div class="data-value">
                  <?php if ($element->{$side . '_doctor'}) {
                      echo $element->{$side . '_doctor'}->grade;
                  } ?>
              </div>
            </td>
          </tr>
          <tr>
            <td>
              <div class="data-label"><?php echo $element->getAttributeLabel($side . '_can_lie_flat') ?>:</div>
            </td>
            <td>
              <div class="data-value"><?php echo $pcr->displayValues($element->{$side . '_can_lie_flat'}) ?></div>
            </td>
          </tr>
          <tr>
            <td class=" pcr-risk-div">
              <label class="<?php echo $element->pcrRiskColour($side) ?>">
                PCR Risk <span
                    class="pcr-span "><?php echo ($element->{$side . '_pcr_risk'}) ? $element->{$side . '_pcr_risk'} : 'N/A' ?></span>
                %
              </label>
            </td>
            <td>
              <label>
                Excess risk compared to average eye <span
                    class="pcr-erisk"><?php echo $element->{$side . '_excess_risk'} ?></span> times
              </label>
            </td>
          </tr>
          </tbody>
        </table>
      </div>
    <?php endforeach; ?>
</div>