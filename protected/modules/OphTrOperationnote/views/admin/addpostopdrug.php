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
        <h2>Add Per Op Drug</h2>
    </div>

    <?php echo $this->renderPartial('//admin/_form_errors', array('errors' => $errors)) ?>
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

    <table class="standard">
        <tbody>
        <tr>
            <td>Name</td>
            <td>
                <?=\CHtml::activeTextField(
                    $drug,
                    'name',
                    ['class' => 'cols-full']
                ); ?>
            </td>
        </tr>
        </tbody>

        <tfoot class="pagination-container">
        <tr>
            <td colspan="2">
                <?=\CHtml::button(
                    'Save',
                    [
                        'class' => 'button large',
                        'name' => 'save',
                        'type' => 'submit',
                        'id' => 'et_save'
                    ]
                ); ?>
                <?=\CHtml::button(
                    'Cancel',
                    [
                        'data-uri' => '/OphTrOperationnote/admin/viewPostOpDrugs',
                        'class' => 'button large',
                        'type' => 'submit',
                        'name' => 'cancel',
                        'id' => 'et_cancel',
                    ]
                ); ?>
            </td>
        </tr>
        </tfoot>
    </table>

    <?php $this->endWidget() ?>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        $('#OphTrOperationnote_PostopDrug_name').focus();
    });
</script>
