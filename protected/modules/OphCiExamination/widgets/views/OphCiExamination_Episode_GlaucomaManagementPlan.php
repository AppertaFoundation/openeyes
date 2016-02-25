<?php
/**
 * (C) OpenEyes Foundation, 2014
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2014, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */
?>
<?php if ($plan): ?>
	<div class="row data-row">
		<div class="large-6 column">
			<table class="grid plain">
				<tbody>
					<tr>
						<th scope="col">Clinic internal</th>
						<td><?= $plan->clinic_internal->name ?></td>
					</tr>
					<tr>
						<th scope="col">Photo</th>
						<td><?= $plan->photo->name ?></td>
					</tr>
					<tr>
						<th scope="col">OCT</th>
						<td><?= $plan->oct->name ?></td>
					</tr>
					<tr>
						<th scope="col">Visual Fields</th>
						<td><?= $plan->hfa->name ?></td>
					</tr>
					<tr>
						<th scope="col">Gonio</th>
						<td><?= $plan->gonio->name ?></td>
					</tr>
					<?php if (isset($plan->hrt)) {
    ?>
					<tr>
						<th scope="col">HRT</th>
						<td><?= $plan->hrt->name ?></td>
					</tr>
					<?php 
} ?>
				</tbody>
			</table>
		</div>
		<div class="large-6 column">
			<?php if ($plan->comments) {
    ?>
				<div class="data-label">Comments:</div>
				<div class="data-value panel comments"><?= Yii::app()->format->nText($plan->comments) ?></div>
			<?php 
}?>
		</div>
	</div>

	<div class="row">
		<div class="large-6 column">
			<?php if ($plan->hasRight()) {
    $this->render('OphCiExamination_Episode_GlaucomaManagementPlan_side', array('plan' => $plan, 'side' => 'right'));
} ?>
		</div>
		<div class="large-6 column">
			<?php if ($plan->hasLeft()) {
    $this->render('OphCiExamination_Episode_GlaucomaManagementPlan_side', array('plan' => $plan, 'side' => 'left'));
} ?>
		</div>
	</div>
<?php else: ?>
	<div class="row">
		<div class="large-12 column">
			<div class="data-row">
				<div class="data-value">(not recorded)</div>
			</div>
		</div>
	</div>
<?php endif; ?>
