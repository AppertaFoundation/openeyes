    <header class="element-header"><h3 class="element-title">Attachments</h3></header>
    <div class="element-fields full-width flex-layout">
        <table id="correspondence_attachments_table" class="cols-10">
            <thead>
            <tr>
                <th>Attachment type</th>
                <th>Attachment name in letter</th>
                <th>Event Date</th>
                <th>Status</th>
                <th><!-- trash --></th>
            </tr>
            </thead>
            <tbody>
            <?php
            if (empty($patient)) {
                $patient = $this->patient;
            }
            $row_index = 0;
            $event_id_compare = array();

            foreach ($associated_content as $key => $value) {
                $method = null;
                $event_name = null;
                $is_macroinit = false;
                $is_deleted_event = false;

                if (isset($value->initMethod)) {
                    $method = $value->initMethod->method;
                    $ac = $value;
                } else {
                    if (isset($value->initAssociatedContent->initMethod->method)) {
                        $method = $value->initAssociatedContent->initMethod->method;
                        $ac = $value->initAssociatedContent;
                        $is_macroinit = true;
                    } else {
                        $ac = $value;
                    }
                }

                if ($method != null) {
                    $event = json_decode($api->{$method}($patient));
                    if ($event !== null) {
                        $event_name = $event->event_name;
                        $event_date = $event->event_date;
                    }
                } else {
                    $event = Event::model()->findByPk($ac->associated_event_id);
                    if (isset($event->eventType)) {
                        $event_name = $event->eventType->name;
                        $event_date = Helper::convertDate2NHS($event->event_date);
                    } elseif (!is_null($event)) {
                        $event_name = $event->event_name;
                        $event_date = Helper::convertDate2NHS($event->event_date);
                    } else {
                        $event_name = $ac->display_title . ' (deleted)';
                        $event_date = '<i>N/A</i>';
                        $is_deleted_event = true;
                    }
                }

                if (!$is_deleted_event && empty($event)) {
                    continue;
                }

                $event_id = !$is_deleted_event ? $event->id : $ac->associated_event_id;

                $event_id_compare[] = $event_id;
                ?>
                <tr data-id="<?= $row_index ?>">
                    <?php

                    if (isset($_POST['attachments_event_id'])) { ?>
                        <input type="hidden" class="attachments_event_id" name="attachments_event_id[<?= $row_index ?>]"
                               value="<?= $_POST['attachments_event_id'][$row_index] ?>"/>
                    <?php } elseif (isset($value->associated_protected_file_id)) { ?>
                        <input type="hidden" class="attachments_event_id" name="attachments_event_id[<?= $row_index ?>]"
                               value="<?= $event_id ?>"/>
                    <?php }

                    if (isset($_POST['attachments_display_title'])) {
                        $display_title = $_POST['attachments_display_title'][$row_index];
                    } else {
                        if (isset($value->display_title) && strlen($value->display_title) > 0) {
                            $display_title = $value->display_title;
                        } else {
                            $display_title = (isset($ac->display_title) ? $ac->display_title : $event_name);
                        }
                    }
                    ?>

                    <td><?= $event_name ?></td>
                    <td><input type="text" class="attachments_display_title"
                               name="attachments_display_title[<?= $row_index ?>]" value="<?= $display_title ?>"/></td>
                    <td>
                        <input type="hidden" name="attachments_event_id[<?= $row_index ?>]" value="<?= $event_id ?>"/>
                        <?php if ($is_macroinit) : ?>
                            <input type="hidden" name="attachments_id[<?= $row_index ?>]" value="<?= $ac->id ?>"/>
                        <?php endif; ?>
                        <input type="hidden" name="attachments_system_hidden[<?= $row_index ?>]"
                               value="<?= $ac->is_system_hidden ?>"/>
                        <input type="hidden" name="attachments_print_appended[<?= $row_index ?>]"
                               value="<?= $ac->is_print_appended ?>"/>
                        <input type="hidden" name="attachments_short_code[<?= $row_index ?>]"
                               value="<?= $ac->short_code ?>"/>
                        <?= $event_date ?>
                    </td>
                    <td class="attachment_status">
                        <?php if ($ac->associated_protected_file_id) { ?>
                            <i class="oe-i tick-green small pad-right"></i>Attached
                        <?php } else {
                            $tooltip_content = 'Temporary error, please try again. If the error still occurs, please contact support.';
                            ?>
                            <i class="oe-i cross-red small pad-right"></i>Unable to attach
                            <i class="oe-i oe-i info small pad js-has-tooltip" data-tooltip-content="<?= $tooltip_content ?>"></i>
                        <?php } ?>
                    </td>
                    <td style="text-align: right;">
                        <button class="reprocess_btn" style="display: <?= $ac->associated_protected_file_id ? 'none' : ''?>" type="button">Try again</button>
                        <i class="oe-i trash"></i>
                    </td>
                </tr>
                <?php $row_index++;
            }

            if (isset($_POST['attachments_event_id'])) {
                $posted_data = array_diff_assoc($_POST['attachments_event_id'], $event_id_compare);
                if (!empty($posted_data)) {
                    foreach ($posted_data as $pdk => $pdv) {
                        $event = Event::model()->findByPk($pdv);
                        $row_index++;
                        ?>

                        <tr data-id="<?= $row_index ?>">
                            <input type="hidden" class="attachments_event_id"
                                   name="attachments_event_id[<?= $row_index ?>]"
                                   value="<?= $_POST['attachments_event_id'][$pdk] ?>"/>
                            <td><?= $event->eventType->name ?></td>
                            <td><input type="text" class="attachments_display_title"
                                       name="attachments_display_title[<?= $row_index ?>]"
                                       value="<?= $_POST['attachments_display_title'][$pdk] ?>"/></td>
                            <td>
                                <?= Helper::convertDate2NHS($event->event_date); ?>
                            </td>
                            <td><i class="oe-i trash"></i></td>
                        </tr>
                        <?php
                    }
                }
            }
            ?>
            </tbody>
        </table>
            <div class="add-data-actions flex-item-bottom" id="correspondence-attachment-popup">
                <button class="button hint green js-add-select-search" id="add-attachment-btn" type="button">
                    <i class="oe-i plus pro-theme"></i>
                </button>
            </div>
    </div>
<script>
    <?php  $events = $this->getAttachableEvents($patient); ?>
    new OpenEyes.UI.AdderDialog({
        openButton: $('#add-attachment-btn'),
        itemSets: [new OpenEyes.UI.AdderDialog.ItemSet(<?= CJSON::encode(
            array_map(function ($key, $attachments) {
                return ['label' => $this->getEventSubType($attachments) . ' - ' . Helper::convertDate2NHS($attachments->event_date) ,
                'id' => $attachments->id];
            }, array_keys($events), $events)
        ) ?>, {'multiSelect': true})],

        onReturn: function (adderDialog, selectedItems) {
            if(selectedItems.length) {
                disableButtons();
                for (let key in selectedItems) {
                    OphCoCorrespondence_addAttachment(selectedItems[key].id);
                }
                enableButtons();
            }
            return true;
        },
        onOpen: function () {
            $('table.select-options').find('li').each(function () {
                var attachmentId = $(this).data('id');
                var alreadyUsed = $('#correspondence_attachments_table')
                    .find('input[type="hidden"][name*="attachments_event_id"][value="' + attachmentId + '"]').length > 0;
                $(this).toggle(!alreadyUsed);
            });
        },
    });
</script>