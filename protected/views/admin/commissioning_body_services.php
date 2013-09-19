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
<div class="report curvybox white">
	<div class="reportInputs">
		<h3 class="georgia">Commissioning body services</h3>
		<div>
			<form id="admin_commissioning_body_services">
				<ul class="grid reduceheight">
					<li class="header">
						<span class="column_checkbox"><input type="checkbox" id="checkall" class="commissioning_body_services" /></span>
						<span class="column_code">Code</span>
						<span class="column_name">Name</span>
						<span class="column_type">Type</span>
						<span class="column_body">Commissioning body</span>
					</li>
					<div class="sortable">
						<?php foreach (CommissioningBodyService::model()->findAll(array('order'=>'name asc')) as $i => $cbs) {?>
							<li class="<?php if ($i%2 == 0) {?>even<?php } else {?>odd<?php }?>" data-attr-id="<?php echo $cbs->id?>">
								<span class="column_checkbox"><input type="checkbox" name="commissioning_body_service[]" value="<?php echo $cbs->id?>" class="wards" /></span>
								<span class="column_code"><?php echo $cbs->code?></span>
								<span class="column_name"><?php echo $cbs->name?></span>
								<span class="column_type"><?php echo $cbs->type->name?></span>
								<span class="column_body"><?php echo $cbs->commissioning_body ? $cbs->commissioning_body->name : 'None'?></span>
							</li>
						<?php }?>
					</div>
				</ul>
			</form>
		</div>
	</div>
</div>
<div>
	<?php echo EventAction::button('Add', 'add_commissioning_body_service', array('colour' => 'blue'))->toHtml()?>
	<?php echo EventAction::button('Delete', 'delete_commissioning_body_service', array('colour' => 'blue'))->toHtml()?>
</div>
<div id="confirm_delete_commissioning_body_services" title="Confirm delete commissioning_body_service" style="display: none;">
	<div>
		<div id="delete_commissioning_body_services">
			<div class="alertBox" style="margin-top: 10px; margin-bottom: 15px;">
				<strong>WARNING: This will remove the commissioning body services from the system.<br/>This action cannot be undone.</strong>
			</div>
			<p>
				<strong>Are you sure you want to proceed?</strong>
			</p>
			<div class="buttonwrapper" style="margin-top: 15px; margin-bottom: 5px;">
				<button type="submit" class="classy red venti btn_remove_commissioning_body_services"><span class="button-span button-span-red">Remove commissioning body services</span></button>
				<button type="submit" class="classy green venti btn_cancel_remove_commissioning_body_services"><span class="button-span button-span-green">Cancel</span></button>
				<img class="loader" src="<?php echo Yii::app()->createUrl('img/ajax-loader.gif')?>" alt="loading..." style="display: none;" />
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">
	$('li.even .column_code, li.even .column_name, li.even .column_type, li.even .column_address, li.odd .column_code, li.odd .column_name, li.odd .column_type, li.odd .column_address').click(function(e) {
		e.preventDefault();
		window.location.href = baseUrl+'/admin/editCommissioningBodyService?commissioning_body_service_id='+$(this).parent().attr('data-attr-id');
	});

	$('#et_add_commissioning_body_service').click(function(e) {
		e.preventDefault();
		window.location.href = baseUrl+'/admin/addCommissioningBodyService';
	});

	$('#checkall').click(function(e) {
		$('input[name="commissioning_body_service[]"]').attr('checked',$(this).is(':checked') ? 'checked' : false);
	});

	$('#et_delete_commissioning_body_service').click(function(e) {
		e.preventDefault();

		if ($('input[type="checkbox"][name="commissioning_body_service[]"]:checked').length <1) {
			alert("Please select the commissioning body services you wish to delete.");
			enableButtons();
			return;
		}

		$.ajax({
			'type': 'POST',
			'url': baseUrl+'/admin/verifyDeleteCommissioningBodyServices',
			'data': $('#admin_commissioning_bodies').serialize()+"&YII_CSRF_TOKEN="+YII_CSRF_TOKEN,
			'success': function(resp) {
				var mention = ($('input[type="checkbox"][name="commissioning_body_service[]"]:checked').length == 1) ? 'commissioning body' : 'commissioning bodies';

				if (resp == "1") {
					enableButtons();

					$('#confirm_delete_commissioning_body_services').attr('title','Confirm delete '+mention);
					$('#delete_commissioning_body_services').children('div').children('strong').html("WARNING: This will remove the "+mention+" from the system.<br/><br/>This action cannot be undone.");
					$('button.btn_remove_commissioning_body_services').children('span').text('Remove '+mention);

					$('#confirm_delete_commissioning_body_services').dialog({
						resizable: false,
						modal: true,
						width: 560
					});
				} else {
					alert("One or more of the selected commissioning body services are in use and so cannot be deleted.");
					enableButtons();
				}
			}
		});
	});

	$('button.btn_cancel_remove_commissioning_body_services').click(function(e) {
		e.preventDefault();
		$('#confirm_delete_commissioning_body_services').dialog('close');
	});

	handleButton($('button.btn_remove_commissioning_body_services'),function(e) {
		e.preventDefault();

		$.ajax({
			'type': 'POST',
			'url': baseUrl+'/admin/deleteCommissioningBodyServices',
			'data': $('#admin_commissioning_body_services').serialize()+"&YII_CSRF_TOKEN="+YII_CSRF_TOKEN,
			'success': function(resp) {
				if (resp == "1") {
					window.location.reload();
				} else {
					alert("There was an unexpected error deleting the commissioning body services, please try again or contact support for assistance");
					enableButtons();
					$('#confirm_delete_commissioning_body_services').dialog('close');
				}
			}
		});
	});
</script>
