<?php

/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
function getRiskLevelColour($risk_level)
{
    switch ($risk_level) {
        case 'low':
            return 'green';
        break;
        case 'moderate':
            return 'amber';
        break;
        case 'high':
            return 'red';
        break;
        default:
            return 'blue';
    }
}
?>
<div class="element-fields flex-layout full-width ">
  <div class="data-group collapse">
    <div class="cols-full column">
      <div class="field-highlight<?php if ($element->risk) {
            ?> <?php echo $element->risk->class ?><?php
                                 } ?> risk">
        <?php $html_options = array('nowrapper' => true, 'empty' => 'Select');
        $risks = OEModule\OphCiExamination\models\OphCiExamination_GlaucomaRisk_Risk::model()->findAll();
        foreach ($risks as $option) {
            $html_options['options'][(string) $option->id] = array(
            'data-clinicoutcome-template-id' => $option->clinicoutcome_template_id,
            'class' => $option->class,
            );
        }
        echo $form->dropdownList($element, 'risk_id', CHtml::listData($risks, 'id', 'name'), $html_options);
        ?>
      </div>
    </div>
    <a href="#" class="field-info descriptions_link">definitions</a>
  </div>
  <div class="glaucoma-risk-descriptions" id="<?= CHtml::modelName($element) ?>_descriptions" style="display: none;">
    <?php foreach ($risks as $option) { ?>
      <div class="status-box <?= getRiskLevelColour($option->class) ?>">
        <b>
          <a href="#" data-risk-id="<?php echo $option->id ?>">
            <?php echo $option->name ?>
          </a>
        </b>
        <br>
        <?php echo nl2br($option->description) ?>
      </div>
    <?php } ?>
  </div>
</div>