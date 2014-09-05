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
<form id="current_report" action="<?php echo Yii::app()->createUrl('report/downloadReport')?>" method="post">
	<input type="hidden" name="YII_CSRF_TOKEN" value="<?php echo Yii::app()->request->csrfToken?>" />
	<input type="hidden" name="report-name" value="Letters" />
	<input type="hidden" name="start_date" value="<?php echo $report->start_date?>" />
	<input type="hidden" name="end_date" value="<?php echo $report->end_date?>" />
	<?php foreach ($report->phrases as $phrase) {
		if ($phrase) {?>
			<input type="hidden" name="phrases[]" value="<?php echo CHtml::encode($phrase)?>" />
		<?php }?>
	<?php }?>
	<input type="hidden" name="condition_type" value="<?php echo $report->condition_type?>" />
	<input type="hidden" name="match_correspondence" value="<?php echo $report->match_correspondence?>" />
	<input type="hidden" name="match_legacy_letters" value="<?php echo $report->match_legacy_letters?>" />
	<input type="hidden" name="author_id" value="<?php echo $report->author_id?>" />
</form>
<table>
	<thead>
		<tr>
			<th><?php echo Patient::model()->getAttributeLabel('hos_num')?></th>
			<th><?php echo Patient::model()->getAttributeLabel('dob')?></th>
			<th><?php echo Patient::model()->getAttributeLabel('first_name')?></th>
			<th><?php echo Patient::model()->getAttributeLabel('last_name')?></th>
			<th>Date</th>
			<th>Type</th>
			<th>Link</th>
		</tr>
	<tbody>
		<?php if (empty($report->letters)) {?>
			<tr>
				<td colspan="6">
					No letters were found with the selected search criteria.
				</td>
			</tr>
		<?php }else{?>
			<?php foreach ($report->letters as $letter) {?>
				<tr>
					<td><?php echo $letter['hos_num']?></td>
					<td><?php echo $letter['dob'] ? date('j M Y',strtotime($letter['dob'])) : 'Unknown'?></td>
					<td><?php echo $letter['first_name']?></td>
					<td><?php echo $letter['last_name']?></td>
					<td><?php echo date('j M Y',strtotime($letter['created_date']))?> <?php echo substr($letter['created_date'],11,5)?></td>
					<td><?php echo $letter['type']?></td>
					<td><a href="<?php echo $letter['link']?>">view</a></td>
				</tr>
			<?php }?>
		<?php }?>
	</tbody>
</table>
<div>
	<button type="submit" class="classy blue mini" id="download-report" name="run"><span class="button-span button-span-blue">Download report</span></button>
</div>
