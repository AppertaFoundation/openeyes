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
<table class="cols-full last-left">
    <tbody>
        <?= $form->hiddenField($element, 'booking_event_id') ?>
        <tr>
            <td>
                <?= \CHtml::encode($element->getAttributeLabel('eye_id')); ?>
            </td>
            <td>
                <?= $form->radioButtons(
                    $element,
                    'eye_id',
                    CHtml::listData(Eye::model()->findAll(array('order' => 'display_order asc')), 'id', 'name'),
                    null,
                    false,
                    false,
                    false,
                    false,
                    array('nowrapper' => true),
                    array()
                ) ?>
            </td>
            <td></td>
        </tr>
        <tr>
            <td>
                <?= \CHtml::encode($element->getAttributeLabel('Anaesthetic Type')); ?>
            </td>
            <td>
                <?= $form->checkBoxes(
                    $element,
                    'AnaestheticType',
                    array_map(function ($element) {
                        return $element['id'];
                    }, $element->anaesthetic_type),
                    'Anaesthetic Type',
                    false,
                    false,
                    false,
                    false,
                    array(
                        'fieldset-class' => $element->getError('anaesthetic_type') ? 'highlighted-error error' : '',
                        'nowrapper' => true
                    )
                ); ?>
            </td>
            <td></td>
        </tr>
        <tr>
            <td>Procedures</td>
            <td>
                <?php $form->widget(
                    'application.widgets.ProcedureSelection',
                    array(
                        'element' => $element,
                        'durations' => false,
                        'identifier' => 'procedures',
                        'read_only' => (bool)$element->booking_event_id,
                        'restrict' => 'unbooked',
                        'restrict_common' => 'unbooked',
                        'label' => ''
                    )
                ) ?>
            </td>
        </tr>
    </tbody>
</table>
