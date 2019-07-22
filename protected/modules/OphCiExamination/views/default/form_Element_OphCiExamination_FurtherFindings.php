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
?>
<div class="element-fields flex-layout full-width ">
  <div class="furtherfindings-multi-select">
        <?php
        $firm = \Firm::model()->findByPk(\Yii::app()->session['selected_firm_id']);
        echo $form->multiSelectListFreeText(
          $element,
          CHtml::modelName($element) . '[further_findings_assignment]',
          'further_findings_assignment',
          'finding_id',
          CHtml::encodeArray(
              CHtml::listData(
                  Finding::model()->activeOrPk(
                      $element->furtherFindingsAssigned)->bySubspecialty(
                      $firm->getSubspecialty())->findAll(),
                  'id',
                  'name')
          ),
          array(),
          array(
              'empty' => '-- Add --',
              'label' => 'Findings',
              'nowrapper' => true,
              'requires_description_field' => 'requires_description',
          ),
          false,
          true,
          'No further findings',
          true,
          true,
          array(),
          'Finding') ?>
  </div>
</div>
