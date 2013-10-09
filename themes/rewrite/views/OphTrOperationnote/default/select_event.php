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
	$this->header();
	$assetpath = Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('application.modules.OphTrOperationbooking.assets')).'/';
?>
<h3 class="withEventIcon"><?php echo $this->event_type->name ?></h3>

<div>
	<?php $form = $this->beginWidget('BaseEventTypeCActiveForm', array(
			'id'=>'clinical-create',
			'enableAjaxValidation'=>false,
			'htmlOptions' => array('class'=>'sliding'),
			// 'focus'=>'#procedure_id'
		));

		// Event actions
		$this->event_actions[] = EventAction::button('Create Operation Note', 'save', array('colour' => 'green'));
		$this->renderPartial('//patient/event_actions');
	?>
	<?php  $this->displayErrors($errors)?>

	<h4>Create Operation note</h4>
	<h3 class="sectiondivider">
		<?php if (count($bookings) >0) {?>
			Please indicate whether this operation note relates to a booking or an unbooked emergency:
		<?php } else {?>
			There are no open bookings in the current episode so only an emergency operation note can be created.
		<?php }?>
	</h3>

	<div class="edetail">
		<div class="label">Select:</div>
		<div class="data">
			<table class="grid nodivider valignmiddle">
				<tbody>
					<?php foreach ($bookings as $booking) {?>
						<tr class="odd clickable">
							<td><input type="radio" value="booking<?php echo $booking->operation->event_id?>" name="SelectBooking" /></td>
							<td><img src="<?php echo Yii::app()->createUrl($assetpath.'img/small.png')?>" alt="op" width="19" height="19" /></td>
							<td><?php echo $booking->operation->booking->session->NHSDate('date')?></td>
							<td>Operation</td>
							<td>
								<?php foreach ($booking->operation->procedures as $i => $procedure) {
									if ($i >0) { echo "<br/>"; }
									echo $procedure->term;
								}?>
							</td>
						</tr>
					<?php }?>
					<tr class="odd clickable">
						<td><input type="radio" value="emergency" name="SelectBooking" <?php if (count($bookings)==0) {?>checked="checked" <?php }?>/></td>
						<td></td>
						<td colspan="3">Emergency</td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>

	<?php  $this->displayErrors($errors)?>

	<div class="cleartall"></div>
	<?php  $this->endWidget(); ?>
</div>

<?php  $this->footer(); ?>
