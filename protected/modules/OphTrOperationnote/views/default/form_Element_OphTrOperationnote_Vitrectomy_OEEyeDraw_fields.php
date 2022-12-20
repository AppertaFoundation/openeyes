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
    <table class="cols-full last-left">
        <colgroup>
            <col class="cols-6">
        </colgroup>
        <tbody>
        <tr>
            <td>
                <?php echo $element->getAttributeLabel('gauge_id') ?>
            </td>
            <td>
                <?php echo $form->dropDownList(
                    $element,
                    'gauge_id',
                    CHtml::listData(OphTrOperationnote_VitrectomyGauge::model()->activeOrPk($element->gauge_id)->findAll(), 'id', 'value'),
                    array('empty' => 'Select', 'textAttribute' => 'data-value', 'nolabel' => true, 'data-prefilled-value' => $template_data['gauge_id'] ?? ''),
                    false,
                    array('field' => 3)
                ) ?>
            </td>
        </tr>
        <tr>
            <td>
                <?php echo $element->getAttributeLabel('pvd_induced') ?>
            </td>
            <td>
                <?php
                echo $form->radioBoolean($element, 'pvd_induced', array('nowrapper' => true, 'prefilled_value' => $template_data['pvd_induced'] ?? '')) ?>
            </td>
        </tr>
        <tr>
            <td>
                <?php echo $element->getAttributeLabel('comments') ?>
            </td>
            <td>
                <?php echo $form->textArea($element, 'comments', array('nowrapper' => true, 'rows' => 4), false, array('data-prefilled-value' => $template_data['comments'] ?? '')) ?>
            </td>
        </tr>
        </tbody>
    </table>

</div>


