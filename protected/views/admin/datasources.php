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
<div class="report curvybox white">
	<div class="admin">
		<h3 class="georgia">Data sources</h3>
		<div>
			<form id="admin_data_sources">
				<ul class="grid reduceheight">
					<li class="header">
						<span class="column_checkbox"><input type="checkbox" id="checkall" class="sources" /></span>
						<span class="column_name">Name</span>
					</li>
					<div class="sortable">
						<?php
						foreach (ImportSource::model()->findAll(array('order'=>'name')) as $i => $source) {?>
							<li class="<?php if ($i%2 == 0) {?>even<?php } else {?>odd<?php }?>" data-attr-id="<?php echo $source->id?>">
								<span class="column_checkbox"><input type="checkbox" name="source[]" value="<?php echo $source->id?>" class="sources" /></span>
								<span class="column_name"><?php echo $source->name?>&nbsp;</span>
							</li>
						<?php }?>
					</div>
				</ul>
			</form>
		</div>
	</div>
</div>
<div>
	<?php echo EventAction::button('Add', 'add', array('colour' => 'blue'))->toHtml()?>
	<?php echo EventAction::button('Delete', 'delete', array('colour' => 'blue'))->toHtml()?>
</div>
<script type="text/javascript">
	$('li.even .column_name,li.odd .column_name').click(function(e) {
		e.preventDefault();
		window.location.href = baseUrl+'/admin/editdatasource/'+$(this).parent().attr('data-attr-id');
	});
	$('#et_delete').click(function(e) {
		e.preventDefault();

		if ($('input[type="checkbox"][name="source[]"]:checked').length == 0) {
			alert("Please select the source(s) you wish to delete.");
			return;
		}

		$.ajax({
			'type': 'POST',
			'url': baseUrl+'/admin/deleteDataSources',
			'data': $('#admin_data_sources').serialize()+"&YII_CSRF_TOKEN="+YII_CSRF_TOKEN,
			'success': function(resp) {
				if (resp == "1") {
					window.location.reload();
				} else {
					if ($('input[type="checkbox"][name="source[]"]:checked').length == 1) {
						alert("The source you selected is in use and cannot be deleted.");
					} else {
						alert("The sources you selected are in use and cannot be deleted.");
					}
				}
			}
		});
	});
</script>
