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
<?php
$this->beginWidget('zii.widgets.jui.CJuiDialog', array(
    'id' => 'add-new-episode-dialog',
    'options' => array(
        'title' => 'Create new ' . strtolower(Episode::getEpisodeLabel()),
        'dialogClass' => 'dialog episode add-episode',
        'autoOpen' => true,
        'modal' => true,
        'draggable' => false,
        'resizable' => false,
        'width' => 580,
    ),
));
?>
	<?php
    $form = $this->beginWidget('CActiveForm', array(
        'id' => 'add-new-episode-form',
    ));
    ?>
		<?=\CHtml::hiddenField('firm_id', $firm->id)?>
		<?=\CHtml::hiddenField('patient_id', $patient->id)?>
		<div class="details">
			<p><span>Firm:</span> <strong><?php echo $firm->name?></strong></p>
			<p><span>Subspecialty:</span> <strong><?php echo $firm->getSubspecialtyText()?></strong></p>
		</div>
		<div class="buttons">
			<button class="secondary small confirm" type="button">Create new <?= strtolower(Episode::getEpisodeLabel()) ?></button>
			<button class="warning small cancel" type="button">Cancel</button>
		</div>
	<?php $this->endWidget()?>
<?php $this->endWidget()?>
<script type="text/javascript">
	$('#add-new-episode-dialog button.confirm').click(function(e) {
		disableButtons();
		$('#add-new-episode-form').submit();
		e.preventDefault();
	});
	$('#add-new-episode-dialog button.cancel').click(function(e) {

		$('#add-new-episode-dialog').dialog('close');
		$('#add-new-episode-dialog').hide();
		$('.details').hide();
		$('.buttons').hide();

		e.preventDefault();
	});

	$('.ui-dialog-titlebar-close').click(function(e) {
		$('#add-new-episode-dialog').dialog('close');
		$('#add-new-episode-dialog').hide();
		$('.details').hide();
		$('.buttons').hide();
		e.preventDefault();
	});

</script>
