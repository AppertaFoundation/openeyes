<?php
/**
 * (C) OpenEyes Foundation, 2018
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
?>

<div class="row divider">
    <h2>Add location</h2>
</div>
<?php echo $this->renderPartial('_form_errors', array('errors' => $errors)) ?>
<div class="data-group">
    <div class="cols-2 column">
        <div class="field-label">Contact:</div>
    </div>
    <div class="cols-10 column">
        <div class="field-value"><?php echo $contact->fullName ?></div>
    </div>
</div>
<?php
$form = $this->beginWidget('BaseEventTypeCActiveForm', array(
    'id' => 'adminform',
    'enableAjaxValidation' => false,
    'focus' => '#username',
)) ?>
<input type="hidden" name="contact_id" value="<?php echo $contact->id ?>"/>
<div class="data-group">
    <div class="cols-2 column">
        <label for="institution_id">Institution:</label>
    </div>
    <div class="cols-5 column end">
        <?=\CHtml::dropDownList('institution_id', @$_POST['institution_id'], CHtml::listData(Institution::model()->active()->findAll(array('order' => 'name')), 'id', 'name'), array('empty' => 'Select')) ?>
    </div>
</div>
<div class="data-group">
    <div class="cols-2 column">
        <label for="site_od">Site:</label>
    </div>
    <div class="cols-5 column end">
        <?=\CHtml::dropDownList('site_id', '', $sites, array('empty' => '- Optional -')) ?>
    </div>
</div>
<?php echo $form->formActions(); ?>
<?php $this->endWidget() ?>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        $('#User_username').focus();

        $('#institution_id').change(function () {
            getInstitutionSites($(this).val(), $('#site_id'));
        });
    });


    handleButton($('#et_cancel'), function (e) {
        e.preventDefault();
        window.location.href = baseUrl + '/admin/editContact?contact_id=<?php echo $contact->id?>';
    });

    handleButton($('#et_save'), function (e) {
        e.preventDefault();
        $('#adminform').submit();
    });

</script>
