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
$layoutColumns = array(
	'label' => 2,
	'field' => 10
);
?>

<section class="element <?php echo $element->elementType->class_name?> on-demand<?php if (@$ondemand) {?> hidden<?php }?><?php if ($this->action->id == 'update' && !$element->event_id) {?> missing<?php }?>"
	data-element-type-id="<?php echo $element->elementType->id ?>"
	data-element-type-class="<?php echo $element->elementType->class_name ?>"
	data-element-type-name="<?php echo $element->elementType->name ?>"
	data-element-display-order="<?php echo $element->elementType->display_order ?>">

	<?php if ($this->action->id == 'update' && !$element->event_id) {?>
		<div class="alert-box alert">This element is missing and needs to be completed</div>
	<?php }?>

	<header class="element-header">
		<h3 class="element-title"><?php echo $element->procedure ? $element->procedure->term : 'No procedure'?></h3>
	</header>

	<div class="element-fields" id="div_Element_OphTrOperationnote_GenericProcedure_comments">
		<div class="row field-row">
			<div class="large-<?php echo $layoutColumns['label'];?> column">
				<label for="<?php echo get_class($element)."_comments";?>">
					Comments:
				</label>
			</div>
			<div class="large-<?php echo $layoutColumns['field'];?> column end">
				<?php echo CHtml::textArea(get_class($element).'[comments][]',$element->comments,array('rows'=>4))?>
			</div>
		</div>
	</div>
	<input type="hidden" name="<?php echo get_class($element)?>[proc_id][]" value="<?php echo $element->proc_id?>" />
	<input type="hidden" name="<?php echo get_class($element)?>[_element_id][]" value="<?php echo $element->id?>" />
</section>
