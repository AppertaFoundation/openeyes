<?php
/**
 * OpenEyes
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */
?>

<?php
$exam_api = Yii::app()->moduleAPI->get('OphCiExamination');
$current_episode = $this->patient->getEpisodeForCurrentSubspecialty();
$event_date = null;
if ($event = $element->event) {
    $event_date = $event->created_date;
}
$hide_fluid = true;
if (@$_POST[CHtml::modelName($element)]) {
    if ($_POST[CHtml::modelName($element)][$side . '_dry'] == '0') {
        $hide_fluid = false;
    }
} else {
    if ($element->{$side . '_dry'} === "0") {
        $hide_fluid = false;
    }
}
?>

<?php echo $form->dropDownList($element, $side . '_method_id', '\OEModule\OphCiExamination\models\OphCiExamination_OCT_Method', array(), false, array('label'=>4, 'field'=>3)) ?>

<?php echo $form->textField($element, $side . '_crt', array('autocomplete' => Yii::app()->params['html_autocomplete'], 'append-text' => '&micro;m'), null, array('label'=>4, 'field'=>'3', 'append-text'=>'4')) ?>

<div class="data-row row">
	<div class="large-4 column">
		<label for="<?php echo CHtml::modelName($element).'_'.$side.'_sft';?>">
			<?php echo $element->getAttributeLabel($side . '_sft') ?>:
		</label>
	</div>
	<div class="large-3 column">
		<?php echo $form->textField($element, $side . '_sft', array('autocomplete' => Yii::app()->params['html_autocomplete'], 'nowrapper' => true)) ?>
	</div>
	<div class="large-4 column end collapse">
		<span class="field-info">&micro;m&nbsp;&nbsp;</span>
		<?php if ($past_sft = $exam_api->getOCTSFTHistoryForSide($current_episode, $side, $event_date)) {
    ?>
			<span id="<?php echo $side;
    ?>_sft_history_icon" class="sft-history-icon">
				<img src="<?php echo $this->assetPath ?>/img/icon_info.png" style="height:20px" />
			</span>
			<div class="quicklook sft-history" style="display: none;">
				<?php
                echo '<b>Previous SFT Measurements</b><br />';
    echo '<dl style="margin-top: 0px; margin-bottom: 2px;">';
    foreach ($past_sft as $previous) {
        echo '<dt>' . Helper::convertDate2NHS($previous['date']) . ' - ' . $previous['sft'] . '&micro;m</dt>';
    }
    echo '</dl>';
    ?>
			</div>
		<?php 
} ?>
	</div>
</div>

<?php echo $form->radioBoolean($element, $side . '_thickness_increase', array(), array('label'=>4, 'field'=>8))?>
<?php echo $form->radioBoolean($element, $side . '_dry', array(), array('label'=>4, 'field'=>8)) ?>

<div class="field-row" id="<?php echo CHtml::modelName($element) . '_' . $side; ?>_fluid_fields"<?php if ($hide_fluid) {
    echo ' style="display: none;"';
}?>>
	<?php
    $html_options = array(
        'style' => 'margin-bottom: 10px; width: 240px;',
        'options' => array(),
        'empty' => '- Please select -',
        'div_id' =>  CHtml::modelName($element) . '_' . $side . '_fluidtypes',
        'label' => 'Findings');
    $fts = \OEModule\OphCiExamination\models\OphCiExamination_OCT_FluidType::model()->activeOrPk($element->fluidTypeValues)->findAll();
    foreach ($fts as $ft) {
        $html_options['options'][(string) $ft->id] = array('data-order' => $ft->display_order);
    }
    echo $form->multiSelectList($element, CHtml::modelName($element) . '[' . $side . '_fluidtypes]', $side . '_fluidtypes', 'id', CHtml::listData($fts, 'id', 'name'), array(), $html_options, false, false, null, false, false, array('label'=>4, 'field'=>6));
    ?>
	<div class="row">
		<div class="large-4 column">
			<label for="<?php echo CHtml::modelName($element).'_'.$side.'_fluidstatus_id';?>">
				<?php echo $element->getAttributeLabel($side . '_fluidstatus_id'); ?>:
			</label>
		</div>
		<div class="large-6 column end">
			<?php echo $form->dropDownList($element, $side . '_fluidstatus_id', '\OEModule\OphCiExamination\models\OphCiExamination_OCT_FluidStatus', array('nowrapper' => true, 'empty' => ' - Please Select - ')) ?>
		</div>
	</div>
</div>

<?php echo $form->textArea($element, $side . '_comments', array(), false, array(), array('label'=>4, 'field'=>8))?>
