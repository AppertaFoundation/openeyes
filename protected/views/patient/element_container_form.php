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
$section_classes = array($element->elementType->class_name);
$section_classes[] = @$child ? 'sub-element' : 'element';
if ($this->isRequired($element)) {
	$section_classes[] = 'required';
}
if ($this->isHiddenInUI($element)) {
	$section_classes[] = 'hide';
}
?>

<section
	class="<?php echo implode(' ', $section_classes);?>"
	data-element-type-id="<?php echo $element->elementType->id?>"
	data-element-type-class="<?php echo $element->elementType->class_name?>"
	data-element-type-name="<?php echo $element->elementType->name?>"
	data-element-display-order="<?php echo $element->elementType->display_order?>">

	<header class="<?php if (@$child) {?>sub-<?php }?>element-header">

		<!-- Element title -->
		<?php if (!@$child) {?>
			<h3 class="element-title"><?php echo $element->elementType->name?></h3>
		<?php }else{?>
			<h4 class="sub-element-title"><?php echo $element->elementType->name?></h4>
		<?php }?>

		<!-- Additional element title information -->
		<?php if (isset($this->clips['element-title-additional'])){?>
			<div class="element-title-additional">
				<?php $this->renderClip('element-title-additional');?>
			</div>
		<?php }?>

		<!-- Element actions -->
		<div class="<?php if (@$child) {?>sub-<?php }?>element-actions">

			<!-- Copy previous element -->
			<?php if ($this->canCopy($element) || $this->canViewPrevious($element)) {?>
				<a href="#" title="View Previous" class="viewPrevious<?php if (@$child) {?> subElement<?php }?>">
					<img src="<?php echo Yii::app()->assetManager->createUrl('img/_elements/btns/load.png')?>" />
				</a>
			<?php }?>

			<!-- Remove element -->
			<?php if (!@$child && !$this->isRequiredInUI($element)) {?>
				<a href="#" class="button button-icon small js-remove-element">
					<span class="icon-button-small-mini-cross"></span>
					<span class="hide-offscreen">Remove element</span>
				</a>
			<?php }?>

			<!-- Remove sub-element -->
			<?php if (@$child) {?>
				<a href="#" class="button button-icon small js-remove-child-element">
					<span class="icon-button-small-mini-cross"></span>
					<span class="hide-offscreen">Remove sub-element</span>
				</a>
			<?php }?>
		</div>
	</header>

	<?php echo $content; ?>

	<!-- Sub elements -->
	<?php if (!@$child) {?>
		<div class="sub-elements active">
			<?php $this->renderChildOpenElements($element, $this->action->id, $form, $data)?>
		</div>
		<div class="sub-elements inactive">
			<ul class="sub-elements-list">
				<?php $this->renderChildOptionalElements($element, $this->action->id, $form, $data)?>
			</ul>
		</div>
	<?php }?>
</section>
