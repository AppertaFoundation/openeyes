<form method="get" action="/Api/Request/admin/request/index">
    <div class="flex-layout">
        <div class="cols-6 flex-left">
            <table class="standard">
                <colgroup>
                    <col class="cols-1">
                    <col>
                </colgroup>
                <thead>Filters</thead>
                <tbody>
                    <tr style="border-bottom: hidden;">
                        <td>From:</td>
                        <td><?= CHtml::textField('from_date', $http_request->getParam('from_date'), ['placeholder' => 'yyyy-mm-dd', 'class' => 'filter_field']) ?></td>
                        <td>To:</td>
                        <td><?= CHtml::textField('to_date', $http_request->getParam('to_date'), ['placeholder' => 'yyyy-mm-dd', 'class' => 'filter_field']) ?></td>
                        <td><a href="javascript:void(0)" id="set_today_date">Set Today</a></td>
                        <td><a href="javascript:void(0)" class="clear">Clear</a></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td><?= CHtml::textField('from_time', $http_request->getParam('from_time'), ['placeholder' => 'hh:mm:ss', 'class' => 'filter_field']) ?></td>
                        <td></td>
                        <td><?= CHtml::textField('to_time', $http_request->getParam('to_time'), ['placeholder' => 'hh:mm:ss', 'class' => 'filter_field']) ?></td>
                        <td><a href="javascript:void(0)" class="clear">Clear</a></td>
                    </tr>
                    <tr>
                        <td>Id:</td>
                        <td><?= CHtml::textField('from_id', $http_request->getParam('from_id'), ['class' => 'filter_field']) ?></td>
                        <td></td>
                        <td><?= CHtml::textField('to_id', $http_request->getParam('to_id'), ['class' => 'filter_field']) ?></td>
                        <td><a href="javascript:void(0)" class="clear">Clear</a></td>
                    </tr>
                    <tr>
                        <td>Overall Status:</td>
                        <td style="text-align: center;">
                            <input type="hidden" name="show_complete" value="0">
                            <?= CHtml::checkBox("show_complete", $http_request->getParam('show_complete') === '0' ? false : true, ['class' => 'filter_field']) . 'Complete'; ?>
                        </td>
                        <td>
                            <input type="hidden" name="show_incomplete" value="0">
                            <?= CHtml::checkBox("show_incomplete", $http_request->getParam('show_incomplete') === '0' ? false : true, ['class' => 'filter_field']) . 'Incomplete'; ?>
                        </td>
                        <td>
                            <input type="hidden" name="show_failed" value="0">
                            <?= CHtml::checkBox("show_failed", $http_request->getParam('show_failed') === '0' ? false : true, ['class' => 'filter_field']) . 'Failed'; ?>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">Order By</td>
                        <td><?= \CHtml::dropDownList('order_by', $http_request->getParam('order_by'), ['latest' => 'Latest dates', 'earliest' => 'Earliest dates'], ['empty' => '- Order By -', 'class' => 'filter_field']) ?></td>
                    </tr>
                    <tr>
                        <td colspan="2">Show requests that have routine(s) with try counter higher than 1</td>
                        <td>
                            <input type="hidden" name="show_trycount_higher_than_one" value="0">
                            <?= CHtml::checkBox("show_trycount_higher_than_one", $http_request->getParam('show_trycount_higher_than_one') === '1' ? true : false, ['class' => 'filter_field']) . ''; ?>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">Request Routine Name</td>
                        <td>
                            <?= CHtml::dropDownList(
                                "routine_and_status_filter[routine_name]",
                                $http_request->getParam('routine_and_status_filter')['routine_name'] ?? '',
                                CHtml::listData(RoutineLibrary::model()->findAll(array('order' => 'routine_name asc')), 'routine_name', 'routine_name'),
                                ['empty' => '- Empty -', 'class' => 'filter_field cols-12']
                            ) ?>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">Request Routine Status</td>
                        <td>
                            <?= CHtml::dropDownList(
                                "routine_and_status_filter[routine_status]",
                                $http_request->getParam('routine_and_status_filter')['routine_status'] ?? '',
                                ['COMPLETE' => 'COMPLETE', 'NEW' => 'NEW', 'VOID' => 'VOID', 'RETRY' => 'RETRY', 'FAILED' => 'FAILED'],
                                ['empty' => '- All -', 'class' => 'filter_field']
                            ) ?>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="cols-5 flex-right">
            <table id="extra-columns-table" class="standard sortable">
                <thead>Extra columns:</thead>
                <tbody>
                    <tr class="info" <?= !empty($extra_columns) ? 'style="display: none;"' : '' ?>>
                        <td><span class="fade">Please select the columns to add from the button below</span></td>
                    </tr>
                    <?php foreach ($extra_columns as $index => $column) { ?>
                        <tr class="extra-column">
                            <td>
                                <span>&uarr;&darr;</span>
                            </td>
                            <td class="column-name">
                                <input type="hidden" name="extra-columns[<?= $index ?>]" value="<?= $column; ?>">
                                <?= $column; ?>
                            </td>
                            <td>
                                <i class="oe-i trash"></i>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
            <div>
                <div class="flex-layout flex-right" style="margin-right: 25px;">
                    <div class="add-data-actions flex-item-bottom">
                        <button class="button hint green" id="add-column" type="button">
                            <i class="oe-i plus pro-theme"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row divider flex-layout">
        <div class="flex-left">
            <table id="default-columns" class="standard">
                <thead>
                    Default columns:
                </thead>
                <tbody>
                    <tr>
                        <input type="hidden" name="show_id" value="0">
                        <td><?= CHtml::checkBox("show_id", $http_request->getParam('show_id') === '0' ? false : true, ['class' => 'filter_field']) . 'ID'; ?></td>
                        <input type="hidden" name="show_payload_received" value="0">
                        <td><?= CHtml::checkBox("show_payload_received", $http_request->getParam('show_payload_received') === '0' ? false : true, ['class' => 'filter_field']) . 'Payload Received'; ?></td>
                        <input type="hidden" name="show_request_type" value="0">
                        <td><?= CHtml::checkBox("show_request_type", $http_request->getParam('show_request_type') === '0' ? false : true, ['class' => 'filter_field']) . 'Request Type'; ?></td>
                        <input type="hidden" name="show_overall_status" value="0">
                        <td><?= CHtml::checkBox("show_overall_status", $http_request->getParam('show_overall_status') === '0' ? false : true, ['class' => 'filter_field']) . 'Overall Status'; ?></td>
                        <input type="hidden" name="show_system_message" value="0">
                        <td><?= CHtml::checkBox("show_system_message", $http_request->getParam('show_system_message') === '0' ? false : true, ['class' => 'filter_field']) . 'System Message'; ?></td>
                    </tr>
                    <tr>
                        <input type="hidden" name="show_steps" value="0">
                        <td><?= CHtml::checkBox("show_steps", $http_request->getParam('show_steps') === '0' ? false : true, ['class' => 'filter_field']) . 'Steps'; ?></td>
                        <input type="hidden" name="show_payload_size" value="0">
                        <td><?= CHtml::checkBox("show_payload_size", $http_request->getParam('show_payload_size') === '0' ? false : true, ['class' => 'filter_field']) . 'Payload Size (count)'; ?></td>
                        <input type="hidden" name="show_attached_size" value="0">
                        <td><?= CHtml::checkBox("show_attached_size", $http_request->getParam('show_attached_size') === '0' ? false : true, ['class' => 'filter_field']) . 'Attached Size (count)'; ?></td>
                        <input type="hidden" name="actions" value="0">
                        <td><?= CHtml::checkBox("actions", $http_request->getParam('actions') === '0' ? false : true, ['class' => 'filter_field']) . 'Actions'; ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div id="extra-filters" class="flex-right cols-5" <?= empty($extra_columns) ? 'style="display: none;"' : '' ?>>
            <table id="extra-filters-table" class="standard">
                <thead>Extra filters:</thead>
                <tbody>
                    <tr class="info" <?= !empty($extra_filters) ? 'style="display: none;"' : '' ?>>
                        <td><span class="fade">Please select the filters to add from the button below</span></td>
                    </tr>
                    <?php foreach ($extra_filters as $name => $value) { ?>
                        <tr>
                            <td><?= $name ?></td>
                            <td>
                                <input class="extra-filters" type="text" name="extra-filters[<?= $name ?>]" value="<?= $value ?>" />
                            </td>
                            <td>
                                <i class="oe-i trash"></i>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
            <div>
                <div class="flex-layout flex-right">
                    <div class="add-data-actions flex-item-bottom">
                        <button class="button hint green" id="add-extra-filter" type="button">
                            <i class="oe-i plus pro-theme"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div>
        <?php echo CHtml::button(
            'Filter',
            [
                'class' => 'button large',
                'name' => 'filter',
                'type' => 'submit',
                'id' => 'et_filter'
            ]
        ); ?>
    </div>
</form>