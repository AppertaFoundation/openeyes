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

<div class="cols-5">

    <div class="row divider">
        <h2><?php echo $laser_operator->id ? 'Edit' : 'Add' ?> laser operator</h2>
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

    <?php echo $this->renderPartial('//admin/_form_errors', array('errors' => $errors)) ?>

    <table class="standard cols-full" id="finding-table">
        <colgroup>
            <col class="cols-1">
            <col class="cols-3">
        </colgroup>
        <tbody>
        <tr>
            <td>Operator</td>
            <td>
                <?=\CHtml::activeDropDownList(
                    $laser_operator,
                    'user_id',
                    CHtml::listData(
                        User::model()->findAll(array('condition' => 'active = 1', 'order' => 'last_name, first_name')),
                        'id',
                        'reversedFullName'
                    ),
                    ['class' => 'cols-full', 'empty' => '- Select -']
                ); ?>
            </td>
        </tr>
        </tbody>
        <tfoot>
        <tr>
            <td colspan="2">
                <?php echo $form->formActions(array(
                    'delete' => $laser_operator->id ? 'Delete' : false,
                    'cancel-uri' => 'viewLaserOperators',
                )); ?>
            </td>
        </tr>
        </tfoot>
    </table>


    <?php $this->endWidget() ?>
</div>

<script type="text/javascript">
    handleButton($('#et_cancel'), function (e) {
        e.preventDefault();
        window.location.href = baseUrl + '/OphTrLaser/admin/viewLaserOperators';
    });

    handleButton($('#et_save'), function (e) {
        $('#adminform').submit();
    });
</script>
