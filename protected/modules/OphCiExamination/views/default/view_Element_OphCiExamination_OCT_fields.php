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
<div class="row data-row">
	<div class="large-4 column">
		<div class="data-label">
			<?php echo $element->getAttributeLabel($side.'_method_id')?>:
		</div>
	</div>
	<div class="large-8 column">
		<div class="data-value">
			<?php echo $element->{$side.'_method'}->name?>
		</div>
	</div>
</div>
<div class="row data-row">
	<div class="large-4 column">
		<div class="data-label">
			<?php echo $element->getAttributeLabel($side.'_crt')?>:
		</div>
	</div>
	<div class="large-8 column">
		<div class="data-value">
			<?php echo $element->{$side.'_crt'}?> &micro;m
		</div>
	</div>
</div>
<div class="row data-row">
	<div class="large-4 column">
		<div class="data-label">
			<?php echo $element->getAttributeLabel($side.'_sft')?>:
		</div>
	</div>
	<div class="large-8 column">
		<div class="data-value">
			<?php echo $element->{$side.'_sft'}?> &micro;m
		</div>
	</div>
</div>
<div class="row data-row">
	<div class="large-4 column">
		<div class="data-label">
			<?php echo $element->getAttributeLabel($side.'_thickness_increase') ?>:
		</div>
	</div>
	<div class="large-8 column">
		<div class="data-value">
			<?php
                if ($element->{$side.'_thickness_increase'} === null) {
                    echo 'Not recorded';
                } else {
                    echo $element->{$side.'_thickness_increase'} ? 'Yes' : 'No';
                }
            ?>
		</div>
	</div>
</div>
<div class="row data-row">
	<div class="large-4 column">
		<div class="data-label">Finding:</div>
	</div>
	<div class="large-8 column">
		<div class="data-value"><?php echo $element->{'get'.ucfirst($side).'FluidString'}();?></div>
	</div>
</div>
<?php if ($element->{$side.'_comments'}) {
    ?>
	<div class="row data-row">
		<div class="large-4 column">
			<div class="data-label"><?php echo $element->getAttributeLabel($side.'_comments') ?>:</div>
		</div>
		<div class="large-8 column">
			<div class="data-value"><?= Yii::app()->format->Ntext($element->{"{$side}_comments"}) ?></div>
		</div>
	</div>
<?php 
}
