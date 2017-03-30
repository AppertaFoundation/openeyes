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
        <div class="row data-row">
            <div class="large-6 column">
                <div
                    class="field-info"><b><?php echo CHtml::encode($element->getAttributeLabel('lens_id_'.$side)) ?></b>:
                </div>
            </div>
            <div class="large-6 column end">
                <div class="field-info iolDisplay"
                     id="lens_<?php echo $side ?>"><?php echo $element->{'lens_'.$side} ? $element->{'lens_'.$side}->display_name : 'None' ?></div>
            </div>
        </div>
        <div class="row data-row">
            <div class="large-6 column">
                <div class="field-info"><b><?php echo CHtml::encode($element->getAttributeLabel('formula_id_'.$side)) ?>&nbsp;Used</b>:
                </div>
            </div>
            <div class="large-6 column">
                <div class="field-info">
                    <?php  if(isset($data)){
                        foreach ($data as $k => $v) { 
                            echo $v->{'name'}; break; 
                        }
                    }?>&nbsp;</div>
            </div>
        </div>
        <div class="row field-row">
            <div class="large-6 column">
                <div class="field-info"><b>A constant</b>:</div>
            </div>
            <div class="large-6 column">
                <div class="field-info" id="acon_<?php echo $side ?>">
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

        ?></div>
            </div>
        </div>
        <div class="row data-row">
            <div class="large-6 column">
                <div
                    class="field-info"><b><?php echo CHtml::encode($element->getAttributeLabel('iol_power_'.$side)) ?></b>:</div>
            </div>
            <div class="large-6 column end">
                <div class="field-info iolDisplay"><?php echo CHtml::encode(number_format((float) $element->{'iol_power_'.$side}, 2, '.', '')) ?></div>
            </div>
        </div>
        <div class="row data-row">
            <div class="large-6 column">
                <div
                    class="field-info"><b><?php echo CHtml::encode($element->getAttributeLabel('predicted_refraction_'.$side)) ?></b>:</div>
            </div>
            <div class="large-6 column end">
                <div class="field-info"
                     id="tr_<?php echo $side ?>"><?php if (($element->{'predicted_refraction_' . $side}) > 0) { echo '+'; } echo CHtml::encode($element->{'predicted_refraction_'.$side}) ?></div>
            </div>
        </div>
        <?php

    }
    ?>
</div>
