<?php
/**
 * OpenEyes.
 *
 * (C) OpenEyes Foundation, 2019
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
?>

<div class="cols-7">

    <?php if (!$event_logs) : ?>
        <div class="row divider">
            <div class="alert-box issue"><b>No results found</b></div>
        </div>
    <?php endif; ?>

    <div class="row divider">
        <form id="event_log_search" method="post">
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
                        <?=\CHtml::textField(
                            'search[query]',
                            $search['query'],
                            [
                                'class' => 'cols-full',
                                'placeholder' => "Event Id, Unique Code, Examination Date"
                            ]
                        ); ?>
                    </td>
                    <td>
                        <?= \CHtml::dropDownList(
                            'search[status_value]',
                            $search['status_value'],
                            CHtml::listData($statuses, 'id', 'status_value'),
                            ['empty' => '-All-']
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

    <form id="admin_eventLogs" method="post">
        <input type="hidden" name="YII_CSRF_TOKEN" value="<?= Yii::app()->request->csrfToken ?>"/>
        <table class="standard">
            <thead>
            <tr>
                <th><input type="checkbox" name="selectall" id="selectall"/></th>
                <th>Event Id</th>
                <th>Unique Code</th>
                <th>Examination Date</th>
                <th>Status Value</th>
            </tr>
            </thead>

            <tbody>
            <?php
            foreach ($event_logs as $key => $event) { ?>
                <tr id="$key" class="clickable" data-id="<?php echo $event->id ?>"
                    data-uri="oeadmin/eventLog/edit/<?php echo $event->id ?>?returnUri=">
                    <td><input type="checkbox" name="select[]" value="<?php echo $event->id ?>" id="select[<?=$event->id ?>]"/></td>
                    <td><?php echo $event->event_id ?></td>
                    <td><?php echo $event->unique_code ?></td>
                    <td><?php echo $event->examination_date ?></td>
                    <td><?php echo $event->import_status->status_value ?></td>
                </tr>
            <?php } ?>
            </tbody>

            <tfoot class="pagination-container">
            <tr>
                <td colspan="2">
                    <?=\CHtml::submitButton(
                        'Delete',
                        [
                            'class' => 'button large disabled',
                            'data-uri' => '/oeadmin/eventLog/delete',
                            'name' => 'delete',
                            'data-object' => 'eventLogs',
                            'id' => 'et_delete',
                            'disabled' => true,
                        ]
                    ); ?>
                </td>
                <td colspan="3">
                    <?php $this->widget(
                        'LinkPager',
                        ['pages' => $pagination]
                    ); ?>
                </td>
            </tr>
            </tfoot>
        </table>
    </form>
</div>

<script>
    $(document).ready(function () {

        /**
         * Deactivate button when no checkbox is selected.
         */
        $(this).on('change', $('input[type="checkbox"]'), function (e) {
            var checked_boxes = $('#admin_eventLogs').find('table.standard tbody input[type="checkbox"]:checked');

            if (checked_boxes.length <= 0) {
                $('#et_delete').attr('disabled', true).addClass('disabled');
            } else {
                $('#et_delete').attr('disabled', false).removeClass('disabled');
            }
        });
    });
</script>
