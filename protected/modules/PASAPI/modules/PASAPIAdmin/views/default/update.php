<?php

/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

?>

<div class="cols-full">

<div class="row divider">
    <?= $title ?>
</div>

<?php
$form = $this->beginWidget('BaseEventTypeCActiveForm', array(
    'id' => 'PASAPI_adminform',
    'enableAjaxValidation' => false,
    'layoutColumns' => array(
        'label' => 2,
        'field' => 5,
    ),
));
?>

<table class="standard">
    <colgroup>
        <col class="cols-2">
        <col class="cols-10">
    </colgroup>
    <tbody>
    <?php
    $this->renderPartial('form_' . Helper::getNSShortname($model), array(
        'model' => $model,
        'form' => $form,
    ));
    ?>
    </tbody>
    <tfoot class="pagination-container">
    <tr>
        <td colspan="2">
            <?= CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save', ['class' => 'large button']) ?>
            <?= CHtml::submitButton('Cancel', [
                'data-uri' => "/PASAPI/admin/default/viewXpathRemaps",
                'name' => 'cancel',
                'id' => "et_cancel",
                'class' => 'large button'
            ]);
?>
        </td>
    </tr>
    </tfoot>
</table>

<?php $this->endWidget(); ?>

<?php if (@$related_view) {
    $this->renderPartial($related_view, array(
        'model' => $model,
    )) ?>
    <?php
} ?>

</div>
