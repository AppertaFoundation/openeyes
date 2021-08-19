<div class="data-group js-<?= $manual_override ? 'manual-override' : 'auto-values'?> <?= $disable ? "js-disable-data-group" : ""?>"
    style="display:<?=$disable ? "none;" : "block"?>">
    <table class="cols-11 last-left">
        <colgroup>
            <col class="cols-6">
            <col class="cols-6">
        </colgroup>
        <tbody>
        <?php
        if ($this->is_auto) {
            $post = Yii::app()->request->getPost('Element_OphInBiometry_Selection');

            if ($element->isNewRecord && empty($post)) {
                $element->lens_id_left = null;
                $element->lens_id_right = null;
            }

            if (!empty($this->iolRefValues)) {
                foreach ($this->iolRefValues as $measurementData) {
                    if (!empty($measurementData->{'iol_ref_values_left'})) {
                        $lens_left[] = $measurementData->{'lens_id'};
                        $formulas_left[] = $measurementData->{'formula_id'};
                        $iolrefdata_left[$measurementData->{'lens_id'}][$measurementData->{'formula_id'}] = $measurementData->{"iol_ref_values_$side"};
                        $iolrefdata['left'][$measurementData->{'lens_id'}][$measurementData->{'formula_id'}] = $measurementData->{"iol_ref_values_$side"};
                        $emmetropiadata['left'][$measurementData->{'lens_id'}][$measurementData->{'formula_id'}] = $measurementData->{'emmetropia_left'};
                        $acon['left'][$measurementData->{'lens_id'}][$measurementData->{'formula_id'}] = $measurementData->{'constant'};
                    }
                    if (!empty($measurementData->{'iol_ref_values_right'})) {
                        $lens_right[] = $measurementData->{'lens_id'};
                        $formulas_right[] = $measurementData->{'formula_id'};
                        $iolrefdata_right[$measurementData->{'lens_id'}][$measurementData->{'formula_id'}] = $measurementData->{"iol_ref_values_$side"};
                        $iolrefdata['right'][$measurementData->{'lens_id'}][$measurementData->{'formula_id'}] = $measurementData->{"iol_ref_values_$side"};
                        $emmetropiadata['right'][$measurementData->{'lens_id'}][$measurementData->{'formula_id'}] = $measurementData->{'emmetropia_right'};
                        $acon['right'][$measurementData->{'lens_id'}][$measurementData->{'formula_id'}] = $measurementData->{'constant'};
                    }
                }
            }
            ?>
            <?php
            if (isset(Element_OphInBiometry_IolRefValues::model()->findByAttributes(array('event_id' => $this->event->id))->surgeon_id)) { ?>
                <tr>
                    <td>
                        <span class="field-info">Surgeon:</span>
                    </td>
                    <td>

                        <?php echo OphInBiometry_Surgeon::model()->findByAttributes(
                            array('id' => Element_OphInBiometry_IolRefValues::model()->findByAttributes(array('event_id' => $this->event->id))->surgeon_id)
                        )->name;

                        ?>
                    </td>
                </tr>
            <?php } ?>
            <tr>
                <td>
                    Lens
                </td>
                <td class="cols-full">
                    <?php
                    $criteria = new CDbCriteria();

                    if (!empty(${'lens_' . $side})) {
                        if ($manual_override) {
                            $criteria->condition = 't.active = 1';
                        } else {
                            $criteria->condition = 't.id in (' . implode(',', array_unique(${'lens_' . $side})) . ')';
                        }
                        $criteria->with = 'institutions';
                        $criteria->condition .= ' AND institutions_institutions.institution_id = :institution_id';
                        $criteria->params[':institution_id'] = Institution::model()->getCurrent()->id;
                        $lenses = OphInBiometry_LensType_Lens::model()->findAll($criteria, array('order' => 'display_order'));
                        $lens_options = [];
                        foreach ($lenses as $lens_data_options) {
                            $lens_options[$lens_data_options->id] = ['data-constant' => number_format($lens_data_options->acon, 2)];
                        }
                        $htmlOptions = array('empty' => 'Select', 'nowrapper' => true, 'class' => 'js-lens-manual-override-dropdown dd1', 'options' => $lens_options);
                        if ($disable) {
                            $htmlOptions['disabled'] = 'disabled';
                        }
                        echo $form->dropDownList(
                            $element,
                            'lens_id_' . $side,
                            CHtml::listData(
                                $lenses,
                                'id',
                                'display_name'
                            ),
                            $htmlOptions,
                            null,
                            array('label' => 6, 'field' => 12)
                        );
                    }
                    ?>
                </td>
            </tr>
            <?php
            echo $form->hiddenField($element, 'iol_power_' . $side, array('value' => $element->{"iol_power_$side"}));
        } else {
            ?>
            <tr>
                <td>
                    Lens
                </td>
                <td>
                    <?php
                    //We should move this code to the controller some point of time.
                    $post = Yii::app()->request->getPost('Element_OphInBiometry_Selection');
                    if ($element->isNewRecord && empty($post)) {
                        $element->lens_id_left = null;
                        $element->lens_id_right = null;
                    }
                    $htmlOptions = array('empty' => 'Select', 'nowrapper' => true);
                    if ($disable) {
                        $htmlOptions['disabled'] = 'disabled';
                    }
                    $criteria = new CDbCriteria();
                    $criteria->order = 'display_order asc';
                    $criteria->with = 'institutions';
                    $criteria->condition = 'institutions_institutions.institution_id = :institution_id';
                    $criteria->params[':institution_id'] = Institution::model()->getCurrent()->id;
                    echo $form->dropDownList($element, 'lens_id_' . $side, CHtml::listData(OphInBiometry_LensType_Lens::model()->activeOrPk($element->{'lens_id_' . $side})->findAll($criteria), 'id', 'display_name'), $htmlOptions, null, array('label' => 6, 'field' => 12))
                    ?>
                </td>
            </tr>
            <?php
        }
        if (!$this->is_auto || $manual_override) {
            ?>
            <tr>
                <td>
                    Lens A constant:
                </td>
                <td>
                    <span class="js-lens-constant"><?php echo $element->{'lens_' . $side} ? number_format($element->{'lens_' . $side}->acon, 1) : '' ?></span>
                </td>
            </tr>
            <?php
        }
        if ($this->is_auto && !$manual_override) {
            echo $form->hiddenField($element, 'predicted_refraction_' . $side, array('value' => $element->{"predicted_refraction_$side"}));
            ?>
            <?php
            $criteria = new CDbCriteria();
            if (!empty(${"formulas_$side"})) {
                $criteria->condition = 'id in (' . implode(',', array_unique(${"formulas_$side"})) . ')';
                $formulae = OphInBiometry_Calculation_Formula::model()->findAll($criteria, array('order' => 'display_order')); ?>
                <tr>
                    <td>
                        Formula
                    </td>
                    <td>

                        <?php echo $form->dropDownList(
                            $element,
                            'formula_id_' . $side,
                            CHtml::listData($formulae, 'id', 'name'),
                            array('empty' => 'Select', 'nowrapper' => true),
                            null,
                            array('label' => 4, 'field' => 6)
                        );

                        ?>
                    </td>
                </tr>
            <?php } ?>

            <tr>
                <td>
                    <span class="field-info">A constant:</span>
                </td>
                <td>
                    <?php
                    if ($side === 'left') {
                        if (!empty($iolrefdata['left'])) {
                            $acon_left = $acon['left'];
                            foreach ($acon_left as $k => $v) {
                                foreach ($v as $key => $value) {
                                    if (!empty($value)) {
                                        $spanid = 'aconstant_' . $side . '_' . $k . '_' . $key;
                                        echo '<span id=' . $spanid . ' class="field-value">' . $this->formatAconst($acon['left'][$k][$key]) . '</span>';
                                    }
                                }
                            }
                        }
                    } else {
                        if (!empty($iolrefdata['right'])) {
                            $acon_right = $iolrefdata['right'];
                            foreach ($acon_right as $k => $v) {
                                foreach ($v as $key => $value) {
                                    if (!empty($value)) {
                                        $spanid = 'aconstant_' . $side . '_' . $k . '_' . $key;
                                        echo '<span id=' . $spanid . ' class="field-value">' . $this->formatAconst($acon['right'][$k][$key]) . '</span>';
                                    }
                                }
                            }
                        }
                    }
                    ?>
                </td>
            </tr>
            <tr>
                <td>
                    <span class="field-info">Emmetropic IOL power:</span>
                </td>
                <td>
                    <?php
                    if ($side == 'left') {
                        if (!empty($iolrefdata['left'])) {
                            $iolrefdata_left = $iolrefdata['left'];
                            foreach ($iolrefdata_left as $k => $v) {
                                foreach ($v as $key => $value) {
                                    if (!empty($value)) {
                                        $spanid = 'emmetropia_' . $side . '_' . $k . '_' . $key;
                                        echo '<span id=' . $spanid . ' class="field-value">' . $emmetropiadata['left'][$k][$key] . '</span>';
                                    }
                                }
                            }
                        }
                    } else {
                        if (!empty($iolrefdata['right'])) {
                            $iolrefdata_right = $iolrefdata['right'];
                            foreach ($iolrefdata_right as $k => $v) {
                                foreach ($v as $key => $value) {
                                    if (!empty($value)) {
                                        $spanid = 'emmetropia_' . $side . '_' . $k . '_' . $key;
                                        echo '<span id=' . $spanid . ' class="field-value">' . $emmetropiadata['right'][$k][$key] . '</span>';
                                    }
                                }
                            }
                        }
                    }
                    ?>
                </td>
            </tr>


            <?php
            if ($side == 'left') {
                if (!empty($iolrefdata['left'])) { ?>
                    <tr>
                        <td></td>
                        <td>
                            <?php $iolrefdata_left = $iolrefdata['left'];
                            foreach ($iolrefdata_left as $k => $v) {
                                foreach ($v as $key => $value) {
                                    if (!empty($value)) {
                                        $iolData = $this->orderIOLData(json_decode($value, true));
                                        $divid = $side . '_' . $k . '_' . $key;
                                        $found = 0;
                                        // $closet = $this->getClosest($emmetropiadata['left'][$k][$key],$iolData['IOL']);
                                        $closest = $this->getClosest($this->calculationValues[0]->{'target_refraction_left'}, $iolData['REF']);
                                        echo '<table id=' . $divid . '  class="cols-full last-left"><colgroup><col class="cols-2"><col class="cols-2"><col class="cols-2"></colgroup><thead><tr><th>#</th> <th>IOL</th><th>REF</th></tr></thead>';
                                        for ($j = 0; $j < count($iolData['IOL']); ++$j) {
                                            $radid = $side . '_' . $k . '_' . $key . '__' . $j;
                                            if (($this->selectionValues[0]->{'predicted_refraction_left'} == $iolData['REF'][$j]) && ($this->selectionValues[0]->{'iol_power_left'} == $iolData['IOL'][$j])) {
                                                $found = 1;
                                                if ($iolData['REF'][$j] == $closest) {
                                                    echo "<tr  class='selected-row' id='iolreftr-$radid'><td><input type='radio' checked  id='iolrefrad-$radid' name='iolrefval_left'></td><td><span class='highlighter'>" . number_format((float)$iolData['IOL'][$j], 2, '.', '') . '</span></td><td><span class="highlighter">' . (($iolData['REF'][$j] > 0) ? '+' : '') . $iolData['REF'][$j] . '</span></td></tr>';
                                                } else {
                                                    echo "<tr  class='selected-row' id='iolreftr-$radid'><td><input type='radio' checked  id='iolrefrad-$radid' name='iolrefval_left'></td><td><span>" . number_format((float)$iolData['IOL'][$j], 2, '.', '') . '</span></td><td><span>' . (($iolData['REF'][$j] > 0) ? '+' : '') . $iolData['REF'][$j] . '</span></td></tr>';
                                                }
                                            } else {
                                                if ($iolData['REF'][$j] == $closest) {
                                                    echo "<tr id='iolreftr-$radid'><td><input type='radio'  id='iolrefrad-$radid' class='iolrefselection' name='iolrefselection_left'></td><td><span class='highlighter'>" . number_format((float)$iolData['IOL'][$j], 2, '.', '') . '</span></td><td><span class="highlighter">' . (($iolData['REF'][$j] > 0) ? '+' : '') . $iolData['REF'][$j] . '</span></td></tr>';
                                                } else {
                                                    echo "<tr id='iolreftr-$radid'><td><input type='radio'  id='iolrefrad-$radid' class='iolrefselection' name='iolrefselection_left'></td><td><span>" . number_format((float)$iolData['IOL'][$j], 2, '.', '') . '</span></td><td><span>' . (($iolData['REF'][$j] > 0) ? '+' : '') . $iolData['REF'][$j] . '</span></td></tr>';
                                                }
                                            }
                                            echo "<input type='hidden'  id='iolval-$radid' value=" . number_format((float)$iolData['IOL'][$j], 2, '.', '') . "><input type='hidden'  id='refval-$radid' value=" . $iolData['REF'][$j] . '>';
                                        }
                                        echo '</table>';
                                    }
                                }
                            } ?>
                        </td>
                    </tr>
                <?php }
            } else { ?>
                <tr>
                <td></td>
                <td>
                <?php if (!empty($iolrefdata['right'])) {
                    $iolrefdata_right = $iolrefdata['right'];
                    foreach ($iolrefdata_right as $k => $v) {
                        foreach ($v as $key => $value) {
                            if (!empty($value)) {
                                $iolData = $this->orderIOLData(json_decode($value, true));
                                $divid = $side . '_' . $k . '_' . $key;
                                $found = 0;
                                //$closet = $this->getClosest($emmetropiadata['right'][$k][$key],$iolData['IOL']);
                                $closest = $this->getClosest($this->calculationValues[0]->{'target_refraction_right'}, $iolData['REF']);
                                echo '<table id=' . $divid . ' class="cols-full last-left"><colgroup>
										<col class="cols-2">
										<col class="cols-2">
										<col class="cols-2">
									</colgroup><tr><th>#</th> <th>IOL</th><th>REF</th>';
                                for ($j = 0; $j < count($iolData['IOL']); ++$j) {
                                    $radid = $side . '_' . $k . '_' . $key . '__' . $j;
                                    if (($this->selectionValues[0]->{'predicted_refraction_right'} == $iolData['REF'][$j]) && ($this->selectionValues[0]->{'iol_power_right'} == $iolData['IOL'][$j])) {
                                        $found = 1;
                                        if ($iolData['REF'][$j] == $closest) {
                                            echo "<tr class='selected-row' id='iolreftr-$radid'><td><input type='radio' checked id='iolrefrad-$radid' name='iolrefval_right'></td><td><span  class='highlighter'>" . number_format((float)$iolData['IOL'][$j], 2, '.', '') . '</span></td><td><span class="highlighter">' . (($iolData['REF'][$j] > 0) ? '+' : '') . $iolData['REF'][$j] . '</span></td></tr>';
                                        } else {
                                            echo "<tr class='selected-row' id='iolreftr-$radid'><td><input type='radio' checked id='iolrefrad-$radid' name='iolrefval_right'></td><td>" . number_format((float)$iolData['IOL'][$j], 2, '.', '') . '</td><td>' . (($iolData['REF'][$j] > 0) ? '+' : '') . $iolData['REF'][$j] . '</td></tr>';
                                        }
                                    } else {
                                        if ($iolData['REF'][$j] == $closest) {
                                            echo "<tr id='iolreftr-$radid'><td><input type='radio'  id='iolrefrad-$radid' class='iolrefselection' name='iolrefselection_right'></td><td><span class='highlighter'>" . number_format((float)$iolData['IOL'][$j], 2, '.', '') . '</span></td><td><span  class="highlighter">' . (($iolData['REF'][$j] > 0) ? '+' : '') . $iolData['REF'][$j] . '</span></td></tr>';
                                        } else {
                                            echo "<tr id='iolreftr-$radid'><td><input type='radio'  id='iolrefrad-$radid' class='iolrefselection' name='iolrefselection_right'></td><td><span>" . number_format((float)$iolData['IOL'][$j], 2, '.', '') . '</span></td><td><span>' . (($iolData['REF'][$j] > 0) ? '+' : '') . $iolData['REF'][$j] . '</span></td></tr>';
                                        }
                                    }
                                    echo "<input type='hidden'  id='iolval-$radid' value=" . number_format((float)$iolData['IOL'][$j], 2, '.', '') . "><input type='hidden'  id='refval-$radid' value=" . $iolData['REF'][$j] . '>';
                                }
                                echo '</table>';
                            }
                        }
                    }
                }
            }
            ?>
            </td>
            </tr>
            <?php
        }
        ?>
        <?php
        if (!$this->is_auto || $manual_override) {
            //$element->iol_power_left = null;
            ?>
            <tr>
                <td>
                    IOL Power
                </td>
                <td>
                    <?php echo $form->textField($element, 'iol_power_' . $side, ($this->is_auto &&  !$manual_override) ? array('readonly' => true) : array('placeholder' => '0.00', 'nowrapper' => true), null, array('label' => 4, 'field' => 2)); ?>
                </td>
            </tr>
            <tr>
                <td>
                    Predicted Refraction:
                </td>
                <td>
                    <?php echo $form->textField($element, 'predicted_refraction_' . $side, ($this->is_auto &&  !$manual_override) ? array('readonly' => true) : array('placeholder' => '0.00', 'nowrapper' => true), null, array('label' => 4, 'field' => 2)); ?>
                </td>
            </tr>
        <?php }
        ?>
        </tbody>
    </table>
</div>
