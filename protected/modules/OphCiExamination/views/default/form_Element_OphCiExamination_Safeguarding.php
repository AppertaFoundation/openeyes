<?php

use OEModule\OphCiExamination\models\OphCiExamination_Safeguarding_Concern;
use OEModule\OphCiExamination\models\OphCiExamination_Safeguarding_Entry;

$model_name = \CHtml::modelName($element);
$concerns = OphCiExamination_Safeguarding_Concern::model()->findAll();
$editable = !isset($element->outcome_id);

if (isset($element->event)) {
    $patient = $element->event->getPatient();
} else {
    $patient = Patient::model()->findByPk(\Yii::app()->request->getQuery('patient_id'));
}

$patient_is_minor = $patient->isChild();
?>
<div id="<?= $model_name . '_element'?>" class="element-fields full-width">
    <?=CHtml::hiddenField('patient_is_minor', $patient_is_minor)?>
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
                $protection_plan_label = 'Child is on a child protection plan';
                $protection_plan_id = OphCiExamination_Safeguarding_Concern::model()->findByAttributes(array('term' => $protection_plan_label))->id;
                $social_worker_label = 'Child has a social worker';
                $social_worker_id = OphCiExamination_Safeguarding_Concern::model()->findByAttributes(array('term' => $social_worker_label))->id;

                echo CHtml::hiddenField(
                    'child_safeguarding_data',
                    json_encode(
                        array(
                            'protection_plan' => array(
                                'id' => $protection_plan_id,
                                'label' => $protection_plan_label
                            ),
                            'social_worker' => array(
                                'id' => $social_worker_id,
                                'label' => $social_worker_label
                            ),
                        )
                    )
                );

                echo CHtml::hiddenField('clear_safeguarding_paediatric_fields', true);
                ?>
                <tr class="js-safeguarding-paediatric-row">
                    <td>Does the child have a social worker?</td>
                    <td>
                        <fieldset>
                            <label class="highlight inline">
                                <?= \CHTML::activeRadioButton($element, 'has_social_worker', array('value' => '1', 'nowrapper' => true, 'no-label' => true, 'uncheckValue' => null)); ?>
                                Yes, (add social worker as a contact)
                            </label>
                            <label class="highlight inline">
                                <?= \CHTML::activeRadioButton($element, 'has_social_worker', array('value' => '0', 'nowrapper' => true, 'no-label' => true, 'uncheckValue' => null)); ?>
                                No
                            </label>
                        </fieldset> 
                    </td>
                    <td>
                    </td>
                </tr>
                <tr class="js-safeguarding-paediatric-row">
                    <td>Is the child under a child protection plan?</td>
                    <td>
                        <fieldset>
                            <label class="highlight inline">
                                <?= \CHTML::activeRadioButton($element, 'under_protection_plan', array('value' => '1', 'nowrapper' => true, 'no-label' => true, 'uncheckValue' => null)); ?>
                                Yes
                            </label>
                            <label class="highlight inline">
                                <?= \CHTML::activeRadioButton($element, 'under_protection_plan', array('value' => '0', 'nowrapper' => true, 'no-label' => true, 'uncheckValue' => null)); ?>
                                No
                            </label>
                        </fieldset> 
                    </td>
                    <td>
                    </td>
                </tr>
                <tr class="js-safeguarding-paediatric-row">
                    <td>Who is accompanying the child and their relationship?</td>
                    <td>
                        <?= \CHTML::activeTextField($element, 'accompanying_person_name', array('class' => 'cols-full', 'placeholder' => 'Full name and relationship of person accompanying the child')); ?>
                    </td>
                    <td>
                    </td>
                </tr>
                <tr class="js-safeguarding-paediatric-row">
                    <td>Who has parental responsibility for the child?</td>
                    <td>
                        <?= \CHTML::activeTextField($element, 'responsible_parent_name', array('class' => 'cols-full', 'placeholder' => 'Full name of parent responsible for child')); ?>
                    </td>
                    <td>
                    </td>
                </tr>
                <tr class="js-safeguarding-paediatric-row"><td colspan="3"><hr class="divider"></td></tr>
                <?php

                $entries = array();

                if (isset($_POST[$model_name])) {
                    if(isset($_POST[$model_name]['entries'])) {
                        foreach ($_POST[$model_name]['entries'] as $post_entry) {
                            if (!empty($post_entry['id'])) {
                                $post_concern = OphCiExamination_Safeguarding_Entry::model()->findByPk($post_entry['id']);
                            } else {
                                $post_concern = new OphCiExamination_Safeguarding_Entry();
                            }
                            $post_concern->concern_id = $post_entry['concern_id'];
                            $post_concern->comment = $post_entry['comment'];

                            $entries[] = $post_concern;
                        }
                    }
                } else {
                    $entries = OphCiExamination_Safeguarding_Entry::model()->findAllByAttributes(array('element_id' => $element->id));
                }

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
</div>

<?php if ($editable) { ?>
    <script type="x-tmpl-mustache" id="add-new-safeguarding-row-template">
        <?php
            $template_model_name = CHtml::modelName(\OEModule\OphCiExamination\models\Element_OphCiExamination_Safeguarding::model()) . "[entries][{{row_count}}]";
            $template_comment_field_id = "safeguarding-entry-comment-field-{{row_count}}";
            $template_comment_container_id = "safeguarding-entry-comment-container-{{row_count}}";
            $template_comment_button_id = "safeguarding-entry-comment-button-{{row_count}}";
        ?>
        <tr class="js-entry-row">
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
            $row.remove();
        }

        function attachSafeguardingTrashEvents($object){
            $object.click(function() {
                let $row = $(this).parent().parent();
                trashSafeguardingEntryRow($row);
                refreshNoConcernsVisibility();
            });
        }

        function refreshNoConcernsVisibility() {
            if ($("#safeguarding-entries-table > tbody > tr.js-entry-row").length > 0) {
                $("#OEModule_OphCiExamination_models_Element_OphCiExamination_Safeguarding_no_concerns").parent().hide();
            }else{
                $("#OEModule_OphCiExamination_models_Element_OphCiExamination_Safeguarding_no_concerns").parent().show();
            }
        }

        function addSafeguardingRow(item) {
            let $safeguarding_table_body = $('table#safeguarding-entries-table > tbody');

            let row_count = $safeguarding_table_body.children('tr').length;
            $safeguarding_table_body.append(function() {
                let data = {
                    'concern_id': item.id,
                    'concern_term': item.label,
                    'row_count': row_count,
                };
                return Mustache.render($('#add-new-safeguarding-row-template').text(), data);
            });

            let $last_safeguarding_row = $safeguarding_table_body.children('tr').last();
            attachSafeguardingTrashEvents($last_safeguarding_row.find('i.safeguarding-trash'));
            refreshNoConcernsVisibility();
        }

        $(document).ready(function () {
            let treat_as_paediatric_radio = $('input[name="OEModule_OphCiExamination_models_Element_OphCiExamination_Triage[triage][treat_as_adult]"][value="0"]');

            let patient_is_minor = treat_as_paediatric_radio.length === 1 && treat_as_paediatric_radio.is(':checked') ? true : $('input#patient_is_minor').val() == 1;

            OphCiExamination_ToggleSafeguardingPaediatricFields(patient_is_minor);

            let child_safeguarding_json = $('input#child_safeguarding_data').val();
            let child_safeguarding_data = JSON.parse(child_safeguarding_json);
            let $protection_radio_button_yes = $('input#OEModule_OphCiExamination_models_Element_OphCiExamination_Safeguarding_under_protection_plan[value="1"]');
            let $protection_radio_button_no = $('input#OEModule_OphCiExamination_models_Element_OphCiExamination_Safeguarding_under_protection_plan[value="0"]');
            let $social_worker_radio_button_yes = $('input#OEModule_OphCiExamination_models_Element_OphCiExamination_Safeguarding_has_social_worker[value="1"]');
            let $social_worker_radio_button_no = $('input#OEModule_OphCiExamination_models_Element_OphCiExamination_Safeguarding_has_social_worker[value="0"]');

            $protection_radio_button_yes.change(function() {
                if($protection_radio_button_yes.prop("checked") && $(`tr > input[value=${child_safeguarding_data.protection_plan.id}]`).length === 0) {
                    $('input#OEModule_OphCiExamination_models_Element_OphCiExamination_Safeguarding_no_concerns').prop("checked", false);
                    addSafeguardingRow(child_safeguarding_data.protection_plan);
                }
            });
            $protection_radio_button_no.change(function() {
                if($protection_radio_button_no.prop("checked") && $(`tr > input[value=${child_safeguarding_data.protection_plan.id}]`).length !== 0) {
                    $(`tr > input[value=${child_safeguarding_data.protection_plan.id}]`).parent().remove();
                    refreshNoConcernsVisibility();
                }
            });

            $social_worker_radio_button_yes.change(function() {
                if($social_worker_radio_button_yes.prop("checked") && $(`tr > input[value=${child_safeguarding_data.social_worker.id}]`).length === 0) {
                    $('input#OEModule_OphCiExamination_models_Element_OphCiExamination_Safeguarding_no_concerns').prop("checked", false);
                    addSafeguardingRow(child_safeguarding_data.social_worker);
                }
            });
            $social_worker_radio_button_no.change(function() {
                if($social_worker_radio_button_no.prop("checked") && $(`tr > input[value=${child_safeguarding_data.social_worker.id}]`).length !== 0) {
                    $(`tr > input[value=${child_safeguarding_data.social_worker.id}]`).parent().remove();
                    refreshNoConcernsVisibility();
                }
            });

            refreshNoConcernsVisibility();

            attachSafeguardingTrashEvents($('i.safeguarding-trash'));

            new OpenEyes.UI.AdderDialog({
                openButton: $('#safeguarding-adder-button'),
                deselectOnReturn: true,
                parentContainer: 'body',
                itemSets:[
                    new OpenEyes.UI.AdderDialog.ItemSet(
                        <?= json_encode(array_map(function ($item) {
                            return ['label' => $item->term, 'id' => $item->id];
                        }, $concerns)) ?>,
                        {'header':'Safeguarding Concern', 'id':'concern_ids', 'multiSelect': true}
                    ),
                ],
                onReturn: function(adderDialog, selectedItems, selectedAdditions){
                    selectedItems.forEach(function(selectedItem) {
                        addSafeguardingRow(selectedItem);
                    });
                    return true;
                }
            })
        });
    </script>
<?php } ?>
