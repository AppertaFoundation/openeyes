<div class="element-data element-eyes">
    <?php
    $pcr = new PcrRisk();
    foreach (['left' => 'right', 'right' => 'left'] as $page_side => $eye_side) { ?>
      <div class="js-element-eye <?= $eye_side ?>-eye <?= $page_side ?>"
           data-side="<?= $eye_side ?>">
          <?php if ($element->{$eye_side . '_glaucoma'}) { ?>
            <div class="listview-expand-collapse">
              <div class="cols-11">
                <table>
                  <tbody>
                  <tr>
                    <td class="pcr-risk-div">
                      <span class="highlighter large-text">PCR Risk
                        <span class="pcr-span"><?php echo $element->{$eye_side . '_pcr_risk'} ?: 'N/A' ?></span>%
                      </span>
                        <span title="<?= ($element->{$eye_side . '_doctor'}->grade ?: 'N/A')?>" class="fade">&nbsp;<?= '(' . ($element->{$eye_side . '_doctor'}->short_name ?: 'N/A') . ')' ?></span>
                    </td>
                    <td>
                      <span>
                      Risk compared to average eye
                          <span class="pcr-erisk highlighter large-text">
                              <?php echo ($element->{$eye_side . '_excess_risk'} ? 'x'.$element->{$eye_side . '_excess_risk'} : 'N/A') ?>
                          </span>
                      </span>
                    </td>
                  </tr>
                  </tbody>
                </table>

                <div id="js-listview-pcr-risk-<?= $page_side ?>-full" style="display: none;">
                  <hr class="divider">
                  <table>
                    <tbody>
                    <tr style="text-align: left;">
                      <td>
                        <div class="data-label">
                            <?php echo $element->getAttributeLabel($eye_side . '_glaucoma') ?>:
                        </div>
                      </td>
                      <td>
                        <div class="data-value">
                            <?php echo $pcr->displayValues($element->{$eye_side . '_glaucoma'}, 'glaucoma') ?>
                        </div>
                      </td>
                    </tr>
                    <tr>
                      <td>
                        <div class="data-label">
                            <?php echo $element->getAttributeLabel($eye_side . '_pxf') ?>:
                        </div>
                      </td>
                      <td>
                        <div class="data-value">
                            <?php echo $pcr->displayValues($element->{$eye_side . '_pxf'}) ?>
                        </div>
                      </td>
                    </tr>
                    <tr>
                      <td>
                        <div class="data-label">
                            <?php echo $element->getAttributeLabel($eye_side . '_diabetic') ?>:
                        </div>
                      </td>
                      <td>
                        <div class="data-value">
                            <?php echo $pcr->displayValues($element->{$eye_side . '_diabetic'}, 'diabetic') ?>
                        </div>
                      </td>
                    </tr>
                    <tr>
                      <td>
                        <div class="data-label">
                            <?php echo $element->getAttributeLabel($eye_side . '_pupil_size') ?>:
                        </div>
                      </td>
                      <td>
                        <div class="data-value">
                            <?php echo $element->{$eye_side . '_pupil_size'} ?>
                        </div>
                      </td>
                    </tr>
                    <tr>
                      <td>
                        <div class="data-label">
                            <?php echo $element->getAttributeLabel($eye_side . '_no_fundal_view') ?>:
                        </div>
                      </td>
                      <td>
                        <div class="data-value">
                            <?php echo $pcr->displayValues($element->{$eye_side . '_no_fundal_view'}) ?>
                        </div>
                      </td>
                    </tr>
                    <tr>
                      <td>
                        <div class="data-label">
                            <?php echo $element->getAttributeLabel($eye_side . '_axial_length_group') ?>:
                        </div>
                      </td>
                      <td>
                        <div class="data-value">
                            <?php echo $pcr->displayValues($element->{$eye_side . '_axial_length_group'}, 'axial') ?>
                        </div>
                      </td>
                    </tr>
                    <tr>
                      <td>
                        <div
                            class="data-label"><?php echo $element->getAttributeLabel($eye_side . '_brunescent_white_cataract') ?>
                          :
                        </div>
                      </td>
                      <td>
                        <div class="data-value">
                            <?php echo $pcr->displayValues($element->{$eye_side . '_brunescent_white_cataract'}) ?>
                        </div>
                      </td>
                    </tr>
                    <tr>
                      <td>
                        <div class="data-label">
                            <?php echo $element->getAttributeLabel($eye_side . '_alpha_receptor_blocker') ?>:
                        </div>
                      </td>
                      <td>
                        <div class="data-value">
                            <?php echo $pcr->displayValues($element->{$eye_side . '_alpha_receptor_blocker'}) ?>
                        </div>
                      </td>
                    </tr>
                    <tr>
                      <td>
                        <div class="data-label">
                            <?php echo $element->getAttributeLabel($eye_side . '_doctor_grade_id') ?>:
                        </div>
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
                        <div class="data-label"><?php echo $element->getAttributeLabel($eye_side . '_can_lie_flat') ?>:
                        </div>
                      </td>
                      <td>
                        <div
                            class="data-value"><?php echo $pcr->displayValues($element->{$eye_side . '_can_lie_flat'}) ?></div>
                      </td>
                    </tr>
                    </tbody>
                  </table>
                </div>
              </div>
                <?php if ($page_side === 'right') { ?>
                  <div class="expand-collapse-icon-btn">
                    <i class="oe-i small js-pcr-risk-expand-btn expand" data-list="pcr-risk"></i>
                  </div>
                <?php } ?>
            </div>
            <?php } else { ?>
            Not recorded
            <?php } ?>
      </div>
    <?php } ?>
</div>

<script>
  $(function () {
    $('.js-pcr-risk-expand-btn').click(function () {
      var expand = $(this).hasClass('expand');
      $(this).toggleClass('collapse expand');
      $('#js-listview-pcr-risk-left-full, #js-listview-pcr-risk-right-full').toggle(expand);
    });
  });
</script>
