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
<div class="admin box">
	<h2>Institution</h2>

	<?php $this->widget('GenericSearch', array('search' => $search)); ?>

	<form id="admin_institutions">
		<table class="grid">
			<thead>
				<tr>
					<th>ID</th>
					<th>Name</th>
					<th>Remote ID</th>
					<th>Short name</th>
				</tr>
			</thead>
			<tbody>
				<?php
                foreach ($institutions as $i => $institution) {?>
					<tr class="clickable" data-id="<?php echo $institution->id?>" data-uri="admin/editinstitution?institution_id=<?php echo $institution->id?>">
						<td><?php echo $institution->id?></td>
						<td><?php echo $institution->name?></td>
						<td><?php echo $institution->remote_id?></td>
						<td><?php echo $institution->short_name?></td>
					</tr>
				<?php }?>
			</tbody>
			<tfoot class="pagination-container">
				<tr>
					<td colspan="4">
						<?php echo EventAction::button('Add', 'add', array(), array('class' => 'small'))->toHtml()?>
						<?php echo $this->renderPartial('_pagination', array(
                            'pagination' => $pagination,
                        ))?>
					</td>
				</tr>
			</tfoot>
		</table>
	</form>
</div>