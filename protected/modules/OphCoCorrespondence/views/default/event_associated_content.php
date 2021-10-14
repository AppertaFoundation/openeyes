    <?php
    if (empty($patient)) {
        $patient = $this->patient;
    }
    ?>
    
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

            //An array of the form [[
            //     'event_id' => $event_id,
            //     'display_title' => $display_title,
            //     'protected_file_id' => $protected_file_id,
            //     'is_system_hidden' => $system_hidden,
            //     'is_print_appended' => $print_appended,
            //     'short_code' => $short_code,
            // ], ..]
            $attachments = array();

            if (isset($init_associated_content)) {
                foreach ($init_associated_content as $val) {
                    $method = null;

                    if (isset($val->initMethod)) {
                        $method = $val->initMethod->method;
                    } else {
                        if (isset($val->initAssociatedContent->initMethod->method)) {
                            $method = $val->initAssociatedContent->initMethod->method;
                        }
                    }

                    $event = json_decode($api->{$method}($patient));
                    $event_id = $event->id;

                    $attachments[$event_id] = [
                        'event_id' => $event_id,
                        'display_title' => $val->display_title,
                        'protected_file_id' => $val->init_protected_file_id,
                        'is_system_hidden' => $val->is_system_hidden,
                        'is_print_appended' => $val->is_print_appended,
                        'short_code' => $val->short_code,
                    ];
                }
            }

            if (isset($this->event)) {
                $event_associated_content = EventAssociatedContent::model()->findAllByAttributes(
                    array('parent_event_id' => $this->event->id)
                );
            }

            if (isset($event_associated_content)) {
                foreach ($event_associated_content as $val) {
                    $event_id = $val->associated_event_id;

                    $attachments[$event_id] = [
                        'event_id' => $event_id,
                        'display_title' => $val->display_title,
                        'protected_file_id' => $val->associated_protected_file_id,
                        'is_system_hidden' => $val->is_system_hidden,
                        'is_print_appended' => $val->is_print_appended,
                        'short_code' => $val->short_code,
                    ];
                }
            }

            if (isset($_POST['attachments_event_id'])) {
                if (count($_POST['attachments_event_id']) != count($_POST['attachments_display_title']) ||
                    count($_POST['attachments_event_id']) != count($_POST['file_id'])) {
                    throw new Exception("Incorrectly formed attachment data in POST");
                }

                for ($i = 0; $i < count($_POST['attachments_event_id']); $i++) {
                    $event_id = $_POST['attachments_event_id'][$i];

                    $attachment = [
                        'event_id' => $event_id,
                        'display_title' => $_POST['attachments_display_title'][$i],
                        'protected_file_id' => $_POST['file_id'][$i],
                    ];

                    if (array_key_exists('attachments_system_hidden', $_POST) && array_key_exists($i, $_POST['attachments_system_hidden'])) {
                        $attachment['is_system_hidden'] = $_POST['attachments_system_hidden'][$i];
                    }
                    if (array_key_exists('attachments_print_appended', $_POST) && array_key_exists($i, $_POST['attachments_print_appended'])) {
                        $attachment['is_print_appended'] = $_POST['attachments_print_appended'][$i];
                    }
                    if (array_key_exists('short_code', $_POST) && array_key_exists($i, $_POST['short_code'])) {
                        $attachment['short_code'] = $_POST['short_code'][$i];
                    }

                    $attachments[$event_id] = $attachment;
                }
            }

            $row_index = 0;

            $pending_attachment_event_ids = array();

            foreach ($attachments as $attachment) {
                $attachment_event_id = $attachment['event_id'];
                $display_title = $attachment['display_title'];
                $file_id = $attachment['protected_file_id'];
                $is_system_hidden = array_key_exists('is_system_hidden', $attachment) ? $attachment['is_system_hidden'] : null;
                $is_print_appended = array_key_exists('is_print_appended', $attachment) ? $attachment['is_print_appended'] : null;
                $short_code = array_key_exists('short_code', $attachment) ? $attachment['short_code'] : null;

                $event = Event::model()->findByPk($attachment_event_id);
                $event_deleted = !isset($event);

                if ($event_deleted) {
                    $is_print_appended = 0;
                }

                if (!isset($file_id) && !$event_deleted) {
                    $pending_attachment_event_ids[] = $attachment_event_id;
                } else {
                    $processed_event_type_text = $event_deleted ? $display_title . " (deleted)" : $event->eventType->name;
                    ?>
                        <tr data-id="<?= $row_index ?>">
                            <input
                                type="hidden"
                                class="attachments_event_id"
                                name="attachments_event_id[<?= $row_index ?>]"
                                value="<?= $attachment_event_id ?>"/>
                            <td><?= $processed_event_type_text ?></td>
                            <td>
                                <input 
                                    type="text" 
                                    class="attachments_display_title"
                                    name="attachments_display_title[<?= $row_index ?>]"
                                    value="<?= $display_title ?>"/>
                            </td>
                            <td>
                                <?php if (!empty($is_system_hidden)) { ?>  
                                <input 
                                    type="hidden" 
                                    name="attachments_system_hidden[<?= $row_index ?>]"
                                    value="<?= $is_system_hidden ?>"/>
                                    <?php
                                }
                                if (!empty($is_print_appended)) {
                                    ?>
                                <input
                                    type="hidden"
                                    name="attachments_print_appended[<?= $row_index ?>]"
                                    value="<?= $is_print_appended ?>"/>
                                    <?php
                                }
                                if (!empty($short_code)) {
                                    ?>
                                <input 
                                    type="hidden"
                                    name="attachments_short_code[<?= $row_index ?>]"
                                    value="<?= $short_code ?>"/>
                                <?php }
                                echo $event_deleted ? "N/A" : Helper::convertDate2NHS($event->event_date);
                                ?>
                            </td>
                            <td class="attachment_status">
                                <?php if (!$event_deleted) { ?>
                                    <?php if ($file_id) {?>
                                    <input type="hidden" name="file_id[<?=$row_index?>]" value="<?=$file_id?>">
                                        <i class="oe-i tick-green small pad-right"></i>Attached
                                    <?php } else { ?>
                                        <i class="oe-i cross-red small pad-right"></i>Unable to attach
                                        <i class="oe-i oe-i info small pad js-has-tooltip" data-tooltip-content="Temporary error, please try again. If the error still occurs, please contact support."></i>
                                    <?php }
                                } else { ?>
                                    <input type="hidden" name="file_id[<?=$row_index?>]" value="<?=$file_id?>">
                                    <i class="oe-i cross-red small pad-right"></i>Unable to attach
                                    <i class="oe-i oe-i info small pad js-has-tooltip" data-tooltip-content="Event cannot be attached as it has been deleted"></i>
                                <?php } ?>
                            </td>
                            <td style="text-align: right;">
                                <button class="reprocess_btn" style="display: <?= $event_deleted || $file_id ? 'none' : ''?>" type="button">Try again</button>
                                <i class="oe-i trash"></i>
                            </td>
                        </tr>
                    <?php
                }
                $row_index++;
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
    $().ready(function(){
        let eventIdsToAttach = <?= json_encode($pending_attachment_event_ids) ?>;
        eventIdsToAttach.forEach(
            function(id) {
                OphCoCorrespondence_addAttachment(id);
            }
        );
    });

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