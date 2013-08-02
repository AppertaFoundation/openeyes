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
$this->beginWidget('zii.widgets.jui.CJuiDialog', array(
	'id' => 'add-new-episode-dialog',
	'options' => array(
		'title' => 'Create new episode',
		'dialogClass' => 'dialog episode',
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
<?php echo CHtml::hiddenField('firm_id',$firm->id)?>
<?php echo CHtml::hiddenField('patient_id',$patient->id)?>

<div class="title">
	<div class="details">
		<p><span>Firm:</span><strong><?php echo $firm->name?></strong></p>
		<p><span>Subspecialty:</span><strong><?php echo $firm->serviceSubspecialtyAssignment->subspecialty->name?></strong></p>
	</div>
	<div class="buttons">
		<button class="classy green mini confirm" type="button"><span class="button-span button-span-green">Create new episode</span></button>
		<button class="classy blue mini cancel" type="button"><span class="button-span button-span-blue">Cancel</span></button>
	</div>
</div>
<?php $this->endWidget()?>
<?php $this->endWidget()?>
<script type="text/javascript">
	$('#add-new-episode-dialog button.confirm').click(function(e) {
		$('#add-new-episode-form').submit();
		e.preventDefault();
	});
	$('#add-new-episode-dialog button.cancel').click(function(e) {
		$('#add-new-episode-dialog').dialog('close');
		$('#add-new-episode-dialog').remove();
		e.preventDefault();
	});
</script>
