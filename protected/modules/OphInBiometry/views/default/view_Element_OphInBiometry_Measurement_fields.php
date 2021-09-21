<div class="data-group">
    <table class="cols-11 large-text borders">
        <colgroup>
            <col class="cols-3">
            <col class="cols-2">
            <col class="cols-1">
        </colgroup>
        <tbody>
        <tr>
            <td>
                AL:
            </td>
            <td>
                <?=\CHtml::encode($element->{'axial_length_' . $side}) ?>&nbsp;mm
            </td>
            <td colspan="2"></td>
            <td>
                <?php $this->widget('EyeLateralityWidget', array('laterality' => $side)) ?>
            </td>
        </tr>
        <tr>
            <?php
            if (!$element->{"al_modified_$side"}) {
                ?>

                <td>
                    SNR:
                </td>
                <td>
                    <?php if ($this->isAutoBiometryEvent($this->event->id) && $this->getAutoBiometryEventData($this->event->id)[0]->is700()) : ?>
                        N/A
                    <?php else : ?>
                        <?=\CHtml::encode($element->{'snr_' . $side}) ?>
                    <?php endif; ?>
                </td>
                <?php
            } else {
                echo '<td><small class="fade">* AL entered manually</small></td>';
            }
            ?>
            <td colspan="2"></td>
            <td>
                <?php $this->widget('EyeLateralityWidget', array('laterality' => $side)) ?>
            </td>
        </tr>
        <tr>
            <td>K1:</td>
            <td>
                <?=\CHtml::encode($element->{'k1_' . $side}) ?>&nbsp;D
            </td>
            <td>
                <?php
                if (!$element->{"k_modified_$side"}) {
                    ?>
                    <span class="field-info">@</span>
                <?php } else { ?>
                    <span class="field-info"><b>*</b></span>
                <?php } ?>
            </td>
            <td>
                <?php
                if (!$element->{"k_modified_$side"}) {
                    ?>
                    <?=\CHtml::encode($element->{'k1_axis_' . $side}) ?>&deg;
                <?php } else {
                    echo '&nbsp;';
                } ?>
            </td>
            <td>
                <?php $this->widget('EyeLateralityWidget', array('laterality' => $side)) ?>
            </td>
        </tr>
        <tr>
            <td>
                &Delta;K:
            </td>
            <td>
                <?php if (($element->{'delta_k_' . $side}) > 0) {
                    echo '+';
                }
                echo CHtml::encode($element->{'delta_k_' . $side});
                ?>&nbsp;D
            </td>
            <td>
                <?php
                if (!$element->{"k_modified_$side"}) {
                    ?>
                    @
                    <?php
                } else { ?>
                    *
                <?php } ?>
            </td>
            <td>
                <?php
                if (!$element->{"k_modified_$side"}) {
                    ?>
                    <?=\CHtml::encode($element->{'delta_k_axis_' . $side}) ?>&deg;
                <?php } else {
                    echo '&nbsp;';
                } ?>

            </td>
            <td>
                <?php $this->widget('EyeLateralityWidget', array('laterality' => $side)) ?>
            </td>
        </tr>
        <tr>
            <td>
                K2:
            </td>
            <td>
                <?=\CHtml::encode($element->{'k2_' . $side}) ?>&nbsp;D
            </td>
            <td>
                <?php
                if (!$element->{"k_modified_$side"}) {
                    ?>
                    <span class="field-info">@</span>
                <?php } else { ?>
                    <span class="field-info"><b>*</b></span>
                <?php } ?>
            </td>
            <td>
                <?php
                if (!$element->{"k_modified_$side"}) {
                    ?>
                    <div class="field-info" id="k2_axis_<?php echo $side ?>">
                        <?=\CHtml::encode($element->{'k2_axis_' . $side}) ?>&deg;
                    </div>
                <?php } else {
                    echo '&nbsp;';
                } ?>
            </td>
            <td>
                <?php $this->widget('EyeLateralityWidget', array('laterality' => $side)) ?>
            </td>
        </tr>
        <tr>
            <td>
                ACD:
            </td>
            <td>
                <?=\CHtml::encode($element->{'acd_' . $side}) ?>&nbsp;mm
            </td>
            <td colspan="2"></td>
            <td>
                <?php $this->widget('EyeLateralityWidget', array('laterality' => $side)) ?>
            </td>
        </tr>
        <tr>
            <td>LVC:</td>
            <td>
                <span class="field-info"><?php echo CHtml::encode($element->{'lvc_' . $side}) ?></span>
            </td>
            <td></td>
            <td></td>
            <td>
                <?php $this->widget('EyeLateralityWidget', array('laterality' => $side)) ?>
            </td>
        </tr>
        <tr>
            <td>
                LVC Mode:
            </td>
            <td>
                <span class="field-info"><?php echo CHtml::encode($element->{'lvc_mode_' . $side}) ?></span>
            </td>
            <td></td>
            <td></td>
            <td>
                <?php $this->widget('EyeLateralityWidget', array('laterality' => $side)) ?>
            </td>
        </tr>
        <tr>
            <td>Status</td>
            <td colspan="3">
                <span class="large-text highlighter orange">
                    <?php echo Eye_Status::model()->findByPk($element->{"eye_status_$side"})->name ?>
                </span>
            </td>
            <td>
                <?php $this->widget('EyeLateralityWidget', array('laterality' => $side)) ?>
            </td>
        </tr>
        </tbody>
    </table>
</div>
