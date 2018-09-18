<?php
/**
 * OpenEyes.
 *
 * (C) OpenEyes Foundation, 2018
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2016, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
?>

<main class="oe-full-main admin-main">

    <?php if (!$procedures) : ?>
        <div class="row divider">
            <div class="alert-box issue"><b>No results found</b></div>
        </div>
    <?php endif; ?>

    <div class="row divider cols-9">
        <form id="procedures_search" method="post">
            <input type="hidden" name="YII_CSRF_TOKEN" value="<?= Yii::app()->request->csrfToken ?>"/>
            <table class="cols-full">
                <colgroup>
                    <col class="cols-8">
                    <col class="cols-2" span="2">
                    <col class="cols-1">
                </colgroup>
                <tbody>
                <tr class="col-gap">
                    <td>
                        <?php echo CHtml::textField(
                            'search[query]',
                            $search['query'],
                            [
                                'class' => 'cols-full',
                                'placeholder' => "Term, Snomed Code, OPCS Code, Default Duration, Aliases"
                            ]
                        ); ?>
                    </td>
                    <td>
                        <?= \CHtml::dropDownList(
                            'search[active]',
                            $search['active'],
                            [
                                1 => 'Only Active',
                                0 => 'Exclude Active',
                            ],
                            ['empty' => 'All']
                        ); ?>
                    </td>
                    <td>
                        <button class="blue hint"
                                type="submit" id="et_search">Search
                        </button>
                    </td>
                </tr>
                </tbody>
            </table>
        </form>
    </div>

    <table class="standard">
        <thead>
        <tr>
            <th><input type="checkbox" name="selectall" id="selectall"/></th>
            <th>Term</th>
            <th>Snomed Code</th>
            <th>OPCS Code</th>
            <th>Default Duration</th>
            <th>Aliases</th>
            <th>Has Benefits</th>
            <th>Has Complications</th>
            <th>Active</th>
        </tr>
        </thead>
        <tbody>
        <?php

        foreach ($procedures as $key => $procedure) { ?>
            <tr id="$key" class="clickable" data-id="<?php echo $procedure->id ?>"
                data-uri="oeadmin/procedure/edit/<?php echo $procedure->id ?>?returnUri=">
                <td><input type="checkbox" name="[$key]select" id="[$key]select"/></td>
                <td><?php echo $procedure->term ?></td>
                <td><?php echo $procedure->snomed_code ?></td>
                <td><?php echo implode(", ", array_map(function ($code) {
                        return $code->name;
                    }, $procedure->opcsCodes)); ?>
                </td>
                <td><?php echo $procedure->default_duration ?></td>
                <td><?php echo $procedure->aliases ?></td>
                <td><?php echo implode(", ", array_map(function ($code) {
                        return $code->name;
                    }, $procedure->benefits)); ?>
                </td>
                <td><?php echo implode(", ", array_map(function ($code) {
                        return $code->name;
                    }, $procedure->complications)); ?>
                </td>
                <td>
                    <?php echo ($procedure->active) ?
                        ('<i class="oe-i tick small"></i>') :
                        ('<i class="oe-i remove small"></i>'); ?>
                </td>
                <td>
                    <?php
                    echo count($procedures);
                    ?>
                </td>
            </tr>
        <?php } ?>
        </tbody>
        <tfoot class="pagination-container">
        <tr>
            <td colspan="9">
                <?php $this->widget(
                    'LinkPager',
                    ['pages' => $pagination]
                ); ?>
            </td>
        </tr>
        </tfoot>
    </table>
</main>