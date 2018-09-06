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
<main class="oe-full-main admin-main">

    <h2>Edit setting</h2>
    <div class="row divider"></div>
    <div class="cols-full">

        <?php
        $form = $this->beginWidget('BaseEventTypeCActiveForm', array(
            'id' => 'settingsform',
            'enableAjaxValidation' => false,
            'focus' => '#username',
            'layoutColumns' => array(
                'label' => 2,
                'field' => 5,
            ),
        )) ?>

            <table class="cols-full last-left standard">
                <colgroup>
                    <col class="cols-1">
                    <col class="cols-1">
                </colgroup>
                <tbody>
                <tr>

                    <td><?= $metadata->name ?></td>
                    <?php if ($metadata->key == 'city_road_satellite_view') : ?>
                        <td>
                            <div class="alert-box issue">
                                Removes the 2 check-boxes from Examination->Clinical Management->Cataract Surgical
                                Management named "At
                                City Road" and "At Satellite"
                            </div>
                        </td>

                    <?php else : ?>
                        <td>
                            <?php
                            $this->renderPartial(
                                '_admin_setting_' . strtolower(str_replace(' ', '_', $metadata->field_type->name)),
                                ['metadata' => $metadata]
                            );
                            ?>
                        </td>
                    <?php endif; ?>
                </tr>
                </tbody>
                <tfoot class="pagination-container">
                <tr>
                    <td colspan="2">
                        <?php if ($metadata->key != 'city_road_satellite_view') : ?>
                            <?= CHtml::htmlButton('Save', [
                                    'class' => 'button small',
                                    'name' => 'save',
                                    'type' => 'submit',
                                    'id' => 'et_save'
                                ]
                            );
                            ?>

                            <?= CHtml::htmlButton('Cancel', [
                                    'class' => 'button small',
                                    'name' => 'cancel',
                                    'type' => 'submit',
                                    'id' => 'et_cancel'
                                ]
                            );
                            ?>

                        <?php endif; ?>
                    </td>
                </tr>
                </tfoot>
            </table>

        <?php $this->endWidget() ?>


</main>

<div class="box admin">
    <h2>Edit setting</h2>
    <?php echo $this->renderPartial('_form_errors', array('errors' => $errors)) ?>
    <?php
    $form = $this->beginWidget('BaseEventTypeCActiveForm', array(
        'id' => 'settingsform',
        'enableAjaxValidation' => false,
        'focus' => '#username',
        'layoutColumns' => array(
            'label' => 2,
            'field' => 5,
        ),
    )) ?>
    <?php if ($metadata->key == 'city_road_satellite_view') { ?>
        <div class="cols-12 column">
            <div class="alert-box with-icon warning">
                Removes the 2 check-boxes from Examination->Clinical Management->Cataract Surgical Management named "At
                City Road" and "At Satellite"
            </div>
        </div>
    <?php } ?>
    <div class="data-group">
        <div class="cols-3 column">
            <label for="<?php echo $metadata->key ?>">
                <?php echo $metadata->name ?>
            </label>
        </div>
        <div class="cols-3 column end">
            <?php $this->renderPartial('_admin_setting_' . strtolower(str_replace(' ', '_', $metadata->field_type->name)), array('metadata' => $metadata)) ?>
        </div>
    </div>
    <?php echo $form->formActions() ?>
    <?php $this->endWidget() ?>
</div>
