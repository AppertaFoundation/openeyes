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
<div class="banner compact">
	<div class="logo"><img src="<?php echo Yii::app()->assetManager->createUrl('img/_print/letterhead_Moorfields_NHS.jpg')?>" alt="letterhead_Moorfields_NHS" /></div>
</div>
<h1>Transport Form</h1>
<table>
	<tr>
		<th>Transport request to</th>
		<td><?php echo $transport['request_to'] ?></td>
	</tr>
	<tr>
		<th>Transport request from</th>
		<td><?php echo $transport['request_from'] ?></td>
	</tr>
	<tr>
		<th>Date</th>
		<td><?php echo date(Helper::NHS_DATE_FORMAT) ?></td>
	</tr>
</table>
<p class="centered">
		<strong>Request for non-urgent transport</strong>
		<br /><?php echo $patient->id ?> - <?php echo $patient->fullname ?>
		<br />Area: <?php echo $patient->contact->homeAddress->postcode ?>
		<br />Date: <?php echo date(Helper::NHS_DATE_FORMAT, strtotime($booking->session->date)) ?>
</p>
<p>
	Please transport the patient from the following address to the hospital on <?php echo date(Helper::NHS_DATE_FORMAT, strtotime($booking->session->date)) ?>
</p>
<p>
	<?php echo $patient->fullname ?>
	<br /><?php echo $patient->contact->homeAddress->getLetterHtml() ?>
	<br />Telephone: <?php echo $patient->primary_phone ?>
</p>
<p>
	<?php echo $patient->fullname ?> is due to attend <strong><?php echo $booking ? $booking->ward->name.', ' : '' ?><?php echo $booking->theatre->site->name?></strong> at <strong><?php echo date('g:i A', strtotime($booking->admission_time)) ?></strong>
</p>
<table>
	<tr>
		<th>Escort</th>
		<td><?=\CHtml::encode($transport['escort'])?></td>
	</tr>
	<tr>
		<th>Mobility</th>
		<td><?=\CHtml::encode($transport['mobility'])?></td>
	</tr>
	<tr>
		<th>Age</th>
		<td><?php echo $patient->age ?></td>
	</tr>
	<tr>
		<th>Comments</th>
		<td><?=\CHtml::encode($transport['comments'])?></td>
	</tr>
	<tr>
		<th>Oxygen</th>
		<td><?=\CHtml::encode($transport['oxygen'])?></td>
	</tr>
</table>
<p>
	Authorised by: <strong><?=\CHtml::encode($transport['request_from'])?></strong>
</p>
<p>
	If you have any questions regarding the above booking, please telephone <?=\CHtml::encode($transport['contact_name'])?> on <?=\CHtml::encode($transport['contact_number'])?>
</p>
