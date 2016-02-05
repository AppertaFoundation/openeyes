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
	<h2>Steps</h2>
	<form id="admin_workflow_steps">
		<table class="grid">
			<thead>
				<tr>
					<th>Position</th>
					<th>Step</th>
					<th>Actions</th>
				</tr>
			</thead>
			<tbody class="sortable">
				<?php
                foreach ($model->steps as $i => $step) {
                    ?>
					<tr class="selectable" data-id="<?php echo $step->id?>">
						<td><?php echo $step->position?></td>
						<td><?php echo $step->name?></td>
						<td><a href="#" class="removeElementSet" rel="<?php echo $step->id?>">Remove</a></td>
					</tr>
				<?php 
                }?>
			</tbody>
			<tfoot class="pagination-container">
				<tr>
					<td colspan="3">
						<?php echo EventAction::button('Add step', 'add_step', null, array('class' => 'small'))->toHtml()?>
					</td>
				</tr>
			</tfoot>
		</table>
	</form>
</div>
<div class="box_admin" id="step_element_types">
</div>
