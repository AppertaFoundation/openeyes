<div class="element-data">
  <?php
  if (empty($element->{'lens_' . $side})) {
        ?>
      <div class="cols-12 column">
        <div class="field-info">
            <?php echo 'No selection has been made - click the "Choose Lens" button to select a lens.'; ?>
        </div>
      </div>
    <?php } else {
      if ($this->selectionValues) {
          $data = OphInBiometry_Calculation_Formula::Model()->findAllByAttributes(
              array(
                  'id' => $this->selectionValues[0]->{"formula_id_$side"},
              )
          );
      } ?>
        <div class="data-group">
            <table class="cols-11 large-text borders">
                <colgroup>
                    <col class="cols-3">
                    <col class="cols-8">
                    <col class="cols-1">
                </colgroup>
                <tbody>
                <tr>
                    <td>
                        <div class="data-label"><?=\CHtml::encode($element->getAttributeLabel('lens_id_' . $side)) ?></div>
                    </td>
                    <td>
                        <div class="data-value"><?php echo $element->{'lens_' . $side} ? $element->{'lens_' . $side}->display_name : 'None' ?></div>
                    </td>
                    <td colspan="2"></td>
                    <td>
                        <?php $this->widget('EyeLateralityWidget', array('laterality' => $side)) ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div class="data-label"><?=\CHtml::encode($element->getAttributeLabel('formula_id_' . $side)) ?>
                            &nbsp;Used:
                        </div>
                    </td>
                    <td>
                        <div class="data-value"><?php if (isset($data)) {
                            foreach ($data as $k => $v) {
                                echo $v->{'name'};
                                break;
                            }
                                                } ?>
                        </div>
                    </td>
                    <td colspan="2"></td>
                    <td>
                        <?php $this->widget('EyeLateralityWidget', array('laterality' => $side)) ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div class="data-label"> A constant:</div>
                    </td>
                    <td>
                        <div class="data-value">
                            <?php
                            $aconst = 'None';
                            if ($this->is_auto && !$element->{"manually_overriden_" . $side}) {
                                if (isset($data[0])) {
                                    $iolrefValues = Element_OphInBiometry_IolRefValues::model()->findAllByAttributes(array('event_id' => $element->event->id));
                                    foreach ($iolrefValues as $iolrefData) {
                                        if ($iolrefData->lens_id == $element->{'lens_' . $side}->id && $iolrefData->formula_id == $data[0]->id) {
                                            $aconst = $this->formatAconst($iolrefData->constant);
                                        }
                                    }
                                }
                            } else {
                                $aconst = ($element->{'lens_' . $side}) ? $this->formatAconst($element->{'lens_' . $side}->acon) : 'None';
                            }
                            echo $aconst;
                            ?>
                        </div>
                    </td>
                    <td colspan="2"></td>
                    <td>
                        <?php $this->widget('EyeLateralityWidget', array('laterality' => $side)) ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div class="data-label">
                            <?=\CHtml::encode($element->getAttributeLabel('iol_power_' . $side)) ?>:
                        </div>
                    </td>
                    <td>
                        <div class="data-value">
                            <span class="large-text highlighter orange">
                                <?=\CHtml::encode(number_format((float)$element->{'iol_power_' . $side}, 2, '.', '')) ?>
                            </span>
                        </div>
                    </td>
                    <td colspan="2"></td>
                    <td>
                        <?php $this->widget('EyeLateralityWidget', array('laterality' => $side)) ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div class="data-label">
                            <?=\CHtml::encode($element->getAttributeLabel('predicted_refraction_' . $side)) ?>:
                        </div>
                    </td>
                    <td>
                        <div class="data-value">
                            <?php if (($element->{'predicted_refraction_' . $side}) > 0) {
                                echo '+';
                            }
                            echo CHtml::encode($element->{'predicted_refraction_' . $side}) ?>
                        </div>
                    </td>
                    <td colspan="2"></td>
                    <td>
                        <?php $this->widget('EyeLateralityWidget', array('laterality' => $side)) ?>
                    </td>
                </tr>
                </tbody>


            </table>
        </div>
        <?php
    }
    ?>
</div>
