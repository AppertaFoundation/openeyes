<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2019
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
?>
<div class="eyedraw-fields">
    <?=\CHtml::activeHiddenField($element, $side . '_ed_report'); ?>
  <div class="collapse in">
    <div class="cols-12 column end autoreport-display">
      <span id="<?= CHtml::modelName($element) . '_' . $side . '_ed_report_display' ?>" class="data-value"> </span>
    </div>
  </div>
    <?= CHtml::activeTextArea($element, $side . '_comments', array(
        'rows' => '1',
        'cols' => '20',
        'class' => 'clearWithEyedraw',
        'placeholder' => $element->getAttributeLabel($side . '_comments'),
    )) ?>
</div>
