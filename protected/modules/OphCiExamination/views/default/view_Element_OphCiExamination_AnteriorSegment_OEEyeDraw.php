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
<div class="field-row">
    <?php
    $this->widget('application.modules.eyedraw.OEEyeDrawWidget', array(
        'idSuffix' => $side.'_'.$element->elementType->id.'_'.$element->id,
        'side' => ($side == 'right') ? 'R' : 'L',
        'mode' => 'view',
        'width' => 200,
        'height' => 200,
        'model' => $element,
        'attribute' => $side.'_eyedraw',
        'toggleScale' => 0.72,
    )); ?>
    <?php
    $this->widget('application.modules.eyedraw.OEEyeDrawWidget', array(
        'idSuffix' => $side.'_'.$element->elementType->id.'_'.$element->id . '_side',
        'side' => ($side == 'right') ? 'R' : 'L',
        'mode' => 'view',
        'width' => 132,
        'height' => 200,
        'model' => $element,
        'attribute' => $side.'_eyedraw2',
        'toggleScale' => 0.72,
        ));
    ?>

</div>

<div class="element-fields column">
    <?php if ($report = $element->{$side.'_ed_report'}) {
        ?>
        <div class="row data-row description">
            <div class="large-2 column">
                <div class="data-label"><?php echo $element->getAttributeLabel($side.'_ed_report')?>:</div>
            </div>
            <div class="large-10 column">
                <div class="data-value">
                    <?php echo nl2br($report)?>
                </div>
            </div>
        </div>
        <?php
    } ?>

    <?php if ($description = $element->{$side.'_description'}) {
    ?>
        <div class="row data-row">
            <div class="large-2 column">
                <div class="data-label"><?php echo $element->getAttributeLabel($side.'_description')?>:</div>
            </div>
            <div class="large-10 column">
                <div class="data-value">
                    <?php echo CHtml::encode($description)?>
                </div>
            </div>
        </div>
    <?php
    } ?>
    <?php /* See OE-4283 */ ?>
    <?php if ($element->{$side.'_pupil'}) {
    ?>
        <div class="row data-row">
            <div class="large-4 column">
                <div class="data-label"><?php echo $element->getAttributeLabel($side.'_pupil_id')?>:</div>
            </div>
            <div class="large-8 column">
                <div class="data-value"><?php echo $element->{$side.'_pupil'}->name?></div>
            </div>
        </div>
    <?php
} ?>
    <?php if ($element->{$side . '_nuclear'}) {
        ?>
        <div class="row data-row hidden">
            <div class="large-4 column">
                <div class="data-label"><?php echo $element->getAttributeLabel($side . '_nuclear_id') ?>:</div>
            </div>
            <div class="large-8 column">
                <div class="data-value"><?php echo $element->{$side . '_nuclear'}->name ?></div>
            </div>
        </div>
        <?php
    }
    if ($element->{$side . '_cortical'}) {
        ?>
    <div class="row data-row hidden">
        <div class="large-4 column">
            <div class="data-label"><?php echo $element->getAttributeLabel($side.'_cortical_id')?>:</div>
        </div>
        <div class="large-8 column">
            <div class="data-value"><?php echo $element->{$side.'_cortical'}->name?></div>
        </div>
    </div>
    <?php
    }

    /* See OE-4283 */
    /*
    <div class="row data-row">
        <div class="large-4 column">
            <div class="data-label"><?php echo $element->getAttributeLabel($side.'_pxe')?>:</div>
        </div>
        <div class="large-8 column">
            <div class="data-value"><?php echo $element->{$side.'_pxe'} ? 'Yes' : 'No'?></div>
        </div>
    </div>
    */
	if ($element->{$side . '_phako'}) {
	?>

    <div class="row data-row">
        <div class="large-4 column">
            <div class="data-label"><?php echo $element->getAttributeLabel($side.'_phako')?>:</div>
        </div>
        <div class="large-8 column">
            <div class="data-value"><?php echo $element->{$side.'_phako'} ? 'Yes' : 'No'?></div>
        </div>
    </div>
	<?php
	} ?>
</div>
