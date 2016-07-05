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

<?php
if(!isset($displayOrder)){
	$displayOrder = 0;
}
if(!isset($uniqueid)){
	$uniqueid = $this->uniqueid;
}
?>
<div class="admin box">
	<?php if(!$admin->isSubList()):?>
	<h2><?php echo $admin->getModelDisplayName(); ?></h2>
	<?php endif;?>
	<?php $this->widget('GenericSearch', array('search' => $admin->getSearch(), 'subList' => $admin->isSubList())); ?>

	<?php
	$returnUri = '';
	if($admin->isSubList()):?>
	<div id="generic-admin-sublist">
		<?php
		if($admin->getSubListParent() && is_array($admin->getSubListParent())):
			foreach($admin->getSubListParent() as $key => $value):
		?>
			<input type="hidden" name="default[<?=$key?>]" value="<?=$value?>" />
		<?php
			endforeach;
		endif;
		$returnUri = $admin->generateReturnUrl(Yii::app()->request->requestUri);
		?>
		<input type="hidden" name="returnUri" id="returnUri" value="<?=$returnUri ?>" />

	<?php else: ?>
	<form id="generic-admin-list">
		<input type="hidden" name="YII_CSRF_TOKEN" value="<?php echo Yii::app()->request->csrfToken ?>"/>
	<?php endif;?>
		<input type="hidden" name="page" value="<?php echo Yii::app()->request->getParam('page', 1) ?>"/>
		<table class="grid">
			<thead>
			<tr>
				<th><input type="checkbox" name="selectall" id="selectall"/></th>
				<?php
				foreach ($admin->getListFields() as $listItem):
				if($listItem != "attribute_elements_id.id")
				{?>
					<th>
						<?php if ($admin->isSortableColumn($listItem)): ?>
						<a href="/<?php echo $uniqueid ?>/list?<?php echo $admin->sortQuery($listItem, $displayOrder,
							Yii::app()->request->getQueryString()) ?>">
							<?php endif;
							?>
							<?php echo $admin->getModel()->getAttributeLabel($listItem); ?>
							<?php if ($admin->isSortableColumn($listItem)): ?>
						</a>
					<?php endif;
					?>
					</th>
				<?php
				}
				endforeach; ?>
				<?php if($listItem == "attribute_elements.name") { ?>
				<th>Action</th>
				<?php } ?>
			</tr>
			</thead>
			<tbody <?php if(in_array('display_order', $admin->getListFields())): echo 'class="sortable"'; endif; ?>>
			<?php
			foreach ($admin->getSearch()->retrieveResults() as $i => $row) { 
                            $returnUri = $admin->generateReturnUrl(Yii::app()->request->requestUri);
                            ?>
				<tr class="clickable" data-id="<?php echo $row->id ?>"
					data-uri="<?php echo $uniqueid ?>/edit/<?php echo $row->id ?>?returnUri=<?=$returnUri?>">
					<td>
						<input type="checkbox" name="<?php echo $admin->getModelName(); ?>[id][]" value="<?php echo $row->id ?>"/>
					</td>
					<?php foreach ($admin->getListFields() as $listItem):
					if($listItem != "attribute_elements_id.id") {
						?>
						<td>
							<?php
							if (gettype($admin->attributeValue($row, $listItem)) === 'boolean'):
								if ($admin->attributeValue($row, $listItem)):
									?><i class="fa fa-check"></i><?php
								else:
									?><i class="fa fa-times"></i><?php
								endif;
							elseif($listItem === 'display_order'):
								?>
								&uarr;&darr;<input type="hidden" name="<?php echo $admin->getModelName(); ?>[display_order][]" value="<?php echo $row->id ?>">
							<?php
							else:
								echo $admin->attributeValue($row, $listItem);
							endif
							?>
						</td>
					<?php }

						if($listItem == "attribute_elements_id.id")
						{
							$mapping_id =  $admin->attributeValue($row, $listItem);
						}
					endforeach; ?>
					<?php if($listItem == "attribute_elements.name")
					{?>
					<td>
							<?php if(($mapping_id > 0)) {?>
							<a onMouseOver="this.style.color='#AFEEEE'" onMouseOut="this.style.color='#00F'" href="../../OphCiExamination/admin/manageElementAttributes?attribute_element_id=<?php echo $mapping_id?>">Manage Options</a>
							<?php } ?>
					</td>
					<?php } ?>
				</tr>
			<?php } ?>
			</tbody>
			<tfoot class="pagination-container">
			<tr>
				<td colspan="<?php echo count($admin->getListFields()) + 1; ?>">
					<?php echo EventAction::button(
						'Add',
						'add',
						array(),
						array(
							'class' => 'small',
							'data-uri' => '/' . $uniqueid . '/edit',
							'formmethod' => 'get'
						)
					)->toHtml() ?>
					<?php echo EventAction::button(
						'Delete',
						'delete',
						array(),
						array(
							'class' => 'small',
							'data-uri' => '/' . $uniqueid . '/delete',
							'data-object' => $admin->getModelName()
						)
					)->toHtml() ?>
					<?php echo EventAction::button(
						'Sort',
						'sort',
						array(),
						array(
							'class' => 'small',
							'style' => 'display:none;',
							'data-uri' => '/' . $uniqueid . '/sort',
							'data-object' => $admin->getModelName()
						)
					)->toHtml() ?>
					<?php echo $this->renderPartial('//admin/_pagination', array(
						'pagination' => $admin->getPagination()
					)) ?>
				</td>
			</tr>
			</tfoot>
		</table>
	<?php if($admin->isSubList()):?>
	</div>
	<?php else: ?>
	</form>
	<?php endif;?>
</div>