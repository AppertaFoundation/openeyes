<?php
/**
 * (C) OpenEyes Foundation, 2018
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
?>
<div class="box admin">
  <div class="cols-8 column">
    <h2>Anaesthetic Agents</h2>
  </div>
	<form id="admin_anaesthetic_agent">
		<input type="hidden" name="YII_CSRF_TOKEN" value="<?php echo Yii::app()->request->csrfToken?>" />
		<table class="standard">
			<thead>
			<tr>
				<th>Agent Name</th>
				<th>Action</th>
			</tr>
			</thead>
			<tbody>
			<?php foreach (AnaestheticAgent::model()->findAll(array('order' => 'display_order asc')) as $i => $anaestheticAgent) {?>
				<tr>
					<td><?php echo $anaestheticAgent->name?></td>
					<td>
						<a href="/admin/editAnaestheticAgent/<?= $anaestheticAgent->id ?>">Edit</a>
						&nbsp;|&nbsp;
						<a href="/admin/deleteAnaestheticAgent/<?= $anaestheticAgent->id ?>">Delete</a>
					</td>
				</tr>
			<?php }?>
			</tbody>
			<tfoot class="pagination-container">
			<tr>
				<td colspan="3">
					<?php echo EventAction::button('Add', 'add', null, array('class' => 'small', 'data-uri' => '/OphTrOperationnote/admin/addPostOpDrug'))->toHtml()?>
					<?php echo EventAction::button('Delete', 'delete', null, array('class' => 'small', 'data-uri' => '/OphTrOperationnote/admin/deletePostOpDrugs', 'data-object' => 'drug'))->toHtml()?>
				</td>
			</tr>
			</tfoot>
		</table>
	</form>
</div>