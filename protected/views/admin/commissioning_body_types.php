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
		<h3 class="georgia">Commissioning body types</h3>
		<div>
			<form id="admin_commissioning_body_types">
				<ul class="grid reduceheight">
					<li class="header">
						<span class="column_checkbox"><input type="checkbox" id="checkall" class="commissioning_body_types" /></span>
						<span class="column_code">Code</span>
						<span class="column_name">Name</span>
						<span class="column_type">Type</span>
						<span class="column_address">Address</span>
					</li>
					<div class="sortable">
						<?php foreach (CommissioningBody::model()->findAll(array('order'=>'name asc')) as $i => $cb) {?>
							<li class="<?php if ($i%2 == 0) {?>even<?php } else {?>odd<?php }?>" data-attr-id="<?php echo $cb->id?>">
								<span class="column_checkbox"><input type="checkbox" name="commissioning_body[]" value="<?php echo $cb->id?>" class="wards" /></span>
								<span class="column_code"><?php echo $cb->code?></span>
								<span class="column_name"><?php echo $cb->name?></span>
								<span class="column_type"><?php echo $cb->type->name?></span>
								<span class="column_address"><?php echo $cb->address ? $cb->address->address1 : 'None'?></span>
							</li>
						<?php }?>
					</div>
				</ul>
			</form>
		</div>
	</div>
</div>
<div>
	<?php echo EventAction::button('Add', 'add_commissioning_body', array('colour' => 'blue'))->toHtml()?>
	<?php echo EventAction::button('Delete', 'delete_commissioning_body', array('colour' => 'blue'))->toHtml()?>
</div>
<div id="confirm_delete_commissioning_bodys" title="Confirm delete commissioning_body" style="display: none;">
	<div>
		<div id="delete_commissioning_bodys">
			<div class="alertBox" style="margin-top: 10px; margin-bottom: 15px;">
				<strong>WARNING: This will remove the commissioning bodies from the system.<br/>This action cannot be undone.</strong>
			</div>
			<p>
				<strong>Are you sure you want to proceed?</strong>
			</p>
			<div class="buttonwrapper" style="margin-top: 15px; margin-bottom: 5px;">
				<input type="hidden" id="medication_id" value="" />
				<button type="submit" class="classy red venti btn_remove_commissioning_bodies"><span class="button-span button-span-red">Remove commissioning bodies(s)</span></button>
				<button type="submit" class="classy green venti btn_cancel_remove_commissioning_bodies"><span class="button-span button-span-green">Cancel</span></button>
				<img class="loader" src="<?php echo Yii::app()->createUrl('img/ajax-loader.gif')?>" alt="loading..." style="display: none;" />
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">
	handleButton($('#et_delete_commissioning_bodies'),function(e) {
		e.preventDefault();

		if ($('input[type="checkbox"][name="commissioning_body[]"]:checked').length <1) {
			alert("Please select the commissioning bodies you wish to delete.");
			enableButtons();
			return;
		}

		$.ajax({
			'type': 'POST',
			'url': baseUrl+'/OphTrOperationbooking/admin/verifyDeleteWards',
			'data': $('form#wards').serialize()+"&YII_CSRF_TOKEN="+YII_CSRF_TOKEN,
			'success': function(resp) {
				if (resp == "1") {
					enableButtons();

					if ($('input[type="checkbox"][name="ward[]"]:checked').length == 1) {
						$('#confirm_delete_wards').attr('title','Confirm delete ward');
						$('#delete_wards').children('div').children('strong').html("WARNING: This will remove the ward from the system.<br/><br/>This action cannot be undone.");
						$('button.btn_remove_wards').children('span').text('Remove ward');
					} else {
						$('#confirm_delete_wards').attr('title','Confirm delete wards');
						$('#delete_wards').children('div').children('strong').html("WARNING: This will remove the wards from the system.<br/><br/>This action cannot be undone.");
						$('button.btn_remove_wards').children('span').text('Remove wards');
					}

					$('#confirm_delete_wards').dialog({
						resizable: false,
						modal: true,
						width: 560
					});
				} else {
					alert("One or more of the selected wards have active future bookings and so cannot be deleted.");
					enableButtons();
				}
			}
		});
	});

	$('button.btn_cancel_remove_wards').click(function(e) {
		e.preventDefault();
		$('#confirm_delete_wards').dialog('close');
	});

	handleButton($('button.btn_remove_wards'),function(e) {
		e.preventDefault();

		// verify again as a precaution against race conditions
		$.ajax({
			'type': 'POST',
			'url': baseUrl+'/OphTrOperationbooking/admin/verifyDeleteWards',
			'data': $('form#wards').serialize()+"&YII_CSRF_TOKEN="+YII_CSRF_TOKEN,
			'success': function(resp) {
				if (resp == "1") {
					$.ajax({
						'type': 'POST',
						'url': baseUrl+'/OphTrOperationbooking/admin/deleteWards',
						'data': $('form#wards').serialize()+"&YII_CSRF_TOKEN="+YII_CSRF_TOKEN,
						'success': function(resp) {
							if (resp == "1") {
								window.location.reload();
							} else {
								alert("There was an unexpected error deleting the wards, please try again or contact support for assistance");
								enableButtons();
								$('#confirm_delete_wards').dialog('close');
							}
						}
					});
				} else {
					alert("One or more of the selected wards now have active future bookings and so cannot be deleted.");
					enableButtons();
					$('#confirm_delete_wards').dialog('close');
				}
			}
		});
	});

	$('li.even, li.odd').click(function(e) {
		e.preventDefault();
		window.location.href = baseUrl+'/admin/editCommissioningBody?commissioning_body_id='+$(this).attr('data-attr-id');
	});

	$('#et_add_commissioning_body').click(function(e) {
		e.preventDefault();
		window.location.href = baseUrl+'/admin/addCommissioning_body';
	});
</script>
