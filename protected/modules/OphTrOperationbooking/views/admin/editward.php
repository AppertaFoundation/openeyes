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
	<h2><?php echo $ward->id ? 'Edit' : 'Add'?> ward</h2>
	<?php
    $form = $this->beginWidget('BaseEventTypeCActiveForm', array(
            'id' => 'adminform',
            'enableAjaxValidation' => false,
            'focus' => '#username',
            'layoutColumns' => array(
                'label' => 2,
                'field' => 5,
            ),
        ))?>
	<?php echo $form->errorSummary($ward); ?>
	<?php echo $form->dropDownList($ward, 'site_id', Site::model()->getListForCurrentInstitution(), array('empty' => '- Site -'))?>
	<?php echo $form->textField($ward, 'name')?>
	<?php echo $form->textField($ward, 'long_name')?>
	<?php echo $form->textField($ward, 'code', array('size' => 10))?>
	<?php echo $form->textArea($ward, 'directions')?>
	<?php echo $form->radioBoolean($ward, 'restriction_male')?>
	<?php echo $form->radioBoolean($ward, 'restriction_female')?>
	<?php echo $form->radioBoolean($ward, 'restriction_child')?>
	<?php echo $form->radioBoolean($ward, 'restriction_adult')?>
	<?php echo $form->radioBoolean($ward, 'restriction_observation')?>
	<?php echo $form->formActions();?>
	<?php $this->endWidget()?>
	<?php echo $form->errorSummary($ward); ?>
</div>

<script type="text/javascript">
	handleButton($('#et_cancel'),function(e) {s
		e.preventDefault();
		window.location.href = baseUrl+'/OphTrOperationbooking/admin/viewWards';
	});
	handleButton($('#et_save'),function(e) {
		$('#adminform').submit();
	});
</script>
