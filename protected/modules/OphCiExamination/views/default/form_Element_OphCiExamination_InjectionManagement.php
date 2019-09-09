<?php
/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2012
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2012, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
?>

<div class="element-fields flex-layout full-width " id="div_<?=\CHtml::modelName($element) ?>_injection">
  <div class="data-group">
    <div class="cols-3 column">
      <label>
            <?php echo $element->getAttributeLabel('injection_status_id') ?>:
      </label>
    </div>
    <div class="cols-9 column">
        <?php
        $options = \OEModule\OphCiExamination\models\OphCiExamination_Management_Status::model()
            ->activeOrPk($element->injection_status_id)
            ->findAll();
        $html_options = array('empty' => 'Select', 'options' => array());
        foreach ($options as $opt) {
            $html_options['options'][(string)$opt->id] = array(
                'data-deferred' => $opt->deferred,
                'data-book' => $opt->book,
                'data-event' => $opt->event
            );
        }
        echo CHtml::activeDropDownList(
            $element,
            'injection_status_id',
            CHtml::listData($options, 'id', 'name'),
            $html_options
        ) ?>
    </div>
  </div>
</div>

<div class="element-fields flex-layout full-width "
     id="div_<?=\CHtml::modelName($element) ?>_injection_deferralreason"
     style="<?=(!($element->injection_status && $element->injection_status->deferred))? "display: none;":''?>"
>
  <div class="data-group">
    <div class="cols-3 column">
      <label>
            <?=$element->getAttributeLabel('injection_deferralreason_id')?>:
      </label>
    </div>
    <div class="cols-4 column ">
        <?php
        $options = \OEModule\OphCiExamination\models\OphCiExamination_Management_DeferralReason::model()->activeOrPk($element->injection_deferralreason_id)->findAll();
        $html_options = array('empty' => 'Select', 'options' => array());
        foreach ($options as $opt) {
            $html_options['options'][(string)$opt->id] = array('data-other' => $opt->other);
        }
        echo CHtml::activeDropDownList($element, 'injection_deferralreason_id', CHtml::listData($options, 'id', 'name'), $html_options) ?>
    </div>
  </div>
</div>

<div class="element-fields flex-layout full-width "
     id="div_<?=\CHtml::modelName($element) ?>_injection_deferralreason_other"<?php if (!($element->injection_deferralreason && $element->injection_deferralreason->other)) {
            ?> style="display: none;"<?php
             } ?>>
  <div class="data-group">
    <div class="cols-3 column">
      <label>
        &nbsp;
      </label>
    </div>
    <div class="cols-9 column">
        <?php echo $form->textArea($element, 'injection_deferralreason_other', array('class' => 'autosize', 'nowrapper' => true)) ?>
    </div>
  </div>
</div>
