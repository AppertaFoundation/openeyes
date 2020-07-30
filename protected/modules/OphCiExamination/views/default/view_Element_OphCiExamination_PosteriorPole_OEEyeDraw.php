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
<div class="eyedraw flex-layout flex-top posterior-pole">
    <?php
        $this->widget('application.modules.eyedraw.OEEyeDrawWidget', array(
            'idSuffix' => $side . '_' . $element->elementType->id . '_' . $element->id,
            'side' => ($side == 'right') ? 'R' : 'L',
            'mode' => 'view',
            'width' => $this->action->id === 'view' ? 200 : 120,
            'height' => $this->action->id === 'view' ? 200 : 120,
            'model' => $element,
            'attribute' => $side . '_eyedraw',
        ))
        ?>
    <div class="eyedraw-data stack">

        <?php if ($report = $element->{$side . '_ed_report'}) : ?>
        <div class="data-value"><?= Yii::app()->format->Ntext($element->{$side . '_ed_report'}) ?></div>
        <?php endif; ?>

        <?php if ($report = $element->{$side . '_description'}) : ?>
        <div class="data-label"><?= $element->getAttributeLabel($side . '_description') ?>:</div>
        <div class="data-value"><?= Yii::app()->format->Ntext($element->{$side . '_description'}) ?></div>
        <?php endif; ?>

    </div>
</div>
