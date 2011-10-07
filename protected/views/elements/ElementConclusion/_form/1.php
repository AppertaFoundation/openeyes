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

?>Conclusion: <br />

	<div class="row">
		<label for="ElementConclusion_value">Conclusion:</label>
		<?php echo CHtml::dropDownList('ElementConclusion[phrase]', '', $model->getPhraseBySpecialtyOptions('Conclusion'),
			array('onChange' => 'appendText($(this), $("#ElementConclusion_value"));')); ?>
	</div>

	<div class="row">
		<label for="ElementConclusion_value">Treatment:</label>
		<?php echo CHtml::dropDownList('ElementConclusion[phrase]', '', $model->getPhraseBySpecialtyOptions('Treatment'),
			array('onChange' => 'appendText($(this), $("#ElementConclusion_value"));')); ?>
	</div>

	<div class="row">
		<label for="ElementConclusion_value">Outcome:</label>
		<?php echo CHtml::dropDownList('ElementConclusion[phrase]', '', $model->getPhraseBySpecialtyOptions('Outcome'),
			array('onChange' => 'appendText($(this), $("#ElementConclusion_value"));')); ?>
	</div>

	<div class="row">
		<label for="ElementConclusion_value">Timing:</label>
		<?php echo CHtml::dropDownList('ElementConclusion[phrase]', '', $model->getPhraseBySpecialtyOptions('Timing'),
			array('onChange' => 'appendText($(this), $("#ElementConclusion_value"));')); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'value'); ?>
		<?php echo $form->textArea($model,'value',array('rows'=>6, 'cols'=>50)); ?>
		<?php echo $form->error($model,'value'); ?>
	</div>
