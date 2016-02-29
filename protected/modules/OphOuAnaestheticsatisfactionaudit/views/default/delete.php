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
<?php $this->beginContent('//patient/event_container'); ?>

	<?php echo CHtml::form(array('Default/delete/'.$this->event->id), 'post', array('id' => 'deleteForm'))?>
	<div id="delete_event">
		<h3>Delete event</h3>
		<div class="alert-box alert with-icon">
			<strong>WARNING: This will permanently delete the event and remove it from view.<br><br>THIS ACTION CANNOT BE UNDONE.</strong>
		</div>
		<?php $this->displayErrors(@$errors)?>
		<div style="width:300px; margin-bottom: 0.6em;">
			<p>Reason for deletion:</p>
			<?php echo CHtml::textArea('delete_reason','')?>
		</div>
		<p>
			<strong>Are you sure you want to proceed?</strong>
		</p>
		<?php
			echo CHtml::hiddenField('event_id', $this->event->id); ?>
			<button type="submit" class="warning" id="et_deleteevent" name="et_deleteevent">
				Delete event
			</button>
			<button type="submit" class="secondary" id="et_canceldelete" name="et_canceldelete">
				Cancel
			</button>
			<img class="loader" src="<?php echo Yii::app()->assetManager->createUrl('img/ajax-loader.gif')?>" alt="loading..." style="display: none;" />
		<?php echo CHtml::endForm(); ?>
	</div>

<?php $this->endContent() ?>
