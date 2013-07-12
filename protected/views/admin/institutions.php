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
		<h3 class="georgia">Institution</h3>
		<div class="pagination">
			<?php echo $this->renderPartial('_pagination',array(
				'prefix' => '/admin/institutions/',
				'page' => $institutions['page'],
				'pages' => $institutions['pages'],
			))?>
		</div>
		<div>
			<form id="admin_institutions">
				<ul class="grid reduceheight">
					<li class="header">
						<span class="column_id">ID</span>
						<span class="column_name">Name</span>
						<span class="column_remote_id">Remote ID</span>
						<span class="column_shortname">Short name</span>
					</li>
					<div class="sortable">
						<?php
						foreach ($institutions['items'] as $i => $institution) {?>
							<li class="<?php if ($i%2 == 0) {?>even<?php } else {?>odd<?php }?>" data-attr-id="<?php echo $institution->id?>">
								<span class="column_id"><?php echo $institution->id?></span>
								<span class="column_name"><?php echo $institution->name?>&nbsp;</span>
								<span class="column_remote_id"><?php echo $institution->remote_id?>&nbsp;</span>
								<span class="column_shortname"><?php echo $institution->short_name?>&nbsp;</span>
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
	$('li.even,li.odd').click(function(e) {
		e.preventDefault();
		window.location.href = baseUrl+'/admin/editinstitution?institution_id='+$(this).attr('data-attr-id');
	});
</script>
