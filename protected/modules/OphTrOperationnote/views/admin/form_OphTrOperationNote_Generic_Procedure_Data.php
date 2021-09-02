<?php
/**
 * (C) OpenEyes Foundation, 2019
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
?>

<div class="cols-5">
    <div class="row divider">
        <h2><?php echo $title ?></h2>
    </div>

    <table class="standard cols-full">
        <colgroup>
            <col class="cols-3">
            <col class="cols-5">
        </colgroup>
        <tbody>
        <tr>
            <td class="cols-full">
                <div id="div_Term" class="data-group flex-layout cols-full">
                    <div class="cols-full">
                        <label for="Term">Procedure Term</label>
                    </div>
                    <div class="cols-full">
                        <?php if (isset($model->procedure)) {
                            echo $model->procedure->term;
                        } else {
                            echo CHtml::activeDropDownList($model, 'proc_id', CHtml::listData($procedures, 'id', 'term'), array('empty' => '-- Select --'));
                        } ?>
                    </div>
                </div>
            </td>
        </tr>
        <tr>
            <td class="cols-full">
                <div id="div_DefaultText" class="data-group flex-layout cols-full">
                    <div class="cols-full">
                        <label for="DefaultText">Default Text</label>
                    </div>
                    <div class="cols-full">
                        <?= \CHtml::activeTextArea(
                            $model,
                            'default_text',
                            ['class' => 'cols-full autosize',
                                'style' => 'overflow: hidden; ']
                        ); ?>
                    </div>
                </div>
            </td>
        </tr>
        </tbody>
    </table>
</div>
