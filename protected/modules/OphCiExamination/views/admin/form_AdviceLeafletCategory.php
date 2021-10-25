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
 *
 * @var $this \OEModule\OphCiExamination\controllers\AdminController
 * @var $model \OEModule\OphCiExamination\models\AdviceLeafletCategory
 * @var $form BaseEventTypeCActiveForm
 */

use OEModule\OphCiExamination\models\AdviceLeaflet;

?>

<table class="standard cols-full">
    <div class="row divider">
        <h2><?php echo $title ?></h2>
    </div>
    <colgroup>
        <col class="cols-3">
        <col class="cols-5">
    </colgroup>
    <tbody>
    <?php if ($form->errorSummary($model)) : ?>
        <tr>
            <td>Errors</td>
            <td class="cols-full">
                <?php echo $form->errorSummary($model) ?>
            </td>
        </tr>
    <?php endif; ?>
    <tr>
        <td>Name</td>
        <td class="cols-full">
            <?= \CHtml::activeTextField($model, 'name') ?>
        </td>
    </tr>
    <tr>
        <td>Active</td>
        <td class="cols-full">
            <?= \CHtml::activeCheckBox($model, 'active') ?>
        </td>
    </tr>
    <tr>
        <td>Leaflets</td>
        <td class="cols-full">
            <?php $this->widget('application.widgets.MultiSelectList', [
                'element' => $model,
                'field' => 'leaflets',
                'relation' => 'leaflet_assignments',
                'relation_id_field' => 'leaflet_id',
                'options' => CHtml::listData(
                    AdviceLeaflet::model()->findAll('active = 1 AND institution_id = :id', [':id' => Yii::app()->session['selected_institution_id']]),
                    'id',
                    'name'
                ),
                'default_options' => array(),
                'htmlOptions' => array(
                    'label' => null,
                    'empty' => '- Select -',
                    'nowrapper' => true,
                    'class' => 'cols-full'
                ),
                'hidden' => false,
                'inline' => false,
                'noSelectionsMessage' => 'None',
                'showRemoveAllLink' => false,
                'layoutColumns' => array(
                    'label' => 3,
                    'field' => 8,
                ),
                'sortable' => true,
            ]) ?>
        </td>
    </tr>
    </tbody>
</table>