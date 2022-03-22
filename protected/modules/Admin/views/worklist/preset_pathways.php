<?php
/**
 * @var $pathway_types PathwayType[]
 * @var $pgd_presets array
 * @var $path_steps PathwayStepType
 * @var $standard_steps PathwayStepType
 * @var $custom_steps PathwayStepType
 * @var $path_steps PathwayStepType
 * @var $workflows OphCiExamination_ElementSet[]
 * @var $letter_macros array
 */

use OEModule\OphCiExamination\models\OphCiExamination_ElementSet;
?>
<div class="admin box" id="js-clinic-manager">
    <?php $this->renderPartial(
        '//worklist/pathway_step_picker',
        array(
            'path_steps' => $path_steps,
            'standard_steps' => $standard_steps,
            'custom_steps' => $custom_steps,
            'show_pathway_selected' => true,
            'show_undo_step' => true,
        )
    ); ?>
    <form action="/Admin/worklist/deactivatePathwayPresets" method="POST">
        <table class="oec-patients">
            <thead>
            <tr>
                <th><input name="select-pathway" class="js-select-pathway" value="all" type="checkbox"/></th>
                <th>Name</th>
                <th>Steps</th>
                <th>
                    <label class="patient-checkbox">
                        <input class="js-check-pathway" value="all" type="checkbox"/>
                        <div class="checkbox-btn"></div>
                    </label>
                </th>
                <th>Active</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($pathway_types as $i => $pathway_type) { ?>
                <tr data-pathway-type-id="<?= $pathway_type->id ?>">
                    <td><input name="pathway[]" value="<?= $pathway_type->id ?>" type="checkbox"/></td>
                    <td>
                        <a href="/Admin/worklist/editPathwayPreset/<?= $pathway_type->id ?>"><?= $pathway_type->name ?></a>
                    </td>
                    <td class="js-pathway-container">
                        <?php
                        $this->renderPartial(
                            '/worklist/_clinical_pathway_admin',
                            array('pathway_type' => $pathway_type)
                        )
                        ?>
                    </td>
                    <td>
                        <label class="patient-checkbox">
                            <input class="js-check-pathway" value="<?= $pathway_type->id ?>" type="checkbox"/>
                            <div class="checkbox-btn"></div>
                        </label>
                    </td>
                    <td>
                        <?= $pathway_type->active ? '<i class="oe-i tick small"/>' : null ?>
                    </td>
                    <td>
                        <!-- Duplicate action -->
                        <i class="oe-i duplicate js-duplicate-pathway-type js-has-tooltip" data-tt-type="basic" data-tooltip-content="Duplicate pathway"></i>
                    </td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
        <input type="hidden" name="YII_CSRF_TOKEN" value="<?= Yii::app()->request->csrfToken ?>"/>
        <button formaction="/Admin/worklist/addPathwayPreset" type="submit" class="button green">Add Pathway Preset</button>
        <button type="submit" class="button red">Deactivate Selected Presets</button>
    </form>
</div>
<script id="js-step-template" type="text/template">
    <span class="oe-pathstep-btn {{status}} {{type}}" data-pathstep-id="{{id}}" data-pathstep-type-id="{{type_id}}" data-patient-id="{{patient_id}}">
        <span class="step{{#icon}} {{icon}}{{/icon}}">{{^icon}}{{short_name}}{{/icon}}</span>
        <span class="info" style="{{^display_info}}display: none;{{/display_info}}">{{#display_info}}{{display_info}}{{/display_info}}</span>
    </span>
</script>
<script type="text/javascript">
    $(document).ready(function() {
        const path_step_type_ids = <?= $path_step_type_ids ?>;
        const picker_setup = <?= $picker_setup ?>;
        let picker = new OpenEyes.UI.PathwayStepPicker({
            ...path_step_type_ids,
            ...picker_setup,
            pathway_checkboxes: '.js-check-pathway',
            base_url: '/Admin/',
        });
        picker.init();

        $('.js-select-pathway').change(function() {
            if ($(this).val() === 'all') {
                if ($(this).is(':checked')) {
                    $('.js-select-pathway').prop('checked', true);
                } else {
                    $('.js-select-pathway').prop('checked', false);
                }
            }
        });

        $(document).on('click', '.js-duplicate-pathway-type', function() {
            window.location = '/Admin/worklist/duplicatePathwayPreset/' + $(this).closest('tr').data('pathway-type-id');
        })
    });
</script>