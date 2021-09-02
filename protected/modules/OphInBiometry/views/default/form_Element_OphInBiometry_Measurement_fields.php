<div class="data-group">
    <table class="cols-11 last-left">
        <colgroup>
            <col class="cols-3">
            <col class="cols-3">
            <col class="cols-2">
            <col class="cols-4">
        </colgroup>
        <thead>
        <tr>
          <th colspan="4">
              <?php if ($side == 'right') { ?>
                    <?php echo $element->getAttributeLabel($side . 'Eye') ?>
              <?php } ?>
                <?php $this->widget('EyeLateralityWidget', array('laterality' => $side)) ?>
                <?php if ($side == 'left') { ?>
                    <?php echo $element->getAttributeLabel($side . 'Eye') ?>
                <?php } ?>
          </th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td><?php echo $element->getAttributeLabel('AL') ?></td>
            <td>
                <?php
                if ($this->is_auto) {
                    echo '<span class="readonly-box">' . $element->{"axial_length_$side"} . '</span><span class="field-info">&nbsp;mm</span>';
                } else {
                    ?>
                    <input type="text" id="Element_OphInBiometry_Measurement_axial_length_<?php echo $side; ?>"
                           class="cols-6"
                           name="Element_OphInBiometry_Measurement[axial_length_<?php echo $side; ?>]"
                           placeholder='0.00'
                           value="<?php echo $element->{"axial_length_$side"} ?>">mm</input>
                    <?php
                }
                ?>
            </td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td>SNR</td>
            <td>
                <?php
                if ($this->is_auto) {
                    if (!$element->{"al_modified_$side"}) {
                        if ($this->getAutoBiometryEventData($this->event->id)[0]->is700()) {
                            echo '<span class="field-value">N/A</span>';
                        } else {
                            echo '<span class="readonly-box">' . $element->{"snr_$side"} . '</span>';
                        }
                    } else {
                        echo '<span class="field-value">* AL entered manually</span>';
                    }
                } else {
                    ?>
                    <input type="text" id="Element_OphInBiometry_Measurement_snr_<?php echo $side; ?>"
                           name="Element_OphInBiometry_Measurement[snr_<?php echo $side; ?>]"
                           placeholder='0.00' class="cols-6"
                           value="<?php echo $element->{"snr_$side"} ?>">
                    <?php
                }
                ?>
            </td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td>K1:</td>
            <td>
                <?php
                if ($this->is_auto) {
                    echo '<span class="readonly-box">' . $element->{"k1_$side"} . '</span><span class="field-info">D</span>';
                } else {
                    ?>
                    <input type="text" id="Element_OphInBiometry_Measurement_k1_<?php echo $side; ?>"
                           name="Element_OphInBiometry_Measurement[k1_<?php echo $side; ?>]"
                           placeholder='0.00' class="cols-6"
                           value="<?php echo $element->{"k1_$side"} ?>">D</input>
                    <?php
                }
                ?>

            </td>
            <td>
                <?php
                if (!$element->{"k_modified_$side"}) {
                    ?>
                    <span class="field-info">@</span>
                    <?php
                } else { ?>
                    <span class="field-info"><b>*</b></span>
                    <?php
                } ?>
            </td>
            <td>
                <?php
                if ($this->is_auto) {
                    if (!$element->{"k_modified_$side"}) {
                        echo '<span class="readonly-box">' . $element->{"k1_axis_$side"} . '</span>&nbsp;&deg;';
                    } else {
                        echo '&nbsp;';
                    }
                } else {
                    ?>
                    <input type="text" id="Element_OphInBiometry_Measurement_k1_axis_<?php echo $side; ?>"
                           name="Element_OphInBiometry_Measurement[k1_axis_<?php echo $side; ?>]"
                           placeholder='0.00' class="cols-6"
                           value="<?php echo $element->{"k1_axis_$side"} ?>">&deg;
                    <?php
                }
                ?>
            </td>
        </tr>
        <tr>
            <td>&Delta;K:</td>
            <td>
                <?php
                if ($this->is_auto) {
                    echo '<span class="readonly-box">';
                    if (($element->{'delta_k_' . $side}) > 0) {
                        echo '+';
                    }
                    echo $element->{"delta_k_$side"} . '</span><span class="field-info">D</span>';
                } else {
                    ?>
                    <input type="text" disabled
                           id="input_Element_OphInBiometry_Measurement_delta_k_<?php echo $side; ?>"
                           name="input_Element_OphInBiometry_Measurement[delta_k_<?php echo $side; ?>]"
                           placeholder='0.00' class="cols-6"
                           value="<?php echo $element->{"delta_k_$side"} ?>"><span class="field-info">D</span>
                    <input type="hidden" id="Element_OphInBiometry_Measurement_delta_k_<?php echo $side; ?>"
                           name="Element_OphInBiometry_Measurement[delta_k_<?php echo $side; ?>]"
                           placeholder='0.00' class="cols-6"
                           value="<?php echo $element->{"delta_k_$side"} ?>">
                    <?php
                }
                ?>
            </td>
            <td>
                <?php
                if (!$element->{"k_modified_$side"}) {
                    ?>
                    <span class="field-info">@</span>
                    <?php
                } else { ?>
                    <span class="field-info"><b>*</b></span>
                    <?php
                } ?>
            </td>
            <td>
                <?php
                if ($this->is_auto) {
                    if (!$element->{"k_modified_$side"}) {
                        echo '<span class="readonly-box">' . $element->{"delta_k_axis_$side"} . '</span>&nbsp;&deg;';
                    } else {
                        echo '&nbsp;';
                    }
                } else {
                    ?>
                    <input type="text" disabled
                           id="input_Element_OphInBiometry_Measurement_delta_k_axis_<?php echo $side; ?>"
                           name="input_Element_OphInBiometry_Measurement[delta_k_axis_<?php echo $side; ?>]"
                           placeholder='0.00' class="cols-6"
                           value="<?php echo $element->{"delta_k_axis_$side"} ?>">&deg;
                    <input type="hidden" id="Element_OphInBiometry_Measurement_delta_k_axis_<?php echo $side; ?>"
                           name="Element_OphInBiometry_Measurement[delta_k_axis_<?php echo $side; ?>]"
                           placeholder='0.00' class="cols-6"
                           value="<?php echo $element->{"delta_k_axis_$side"} ?>">
                    <?php
                }
                ?>
            </td>
        </tr>
        <tr>
            <td>K2</td>
            <td>
                <?php
                if ($this->is_auto) {
                    echo '<span class="readonly-box">' . $element->{"k2_$side"} . '</span><span class="field-info">D</span>';
                } else { ?>
                    <input type="text" id="Element_OphInBiometry_Measurement_k2_<?php echo $side; ?>"
                           name="Element_OphInBiometry_Measurement[k2_<?php echo $side; ?>]"
                           placeholder='0.00' class="cols-6"
                           value="<?php echo $element->{"k2_$side"} ?>"><span class="field-info">D</span>
                <?php } ?>
            </td>
            <td>
                <?php
                if (!$element->{"k_modified_$side"}) {
                    ?>
                    <span class="field-info">@</span>
                    <?php
                } else { ?>
                    <span class="field-info"><b>*</b></span>
                <?php } ?>
            </td>
            <td>
                <?php
                if ($this->is_auto) {
                    if (!$element->{"k_modified_$side"}) {
                        echo '<span class="readonly-box">' . $element->{"k2_axis_$side"} . '</span>&nbsp;&deg;';
                    } else {
                        echo '&nbsp;';
                    }
                } else {
                    ?>
                    <input type="text" id="Element_OphInBiometry_Measurement_k2_axis_<?php echo $side; ?>"
                           name="Element_OphInBiometry_Measurement[k2_axis_<?php echo $side; ?>]"
                           placeholder='0.00' class="cols-6"
                           value="<?php echo $element->{"k2_axis_$side"} ?>">&deg;
                    <?php
                }
                ?>
            </td>
        </tr>
        <tr>
            <td>ACD:</td>
            <td>
                <?php
                if ($this->is_auto) {
                    echo '<span class="readonly-box">' . $element->{"acd_$side"} . '</span><span class="field-info">&nbsp;mm</span>';
                } else {
                    ?>
                    <input type="text" id="Element_OphInBiometry_Measurement_acd_<?php echo $side; ?>"
                           name="Element_OphInBiometry_Measurement[acd_<?php echo $side; ?>]"
                           placeholder='0.00' class="cols-6"
                           value="<?php echo $element->{"acd_$side"} ?>"><span class="field-info">mm</span>
                    <?php
                }
                ?>
            </td>
            <td></td>
            <td></td>
        </tr>
        </tbody>
    </table>
</div>
<div class="data-group">
    <table class="large-12 column">
        <colgroup>
            <col class="cols-3">
            <col class="cols-3">
            <col class="cols-3">
            <col class="cols-3">
            <col class="cols-2">
        </colgroup>
        <tbody>
            <td>
                LVC:
            </td>
            <td>
            <?php
            if ($this->is_auto) {
                if ($element->{"lvc_$side"} != "") {
                    $lvc = $element->{"lvc_$side"};
                } else {
                    $lvc = "&nbsp;";
                }
                echo '<div class="readonly-box">' . CHtml::encode($lvc) . '</div>';
            } else {
                ?>
                    <input type="text" id="Element_OphInBiometry_Measurement_lvc_<?php echo CHtml::encode($side); ?>" 
                            name="Element_OphInBiometry_Measurement[lvc_<?php echo CHtml::encode($side); ?>]" 
                            value="<?php echo CHtml::encode($element->{"lvc_$side"})?>">
                <?php
            }
            ?>
            </td>
            <td>
                LVC Mode:
            </td>
            <td>
            <?php
            if ($this->is_auto) {
                if ($element->{"lvc_mode_$side"} != "") {
                    $lvc_mode = $element->{"lvc_mode_$side"};
                } else {
                    $lvc_mode = "&nbsp;";
                }
                echo '<div class="readonly-box">' . CHtml::encode($lvc_mode) . '</div>';
            } else {
                ?>
                    <input type="text" id="Element_OphInBiometry_Measurement_lvc_mode_<?php echo CHtml::encode($side); ?>" 
                            name="Element_OphInBiometry_Measurement[lvc_mode_<?php echo CHtml::encode($side); ?>]" 
                            value="<?php echo CHtml::encode($element->{"lvc_mode_$side"})?>">
                <?php
            }
            ?>
            </td>
            <td></td>
        </tbody>
    </table>
</div>
<div class="data-group">
    <table class="cols-11 last-left">
        <colgroup>
            <col class="cols-6">
            <col class="cols-6">
        </colgroup>
        <tbody>
        <td>
            Status:
        </td>
        <td>
            <?php
            if ($this->is_auto) {
                echo '<div class="readonly-box">' . Eye_Status::model()->findByPk($element->{"eye_status_$side"})->name . '</div>';
            } else {
                ?>
                <?php
                $eye_status_data = Eye_Status::model()->findAll();
                echo CHtml::dropDownList(
                    'Element_OphInBiometry_Measurement[eye_status_' . $side . ']',
                    'Element_OphInBiometry_Measurement[eye_status_' . $side . ']',
                    CHtml::listData($eye_status_data, 'id', 'name'),
                    array('options' => array($element->{"eye_status_$side"} => array('selected' => true)))
                );
                ?>
                <?php
            }
            ?>
        </td>
        </tbody>
    </table>
</div>
