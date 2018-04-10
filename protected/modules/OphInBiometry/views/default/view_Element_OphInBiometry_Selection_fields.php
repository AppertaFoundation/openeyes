<div class="element-data">
    <?php
    if (empty($element->{'lens_'.$side})) {
        ?>
        <div class="row data-row">
            <div class="large-12 column">
                <div
                    class="field-info">
                    <?php
                    echo 'No selection has been made - use edit mode to select a lens.';
        ?>
                </div>
            </div>
        </div>
        <?php

    } else {
        if ($this->selectionValues) {
            $data = OphInBiometry_Calculation_Formula::Model()->findAllByAttributes(
                array(
                    'id' => $this->selectionValues[0]->{"formula_id_$side"},
                ));
        }
        ?>
        <div class="field-row">
            <table class="label-value last-left">
                <colgroup>
                    <col class="cols-6">
                </colgroup>
                <tbody>
                <tr>
                    <td>
                        <div class="data-label"><?php echo CHtml::encode($element->getAttributeLabel('lens_id_' . $side)) ?></div>
                    </td>
                    <td>
                        <div class="data-value"><?php echo $element->{'lens_' . $side} ? $element->{'lens_' . $side}->display_name : 'None' ?></div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div class="data-label"><?php echo CHtml::encode($element->getAttributeLabel('formula_id_'.$side)) ?>&nbsp;Used:</div>
                    </td>
                    <td>
                        <div class="data-value"><?php  if(isset($data)){
                            foreach ($data as $k => $v) {
                                echo $v->{'name'}; break;
                            }
                        }?>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div class="data-label"> A constant:</div>
                    </td>
                    <td>
                        <div class="data-value">
                            <?php
                            $aconst='None';
                            if ($this->is_auto) {
                                $iolrefValues = Element_OphInBiometry_IolRefValues::model()->findAllByAttributes(array('event_id' => $element->event->id));
                                foreach ($iolrefValues as $iolrefData) {
                                    if (isset($data)) {
                                        if ($iolrefData->lens_id == $element->{'lens_'.$side}->id && $iolrefData->formula_id == $data[0]->id) {
                                            $aconst = $this->formatAconst($iolrefData->constant);
                                        }
                                    }
                                }
                            } else {
                                $aconst = ($element->{'lens_'.$side}) ? $this->formatAconst($element->{'lens_'.$side}->acon) : 'None';
                            }
                            echo $aconst;
                            ?>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div class="data-label">
                            <?php echo CHtml::encode($element->getAttributeLabel('iol_power_'.$side)) ?>:
                        </div>
                    </td>
                    <td>
                        <div class="data-value">
                            <?php echo CHtml::encode(number_format((float) $element->{'iol_power_'.$side}, 2, '.', '')) ?>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div class="data-label">
                            <?php echo CHtml::encode($element->getAttributeLabel('predicted_refraction_'.$side)) ?>:
                        </div>
                    </td>
                    <td>
                        <div class="data-value">
                            <?php if (($element->{'predicted_refraction_' . $side}) > 0) { echo '+'; } echo CHtml::encode($element->{'predicted_refraction_'.$side}) ?>
                        </div>
                    </td>
                </tr>
                </tbody>


            </table>
        </div>
    <?php

    }
    ?>
</div>
