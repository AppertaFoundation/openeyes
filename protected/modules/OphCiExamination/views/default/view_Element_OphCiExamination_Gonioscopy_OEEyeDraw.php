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
<div class="column fixed">
	<?php $this->widget('application.modules.eyedraw.OEEyeDrawWidget', array(
            'idSuffix' => $side.'_'.$element->elementType->id.'_'.$element->id,
            'side' => ($side == 'right') ? 'R' : 'L',
            'mode' => 'view',
            'width' => 200,
            'height' => 200,
            'model' => $element,
            'attribute' => $side.'_eyedraw',
    ))?>
</div>
<div class="column fluid">
    <div class="row">
        <div class="column large-4">
            <div class="data-label"><?= $element->getAttributeLabel($side.'_ed_report') ?>:</div>
        </div>
        <div class="column large-8">
            <div class="data-value">
                <?= Yii::app()->format->Ntext($element->{$side.'_ed_report'}) ?>
            </div>
        </div>
    </div>

	<?php if ($element->{$side.'_description'}) {?>
        <div class="row">
            <div class="column large-4">
                <div class="data-label"><?= $element->getAttributeLabel($side.'_description') ?>:</div>
            </div>
            <div class="column large-8">
                <div class="data-value">
                    <?= Yii::app()->format->Ntext($element->{$side.'_description'}) ?>
                </div>
            </div>
        </div>
	<?php }?>

    <div class="shaffer-grade" style="margin-top: 5px;">
		<div class="data-label">Shaffer Grade:</div>
		<div class="gonio-cross">
			<div class="gonio-sup">
				<span class="data-value">
					<?php echo $element->{$side.'_gonio_sup'}->name; ?>
				</span>
			</div>
			<div class="gonio-tem">
				<span class="data-value">
					<?php echo $element->{$side.'_gonio_tem'}->name; ?>
				</span>
			</div>
			<div class="gonio-nas">
				<span class="data-value">
					<?php echo $element->{$side.'_gonio_nas'}->name; ?>
				</span>
			</div>
			<div class="gonio-inf">
				<span class="data-value">
					<?php echo $element->{$side.'_gonio_inf'}->name; ?>
				</span>
			</div>
		</div>
	</div>
	<div class="data-row">
		<span class="data-label">
			<?php echo $element->getAttributeLabel($side.'_van_herick_id') ?>:
		</span>
		<span class="data-value">
			<?php if ($van_herick = $element->{$side.'_van_herick'}) {
    echo $van_herick->name;
} else {
    echo 'NR';
} ?>
		</span>
	</div>
</div>
