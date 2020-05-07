<?php
/**
 * (C) Copyright Apperta Foundation 2020
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2017, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
?>


<div class="cols-5">
    <div class="row divider">
        <h2>Add Request</h2>
    </div>

    <?php echo $this->renderPartial('//admin/_form_errors', ['errors' => $errors]) ?>
    <?php
    $form = $this->beginWidget(
        'CActiveForm',
        [
            'id' => 'upload-form',
            'enableAjaxValidation' => false,
            'htmlOptions' => ['enctype' => 'multipart/form-data'],
        ]
    );
    ?>
    <table class="standard">
        <tbody>
        <tr>
            <td><?= RequestType::model()->getAttributeLabel('request_type'); ?></td>
            <td>
                <?= \CHtml::dropDownList(
                    'request_type',
                    null,
                    CHtml::listData(RequestType::model()->findAll(), 'request_type', 'title_short')
                ); ?>
            </td>
        </tr>
        <tr>
            <td><?= RequestType::model()->getAttributeLabel('system_message'); ?></td>
            <td><?= \CHtml::textField('system_message', null, ['class' => 'cols-full']) ?></td>
        </tr>

        <tr>
            <td colspan="2"><?= \CHtml::fileField('file'); ?></td>
        </tr>
        <tr>
            <td colspan="2">
                <table id="extra-post-fields">
                    <colgroup>
                        <col class="cols-4">
                        <col class="cols-4">
                    </colgroup>
                    <thead>
                    <th>Key</th>
                    <th>Value</th>
                    </thead>
                    <tbody>
                    <tr>
                        <td><input type="text" class="js-field-name cols-full"></td>
                        <td><input type="text" class="js-field-post cols-full"></td>
                    </tr>
                    </tbody>
                    <tfoot>
                    <tr>
                        <td colspan="2" class="right"><?= \OEHtml::button('Add', ['id' => 'add-new-postfield']) ?></td>
                    </tr>
                    </tfoot>
                </table>
            </td>
        </tr>
        </tbody>
        <tfoot>
        <tr>
            <td><?= \OEHtml::submitButton() ?></td>
        </tr>
        </tfoot>
    </table>
    <?php $this->endWidget(); ?>
</div>
<div class="row divider">
    <h2>Requests</h2>
</div>
<div class="cols-12">
    <table class="standard">
        <thead>
        <tr>
            <th>ID</th>
            <th>Request Type</th>
            <th>System Message</th>
            <th>Request Override Default Queue</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($data_provider->getData() as $_request) : ?>
            <tr data-request-id="<?= $_request->id; ?>">
                <td><?= $_request->id; ?></td>
                <td><?= $_request->requestType->title_short; ?></td>
                <td><?= $_request->system_message; ?></td>
                <td><?= $_request->request_override_default_queue; ?></td>
                <td><?php if (count($_request->attachmentDatas)) {
                        echo \OEHtml::button('Show attachments', ['class' => 'button small js-show-attachments']);
                    }
                    ?>
                </td>
            </tr>
            <?php foreach ($_request->attachmentDatas as $attachment) : ?>
                <tr class="js-request-<?= $_request->id; ?> attachment" style="display:none">
                    <td colspan="5">
                        <table class="standard" style="background-color: white">
                            <colgroup>
                                <col class="cols-1">
                                <col class="cols-2">
                                <col class="cols-1">
                                <col class="cols-1">
                                <col class="cols-1">
                                <col class="cols-2">
                                <col class="cols-2">
                                <col class="cols-2">
                            </colgroup>
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th><?= $attachment->getAttributeLabel('attachment_mnemonic'); ?></th>
                                <th><?= $attachment->getAttributeLabel('body_site_snomed_type'); ?></th>
                                <th><?= $attachment->getAttributeLabel('system_only_managed'); ?></th>
                                <th><?= $attachment->getAttributeLabel('attachment_type'); ?></th>
                                <th><?= $attachment->getAttributeLabel('mime_type'); ?></th>
                                <?php if ($attachment->blob_data) : ?>
                                    <th><?= $attachment->getAttributeLabel('blob_data'); ?></th>
                                <?php endif; ?>
                                <?php if ($attachment->text_data) : ?>
                                    <th><?= $attachment->getAttributeLabel('text_data'); ?></th>
                                <?php endif; ?>
                                <?php if ($attachment->blob_data) : ?>
                                    <th><?= $attachment->getAttributeLabel('upload_file_name'); ?></th>
                                <?php endif; ?>
                            </tr>
                            </thead>
                            <tbody>
                            <td><?= $attachment->id; ?></td>
                            <td><?= $attachment->attachment_mnemonic; ?></td>
                            <td><?= $attachment->body_site_snomed_type; ?></td>
                            <td><?= $attachment->system_only_managed; ?></td>
                            <td><?= $attachment->attachment_type; ?></td>
                            <td><?= $attachment->mime_type; ?></td>
                            <?php if ($attachment->blob_data) : ?>
                                <!--<td><?php /*=$attachment->thumbnail_small_blob;*/ ?></td>-->
                                <td><?= round(strlen($attachment->blob_data) / 1000); ?> K</td>
                            <?php endif; ?>
                            <?php if ($attachment->text_data) : ?>
                                <td style="word-break: break-all"><?= $attachment->text_data; ?></td>
                            <?php endif; ?>
                            <?php if ($attachment->blob_data) : ?>
                                <td><?= $attachment->upload_file_name; ?></td>
                            <?php endif; ?>
                            </tbody>
                        </table>
                    </td>
                </tr>
            <?php endforeach; ?>

        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<script type="text/template" id="post-field-row" class="hidden">
    <tr>
        <td><input type="text" class="js-field-name cols-full"></td>
        <td><input type="text" class="js-field-post cols-full"></td>
    </tr>
</script>
<script>
    $(document).ready(function () {
        $('.js-show-attachments').on('click', function () {
            let $button = this;
            let $tr = $(this).closest('tr');
            let request_id = $tr.data('request-id');

            $('.js-request-' + request_id).toggle();
        });

        $('#upload-form').on('submit', function (event) {
            event.preventDefault();

            $.each($('#extra-post-fields').find('tbody tr'), function (i, tr) {
                let name = $(tr).find('.js-field-name').val();
                $(tr).find('.js-field-post').attr('name', name);
            });
            $(this).unbind('submit').submit();
        });

        $('#add-new-postfield').on('click', function (event) {
            event.preventDefault();
            let $row = Mustache.render(
                $('#post-field-row').text()
            );
            $('#extra-post-fields tbody').append($row);
        });
    });
</script>
