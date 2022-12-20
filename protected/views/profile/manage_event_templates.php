<h2>Your templates for pre-filling data</h2>
<?php foreach ($structured_templates as $event_type_name => $procedure_set_templates) { ?>
<div class="flex-l row"><i class="oe-i-e i-TrOperationNotes"></i>  <?= $event_type_name ?></div>
<table class="standard last-right no-pad">
    <colgroup>
        <col class="cols-5" />
        <col class="cols-6" />
        <col class="cols-1" />
    </colgroup>
    <thead>
        <tr>
            <th>Procedure(s)</th>
            <th>Your templates</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($procedure_set_templates as $procedure_set_id => $templates) { ?>
        <tr class="divider" data-test="template-row">
            <td>
                <ul class="dot-list">
                    <?php
                    $procedure_set = ProcedureSet::model()->findByPk($procedure_set_id);
                    foreach ($procedure_set->procedures as $procedure) { ?>
                        <li><?= $procedure->short_format ?></li>
                    <?php } ?>
                </ul>
            </td>
            <td>
                <table class="standard cols-11 js-template-list">
                    <colgroup>
                        <col class="cols-7" />
                        <col class="cols-1" />
                    </colgroup>
                    <tbody>
                        <?php foreach ($templates as $template) { ?>
                            <tr>
                                <td>
                                    <input class="js-template-id" type="hidden" value="<?= $template['id'] ?>">
                                    <span class="js-template-label" data-test="template-name-label"><?= $template['name'] ?></span>
                                    <div class="flex">
                                        <input value=""
                                        class="cols-10 js-template-input"
                                        placeholder="<?= $template['name'] ?>"
                                        minlength="4"
                                        maxlength="48"
                                        required=""
                                        style="display: none;"
                                        data-test="template-name-field">
                                    </div>
                                </td>
                                <td>
                                    <div class="flex nowrap">
                                        <i class="oe-i edit-blue js-edit-templates" data-test="template-edit-button"></i>
                                        <i class="oe-i undo small-icon js-undo-changes" style="display: none;" data-test="template-undo-button"></i>
                                        <i class="oe-i trash js-trash-templates" data-test="template-delete-button"></i>
                                        <i class="oe-i save js-save-changes" style="display: none;" data-test="template-save-button"></i>
                                    </div>
                                </td>
                            </tr>
                        <?php } ?>
                    <tbody>
                </table>
            </td>
            <td>

            </td>
        </tr>
        <?php } ?>
    </tbody>
</table>
<?php } ?>

<script type="text/javascript">
    function enableEdit($parent_tr) {
        $parent_tr.find('span.js-template-label').hide();
        $parent_tr.find('input.js-template-input').show();
        $parent_tr.find('i.js-edit-templates').hide();
        $parent_tr.find('i.js-undo-changes').show();
        $parent_tr.find('i.js-trash-templates').hide();
        $parent_tr.find('i.js-save-changes').show();
    }

    function disableEdit($parent_tr) {
        $parent_tr.find('span.js-template-label').show();
        $parent_tr.find('input.js-template-input').hide();
        $parent_tr.find('i.js-edit-templates').show();
        $parent_tr.find('i.js-undo-changes').hide();
        $parent_tr.find('i.js-trash-templates').show();
        $parent_tr.find('i.js-save-changes').hide();
    }

    $(document).ready(function() {
        $('.js-edit-templates').click(function() {
            $parent_tr = $(this).parent().parent().parent();
            $label = $parent_tr.find('span.js-template-label');
            $input = $parent_tr.find('input.js-template-input').val($label.text());

            enableEdit($parent_tr);
        });

        $('.js-undo-changes').click(function() {
            $parent_tr = $(this).parent().parent().parent();
            $parent_tr.find('input').hide();
            $parent_tr.find('span').show();

            disableEdit($parent_tr);
        });

        $('.js-trash-templates').click(function() {
            $parent_tr = $(this).parent().parent().parent();

            let template_ids = [$parent_tr.find('input.js-template-id').val()];

            new OpenEyes.UI.Dialog.Alert({
                content: 'Are you sure you wish to delete this event template?',
                closeCallback: function() {
                    $.ajax({
                        'type': 'POST',
                        'async': false,
                        'url': window.baseUrl + '/profile/deleteeventtemplates',
                        'data': {
                            "YII_CSRF_TOKEN": YII_CSRF_TOKEN,
                            "template_ids": template_ids,
                        },
                        'success': function (response) {
                            if(response.success) {
                                $parent_tr.remove();
                            } else {
                                new OpenEyes.UI.Dialog.Alert({
                                    content: "Something went wrong attempting to delete event templates.\n\nPlease contact a system administrator."
                                }).open();
                            }
                        }
                    });
                }
            }).open();
        });

        $('.js-save-changes').click(function() {
            $parent_tr = $(this).parent().parent().parent();

            let template_data = {};

            let id = $parent_tr.find('input.js-template-id').val();

            let $input = $parent_tr.find('input.js-template-input');
            let input_text = $input.val();
            let $label = $parent_tr.find('span.js-template-label').text(input_text);

            template_data[id] = input_text;

            $.ajax({
                'type': 'POST',
                'async': false,
                'url': window.baseUrl + '/profile/modifyeventtemplates',
                'data': {
                    "YII_CSRF_TOKEN": YII_CSRF_TOKEN,
                    "template_data": template_data,
                },
                'success': function (response) {
                    if(!response.success) {
                        let error_string = "Unable to save changes. Please fix the following errors:\n<ul>";

                        response.errors.forEach(function(template, template_index) {
                            Object.entries(template).forEach(function(attribute, attribute_name) {
                                error_string += `<li>${attribute[1]}</li>`;
                            });
                        });

                        error_string += "</ul>";

                        new OpenEyes.UI.Dialog.Alert({
                            content: error_string
                        }).open();
                    }
                }
            });

            disableEdit($parent_tr);
        });
    });
</script>
