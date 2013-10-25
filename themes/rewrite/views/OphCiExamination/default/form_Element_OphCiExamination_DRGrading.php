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
<div class="clearfix">

<!--  only provide image popup if provided to the webserver -->
<?php if (file_exists(Yii::getPathOfAlias('application.modules.'.$this->getModule()->name.'.assets') . "/img/drgrading.jpg") ) { ?>
	<div style="margin-top:-30px; margin-left:120px;">
		<a href="#" class="drgrading_images_link"><img src="<?php echo $this->assetPath ?>/img/photo_sm.png" /></a>
		<a href="#" id="drgrading_dirty" style="display: none;">re-sync</a>
	</div>
	<div class="drgrading_images_dialog"
		title="DR Grading Images">
		<img src="<?php echo $this->assetPath ?>/img/drgrading.jpg">
	</div>
<?php } else { ?>
	<div style="margin-top:-25px; margin-left:120px;">
	<a href="#" id="drgrading_dirty" style="display: none;">re-sync</a>
	</div>
<?php } ?>
</div>

<div class="eventDetail">
	<div class="label"><?php echo $element->getAttributeLabel('secondarydiagnosis_disorder_id');?></div>
	<div class="data">
		<?php
			if ($diabetes = $this->patient->getDiabetesType()) {
				echo $diabetes->term;
			}
			else {
				$form->radioButtons($element, 'secondarydiagnosis_disorder_id', 'diabetes_types', null, false, false, false, false, array('nowrapper' => true));
			}
		?>
	</div>
</div>
<div class="cols2 clearfix">
	<?php echo $form->hiddenInput($element, 'eye_id', false, array('class' => 'sideField')); ?>
	<div
		class="side left eventDetail<?php if (!$element->hasRight()) { ?> inactive<?php } if ($element->id || !empty($_POST) ) { ?> uninitialised<?php } ?>"
		data-side="right">
		<div class="activeForm">
			<?php $this->renderPartial('form_' . get_class($element) . '_fields',
				array('side' => 'right', 'element' => $element, 'form' => $form)); ?>
		</div>
		<div class="inactiveForm">
			Add right posterior segment.
		</div>
	</div>
	<div
		class="side right eventDetail<?php if (!$element->hasLeft()) { ?> inactive<?php } if ($element->id || !empty($_POST) ) { ?> uninitialised<?php } ?>"
		data-side="left">
		<div class="activeForm">
			<?php $this->renderPartial('form_' . get_class($element) . '_fields',
				array('side' => 'left', 'element' => $element, 'form' => $form)); ?>
		</div>
		<div class="inactiveForm">
			Add left posterior segment.
		</div>
	</div>
</div>
