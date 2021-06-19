<?php
if (empty($patient)) {
    $patient = $this->patient;
}
?>
<header class="element-header"><h3 class="element-title">Attachments</h3></header>
<div class="data-group element-fields full-width flex-layout">
        <table id="correspondence_attachments_table" class="cols-10">
            <thead>
            <tr>
                <th>Attachment type</th>
                <th>Title</th>
                <th>Event Date</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
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
            array_map(function ($attachments) {
                return ['label' => $this->getEventSubType($attachments) . ' - ' . Helper::convertDate2NHS($attachments->event_date) ,
                    'id' => $attachments->id];
            }, $events)
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
        }
    });
</script>