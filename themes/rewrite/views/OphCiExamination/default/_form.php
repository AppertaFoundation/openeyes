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
<section class="<?php if (@$child) {?>sub-<?php }?>element <?php echo get_class($element)?>" data-element-type-id="<?php echo $element->elementType->id?>" data-element-type-name="<?php echo $element->elementType->name?>" data-element-display-order="<?php echo $element->elementType->display_order?>" data-element-type-class="<?php echo $element->elementType->class_name?>">
	<header class="<?php if (@$child) {?>sub-<?php }?>element-header">

		<?php if (@$child) {?>
			<h4 class="sub-element-title"><?php echo $element->elementType->name?></h4>
		<?php }else{?>
			<h3 class="element-title"><?php echo $element->elementType->name?></h3>
		<?php }?>

		<div class="element-title-additional">
			<?php
				if (get_class($element) === 'Element_OphCiExamination_VisualAcuity') {?>
					<?php if ($element->isNewRecord) { ?>
						<?php echo CHtml::dropDownList('visualacuity_unit_change', @$element->unit_id, CHtml::listData($element->getUsableUnits(), 'id', 'name')); ?>
					<?php } ?>
					<?php if ($element->unit->information) {?>
						<small><em><?php echo $element->unit->information ?></em></small>
					<?php } ?>
			<?php } ?>
			<?php
				if (get_class($element) === 'Element_OphCiExamination_DRGrading') {?>
					<?php if (file_exists(Yii::getPathOfAlias('application.modules.'.$this->getModule()->name.'.assets') . "/img/drgrading.jpg")) {?>
						<a href="#" class="drgrading_images_link"><img src="<?php echo $this->assetPath ?>/img/photo_sm.png" /></a>
						<a href="#" id="drgrading_dirty" style="display: none;">re-sync</a>
						<div class="drgrading_images_dialog" title="DR Grading Images">
							<img src="<?php echo $this->assetPath ?>/img/drgrading.jpg">
						</div>
					<?php }else{?>
						<a href="#" id="drgrading_dirty" style="display: none;">re-sync</a>
					<?php }?>
				<?php }
			?>
		</div>

		<div class="<?php if (@$child) {?>sub-<?php }?>element-actions">
			<?php
			$event_id = ($element->id) ? $element->event_id : null;
			if ($this->canCopy($element->elementType->class_name, $event_id)) {?>
				<a href="#" title="View Previous" class="viewPrevious"><img src="<?php echo Yii::app()->createUrl('img/_elements/btns/load.png')?>" /></a>
			<?php }?>
			<?php if (!@$child && !$element->elementType->required) {?>
				<a href="#" class="button button-icon small js-remove-element">
					<span class="icon-button-small-mini-cross"></span>
					<span class="hide-offscreen">Remove element</span>
				</a>
			<?php }?>
			<?php if (@$child) {?>
				<div class="sub-element-actions">
					<a href="#" class="button button-icon small js-remove-child-element">
						<span class="icon-button-small-mini-cross"></span>
						<span class="hide-offscreen">Remove sub-element</span>
					</a>
				</div>
			<?php }?>
		</div>
	</header>

	<?php
	$this->renderPartial(
		'form_' . get_class($element),
		array('element' => $element, 'data' => $data, 'form' => $form),
		false, false
	);
	?>

	<?php if (!@$child) {?>
		<div class="sub-elements active">
			<?php $this->renderChildDefaultElements($element, $this->action->id, $form, $data)?>
		</div>
		<div class="sub-elements inactive">
			<ul class="sub-elements-list">
				<?php $this->renderChildOptionalElements($element, $this->action->id, $form, $data)?>
			</ul>
		</div>
	<?php }?>
</section>
