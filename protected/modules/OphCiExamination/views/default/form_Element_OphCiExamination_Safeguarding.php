<?php
    $model_name = \CHtml::modelName($element);
    $concerns = \OEModule\OphCiExamination\models\OphCiExamination_Safeguarding_Concern::model()->findAll();
    $editable = !isset($element->outcome_id);
?>
<div id="<?= $model_name . '_element'?>" class="element-fields full-width">
    <div class="cols-11">
        <?php if ($editable) { ?>
            <label class="highlight inline">
                <?= $form->checkBox($element, 'no_concerns', array('nowrapper' => true, 'no-label' => true)); ?>
                No safeguarding concerns
            </label>
            <hr id="safeguarding-divider" class="divider">
        <?php } else { ?>
            <?= $form->hiddenInput($element, 'no_concerns', $element->no_concerns); ?>
        <?php } ?>

        <table id="safeguarding-entries-table" class="cols-full">
            <colgroup><col class="cols-4"></colgroup>
            <tbody>
                <?php
                    $entries = \OEModule\OphCiExamination\models\OphCiExamination_Safeguarding_Entry::model()->findAllByAttributes(array('element_id' => $element->id));

                    $row_count = 0;

                foreach ($entries as $entry) {
                    $this->renderPartial('form_Element_OphCiExamination_Safeguarding_Entry', array('element' => $element, 'entry' => $entry, 'row_count' => $row_count++, 'editable' => $editable));
                }
                ?>
            </tbody>
        </table>
    </div>
    <?php if ($editable) { ?>
        <div class="add-data-actions flex-item-bottom ">
            <button id="safeguarding-adder-button" type="button" class="adder js-add-select-btn"></button>
        </div>
    <?php } ?>
    <input id="safeguarding-ids-to-delete" type="hidden" name="safeguarding_ids_to_delete" value="[]">
</div>

<?php if ($editable) { ?>
    <script type="x-tmpl-mustache" id="add-new-safeguarding-row-template">
        <?php
            $template_model_name = CHtml::modelName(\OEModule\OphCiExamination\models\Element_OphCiExamination_Safeguarding::model())."[entries][{{row_count}}]";
            $template_comment_field_id = "safeguarding-entry-comment-field-{{row_count}}";
            $template_comment_container_id = "safeguarding-entry-comment-container-{{row_count}}";
            $template_comment_button_id = "safeguarding-entry-comment-button-{{row_count}}";
        ?>
        <tr>
                <?= CHtml::hiddenField($template_model_name . '[concern_id]', '{{concern_id}}') ?>
            <td>{{concern_term}}</td>
            <td>
                <div class="cols-full">
                    <button id="<?= $template_comment_button_id ?>" type="button" class="button js-add-comments" style="" data-comment-container="#<?= $template_comment_container_id?>">
                        <i class="oe-i comments small-icon"></i>
                    </button> <!-- comments wrapper -->
                    <div class="cols-full comment-container" style="display: block;">
                        <!-- comment-group, textarea + icon -->
                        <div id="<?= $template_comment_container_id ?>" class="flex-layout flex-left js-comment-container" style="display: none;" data-comment-button="#<?=$template_comment_button_id?>">
                            <?=
                                \CHtml::textArea(
                                    $template_model_name . '[comment]',
                                    '',
                                    array(
                                        'id' => $template_comment_field_id,
                                        'autocomplete' => 'off',
                                        'rows' => '1',
                                        'class' => 'js-comment-field cols-full'
                                    )
                                )
                            ?>
                            <i class="oe-i remove-circle small-icon pad-left js-remove-add-comments"></i>
                        </div>
                    </div>
                </div>
            </td>
            <td>
                <i id="safeguarding-row-trash-{{row_count}}" data-row="{{row_count}}" class="oe-i safeguarding-trash trash"></i>
            </td>
        </tr>
    </script>

    <script type="text/javascript">
        function trashSafeguardingEntryRow($row) {
            let $to_delete_input = $('input#safeguarding-ids-to-delete');
            let to_delete_array = JSON.parse($to_delete_input.val());

            let record_id = $row.children('input.safeguarding-entry-id').val();
            //Only add ID to remove if the record has one, otherwise don't
            if (record_id !== undefined){
                to_delete_array.push(record_id);
                $to_delete_input.val(JSON.stringify(to_delete_array));
            }
            $row.remove();
        }

        function attachSafeguardingTrashEvents($object){
            $object.click(function() {
                let $row = $(this).parent().parent();
                trashSafeguardingEntryRow($row);
            });
        }

        function updateNoConcerns($no_concerns_checkbox) {
            if ($no_concerns_checkbox.checked) {
                $("#safeguarding-entries-table").hide();
                $("#safeguarding-adder-button").hide();
                $("#safeguarding-divider").hide();
                $("#safeguarding-entries-table tr").each(function() {
                    trashSafeguardingEntryRow($(this));
                });
            } else {
                $("#safeguarding-entries-table").show();
                $("#safeguarding-adder-button").show();
                $("#safeguarding-divider").show();
            }
        }

        $(document).ready(function () {
            let $no_concerns_checkbox = $("#OEModule_OphCiExamination_models_Element_OphCiExamination_Safeguarding_no_concerns");
            $no_concerns_checkbox.change(function() {
                updateNoConcerns(this);
            });
            updateNoConcerns($no_concerns_checkbox[0]);

            attachSafeguardingTrashEvents($('i.safeguarding-trash'));

            new OpenEyes.UI.AdderDialog({
                openButton: $('#safeguarding-adder-button'),
                deselectOnReturn: true,
                // source: 'sidebar',
                parentContainer: 'body',
                itemSets:[
                    new OpenEyes.UI.AdderDialog.ItemSet(
                        <?= CJSON::encode(array_map(function ($item) {
    return ['label' => $item->term, 'id' => $item->id];
                        }, $concerns)) ?>,
                        {'header':'Safeguarding Concern', 'id':'concern_ids', 'multiSelect': true}
                    ),
                ],
                onReturn: function(adderDialog, selectedItems, selectedAdditions){
                    let $safeguarding_table_body = $('table#safeguarding-entries-table > tbody');

                    selectedItems.forEach(function(selectedItem) {
                        let row_count = $safeguarding_table_body.children('tr').length;
                        $safeguarding_table_body.append(function() {
                            let data = {
                                'concern_id': selectedItem.id,
                                'concern_term': selectedItem.label,
                                'row_count': row_count,
                            };
                            return Mustache.render($('#add-new-safeguarding-row-template').text(), data);
                        });

                        let $last_safeguarding_row = $safeguarding_table_body.children('tr').last();
                        attachSafeguardingTrashEvents($last_safeguarding_row.find('i.safeguarding-trash'));
                    });
                    return true;
                }
            })
        });
    </script>
<?php } ?>