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
if ($this->checkAccess('admin')) {
    $sites = Site::model()->getListForAllInstitutions();
} else {
    $sites = Site::model()->getListForCurrentInstitution();
}
?>
<div class="row divider">
    <h2><?php echo $theatre->id ? 'Edit' : 'Add' ?> theatre</h2>
</div>

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

<div class="cols-5">
<?php echo $form->errorSummary($theatre); ?>
    <table class="standard">
        <colgroup>
            <col class="cols-2">
            <col class="cols-2">
        </colgroup>
        <tbody>
        <tr>
            <td><?=$theatre->getAttributeLabel('site_id')?></td>
            <td><?=CHtml::activeDropDownList($theatre, 'site_id', $sites, ['empty' => '- Site -', 'class' => 'cols-full']);?></td>
        </tr>
        <?php foreach (['name', 'code'] as $attr) :?>
        <tr>
            <td><?=$theatre->getAttributeLabel($attr);?></td>
            <td><?=CHtml::activeTextField($theatre, $attr, ['class' => 'cols-full'])?></td>
        </tr>
        <?php endforeach; ?>
        <tr>
            <td>Ward:</td>
            <td><?=$this->actionGetWardOptions($theatre->site_id, $theatre->ward_id);?></td>
        </tr>
        </tbody>
        <tfoot class="pagination-container">
            <tr>
                <td colspan="2">
                    <?=\CHtml::submitButton('Save', [ 'class' => 'button large', 'id' => 'et_save']);?>
                    <?=\CHtml::htmlButton('Cancel', [ 'class' => 'button large', 'id' => 'et_cancel', 'data-uri' => '/OphTrOperationbooking/admin/viewTheatres']);?>
                </td>
            </tr>
        </tfoot>
    </table>
    <?php echo $form->errorSummary($theatre); ?>
</div>
<?php $this->endWidget() ?>
