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

<section class="<?php echo $type; ?> box patient-info js-toggle-container element">
	<h3 class="box-title">Personal Details:</h3>
	
        <?=\CHtml::activeHiddenField($model, "{$type}_id", array('class' => 'id-input')); ?>
        
	<div class="js-toggle-body">
        <table class="cols-12">
            <tbody class="cols-12">
            <tr class="cols-full">
                <td class="cols-full" style="text-align: left">
                    <div class="large-4 column">
                        <div class="data-label">Hospital No.</div>
                    </div>
                    <div class="large-8 column">
                        <div style="line-height:1.3">
                            <div class="hospital-number">
                                <span class="data-value hos_num"><?php echo $model->isNewRecord ? '' : $model->{"{$type}_hos_num"}; ?></span>
                                <?=\CHtml::activeHiddenField($model, "{$type}_hos_num", array('class' => 'hos_num-input')); ?>
                            </div>
                        </div>
                    </div>
                </td>
            </tr>
            <tr class="cols-full">
                <td class="cols-full" style="text-align: left">
                    <div class="large-8 column">
                        <div class="">
                            <!-- NHS number -->
                            <div class="nhs-number">
                                <span class="hide-text print-only">
                                    <?php echo Yii::app()->params['nhs_num_label']?> number:
                                </span>
                                <span data-default="000 000 0000" class="data-value nhsnum"><?php echo $model->isNewRecord ? '000 000 0000' : $model->{"{$type}_nhsnum"}; ?></span>
                                <?=\CHtml::activeHiddenField($model, "{$type}_nhsnum", array('class' => 'nhsnum-input')); ?>
                            </div>
                        </div>
                    </div>
                </td>
            </tr>

            <tr class="cols-full">
                <td class="cols-full" style="text-align: left">
                    <div class="large-4 column">
                        <div class="data-label">First name(s):</div>
                    </div>
                    <div class="large-8 column">
                        <div class="data-value first_name">
                            <?php echo $model->isNewRecord ? '' : $model->{"{$type}Patient"}->first_name; ?>
                        </div>
                    </div>
                </td>
            </tr>

            <tr class="cols-full">
                <td class="cols-full" style="text-align: left">
                    <div class="large-4 column">
                        <div class="data-label">Last name:</div>
                    </div>
                    <div class="large-8 column">
                        <div class="data-value last_name"><?php echo $model->isNewRecord ? '' : $model->{"{$type}Patient"}->last_name; ?></div>
                    </div>
                </td>
            </tr>

            <tr class="cols-full">
                <td class="cols-full" style="text-align: left">
                    <div class="large-4 column">
                        <div class="data-label">Date of Birth:</div>
                    </div>
                    <div class="large-8 column">
                        <div class="data-value dob"><?php echo $model->isNewRecord ? '' : $model->{"{$type}Patient"}->NHSDate('dob'); ?></div>
                        <?=\CHtml::activeHiddenField($model, "{$type}_dob", array('class' => 'dob-input')); ?>
                    </div>
                </td>
            </tr>

            <tr class="cols-full">
                <td class="cols-full" style="text-align: left">
                    <div class="large-4 column">
                        <div class="data-label">Gender:</div>
                    </div>
                    <div class="large-8 column">
                        <div class="data-value gender"><?php echo $model->isNewRecord ? '' : $model->{"{$type}Patient"}->getGenderString(); ?></div>
                        <?=\CHtml::activeHiddenField($model, "{$type}_gender", array('class' => 'genderletter-input')); ?>
                    </div>
                </td>
            </tr>
            </tbody>
        </table>
            

	</div>
</section>

<?php if (!$model->isNewRecord): ?>

<?php echo $this->getEpisodesHTML($model->{"{$type}Patient"});?>
<?php echo $this->getGeneticsHTML($model->{"{$type}Patient"}); ?>
<?php endif; ?>
