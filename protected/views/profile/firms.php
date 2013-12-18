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
	<h2>Firms you work in</h2>
	<form id="profile_firms" method="post" action="/profile/firms">
		<table class="grid">
			<thead>
				<tr>
					<th><input type="checkbox" id="checkall" /></th>
					<th>Name</th>
					<th>Subspecialty</th>
				</tr>
			</thead>
			<tbody>
				<?php
				foreach ($user->firmSelections as $i => $firm) {?>
					<tr data-attr-id="<?php echo $firm->id?>">
						<td><input type="checkbox" name="firms[]" value="<?php echo $firm->id?>" /></td>
						<td><?php echo $firm->name?></td>
						<td><?php echo $firm->subspecialtyText?></td>
					</tr>
				<?php }?>
			</tbody>
		</table>
	</form>

	<div class="row">
		<div class="large-6 column">
			<?php echo EventAction::button('Delete', 'delete', array(), array('class' => 'small'))->toHtml()?>
		</div>
		<div class="large-6 column text-right table-actions">
			<label for="profile_firm_id" class="inline">Add firm:</label>
			<?php echo CHtml::dropDownList('profile_firm_id','',$user->getNotSelectedFirmList(),array('empty'=>'- Select -'))?>
			<?php echo CHtml::link('Add all','#',array('id'=>'add_all','class'=>'field-info'))?>
		</div>
	</div>

</div>

<div class="box admin">
	<p>Note: you can also set the sites you work at, <?php echo CHtml::link('click here',Yii::app()->createUrl('/profile/sites'))?> to do so.</p>
</div>

<script type="text/javascript">
	$('#profile_firm_id').change(function(e) {
		var firm_id = $(this).val();

		if (firm_id != '') {
			$.ajax({
				'type': 'POST',
				'url': baseUrl+'/profile/addfirm',
				'data': 'firm_id='+firm_id+"&YII_CSRF_TOKEN="+YII_CSRF_TOKEN,
				'success': function(html) {
					if (html == "1") {
						window.location.reload();
					}
				}
			});
		}

		$(this).val('');
	});

	$('#checkall').click(function() {
		$('input[name="firms[]"]').attr('checked',$(this).is(':checked') ? 'checked' : false);
	});

	$('#et_delete').click(function() {
		$('#profile_firms').submit();
	});

	$('#add_all').click(function() {
		$.ajax({
			'type': 'POST',
			'url': baseUrl+'/profile/addfirm',
			'data': 'firm_id=all&YII_CSRF_TOKEN='+YII_CSRF_TOKEN,
			'success': function(html) {
				if (html == "1") {
					window.location.reload();
				} else {
					new OpenEyes.UI.Dialog.Alert({
						content: "Something went wrong trying to add the firms.	Please try again or contact support for assistance."
					}).open();
				}
			}
		});
	});
</script>
