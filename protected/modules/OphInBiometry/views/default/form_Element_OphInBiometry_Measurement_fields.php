<div class="element-fields">

	<div class="row">
		<div class="large-12 column">
			<div class="row field-row">
				<div class="large-1 column">
					<span class="field-info">AL:</span>
				</div>
				<div class="large-5 column">
					<?php
                    if ($this->is_auto) {
                        echo '<span class="readonly-box">'.$element->{"axial_length_$side"}.'</span><span class="field-info">&nbsp;mm</span>';
                    } else {
                        ?>
						<input type="text" id="Element_OphInBiometry_Measurement_axial_length_<?php echo $side; ?>"
                               name="Element_OphInBiometry_Measurement[axial_length_<?php echo $side; ?>]"
                               placeholder='0.00'
                               value="<?php echo $element->{"axial_length_$side"}?>"><span class="field-info">mm</span>
						<?php

                    }
                    ?>
				</div>
				<div class="large-1 column">
					<span class="field-info">SNR:</span>
				</div>
				<div class="large-5 column">
					<?php
                    if ($this->is_auto) {
                        if (!$element->{"al_modified_$side"}) {
                            if ($this->getAutoBiometryEventData($this->event->id)[0]->is700()) {
                                echo '<span class="field-value">N/A</span>';
                            } else {
                                echo '<span class="readonly-box">'.$element->{"snr_$side"}.'</span>';
                            }
                        } else {
                            echo '<span class="field-value">* AL entered manually</span>';
                        }
                    } else {
                        ?>
						<input type="text" id="Element_OphInBiometry_Measurement_snr_<?php echo $side; ?>"
                               name="Element_OphInBiometry_Measurement[snr_<?php echo $side; ?>]"
                               placeholder='0.00'
                               value="<?php echo $element->{"snr_$side"}?>">
						<?php

                    }
                    ?>
				</div>

			</div>
		</div>
	</div>

	<div class="row">
		<div class="large-12 column">
			<div class="row field-row">
				<div class="large-1 column">
					<span class="field-info">K1:</span>
				</div>
				<div class="large-2 column">
					<?php
                    if ($this->is_auto) {
                        echo '<span class="readonly-box">'.$element->{"k1_$side"}.'</span><span class="field-info">D</span>';
                    } else {
                        ?>
					<input type="text" id="Element_OphInBiometry_Measurement_k1_<?php echo $side; ?>"
                           name="Element_OphInBiometry_Measurement[k1_<?php echo $side; ?>]"
                           placeholder='0.00'
                           value="<?php echo $element->{"k1_$side"}?>"><span class="field-info">D</span>
						<?php

                    }
                    ?>
				</div>

				<div class="large-1 column">
					<?php
                    if (!$element->{"k_modified_$side"}) {
                        ?>
						<span class="field-info">@</span>
					<?php
                    } else {?>
						<span class="field-info"><b>*</b></span>
					<?php
                    } ?>
				</div>
				<div class="large-2 column">
					<?php
                    if ($this->is_auto) {
                        if (!$element->{"k_modified_$side"}) {
                            echo '<span class="readonly-box">'.$element->{"axis_k1_$side"}.'</span>&nbsp;&deg;';
                        } else {
                            echo '&nbsp;';
                        }
                    } else {
                        ?>
						<input type="text" id="Element_OphInBiometry_Measurement_axis_k1_<?php echo $side; ?>"
                               name="Element_OphInBiometry_Measurement[axis_k1_<?php echo $side; ?>]"
                               placeholder='0.00'
                               value="<?php echo $element->{"axis_k1_$side"}?>">&deg;
						<?php

                    }
                    ?>
				</div>

				<div class="large-1 column">
					<span class="field-info">&Delta;K:</span>
				</div>
				<div class="large-2 column">
					<?php
                    if ($this->is_auto) {
                        echo '<span class="readonly-box">';
                        if (($element->{'delta_k_'.$side}) > 0) {
                            echo '+';
                        }
                        echo $element->{"delta_k_$side"}.'</span><span class="field-info">D</span>';
                    } else {
                        ?>
					<input type="text" disabled id="input_Element_OphInBiometry_Measurement_delta_k_<?php echo $side; ?>"
                           name="input_Element_OphInBiometry_Measurement[delta_k_<?php echo $side; ?>]"
                           placeholder='0.00'
                           value="<?php echo $element->{"delta_k_$side"}?>"><span class="field-info">D</span>
					<input type="hidden" id="Element_OphInBiometry_Measurement_delta_k_<?php echo $side; ?>"
                           name="Element_OphInBiometry_Measurement[delta_k_<?php echo $side; ?>]"
                           placeholder='0.00'
                           value="<?php echo $element->{"delta_k_$side"}?>">
						<?php

                    }
                    ?>
				</div>
				<div class="large-1 column">
					<?php
                    if (!$element->{"k_modified_$side"}) {
                        ?>
					<span class="field-info">@</span>
					<?php
                    } else {?>
						<span class="field-info"><b>*</b></span>
					<?php
                    } ?>
				</div>
				<div class="large-2 column">
					<?php
                    if ($this->is_auto) {
                        if (!$element->{"k_modified_$side"}) {
                            echo '<span class="readonly-box">'.$element->{"delta_k_axis_$side"}.'</span>&nbsp;&deg;';
                        } else {
                            echo '&nbsp;';
                        }
                    } else {
                        ?>
						<input type="text" disabled id="input_Element_OphInBiometry_Measurement_delta_k_axis_<?php echo $side; ?>"
                               name="input_Element_OphInBiometry_Measurement[delta_k_axis_<?php echo $side; ?>]"
                               placeholder='0.00'
                               value="<?php echo $element->{"delta_k_axis_$side"}?>">&deg;
						<input type="hidden" id="Element_OphInBiometry_Measurement_delta_k_axis_<?php echo $side; ?>"
                               name="Element_OphInBiometry_Measurement[delta_k_axis_<?php echo $side; ?>]"
                               placeholder='0.00'
                               value="<?php echo $element->{"delta_k_axis_$side"}?>">
						<?php

                    }
                    ?>
				</div>
			</div>
		</div>
	</div>

	<div class="row">
		<div class="large-12 column">
			<div class="row field-row">
				<div class="large-1 column">
					<span class="field-info">K2:</span>
				</div>
				<div class="large-2 column ">
					<?php
                    if ($this->is_auto) {
                        echo '<span class="readonly-box">'.$element->{"k2_$side"}.'</span><span class="field-info">D</span>';
                    } else { ?>
						<input type="text" id="Element_OphInBiometry_Measurement_k2_<?php echo $side;?>"
						    name="Element_OphInBiometry_Measurement[k2_<?php echo $side;?>]"
						    placeholder='0.00'
						    value="<?php echo $element->{"k2_$side"}?>"><span class="field-info">D</span>
					<?php } ?>
				</div>
				<div class="large-1 column">
					<?php
                    if (!$element->{"k_modified_$side"}) {
                        ?>
						<span class="field-info">@</span>
					<?php
                    } else { ?>
						<span class="field-info"><b>*</b></span>
					<?php } ?>
				</div>
				<div class="large-2 column">
					<?php
                    if ($this->is_auto) {
                        if (!$element->{"k_modified_$side"}) {
                            echo '<span class="readonly-box">'.$element->{"k2_axis_$side"}.'</span>&nbsp;&deg;';
                        } else {
                            echo '&nbsp;';
                        }
                    } else {
                        ?>
						<input type="text" id="Element_OphInBiometry_Measurement_k2_axis_<?php echo $side; ?>"
                               name="Element_OphInBiometry_Measurement[k2_axis_<?php echo $side; ?>]"
                               placeholder='0.00'
                               value="<?php echo $element->{"k2_axis_$side"}?>">&deg;
						<?php
                    }
                    ?>
				</div>

				<div class="large-1 column">
					<span class="field-info">ACD:</span>
				</div>
				<div class="large-5 column">
					<?php
                    if ($this->is_auto) {
                        echo '<span class="readonly-box">'.$element->{"acd_$side"}.'</span><span class="field-info">&nbsp;mm</span>';
                    } else {
                        ?>
						<input type="text" id="Element_OphInBiometry_Measurement_acd_<?php echo $side; ?>"
                               name="Element_OphInBiometry_Measurement[acd_<?php echo $side; ?>]"
                               placeholder='0.00'
                               value="<?php echo $element->{"acd_$side"}?>"><span class="field-info">mm</span>
					<?php

                    }
                    ?>
				</div>

			</div>
		</div>
	</div>
	<div class="row">
		<div class="large-12 column">
			<div class="row field-row">
				<div class="large-4 column">
					<span class="field-info">Status:</span>
				</div>
				<div class="large-6 column end">
					<?php
                    if ($this->is_auto) {
                        echo '<div class="readonly-box">'.Eye_Status::model()->findByPk($element->{"eye_status_$side"})->name.'</div>';
                    } else {
                        ?>
						<?php
                        $eye_status_data = Eye_Status::model()->findAll();
                        echo CHtml::dropDownList('Element_OphInBiometry_Measurement[eye_status_'.$side.']', 'Element_OphInBiometry_Measurement[eye_status_'.$side.']', CHtml::listData($eye_status_data, 'id', 'name'),
                            array('options' => array($element->{"eye_status_$side"} => array('selected' => true))));
                        ?>
						<?php

                    }
                    ?>
				</div>
			</div>
		</div>
	</div>
</div>
