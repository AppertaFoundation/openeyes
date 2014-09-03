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
<form id="current_report" action="<?php echo Yii::app()->createUrl('report/downloadDiagnoses')?>" method="post">
	<input type="hidden" name="YII_CSRF_TOKEN" value="<?php echo Yii::app()->request->csrfToken?>" />
	<input type="hidden" name="start-date" value="<?php echo $_POST['start-date']?>" />
	<input type="hidden" name="end-date" value="<?php echo $_POST['end-date']?>" />
	<?php if (!empty($_POST['principal'])) {?>
		<?php foreach ($_POST['principal'] as $disorder_id) {?>
			<input type="hidden" name="principal[]" value="<?php echo $disorder_id?>" />
		<?php }?>
	<?php }?>
	<?php if (!empty($_POST['secondary'])) {?>
		<?php foreach ($_POST['secondary'] as $disorder_id) {?>
			<input type="hidden" name="secondary[]" value="<?php echo $disorder_id?>" />
		<?php }?>
	<?php }?>
	<input type="hidden" name="condition" value="<?php echo $_POST['condition']?>" />
</form>
<table>
	<thead>
		<tr>
			<th><?php echo Patient::model()->getAttributeLabel('hos_num')?></th>
			<th><?php echo Patient::model()->getAttributeLabel('dob')?></th>
			<th><?php echo Patient::model()->getAttributeLabel('first_name')?></th>
			<th><?php echo Patient::model()->getAttributeLabel('last_name')?></th>
			<th>Date</th>
			<th>Diagnoses</th>
		</tr>
	<tbody>
		<?php if (empty($diagnoses)) {?>
			<tr>
				<td colspan="6">
					No patients were found with the selected search criteria.
				</td>
			</tr>
		<?php }else{?>
			<?php foreach ($diagnoses as $ts => $diagnosis) {?>
				<tr>
					<td><?php echo $diagnosis['hos_num']?></td>
					<td><?php echo $diagnosis['dob'] ? date('j M Y',strtotime($diagnosis['dob'])) : 'Unknown'?></td>
					<td><?php echo $diagnosis['first_name']?></td>
					<td><?php echo $diagnosis['last_name']?></td>
					<td><?php echo date('j M Y',$ts)?></td>
					<td>
						<?php
						$_diagnosis = array_shift($diagnosis['diagnoses']);
						echo $_diagnosis['eye'].' '.$_diagnosis['disorder'].' ('.$_diagnosis['type'].')';
						?>
					</td>
				</tr>
				<?php foreach ($diagnosis['diagnoses'] as $_diagnosis) {?>
					<tr>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td><?php echo date('j M Y',strtotime($_diagnosis['date']))?></td>
						<td>
							<?php echo $_diagnosis['eye'].' '.$_diagnosis['disorder'].' ('.$_diagnosis['type'].')'?>
						</td>
					</tr>
				<?php }?>
			<?php }?>
		<?php }?>
	</tbody>
</table>
<div>
	<button type="submit" class="classy blue mini" id="diagnoses_report_download" name="run"><span class="button-span button-span-blue">Download report</span></button>
</div>
