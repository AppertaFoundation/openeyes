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

	<h2>Common subspecialty medications</h2>

	<?php $this->widget('GenericSearch', array('search' => $search)); ?>

	<form id="admin_firms">
		<input type="hidden" name="YII_CSRF_TOKEN" value="<?php echo Yii::app()->request->csrfToken ?>"/>
		<table class="grid">
			<thead>
			<tr>
				<th><input type="checkbox" name="selectall" id="selectall"/></th>
				<th>ID</th>
				<th><a href="commonSubspecialtyMedicationsAdmin/list?c=nm&d=<?php echo $displayOrder; ?>">Name</a></th>
			</tr>
			</thead>
			<tbody>
			<?php
            foreach ($ref_sets as $i => $ref_set) { ?>
				<tr class="clickable" data-id="<?php echo $ref_set->id ?>"
					data-uri="commonSubspecialtyMedicationsAdmin/edit/<?php echo $ref_set->id ?>">
					<td><input type="checkbox" name="ref_sets[]" value="<?php echo $ref_set->id ?>"/></td>
					<td><?php echo CHtml::encode($ref_set->name ); ?></td>
				</tr>
			<?php } ?>
			</tbody>
			<tfoot class="pagination-container">
			<tr>
				<td colspan="6">
					<?php echo EventAction::button('Add', 'add', array(), array('class' => 'small'))->toHtml() ?>
					<?php echo EventAction::button('Delete', 'delete', array(), array('class' => 'small'))->toHtml() ?>
					<?php echo $this->renderPartial('_pagination', array(
                        'pagination' => $pagination,
                    )) ?>
				</td>
			</tr>
			</tfoot>
		</table>
	</form>
</div>
