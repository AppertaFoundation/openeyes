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
<div class="box admin">
	<div class="row">
		<div class="large-8 column">
			<h2>Sites</h2>
		</div>
		<div class="large-4 column">
			<?php
            $form = $this->beginWidget('BaseEventTypeCActiveForm', array(
                'id' => 'searchform',
                'enableAjaxValidation' => false,
                'focus' => '#search',
                'action' => Yii::app()->createUrl('/admin/sites'),
            ))?>
				<div class="row">
					<div class="large-12 column">
						<input type="text" autocomplete="<?php echo Yii::app()->params['html_autocomplete']?>" name="search" id="search" placeholder="Enter search query..." value="<?php echo strip_tags(@$_POST['search'])?>" />
					</div>
				</div>
			<?php $this->endWidget()?>
		</div>
	</div>
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
						<td><?php echo $site->getLetterAddress(array('delimiter' => ', '))?></td>
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