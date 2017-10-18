<?php
/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2014
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2014, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
?>

<fieldset class="field-row row" data-formName="<?=$this->form_name ?>">
	<div class="large-<?= $this->label_width ?> column">
		<label for="site">Outcome:</label>
	</div>
	<div class="large-<?= $this->data_width ?> column end">
		<?php
        $outcomes = $this->getOutcomeOptions();
        echo CHtml::dropDownList($this->form_name.'[outcome]', @$this->form_data[$this->form_name]['outcome'], $outcomes['list_data'], array('empty' => '- Please select -', 'options' => $outcomes['options'], 'class' => 'outcome-select')); ?>
	</div>
</fieldset>
<span id="<?= $this->form_name ?>-followup"<?php if ($this->hideFollowUp && !@$this->form_data[$this->form_name]['followup_quantity']) {?> style="display: none;"<?php }?>>
<fieldset class="field-row row">
	<div class="large-<?= $this->label_width ?> column">
		<label for="followup_quantity">Follow up:</label>
	</div>
	<div class="large-<?= $this->data_width ?> column end">
		<?php
        $html_options = array('empty' => '- Please select -', 'options' => array(), 'class' => 'inline');
        echo CHtml::dropDownList($this->form_name.'[followup_quantity]', @$this->form_data[$this->form_name]['followup_quantity'], Yii::app()->params['follow_up_months'], $html_options);
        echo CHtml::dropDownList($this->form_name.'[followup_period]', @$this->form_data[$this->form_name]['followup_period'], CHtml::listData(\Period::model()->findAll(array('order' => 'display_order')), 'name', 'name'), $html_options);
        ?>
	</div>
</fieldset>
<fieldset class="field-row row">
	<div class="large-<?= $this->label_width ?> column">
		<label for="site">Clinic location:</label>
	</div>
	<div class="large-<?= $this->data_width ?> column end">
		<?php echo CHtml::dropDownList($this->form_name.'[clinic_location]', @$this->form_data[$this->form_name]['clinic_location'], \CHtml::listData(OEModule\PatientTicketing\models\ClinicLocation::model()->findAll(array('order' => 'display_order asc')), 'name', 'name'), $html_options); ?>
	</div>
</fieldset>
</span>
