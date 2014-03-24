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
	<h2>Site</h2>
	<form id="admin_institution_sites">
		<table class="grid">
			<thead>
				<tr>
					<th>ID</th>
					<th>Remote ID</th>
					<th>Name</th>
					<th>Address</th>
				</tr>
			</thead>
			<tbody>
				<?php
				foreach ($sites as $i => $site) {?>
					<tr class="clickable" data-id="<?php echo $site->id?>" data-uri="admin/editsite?site_id=<?php echo $site->id?>">
						<td><?php echo $site->id?></td>
						<td><?php echo $site->remote_id?></td>
						<td><?php echo $site->name?></td>
						<td><?php echo $site->getLetterAddress(array('delimiter'=>', '))?></td>
					</tr>
				<?php }?>
			</tbody>
			<tfoot class="pagination-container">
				<tr>
					<td colspan="4">
						<?php echo EventAction::button('Add', 'add', array(), array('class' => 'small'))->toHtml()?>
						<?php echo $this->renderPartial('_pagination',array(
							'pagination' => $pagination
						))?>
					</td>
				</tr>
			</tfoot>
		</table>
	</form>
</div>