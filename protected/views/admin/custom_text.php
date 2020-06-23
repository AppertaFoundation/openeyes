<?php
/**
 * @var $model_list ElementType[]|EventType[]
 * @var $errors array
 */
$model_name = str_replace('Type', '', get_class($model_list[0]));
$event_name = null;
?>
<h3>Event custom text</h3>
<?php echo $this->renderPartial('_form_errors', array('errors' => $errors)) ?>

<?= CHtml::beginForm(array('id' => 'custom-text-form')) ?>
<?= CHtml::submitButton('Save', array('class' => 'button small green'))?>
<table class="standard">
    <thead>
    <tr>
        <?php if ($model_name === 'Element') {?>
        <th>Event</th>
        <?php } ?>
        <th><?= $model_name?> Name</th>
        <th>Custom text block</th>
        <?php if ($model_name === 'Event') {?>
            <th>Display Position</th>
        <?php } ?>
    </tr>
    </thead>
    <tbody>
        <?php foreach ($model_list as $id => $model) { ?>
            <tr>
                <?php if ($model_name === 'Element') {
                    if ($event_name !== $model->eventType->name) {
                        $event_name = $model->eventType->name; ?>
                        <td><?= $model->eventType->name ?></td>
                    <?php } else {?>
                        <td></td>
                    <?php }?>

                <?php } ?>
                <td>
                    <?= $model->name ?>
                    <?= CHtml::activeHiddenField($model, "[$id]id") ?>
                </td>
                <td>
                    <?= CHtml::activeTextArea($model, "[$id]custom_hint_text", array('class' => 'custom-text-field')) ?>
                </td>
                <?php if ($model_name === 'Event') { ?>
                <td>
                    <?= CHtml::activeRadioButtonList(
                        $model,
                        "[$id]hint_position",
                        array(
                                'TOP' => 'Top',
                                'BOTTOM' => 'Bottom'
                            )
                    ) ?>
                </td>
                <?php } ?>
            </tr>
        <?php } ?>
    </tbody>
</table>
<?= CHtml::submitButton('Save', array('class' => 'button small green'))?>
<?= CHtml::endForm() ?>

<script type="text/javascript">
    $(document).ready(function() {
        const tinymce_options =
            {
                selector: '.custom-text-field',
                setup: function (editor) {
                    /*editor.on('keydown', function (e) {
                        if (e.keyCode === 9) {
                            editor.execCommand('mceInsertContent', false, '&emsp;');
                            e.preventDefault();
                            e.stopPropagation();
                            return false;
                        }
                    });*/

                    /*
                    Sets up a common letter stucture using a table.
                    Layout can NOT be achieved using TABs.
                    note: style="width:100%" sets Tiny to use %!
                    */
                    editor.addButton('labelitem', {
                        text: 'Label - Item',
                        icon: false,
                        tooltip: "Use TAB to create new rows",
                        onpostrender: monitorNodeChange,
                        onclick: function () {
                            editor.insertContent('<table class="label-item" style="width:100%"><tbody><tr><th>Label</th><td>(use tab to add extra rows)</td></tr></tbody></table>');
                        }
                    });

                    /*
                    Set up some table defaults.
                    Not using table matrix button so that i can control the DOM
                    note: style="width:100%" sets Tiny to use %!
                    */
                    editor.addButton('datatable', {
                        text: 'Table',
                        icon: false,
                        tooltip: "Insert a table",
                        onpostrender: monitorNodeChange,
                        onclick: function () {
                            editor.insertContent('<table class="borders" style="width:100%"><tbody><tr><td></td><td></td><td></td></tr></tbody></table>');
                        }
                    });

                    /*
                    Disable custom table creation within tables.
                    Limited use. User can create a <p> inside <td> by Enter.
                    Then they can add another table
                    */
                    function monitorNodeChange() {
                        editor.on('NodeChange', (e) => {
                            const nodeName = e.element.nodeName.toLowerCase();
                            this.disabled(nodeName === 'td' || nodeName === 'th');
                        });
                    }
                },
                plugins: 'lists'
            };
        tinymce.init(tinymce_options);
    });
</script>
