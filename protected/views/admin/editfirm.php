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
	<h2><?php echo $firm->id ? 'Edit' : 'Add' ?> firm</h2>
	<?php echo $this->renderPartial('_form_errors', array('errors' => $errors)) ?>
	<?php
    $form = $this->beginWidget('BaseEventTypeCActiveForm', array(
        'id' => 'adminform',
        'enableAjaxValidation' => false,
        'focus' => '#username',
        'layoutColumns' => array(
            'label' => 2,
            'field' => 5,
        ),
    )) ?>
	<?php echo $form->textField($firm, 'pas_code', array('autocomplete' => Yii::app()->params['html_autocomplete'])) ?>
	<?php echo $form->textField($firm, 'name', array('autocomplete' => Yii::app()->params['html_autocomplete'])) ?>

	<div id="div_Firm_subspecialty_id" class="row field-row">
		<div class="large-2 column">
			<label for="Firm_subspecialty_id">Subspecialty:</label>
		</div>
		<div class="large-5 column end">
			<?php echo CHtml::dropDownList('Firm[subspecialty_id]',
                $firm->serviceSubspecialtyAssignment ? $firm->serviceSubspecialtyAssignment->subspecialty_id : null,
                CHtml::listData(Subspecialty::model()->findAll(array('order' => 'name')), 'id', 'name'), array('empty' => '- None -')) ?>
		</div>
	</div>
	<div id="div_Firm_consultant_id" class="row field-row">
		<div class="large-2 column">
			<label for="Firm_consultant_id">Consultant:</label>
		</div>
		<div class="large-5 column end">
			<?php echo CHtml::dropDownList('Firm[consultant_id]', $firm->consultant_id,
                CHtml::listData(User::model()->findAll(array('order' => 'first_name,last_name')), 'id', 'fullName'), array('empty' => '- None -')) ?>
		</div>
	</div>

    <div id="div_Firm_active" class="row field-row">
        <div class="large-2 column">
            <label for="Firm_can_own_an_episode"><?= ucfirst(Yii::app()->params['service_firm_label']) ?> Enabled:</label>
        </div>
        <div class="large-5 column end">
            <?php echo CHtml::activeCheckBox($firm, 'can_own_an_episode') ?>
        </div>
    </div>

    <div id="div_Firm_active" class="row field-row">
        <div class="large-2 column">
            <label for="Firm_runtime_selectable"><?= ucfirst(Yii::app()->params['context_firm_label']) ?> Enabled:</label>
        </div>
        <div class="large-5 column end">
            <?php echo CHtml::activeCheckBox($firm, 'runtime_selectable') ?>
        </div>
    </div>

    <div id="div_Firm_active" class="row field-row">
		<div class="large-2 column">
			<label for="Firm_active">Active:</label>
		</div>
		<div class="large-5 column end">
			<?php echo CHtml::activeCheckBox($firm, 'active') ?>
		</div>
	</div>

	<?php echo $form->formActions(); ?>

	<?php $this->endWidget() ?>

	<?php if (isset($siteSecretaries) && $siteSecretaries): ?>
		<?php echo $this->renderPartial('application.modules.OphCoCorrespondence.views.admin.secretary.edit', array(
                'errors' => array(),
                'siteSecretaries' => $siteSecretaries,
            )
        )
        ?>
	<?php endif; ?>
</div>
