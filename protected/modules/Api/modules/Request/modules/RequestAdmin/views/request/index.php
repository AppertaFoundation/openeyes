<?php
$http_request = Yii::app()->getRequest();
$requests = $data['requests'];
$pagination = $data['pagination'];
$display_none = 'style="display:none;"';
$default_columns = ['id', 'payload_received', 'request_type', 'overall_status', 'system_message', 'steps', 'payload_size', 'attached_size'];
$extra_columns = Yii::app()->getRequest()->getParam('extra-columns', []);
$extra_filters = Yii::app()->getRequest()->getParam('extra-filters', []);
$assetManager = Yii::app()->getAssetManager();
$widgetPath = $assetManager->publish('protected/modules/OphGeneric/widgets/js');
Yii::app()->clientScript->registerScriptFile($widgetPath . '/Attachment.js');
?>
<div class="row divider">
    <h2>Requests</h2>
</div>
<div class="cols-12">
    <?php $this->renderPartial('request_filters', ['http_request' => $http_request, 'extra_columns' => $extra_columns, 'extra_filters' => $extra_filters]) ?>
    <table class="standard">
        <?php if (count($requests) > 0) { ?>
            <thead>
            <tr>
                <?php foreach ($default_columns as $default_column) {
                    if ($http_request->getParam('show_' . $default_column) !== '0') {
                        echo '<th>' . $requests[0]->getAttributeLabel($default_column) . '</th>';
                    }
                } ?>
                <?php foreach ($extra_columns as $column) {
                    echo "<th>$column</th>";
                } ?>
                <th>Actions</th>
            </tr>
            </thead>
        <?php } ?>
        <tbody>
        <?php foreach ($requests as $request) : ?>
        <tr data-request-id="<?= $request->id; ?>">
            <td <?= $http_request->getParam('show_id') === '0' ? $display_none : '' ?>><?= $request->id; ?></td>
            <td <?= $http_request->getParam('show_payload_received') === '0' ? $display_none : '' ?>><?= $request->created_date ?></td>
            <td <?= $http_request->getParam('show_request_type') === '0' ? $display_none : '' ?>><?= $request->requestType->title_short; ?></td>
            <td <?= $http_request->getParam('show_overall_status') === '0' ? $display_none : '' ?>><?= $request->getRequestStatus(); ?></td>
            <td <?= $http_request->getParam('show_system_message') === '0' ? $display_none : '' ?>><?= $request->system_message; ?></td>
            <td <?= $http_request->getParam('show_steps') === '0' ? $display_none : '' ?>><?= $request->getTotalAndCompletedRoutinesDisplay(); ?></td>
            <td <?= $http_request->getParam('show_payload_size') === '0' ? $display_none : '' ?>><?= $request->getAttachmentsSizeAndCount(); ?></td>
            <td <?= $http_request->getParam('show_attached_size') === '0' ? $display_none : '' ?>><?= $request->getAttachmentsSizeAndCount(true); ?></td>
            <?php foreach ($extra_columns as $column) {
                $key_found = false;
                foreach ($request->requestDetails as $detail) {
                    if ($detail['name'] === $column) {
                        $value = $detail['value'];
                        echo "<td>$value</td>";
                        $key_found = true;
                        break;
                    }
                }
                if (!$key_found) {
                    echo "<td></td>";
                }
            } ?>
            <td>
                <div class="flex-layout">
                    <?php if (count($request->attachmentDatas)) {
                        echo \OEHtml::button('Show attachments', ['class' => 'button small js-show-attachments']);
                    }

                    if (count($request->requestRoutines)) {
                        echo \OEHtml::button('Show routines', ['class' => 'button small js-show-routines']);
                    }
                    ?>
                    <?php if ($request->mediaAttachmentData) { ?>
            <td>
                <input class="button small js-show-request-media" data-id="<?= $request->mediaAttachmentData->id ?>"
                       data-mime="<?= $request->mediaAttachmentData->mime_type ?>"
                       data-full-title="<?= $request->mediaAttachmentData->attachment_mnemonic ?>" name="yt2"
                       type="button" value="Show media">
            </td>
                    <?php } ?>
</div>
    </td>
    </tr>
            <?php foreach ($request->attachmentDatas as $attachmentData) : ?>
    <tr class="js-request-<?= $request->id; ?> attachment" style="display:none">
        <td colspan="10">
            <table class="standard" style="background-color: white; table-layout: fixed">
                <colgroup>
                    <col class="cols-1">
                    <col class="cols-2">
                    <col class="cols-1" span="3">
                    <col class="cols-2">
                    <?php if ($attachmentData->text_data) { ?>
                        <col class="cols-8">
                    <?php } elseif ($attachmentData->blob_data) { ?>
                        <col class="cols-1">
                        <col class="cols-3">
                    <?php } ?>
                    <col class="cols-1">
                </colgroup>
                <thead>
                <tr>
                    <th>ID</th>
                    <th><?= $attachmentData->getAttributeLabel('attachment_mnemonic'); ?></th>
                    <th><?= $attachmentData->getAttributeLabel('body_site_snomed_type'); ?></th>
                    <th><?= $attachmentData->getAttributeLabel('system_only_managed'); ?></th>
                    <th><?= $attachmentData->getAttributeLabel('attachment_type'); ?></th>
                    <th><?= $attachmentData->getAttributeLabel('mime_type'); ?></th>
                    <?php if ($attachmentData->blob_data) : ?>
                        <th><?= $attachmentData->getAttributeLabel('blob_data'); ?></th>
                    <?php endif; ?>
                    <?php if ($attachmentData->text_data) : ?>
                        <th><?= $attachmentData->getAttributeLabel('text_data'); ?></th>
                    <?php endif; ?>
                    <?php if ($attachmentData->blob_data) : ?>
                        <th><?= $attachmentData->getAttributeLabel('upload_file_name'); ?></th>
                    <?php endif; ?>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                <td><?= $attachmentData->id; ?></td>
                <td><?= $attachmentData->attachment_mnemonic; ?></td>
                <td><?= $attachmentData->body_site_snomed_type; ?></td>
                <td><?= $attachmentData->system_only_managed; ?></td>
                <td><?= $attachmentData->attachment_type; ?></td>
                <td style="word-wrap: break-word;"><?= $attachmentData->mime_type; ?></td>
                            <?php if ($attachmentData->blob_data) : ?>
                    <!--<td><?php /*=$attachment->thumbnail_small_blob;*/ ?></td>-->
                    <td><?= round(strlen($attachmentData->blob_data) / 1000); ?> K</td>
                            <?php endif; ?>
                            <?php if ($attachmentData->text_data) : ?>
                    <td>
                        <div style="overflow: auto; max-height: 500px">
                            <pre><?= json_encode(json_decode($attachmentData->text_data), JSON_PRETTY_PRINT); ?></pre>
                        </div>
                    </td>
                            <?php endif; ?>
                            <?php if ($attachmentData->blob_data) : ?>
                    <td><?= $attachmentData->upload_file_name; ?></td>
                            <?php endif; ?>
                            <?php if ($attachmentData->mime_type === "application/pdf" || $attachmentData->mime_type === "image/png") { ?>
                    <td>
                        <input class="button small js-show-request-media" data-id="<?= $attachmentData->id ?>"
                               data-mime="<?= $attachmentData->mime_type ?>"
                               data-full-title="<?= $attachmentData->attachment_mnemonic ?>" name="yt2" type="button"
                               value="Show media">
                    </td>
                            <?php } ?>
                            <?php if ($attachmentData->attachment_mnemonic === 'REQUEST_DATA' && $this->checkAccess('OprnEditRequestData')) { ?>
                    <td>
                        <a class="button small"
                           href="/Api/Request/admin/attachmentData/edit/<?= $attachmentData->id; ?>">Edit</a>
                    </td>
                            <?php } ?>
                </tbody>
            </table>
        </td>
    </tr>
            <?php endforeach; ?>
            <?php foreach ($request->requestRoutines as $requestRoutine) : ?>
    <tr class="js-request-routine-<?= $request->id; ?>" style="display:none">
        <td colspan="10">
            <table class="standard" style="background-color: white">
                <colgroup>
                    <col class="cols-1">
                    <col class="cols-2">
                    <col class="cols-2">
                    <col class="cols-1">
                    <col class="cols-1">
                    <col class="cols-2">
                    <col class="cols-2">
                    <col class="cols-2">
                </colgroup>
                <thead>
                <tr>
                    <th>ID</th>
                    <th><?= $requestRoutine->getAttributeLabel('status'); ?></th>
                    <th><?= $requestRoutine->getAttributeLabel('routine_name'); ?></th>
                    <th><?= $requestRoutine->getAttributeLabel('try_count'); ?></th>
                    <th><?= $requestRoutine->getAttributeLabel('next_try_date_time'); ?></th>
                    <th><?= $requestRoutine->getAttributeLabel('execute_request_queue'); ?></th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                <tr data-request-routine-id="<?= $requestRoutine->id ?>">
                    <td><?= $requestRoutine->id; ?></td>
                    <td><?= $requestRoutine->status; ?></td>
                    <td style="word-break: break-all;"><?= $requestRoutine->routine_name; ?></td>
                    <td><?= $requestRoutine->try_count; ?></td>
                    <td><?= $requestRoutine->next_try_date_time; ?></td>
                    <td><?= $requestRoutine->execute_request_queue; ?></td>
                    <td>
                        <a class="button small"
                           href="/Api/Request/admin/requestRoutine/edit/<?= $requestRoutine->id ?>">Edit</a>
                        <?= \OEHtml::button('Show logs', ['class' => 'button small js-show-routine-logs']); ?>
                    </td>
                </tr>
                </tbody>
            </table>
        </td>
    </tr>
                <?php foreach ($requestRoutine->requestRoutineExecutions as $routineExecution) : ?>
        <tr class="js-request-routine-log-<?= $requestRoutine->id; ?>" data-request_id="<?= $request->id ?>"
            style="display:none">
            <td colspan="10">
                <table class="standard" style="background-color: white">
                    <colgroup>
                        <col class="cols-2">
                        <col class="cols-2">
                        <col class="cols-2">
                        <col class="cols-2">
                        <col class="cols-2">
                        <col class="cols-2">
                    </colgroup>
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th><?= $routineExecution->getAttributeLabel('log_text'); ?></th>
                        <th><?= $routineExecution->getAttributeLabel('request_routine_id'); ?></th>
                        <th><?= $routineExecution->getAttributeLabel('try_number'); ?></th>
                        <th><?= $routineExecution->getAttributeLabel('execution_date_time'); ?></th>
                        <th><?= $routineExecution->getAttributeLabel('status'); ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td><?= $routineExecution->id; ?></td>
                        <td><?= $routineExecution->log_text; ?></td>
                        <td style="word-break: break-all;"><?= $routineExecution->request_routine_id; ?></td>
                        <td><?= $routineExecution->try_number; ?></td>
                        <td><?= $routineExecution->execution_date_time; ?></td>
                        <td><?= $routineExecution->status; ?></td>

                    </tr>
                    </tbody>
                </table>
            </td>
        </tr>
                <?php endforeach; ?>
            <?php endforeach; ?>
        <?php endforeach; ?>
<?php if (!count($requests)) { ?>
    <tr>
        <td class="empty">No results found.</td>
    </tr>
<?php } ?>
</tbody>
</table>
<div class="pagination">
    <?php echo $this->renderPartial('//admin/_pagination', ['pagination' => $pagination, 'includeFirstAndLastPageLabel' => true]) ?>
</div>
</div>
<script type="text/template" id="post-field-row" class="hidden">
    <tr>
        <td><input type="text" class="js-field-name cols-full"></td>
        <td><input type="text" class="js-field-post cols-full"></td>
    </tr>
</script>
<?php $distinct_request_details = \OEModule\OphGeneric\models\RequestDetails::model()->findAll(['select' => "t.name", 'distinct' => true, 'order' => "t.name"]); ?>
<script>
    $(document).ready(function () {
        new OpenEyes.UI.AdderDialog({
            openButton: $('#add-column'),
            itemSets: [new OpenEyes.UI.AdderDialog.ItemSet(<?= CJSON::encode(
                array_map(function ($detail) {
                    return ['label' => $detail->name, 'id' => $detail->id];
                }, $distinct_request_details)
            )?>, {'multiSelect': true})],
            onOpen: function (adder_dialog) {
                $(adder_dialog.popup).find('li').each(function () {
                    let already_used = $("#extra-columns-table tr input[value='" + $(this).data('label') + "']").length > 0;
                    $(this).toggle(!already_used);
                });
            },
            onReturn: function (adder_dialog, selected_items) {
                $('#extra-columns-table').find('.info').hide();

                let row_num = $('#extra-columns-table tr:not(.info)').length;
                for (let index = 0; index < selected_items.length; index++) {
                    let column_name = selected_items[index].label;
                    $('#extra-columns-table tbody').append('<tr><td><span>&uarr;&darr;</span></td><td><input type="hidden" name="extra-columns[' + row_num + ']" value="' + column_name + '">' + column_name + '</td><td><i class="oe-i trash"></i></td></tr>');
                    row_num++;
                }
                $('#extra-filters').show();
                return true;
            }
        });

        new OpenEyes.UI.AdderDialog({
            openButton: $('#add-extra-filter'),
            itemSets: [new OpenEyes.UI.AdderDialog.ItemSet(<?= CJSON::encode(
                array_map(function ($detail) {
                    return ['label' => $detail->name, 'id' => $detail->id];
                }, $distinct_request_details)
            )?>, {'multiSelect': true})],
            onOpen: function (adder_dialog) {
                $(adder_dialog.popup).find('li').each(function () {
                    //show filters for added extra-columns
                    let column_used = $("#extra-columns-table input[value='" + $(this).data('label') + "']").length > 0;
                    $(this).toggle(column_used);
                    //hide already used extra filters
                    item = $("#extra-filters-table tr td:contains(" + $(this).data('label') + ")");
                    if ($(this).is(':visible') && item.html() === $(this).data('label')) {
                        $(this).toggle(false);
                    }
                });
            },
            onReturn: function (adder_dialog, selected_items) {
                $('#extra-filters-table').find('.info').hide();

                for (let index = 0; index < selected_items.length; index++) {
                    $('#extra-filters-table tbody').append('<tr><td>' + selected_items[index].label + '</td><td><input class="extra-filter" type="text" name="extra-filters[' + selected_items[index].label + ']"/></td><td><i class="oe-i trash"></i></td></tr>');
                }

                return true;
            }
        });

        $('.js-show-attachments').on('click', function () {
            let $button = this;
            let $tr = $(this).closest('tr');
            let request_id = $tr.data('request-id');

            $('.js-request-' + request_id).toggle();
        });

        $('.js-show-routines').on('click', function () {
            let $button = this;
            let $tr = $(this).closest('tr');
            let request_id = $tr.data('request-id');

            $('.js-request-routine-' + request_id).toggle();
            $('tr[data-request_id="' + request_id + '"]:visible').hide();
        });

        $('.js-show-routine-logs').on('click', function () {
            let $button = this;
            let $tr = $(this).closest('tr');
            let request_id = $tr.data('request-routine-id');

            $('.js-request-routine-log-' + request_id).toggle();
        });

        $('.js-show-request-media').on('click', function () {
            createDialog(
                createSingleView(
                    $(this).data('id'),
                    $(this).data('mime'),
                    'blob_data'
                ),
                $(this).data('full-title'),
                $(window).width() * 0.9 + 'px',
                $(window).height() * 0.7 + 'px'
            );
        });

        $('#set_today_date').on('click', function () {
            let date = new Date().toISOString().slice(0, 10);
            $('#from_date').val(date);
            $('#to_date').val(date);
        });

        $('.clear').on('click', function () {
            $(this).closest('tr').find('input').val('');
        });

        let datepicker_ids = ['#from_date', '#to_date'];
        datepicker_ids.forEach(function (datepicker_id) {
            pickmeup(datepicker_id, {
                format: 'Y-m-d',
                hide_on_select: true,
                default_date: false,
                max: new Date(),
            });
        });

        $('#extra-columns-table, #extra-filters-table').on('click', '.trash', function () {
            let $row = $(this).closest('tr');
            if ($row.hasClass('extra-column')) {
                let filter_name = $row.find('.column-name').html();
                $('.extra-filter[name="' + filter_name + '"]').closest('tr').remove();
            }
            $row.remove();
            if ($('#extra-columns-table tbody tr').length === 1) {
                $('#extra-filters').hide();
                $('#extra-columns-table').find('.info').show();
            }
            if ($('#extra-filters-table tbody tr').length === 1) {
                $('#extra-filters-table').find('.info').show();
            }
        });

        $('.sortable tbody').sortable({
            stop: function () {
            }
        });
    });
</script>