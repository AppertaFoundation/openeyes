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
<?php if(BaseController::checkUserLevel(3)) { ?>
<div id="add_event_wrapper">
	<button tabindex="2" class="classy venti green" id="addNewEvent" type="submit" style="float: right; margin-right: 1px;"><span class="button-span button-span-green with-plussign">add new Event</span></button>
	<div id="add-event-select-type" class="whiteBox addEvent clearfix" style="display: none;">
		<h3>Adding New Event</h3>
		<p><strong>Select event to add:</strong></p>
		<?php foreach ($eventTypes as $eventType) {
			if (!$eventType->disabled && $this->checkEventAccess($eventType)) {
				if (file_exists(Yii::getPathOfAlias('application.modules.'.$eventType->class_name.'.assets.img'))) {
					$assetpath = Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('application.modules.'.$eventType->class_name.'.assets.img').'/').'/';
				} else {
					$assetpath = '/assets/';
				}
				?>
				<p><?php echo CHtml::link('<img src="'.$assetpath.'small.png" alt="operation" /> - <strong>'.$eventType->name.'</strong>',Yii::app()->createUrl($eventType->class_name.'/Default/create').'?patient_id='.$this->patient->id)?></p>
			<?php }else{
				if (file_exists(Yii::getPathOfAlias('application.modules.'.$eventType->class_name.'.assets.img'))) {
					$assetpath = Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('application.modules.'.$eventType->class_name.'.assets.img').'/').'/';
				} else {
					$assetpath = '/assets/';
				}
				?>
				<p id="<?php echo $eventType->class_name?>_disabled" class="add_event_disabled" data-title="<?php echo $eventType->disabled_title?>" data-detail="<?php echo $eventType->disabled_detail?>">
					<?php echo CHtml::link('<img src="'.$assetpath.'small.png" alt="operation" /> - <strong>'.$eventType->name.'</strong>','#')?>
				</p>
			<?php }?>
		<?php }?>
		<div id="add-event-disabled-pop-up">
			<h3 id="add-event-disabled-pop-up-title"></h3>
			<p id="add-event-disabled-pop-up-detail"></p>
		</div>
	</div>
</div>
<script type="text/javascript">
	$('p.add_event_disabled').hover(function(e) {
		$('#add-event-disabled-pop-up-title').text($(this).attr('data-title'));
		$('#add-event-disabled-pop-up-detail').text($(this).attr('data-detail'));
		$('#add-event-disabled-pop-up').show();

	}, function() {
		$('#add-event-disabled-pop-up').hide();
	});
</script>
<?php } ?>