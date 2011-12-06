<?php
/*
_____________________________________________________________________________
(C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
(C) OpenEyes Foundation, 2011
This file is part of OpenEyes.
OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
_____________________________________________________________________________
http://www.openeyes.org.uk   info@openeyes.org.uk
--
*/

?>Anterior segment: <br />

	<?php echo $form->errorSummary($model); ?>

	<?php echo EyeDrawService::activeEyeDrawField($this, $model, 'left');?>
	<p>
	<label for="ElementAnteriorSegment_description_left"><?php echo CHtml::encode($model->getAttributeLabel('description_left')); ?></label><br />
	<?php echo $form->textArea($model, 'description_left', array('rows'=>15, 'cols'=>75)); ?>
	<?php echo $form->error($model,'description_left'); ?> <br />
	</p>

	<?php echo EyeDrawService::activeEyeDrawField($this, $model, 'right');?>
	<p>
	<label for="ElementAnteriorSegment_description_right"><?php echo CHtml::encode($model->getAttributeLabel('description_right')); ?></label><br />
	<?php echo $form->textArea($model, 'description_right', array('rows'=>15, 'cols'=>75)); ?>
	<?php echo $form->error($model,'description_right'); ?> <br />
	</p>
