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
<div class="box admin">
	<h2>Wards</h2>
	<form id="admin_wards">
		<table class="grid">
			<thead>
				<tr>
					<th><input type="checkbox" id="checkall" class="wards" /></th>
					<th>Site</th>
					<th>Name</th>
					<th>Code</th>
					<th>Restrictions</th>
				</tr>
			</thead>
			<tbody class="sortable" data-sort-uri="/OphTrOperationbooking/admin/sortwards">
				<?php
                $criteria = new CDbCriteria();
                $criteria->order = 'display_order asc';
                foreach (OphTrOperationbooking_Operation_Ward::model()->active()->findAll() as $i => $ward) {?>
					<tr class="clickable <?php if ($i % 2 == 0) {?>even<?php } else {?>odd<?php }?>" data-attr-id="<?php echo $ward->id?>" data-uri="OphTrOperationbooking/admin/editWard/<?php echo $ward->id?>">
						<td><input type="checkbox" name="ward[]" value="<?php echo $ward->id?>" class="wards" /></td>
						<td><?php echo $ward->site->name?></td>
						<td><?php echo $ward->name?></td>
						<td><?php echo $ward->code?>&nbsp;</td>
						<td><?php echo $ward->restrictionText?></td>
					</tr>
				<?php }?>
			</tbody>
			<tfoot>
				<tr>
					<td colspan="5">
						<?php echo EventAction::link('Add', '#', null, array('class' => 'small button', 'id' => 'et_add_ward'))->toHtml()?>
						<?php echo EventAction::link('Delete', '#', null, array('class' => 'small button', 'id' => 'et_delete_ward'))->toHtml()?>
					</td>
				</tr>
			</tfoot>
		</table>
	</form>
</div>

<div id="confirm_delete_wards" title="Confirm delete ward" style="display: none;">
	<div id="delete_wards">
		<div class="alert-box alert with-icon">
			<strong>WARNING: This will remove the wards from the system.<br/>This action cannot be undone.</strong>
		</div>
		<p>
			<strong>Are you sure you want to proceed?</strong>
		</p>
		<div class="buttons">
			<input type="hidden" id="medication_id" value="" />
			<button type="submit" class="warning btn_remove_wards">Remove ward(s)</button>
			<button type="submit" class="secondary btn_cancel_remove_wards">Cancel</button>
			<img class="loader" src="<?php echo Yii::app()->assetManager->createUrl('img/ajax-loader.gif')?>" alt="loading..." style="display: none;" />
		</div>
	</div>
</div>

<script type="text/javascript">
	$(document).ready(function() {
		$('.sortable').sortable({
			update: function (event, ui) {
				var ids = [];
				$('tbody.sortable').children('tr').map(function () {
					ids.push($(this).data('attr-id'));
				});
				$.ajax({
					'type': 'POST',
					'url': $('tbody.sortable').data('sort-uri'),
					'data': {order: ids, YII_CSRF_TOKEN: YII_CSRF_TOKEN},
					'success': function (data) {
						new OpenEyes.UI.Dialog.Alert({
							content: 'Re-ordered'
						}).open();
					}
				});

			}
		}).disableSelection();
	});

	handleButton($('#et_delete_ward'),function(e) {
		e.preventDefault();

		if ($('input[type="checkbox"][name="ward[]"]:checked').length <1) {
			new OpenEyes.UI.Dialog.Alert({
				content: "Please select the ward(s) you wish to delete."
			}).open();
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
						$('.btn_remove_wards').children('span').text('Remove ward');
					} else {
						$('#confirm_delete_wards').attr('title','Confirm delete wards');
						$('#delete_wards').children('div').children('strong').html("WARNING: This will remove the wards from the system.<br/><br/>This action cannot be undone.");
						$('.btn_remove_wards').children('span').text('Remove wards');
					}

					$('#confirm_delete_wards').dialog({
						resizable: false,
						modal: true,
						width: 560
					});
				} else {
					new OpenEyes.UI.Dialog.Alert({
						content: "One or more of the selected wards have active future bookings and so cannot be deleted."
					}).open();
					enableButtons();
				}
			}
		});
	});

	$('.btn_cancel_remove_wards').click(function(e) {
		e.preventDefault();
		$('#confirm_delete_wards').dialog('close');
	});

	handleButton($('.btn_remove_wards'),function(e) {
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
								new OpenEyes.UI.Dialog.Alert({
									content: "There was an unexpected error deleting the wards, please try again or contact support for assistance",
									onClose: function() {
										enableButtons();
										$('#confirm_delete_wards').dialog('close');
									}
								}).open();

							}
						}
					});
				} else {
					new OpenEyes.UI.Dialog.Alert({
						content: "One or more of the selected wards now have active future bookings and so cannot be deleted.",
						onClose: function() {
							enableButtons();
							$('#confirm_delete_wards').dialog('close');
						}
					}).open();
				}
			}
		});
	});
</script>
