<?php
/**
 * OpenEyes
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2012
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
 * @copyright Copyright (c) 2011-2012, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */
?>
<div class="box admin">
	<div class="row">
		<div class="large-8 column">
			<h2>Settings</h2>
		</div>
		<div class="large-4 column">
			<?php
			$form = $this->beginWidget('BaseEventTypeCActiveForm',array(
				'id' => 'searchform',
				'enableAjaxValidation' => false,
				'focus' => '#search',
				'action' => Yii::app()->createUrl('/admin/settings'),
			))?>
				<div class="row">
					<div class="large-12 column">
						<input type="text" name="search" id="search" placeholder="Enter search query..." value="<?php echo strip_tags(@$_POST['search'])?>" />
					</div>
				</div>
			<?php $this->endWidget()?>
		</div>
	</div>
	<form id="admin_settings">
		<input type="hidden" name="YII_CSRF_TOKEN" value="<?php echo Yii::app()->request->csrfToken?>" />
		<table class="grid">
			<thead>
				<tr>
					<th>Setting</th>
					<th>Value</th>
				</tr>
			</thead>
			<tbody>
				<?php
				foreach (SettingMetadata::model()->findAll('element_type_id is null') as $metadata) {?>
					<tr class="clickable" data-key="<?php echo $metadata->key?>">
						<td><?php echo $metadata->name?></td>
						<td><?php echo $metadata->getSettingName()?></td>
					</tr>
				<?php }?>
			</tbody>
		</table>
	</form>
</div>
