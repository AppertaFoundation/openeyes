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
<div class="box admin">
	<h2>Commissioning body service types</h2>
	<form id="admin_commissioning_body_service_types">
		<table class="grid">
			<thead>
				<tr>
					<th><input type="checkbox" id="checkall" class="commissioning_body_service_type" /></th>
					<th>Code</th>
					<th>Name</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach (CommissioningBodyServiceType::model()->findAll(array('order'=>'name asc')) as $i => $cb) {?>
					<tr class="clickable" data-id="<?php echo $cb->id?>" data-uri="admin/editCommissioningBodyServiceType?commissioning_body_service_type_id=<?php echo $cb->id?>">
						<td><input type="checkbox" name="commissioning_body_service_type[]" value="<?php echo $cb->id?>" class="wards" /></td>
						<td><?php echo $cb->shortname?></td>
						<td><?php echo $cb->name?></td>
					</tr>
				<?php }?>
			</tbody>
			<tfoot>
				<tr>
					<td colspan="3">
						<?php echo EventAction::button('Add', 'add_commissioning_body_service_type', array(), array('class' => 'small'))->toHtml()?>
						<?php echo EventAction::button('Delete', 'delete_commissioning_body_service_type', array(), array('class' => 'small'))->toHtml()?>
					</td>
				</tr>
			</tfoot>
		</table>
	</form>
</div>
<div id="confirm_delete_commissioning_body_service_types" title="Confirm delete commissioning_body_service_type" style="display: none;">
	<div>
		<div id="delete_commissioning_body_service_types">
			<div class="alertBox" style="margin-top: 10px; margin-bottom: 15px;">
				<strong>WARNING: This will remove the commissioning body service types from the system.<br/>This action cannot be undone.</strong>
			</div>
			<p>
				<strong>Are you sure you want to proceed?</strong>
			</p>
			<div class="buttonwrapper" style="margin-top: 15px; margin-bottom: 5px;">
				<input type="hidden" id="medication_id" value="" />
				<button type="submit" class="classy red venti btn_remove_commissioning_body_service_types"><span class="button-span button-span-red">Remove commissioning body service types(s)</span></button>
				<button type="submit" class="classy green venti btn_cancel_remove_commissioning_body_service_types"><span class="button-span button-span-green">Cancel</span></button>
				<img class="loader" src="<?php echo Yii::app()->assetManager->createUrl('img/ajax-loader.gif')?>" alt="loading..." style="display: none;" />
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">
	$('li.even .column_code, li.even .column_name, li.even .column_type, li.even .column_address, li.odd .column_code, li.odd .column_name, li.odd .column_type, li.odd .column_address').click(function(e) {
		e.preventDefault();
		window.location.href = baseUrl+'/admin/editCommissioningBodyServiceType?commissioning_body_service_type_id='+$(this).parent().attr('data-attr-id');
	});

	$('#et_add_commissioning_body_service_type').click(function(e) {
		e.preventDefault();
		window.location.href = baseUrl+'/admin/addCommissioningBodyServiceType';
	});

	$('#checkall').click(function(e) {
		$('input[name="commissioning_body_service_type[]"]').attr('checked',$(this).is(':checked') ? 'checked' : false);
	});

	$('#et_delete_commissioning_body_service_type').click(function(e) {
		e.preventDefault();

		if ($('input[type="checkbox"][name="commissioning_body_service_type[]"]:checked').length <1) {
			alert("Please select the commissioning body service types you wish to delete.");
			enableButtons();
			return;
		}

		$.ajax({
			'type': 'POST',
			'url': baseUrl+'/admin/verifyDeleteCommissioningBodyServiceTypes',
			'data': $('#admin_commissioning_body_service_types').serialize()+"&YII_CSRF_TOKEN="+YII_CSRF_TOKEN,
			'success': function(resp) {
				var mention = ($('input[type="checkbox"][name="commissioning_body_service_type[]"]:checked').length == 1) ? 'commissioning body service type' : 'commissioning body service types';

				if (resp == "1") {
					enableButtons();

					$('#confirm_delete_commissioning_body_service_types').attr('title','Confirm delete '+mention);
					$('#delete_commissioning_body_service_types').children('div').children('strong').html("WARNING: This will remove the "+mention+" from the system.<br/><br/>This action cannot be undone.");
					$('button.btn_remove_commissioning_body_service_types').children('span').text('Remove '+mention);

					$('#confirm_delete_commissioning_body_service_types').dialog({
						resizable: false,
						modal: true,
						width: 560
					});
				} else {
					alert("One or more of the selected commissioning body service types are in use and so cannot be deleted.");
					enableButtons();
				}
			}
		});
	});

	$('button.btn_cancel_remove_commissioning_body_service_types').click(function(e) {
		e.preventDefault();
		$('#confirm_delete_commissioning_body_service_types').dialog('close');
	});

	handleButton($('button.btn_remove_commissioning_body_service_types'),function(e) {
		e.preventDefault();

		$.ajax({
			'type': 'POST',
			'url': baseUrl+'/admin/deleteCommissioningBodyServiceTypes',
			'data': $('#admin_commissioning_body_service_types').serialize()+"&YII_CSRF_TOKEN="+YII_CSRF_TOKEN,
			'success': function(resp) {
				if (resp == "1") {
					window.location.reload();
				} else {
					alert("There was an unexpected error deleting the commissioning body service types, please try again or contact support for assistance");
					enableButtons();
					$('#confirm_delete_commissioning_body_service_types').dialog('close');
				}
			}
		});
	});
</script>
