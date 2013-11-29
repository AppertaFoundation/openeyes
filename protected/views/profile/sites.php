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
	<h2>Sites you work at</h2>
	<form id="profile_sites" method="post" action="/profile/sites">
		<table class="grid">
			<thead>
				<tr>
					<th><input type="checkbox" id="checkall" /></th>
					<th>Name</th>
					<th>Address</th>
				</tr>
			</thead>
			<tbody>
				<?php
				foreach ($user->siteSelections as $i => $site) {?>
					<tr data-attr-id="<?php echo $site->id?>">
						<td><input type="checkbox" name="sites[]" value="<?php echo $site->id?>" /></td>
						<td><?php echo $site->name?></td>
						<td><?php echo $site->getLetterAddress(array('delimiter'=>', '))?>&nbsp;</td>
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
			<label for="profile_site_id" class="inline">Add site:</label>
			<?php echo CHtml::dropDownList('profile_site_id','',CHtml::listData($user->getNotSelectedSiteList(),'id','name'),array('empty'=>'- Select -'))?>
			<?php echo CHtml::link('Add all','#',array('id'=>'add_all', 'class' => 'field-info'))?>
		</div>
	</div>
</div>
<div class="box admin">
	<p>Note: you can also set the firms you work at, <?php echo CHtml::link('click here',Yii::app()->createUrl('/profile/firms'))?> to do so.</p>
</div>

<script type="text/javascript">
	$('#profile_site_id').change(function(e) {
		var site_id = $(this).val();

		if (site_id != '') {
			$.ajax({
				'type': 'POST',
				'url': baseUrl+'/profile/addsite',
				'data': 'site_id='+site_id+"&YII_CSRF_TOKEN="+YII_CSRF_TOKEN,
				'success': function(html) {
					if (html == "1") {
						window.location.reload();
					} else {
						new OpenEyes.UI.Dialog.Alert({
							content: "Something went wrong trying to add the site.  Please try again or contact support for assistance."
						}).open();
					}
				}
			});
		}

		$(this).val('');
	});

	$('#checkall').click(function() {
		$('input[name="sites[]"]').attr('checked',$(this).is(':checked') ? 'checked' : false);
	});

	$('#et_delete').click(function() {
		$('#profile_sites').submit();
	});

	$('#add_all').click(function() {
		$.ajax({
			'type': 'POST',
			'url': baseUrl+'/profile/addsite',
			'data': 'site_id=all&YII_CSRF_TOKEN='+YII_CSRF_TOKEN,
			'success': function(html) {
				if (html == "1") {
					window.location.reload();
				} else {
					new OpenEyes.UI.Dialog.Alert({
						content: "Something went wrong trying to add the sites.  Please try again or contact support for assistance."
					}).open();
				}
			}
		});
	});
</script>
