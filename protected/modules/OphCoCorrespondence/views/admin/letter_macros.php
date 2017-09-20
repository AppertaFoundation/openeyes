<?php
/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2012
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2012, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
?>
<div class="box admin">
	<div class="row">
		<div class="large-8 column end">
			<h2>Letter macros</h2>
		</div>
	</div>
	<div class="row data-row">
		<div class="large-4 column end">
			<?php echo CHtml::htmlButton('Add macro', array('class' => 'button small addLetterMacro'))?>
		</div>
	</div>
	<form id="admin_sessions_filters" class="panel">
		<div class="row field-row">
			<div class="large-2 column">
				<?php echo CHtml::dropDownList('type', '', array('site' => 'Site', 'subspecialty' => 'Subspecialty', 'firm' => 'Firm'), array('empty' => '- Type -'))?>
			</div>
			<div class="large-2 column typeSite" style="display: none">
				<?php echo CHtml::dropDownList('site_id', @$_GET['site_id'], Site::model()->getListForCurrentInstitution(), array('empty' => '- Site -'))?>
			</div>
			<div class="large-2 column typeSubspecialty" style="display: none">
				<?php echo CHtml::dropDownList('subspecialty_id', @$_GET['subspecialty_id'], CHtml::listData(Subspecialty::model()->findAll(array('order' => 'name asc')), 'id', 'name'), array('empty' => '- Subspecialty -'))?>
			</div>
			<div class="large-2 column typeFirm" style="display: none">
				<?php echo CHtml::dropDownList('firm_id', @$_GET['firm_id'], Firm::model()->getListWithSpecialties(), array('empty' => '- Firm -'))?>
			</div>
			<div class="large-2 column">
				<?php echo CHtml::dropDownList('name', @$_GET['name'], $unique_names, array('empty' => '- Name -'))?>
			</div>
			<div class="large-2 column end">
				<?php echo CHtml::dropDownList('episode_status_id', @$_GET['episode_status_id'], $episode_statuses, array('empty' => '- Episode status -'))?>
			</div>
		</div>
	</form>
	<form id="admin_letter_macros">
		<input type="hidden" name="YII_CSRF_TOKEN" value="<?php echo Yii::app()->request->csrfToken?>" />
		<table class="grid">
			<thead>
				<tr>
					<th><input type="checkbox" name="selectall" id="selectall" /></th>
					<th>ID</th>
					<th>Owner</th>
					<th>Name</th>
					<th>Recipient</th>
					<th>CC patient</th>
					<th>CC doctor</th>
					<th>CC DRSS</th>
					<th>Use nickname</th>
					<th>Episode status</th>
				</tr>
			</thead>
			<tbody>
				<?php $this->renderPartial('_macros', array('macros' => $macros))?>
			</tbody>
		</table>
	</form>
	<div class="row field-row">
		<div class="large-4 column end">
			<?php echo CHtml::htmlButton('Delete macros', array('class' => 'button small deleteMacros'))?>
		</div>
	</div>
</div>
