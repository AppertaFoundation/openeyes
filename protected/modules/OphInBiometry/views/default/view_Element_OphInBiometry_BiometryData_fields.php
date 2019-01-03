<div class="element-data">
  <div class="cols-12 column">
    <div class="data-group">
      <div class="cols-3 column">
        <div class="data-label"><?=\CHtml::encode($element->getAttributeLabel('axial_length_' . $side)) ?></div>
      </div>
      <div class="cols-3 column">
        <div class="data-value"
             id="al_<?php echo $side ?>"><?=\CHtml::encode($element->{'axial_length_' . $side}) ?></div>
      </div>
      <div class="cols-6 column">
        <div class="data-value">SNR = 193.0</div>
      </div>
    </div>
  </div>
  <div class="cols-12 column">
    <div class="data-group">
      <div class="cols-3 column">
        <div class="data-label"><?=\CHtml::encode($element->getAttributeLabel('r1')) ?></div>
      </div>
      <div class="cols-3 column">
        <div class="data-value" id="r1_<?php echo $side ?>"><?=\CHtml::encode($element->{'r1_' . $side}) ?></div>
      </div>
      <div class="cols-6 column">
        <div class="data-value" id="r1info_<?php echo $side ?>"></div>
      </div>
    </div>
  </div>
  <div class="cols-12 column">
    <div class="data-group">
      <div class="cols-3 column">
        <div class="data-label"><?=\CHtml::encode($element->getAttributeLabel('r2')) ?></div>
      </div>
      <div class="cols-3 column">
        <div class="data-value" id="r2_<?php echo $side ?>"><?=\CHtml::encode($element->{'r2_' . $side}) ?></div>
      </div>
      <div class="cols-6 column">
        <div class="data-value" id="r2info_<?php echo $side ?>"></div>
      </div>
    </div>
  </div>
  <div class="cols-12 column">
    <div class="data-group">
      <div class="cols-3 column">
        <div class="data-label"><?=\CHtml::encode($element->getAttributeLabel('r1_axis' . $side)) ?></div>
      </div>
      <div class="cols-3 column end">
        <div class="data-value"
             id="r1_axis_<?php echo $side ?>"><?=\CHtml::encode($element->{'r1_axis_' . $side}) ?></div>
      </div>
    </div>
  </div>
  <div class="cols-12 column">
    <div class="data-group">
      <div class="cols-3 column">
        <div class="data-label">R/SE</div>
      </div>
      <div class="cols-3 column">
        <div class="data-value" id="rse_<?php echo $side ?>" class="field-info"></div>
      </div>
      <div class="cols-6 column">
        <div class="data-value">SD = 43.16 mm</div>
      </div>
    </div>
  </div>
  <div class="cols-12 column">
    <div class="data-group">
      <div class="cols-3 column">
        <div class="data-label">Cyl</div>
      </div>
      <div class="cols-9 column">
        <div class="data-value" id="cyl_<?php echo $side ?>"></div>
      </div>
    </div>
  </div>
  <div class="cols-12 column">
    <div class="data-group">
      <div class="cols-3 column">
        <div class="data-label">Acd</div>
      </div>
      <div class="cols-9 column">
        <div class="data-value" id="arc__<?php echo $side ?>">2.28mm</div>
      </div>
    </div>
  </div>
    <?php if (isset($element->{'r1_axis_' . $side}) && $element->{'r1_axis_' . $side} != 0) {
        $this->widget('application.modules.eyedraw.OEEyeDrawWidget',
            array(
                'onReadyCommandArray' => array(
                    array('addDoodle', array('SteepAxis', array('axis' => $element->{'r1_axis_' . $side}))),
                    array('deselectDoodles', array()),
                ),

                'idSuffix' => $side . '_' . $element->elementType->id . '_' . $element->id,
                'side' => ($side == 'right') ? 'R' : 'L',
                'mode' => 'view',
                'width' => $this->action->id === 'view' ? 200 : 120,
                'height' => $this->action->id === 'view' ? 200 : 120,
            ));
    } ?>
</div>