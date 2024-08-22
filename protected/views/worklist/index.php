<?php
/**
 * @var Worklist[] $worklists
 * @var array $workflows
 * @var array $letter_macros
 * @var PathwayStep[] $path_steps
 * @var Pathway[] $pathways
 * @var PathwayStep[] $standard_steps
 * @var PathwayStep[] $custom_steps
 * @var string $sync_interval_value
 * @var string[] $sync_interval_options
 * @var string $preset_orders
 * @var string $sync_interval_setting_key
 */

$coreapi = new CoreAPI();
$pathway_json = json_encode(
    array_map(
        static function ($item) {
            return array('id' => $item->id, 'name' => 'pathway', 'label' => $item->name);
        },
        $pathways
    ),
    JSON_THROW_ON_ERROR
);


$session_filter_info = WorklistFilterQuery::getLastUsedFilterFromSession();
$initial_filter = $session_filter_info['filter'];
?>
<script src="<?= Yii::app()->assetManager->createUrl('../../node_modules/object-hash/dist/object_hash.js')?>"></script>
<input type="hidden" id="wl_print_selected_worklist" value="" />

<div class="oe-full-header">
    <div class="sync-data" id="js-sync-data">
        <div class="sync-btn <?=$sync_interval_value === 'off' ? '' : 'on'?>" id="js-sync-btn">
            <div class="last-sync"><?=date('H:i')?></div>
            <div class="sync-interval"><?=$sync_interval_value === 'off' ? 'Sync OFF' : $sync_interval_options[$sync_interval_value]?></div>
        </div>
        <div class="sync-options" id="js-sync-options" style="display:none;">
            <ul>
                <?php foreach ($sync_interval_options as $key => $option) {?>
                    <li>
                        <button data-value="<?=$key === 'off' ? 'Sync OFF' : $option?>" data-value-key="<?=$key?>" class="header-tab">
                            <?=($key === 'off' ? '' : 'Sync: ') . $option?>
                        </button>
                    </li>
                <?php }?>
            </ul>
        </div>
        <div class="title wordcaps"></div>
    </div>
    <nav class="options left">
        <div id="js-clinic-filters" class="clinic-filters">
            <button class="popup-filter table-sort" name="table-sort">Time</button>
            <input class="search" type="text" placeholder="Name filter"/>
            <ul class="quick-filters">
                <li class="filter-btn js-clinic-btn-filter selected" data-filter="all">
                    <div class="name">All</div>
                    <div class="count"></div>
                </li>
                <li class="filter-btn js-clinic-btn-filter" data-filter="clinic" data-test="clinic-filter">
                    <div class="name">Arrived</div>
                    <div class="count" data-test="clinic-filter-count"></div>
                </li>
                <li class="filter-btn js-clinic-btn-filter" data-filter="issues">
                    <div class="name">Issues</div>
                    <div class="count"></div>
                </li>
                <li class="filter-btn js-clinic-btn-filter" data-filter="discharged">
                    <div class="name">Checked-out</div>
                    <div class="count"></div>
                </li>
                <li class="filter-btn js-clinic-btn-filter" data-filter="done">
                    <div class="name">Completed</div>
                    <div class="count"></div>
                </li>
            </ul>
            <div class="changeable-filter js-clinic-filter-menu" data-filter="waitingFor">
                <div class="filter-btn">
                    <div class="name">Waiting for...</div>
                    <div class="count"></div>
                </div>
                <nav class="options" style="display: none;">
                    <!-- List of standard and custom step types here? -->
                </nav>
            </div>
            <div class="changeable-filter js-clinic-filter-menu" data-filter="assignedTo">
                <div class="filter-btn">
                    <div class="name">
                        <i class="oe-i person no-click medium"></i>
                    </div>
                    <div class="count"></div>
                </div>
                <nav class="options" style="display: none;">
                    <!-- List of assignees here -->
                </nav>
            </div>
        </div>
    </nav>
    <div class="options-right">
        <button class="button header-tab icon" onclick="goPrint();" name="print" type="button" id="et_print"><i class="oe-i print"></i></button>
    </div>
</div>

<div id="js-worklist-save-filter-popup" class="oe-popup-wrap" style="display: none; z-index: 100">
    <div class="oe-popup ">
        <div class="close-icon-btn" data-test="worklist-filter-popup-close-icon-btn"><i class="oe-i remove-circle pro-theme js-close-popup-btn"></i></div>
        <div class="title">Save list view in favourites for quick access</div>
        <div class="oe-popup-content wide">
            <div class="flex-t">
                <div class="cols-7">
                    <table class="cols-full">
                        <tbody>
                            <tr>
                                <th>Site</th>
                                <td class="js-filter-site" data-test="worklist-save-filter-site"></td>
                            </tr>
                            <tr>
                                <th>Context</th>
                                <td class="js-filter-context"></td>
                            </tr>
                            <tr>
                                <th>Date</th>
                                <td class="js-filter-dates"></td>
                            </tr>
                            <tr>
                                <th>Filter</th>
                                <td class="js-filter-optional"></td>
                            </tr>
                            <tr>
                                <th>Lists</th>
                                <td class="js-filter-lists"></td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="cols-4">
                    <h3>Save favourite as...</h3>
                    <input type="text" maxlength="128" class="cols-full js-filter-save-name" placeholder="Add favourite name" data-test="input-favourite-name">

                    <div class="small-row">
                        <small>
                            This is the name that will be shown, only you will see this favourite.
                            Favourites can be removed or added any time.<br/><br/>
                            <strong>Note: Entering the name of an existing favourite will update that favourite with these new settings.</strong>
                        </small>
                    </div

                    <div class="row align-right">
                        <button class="green hint cols-6 js-save-filter-btn" data-test="save-favourite-filter">
                            Save in my favourites
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="js-worklist-sort-order-popup" class="oe-popup-wrap" style="display: none">
    <div class="oe-popup">
        <div class="close-icon-btn js-close-popup-btn">
            <i class="oe-i remove-circle pro-theme"></i>
        </div>

        <div class="title">Sort order</div>

        <div class="oe-popup-content">
            <div class="block-layout">
                <div class="block">
                    <fieldset class="btn-list">
                        <label>
                            <input type="radio" name="sort-btn" value="0" checked="">
                            <div class="li">Time</div>
                        </label>
                        <label>
                            <input type="radio" name="sort-btn" value="1">
                            <div class="li">Clinic (A-Z)</div>
                        </label>
                        <label>
                            <input type="radio" name="sort-btn" value="2">
                            <div class="li">Clinic (Z-A)</div>
                        </label>
                        <label>
                            <input type="radio" name="sort-btn" value="3">
                            <div class="li">Wait (longest)</div>
                        </label>
                        <label>
                            <input type="radio" name="sort-btn" value="4">
                            <div class="li">Wait (shortest)</div>
                        </label>
                        <label>
                            <input type="radio" name="sort-btn" value="5">
                            <div class="li">Priority</div>
                        </label>
                        <label>
                            <input type="radio" name="sort-btn" value="6">
                            <div class="li">Duration</div>
                        </label>
                    </fieldset>
                </div>
            </div>

        </div>
        <!-- actions outside scrolling area, same structure -->
        <div class="popup-actions flex-right">
            <button class="blue hint js-set-sort-by-btn">
                Reload &amp; show sorted patient pathways
            </button>
            <button class="red hint js-close-popup-btn">Cancel</button>
        </div>
    </div>
</div>

<main class="oe-full-content oe-clinic" id="js-clinic-manager">
    <?php
    $this->renderPartial(
        '//worklist/pathway_step_picker',
        array(
            'path_steps' => $path_steps,
            'pathways' => $pathways,
            'standard_steps' => $standard_steps,
            'custom_steps' => $custom_steps,
            'show_pathway_selected' => true,
            'show_undo_step' => true,
        )
    );
    if ($initial_filter->getCombineWorklistsStatus()) {
        $this->renderPartial('//worklist/_worklist', array('worklist' => $worklists, 'coreapi' => $coreapi, 'filter' => $initial_filter));
    } else {
        foreach ($worklists as $worklist) {
            $this->renderPartial('//worklist/_worklist', array('worklist' => $worklist, 'coreapi' => $coreapi, 'filter' => $initial_filter));
        }
    }
    ?>
    <i class="js-pathway-spinner spinner"></i>
</main>
<div class="oe-patient-quick-overview" style="display: none;" data-mode="full-to-right">
    <div class="close-icon-btn" style="display: none;" onclick="closePatientPop();">
        <i class="oe-i remove-circle medium"></i>
    </div>
</div>
<div class="js-patient-popup-data">
</div>
<input type="hidden" name="selected-complete-option" id="selected-complete-option" value="none"/>
<script id="js-step-template" type="text/template">
    <span class="oe-pathstep-btn {{status}} {{type}}" data-pathstep-id="{{id}}" data-pathstep-type-id="{{type_id}}" data-patient-id="{{patient_id}}">
        <span class="step{{#icon}} {{icon}}{{/icon}}">{{^icon}}{{short_name}}{{/icon}}</span>
        <span class="info" style="{{^display_info}}display: none;{{/display_info}}">{{#display_info}}{{display_info}}{{/display_info}}</span>
    </span>
</script>
<?php
    $assetManager = Yii::app()->getAssetManager();
    Yii::app()->clientScript->registerScriptFile(Yii::app()->assetManager->getPublishedPathOfAlias('application.widgets.js') . '/PatientPanelPopupMulti.js');
?>
<script id="oe-patient-quick-overview-template" type="text/html">
    <div class="oe-patient-meta">
        <div class="patient-name">
            <a href="{{ href }}">
                <span class="patient-surname">{{ lastname }}</span>,
                <span class="patient-firstname">
                    {{ firstname }} ({{ title }})
                </span>
            </a>
        </div>

        <div class="patient-details">
            {{#displayPrimaryNumberUsageCode}}
                <div class="hospital-number">
                    <span>{{ hospitalNumberPrompt }}</span> {{ hospitalNumberValue }}
                </div>
            {{/displayPrimaryNumberUsageCode}}
            {{#displaySecondaryNumberUsageCode}}
                <div class="nhs-number">
                    <span>{{ nhsNumberPrompt }}</span>
                    {{ nhsNumberValue }}
                    {{#patientSecondaryIdentifierStatus}}
                        <i class="oe-i {{ patientSecondaryIdentifierStatusClassName }}"></i>
                    {{/patientSecondaryIdentifierStatus}}
                </div>
            {{/displaySecondaryNumberUsageCode}}
            <div class="patient-gender">
                <em>Sex</em>
                {{ gender }}
            </div>
            <div class="patient-{{#deceased}}died{{/deceased}}{{^deceased}}age{{/deceased}}">
                {{#deceased}}
                    <em>Died</em> {{dateOfDeath}}
                {{/deceased}}
                <em>Age{{#deceased}}d{{/deceased}}</em> {{patientAge}}y
            </div>
        </div>
    </div>

    <div class="quick-overview-content">
        {{#patientIdentifiers.length}}
            {{#patientIdentifiers}}
                {{#patientIdentifierStatus}}
                    <div class="alert-box {{#iconBannerClassName}}{{iconBannerClassName}}{{/iconBannerClassName}}{{^iconBannerClassName}}issue{{/iconBannerClassName}}">
                        <i class="oe-i exclamation pad-right no-click medium-icon"></i>
                        <b>
                            {{shortTitle}}:
                            {{description}}
                        </b>
                    </div>
                {{/patientIdentifierStatus}}
            {{/patientIdentifiers}}
        {{/patientIdentifiers.length}}

        {{#patientIdentifiers.length}}
            <div class="patient-numbers flex-layout">
                <div class="local-numbers">
                    {{#patientLocalIdentifiers}}
                        {{#hasValue}}
                            <div class="num nowrap">
                                {{shortTitle}}
                                <label class="inline highlight">
                                    {{displayValue}}
                                </label>
                            </div>
                        {{/hasValue}}
                    {{/patientLocalIdentifiers}}
                </div>
                {{#patientGlobalIdentifier}}
                    <div class="nhs-number">
                        <span>{{patientGlobalIdentifierPrompt}}</span>
                        {{patientGlobalIdentifierLabel}}
                    </div>
                 {{/patientGlobalIdentifier}}
            </div>
        {{/patientIdentifiers.length}}

        <!-- Warnings: Allergies -->
        <div class="data-group">
            {{#patientAllergies.hasAllergyStatus}}
                <div class="alert-box info">
                    <strong>Allergies</strong> - status unknown.
                </div>
            {{/patientAllergies.hasAllergyStatus}}
            {{#patientAllergies.noAllergiesDate}}
                <div class="alert-box success">
                    <strong>Allergies</strong> - none known.
                </div>
            {{/patientAllergies.noAllergiesDate}}

            {{#patientAllergies.data}}
                <div class="alert-box patient">
                    <strong>Allergies</strong>
                </div>
                <div class="popup-overflow">
                    <table class="risks">
                        <colgroup>
                            <col class="cols-6">
                        </colgroup>
                        <tbody>
                            {{#patientAllergies.entries}}
                                <tr>
                                    <td>
                                        <i class="oe-i warning small pad-right"></i>
                                        {{displayAllergy}}
                                        <span class="fade">{{reactionString}}</span>
                                    </td>
                                    <td>
                                        {{#comments}}
                                            <i class="oe-i comments-who small pad-right js-has-tooltip"
                                               data-tooltip-content="<small>User comment by </small><br/>{{lastModifiedUser}}"></i><span class="user-comment">
                                            {{comments}}</span>
                                        {{/comments}}
                                    </td>
                                    <td></td>
                                </tr>
                            {{/patientAllergies.entries}}
                        </tbody>
                    </table>
                </div>
            {{/patientAllergies.data}}
        </div>

        <!-- Warnings: Risks -->
        <div class="data-group">
            {{#patientRisks.riskAlertInfo}}
                <div class="alert-box info">
                    <strong>Alerts</strong> - none known.
                </div>
            {{/patientRisks.riskAlertInfo}}
            {{#patientRisks.noRisksDate}}
                <div class="alert-box success">
                    <strong>Alerts</strong> - none known.
                </div>
            {{/patientRisks.noRisksDate}}

            {{#patientRisks.entries.length}}
                <div class="alert-box patient">
                    <strong>Alerts</strong>
                </div>
                <div class="popup-overflow">
                    <table class="risks">
                        <colgroup>
                            <col class="cols-6">
                        </colgroup>
                        <tbody>
                            {{#patientRisks.entries}}
                                <tr>
                                    <td>
                                        <i class="oe-i warning small pad-right"></i>
                                        {{displayRisk}}
                                    </td>
                                    <td>
                                        {{comments}}
                                    </td>
                                    <td></td>
                                </tr>
                            {{/patientRisks.entries}}
                            {{#patientRisks.disorders}}
                                <tr>
                                    <td>
                                        <i class="oe-i warning small pad-right"></i>
                                        {{disorderTerm}}
                                    </td>
                                    <td>(Active Diagnosis) </td>
                                    <td></td>
                                </tr>
                            {{/patientRisks.disorders}}
                        </tbody>
                    </table>
                </div>
            {{/patientRisks.entries.length}}
        </div>

        <!-- Patient Quicklook popup. Show Risks, Medical Data, Management Summary and Problem and Plans -->
        <div class="situational-awareness">
            {{#vaData}}
                <div class="group">
                    {{#has_beo}}
                        <span class="data">BEO {{beo_result}}</span>
                        <span class="data">{{beo_method_abbr}}</span>
                    {{/has_beo}}
                    <span class="data">R {{#has_right}}{{right_result}}{{/has_right}}{{^has_right}}NA{{/has_right}}</span>
                    {{#has_right}}
                        <span class="data">{{right_method_abbr}}</span>
                    {{/has_right}}
                    <span class="data">L {{#has_left}}{{left_result}}{{/has_left}}{{^has_left}}NA{{/has_left}}</span>
                    {{#has_left}}
                        <span class="data">{{left_method_abbr}}</span>
                    {{/has_left}}
                    <span class="oe-date" style="text-align: left;">
                      {{event_date}}
                    </span>
                </div>
            {{/vaData}}
            {{^vaData}}
                <div class="group">
                    <span class="data-value not-available">VA: NA</span>
                </div>
            {{/vaData}}

            <div class="group">
                {{#refractionData}}
                    <span class="data">R {{#has_right}}{{right}}{{/has_right}}{{^has_right}}NA{{/has_right}}</span>
                    <span class="data">L {{#has_left}}{{left}}{{/has_left}}{{^has_left}}NA{{/has_left}}</span>
                    <span class="oe-date" style="text-align: left">{{event_date}}</span>
                {{/refractionData}}
                {{^refractionData}}
                    <span class="data">Refraction: NA</span>
                {{/refractionData}}
            </div>

            <div class="group">
                {{#cct}}
                    <span class="data">R {{#has_right}}{{right}}{{/has_right}}{{^has_right}}NA{{/has_right}} </span>
                    <span class="data">L {{#has_left}}{{left}}{{/has_left}}{{^has_left}}NA{{/has_left}} </span>
                    <span class="oe-date" style="text-align: left">{{event_date}}</span>
                {{/cct}}
                {{^cct}}
                    <span class="data">CCT: NA</span>
                {{/cct}}
            </div>

            <div class="group">
                {{#cvi}}
                    <span class="data">CVI Status: {{data}}</span>
                    <span class="oe-date"> {{date}}</span>
                {{/cvi}}
                {{^cvi}}
                    <span class="data">CVI Status: NA</span>
                {{/cvi}}
            </div>
        </div>
        <div class="data-group">
            <div class="quicklook-data-groups">
                <div class="group">
                    <div class="label">Eye Diagnoses</div>
                    <div class="data">
                        <table>
                            <tbody>
                                {{#ophthalmicDiagnoses.length}}
                                    {{#ophthalmicDiagnoses}}
                                        <tr>
                                            <td>{{name}}</td>
                                            <td>
                                                <span class="oe-eye-lat-icons">
                                                    <i class="oe-i laterality {{right}}"></i>
                                                    <i class="oe-i laterality {{left}}"></i>
                                                </span>
                                            </td>
                                            <td>
                                                <span class="oe-date">{{date}}</span>
                                            </td>
                                        </tr>
                                    {{/ophthalmicDiagnoses}}
                                {{/ophthalmicDiagnoses.length}}
                                {{^ophthalmicDiagnoses.length}}
                                    <tr>
                                        <td>
                                            <div class="nil-recorded">Nil recorded</div>
                                         </td>
                                    </tr>
                                {{/ophthalmicDiagnoses.length}}
                             </tbody>
                        </table>
                    </div>
                </div>

                <div class="group">
                    <div class="label">Systemic Diagnoses</div>
                    <div class="data">
                        <table>
                            <tbody>
                                {{#systemicDiagnoses.length}}
                                    {{#systemicDiagnoses}}
                                        <tr>
                                            <td>{{term}}</td>
                                            <td>
                                                <span class="oe-eye-lat-icons">
                                                    <i class="oe-i laterality {{right}}"></i>
                                                    <i class="oe-i laterality {{left}}"></i>
                                                </span>
                                            </td>
                                            <td>
                                                <span class="oe-date">{{date}}</span>
                                            </td>
                                        </tr>
                                    {{/systemicDiagnoses}}
                                {{/systemicDiagnoses.length}}
                                {{^systemicDiagnoses.length}}
                                    <tr>
                                        <td>
                                            <div class="nil-recorded">Nil recorded</div>
                                         </td>
                                    </tr>
                                {{/systemicDiagnoses.length}}
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="group">
                    <div class="label">Surgical History</div>
                    <div class="data">
                        <table>
                            <colgroup>
                                <col class="cols-8"><col>
                            </colgroup>
                            <tbody>
                                {{#pastSurgery.nilRecord}}
                                    <div class="nil-recorded">Nil recorded</div>
                                {{/pastSurgery.nilRecord}}
                                {{#pastSurgery.noPreviousData}}
                                    <div class="nil-recorded">Patient has had no previous eye surgery or laser treatment</div>
                                {{/pastSurgery.noPreviousData}}
                                {{#pastSurgery.operation.length}}
                                    {{#pastSurgery.operation}}
                                        <tr>
                                            <td>{{operation}}</td>
                                            <td></td>
                                        <td class="nowrap">
                                        <span class="oe-eye-lat-icons">
                                            <i class="oe-i laterality {{right}}"></i>
                                            <i class="oe-i laterality {{left}}"></i>
                                        </span>
                                          <span class="oe-date">
                                             {{date}}
                                          </span>
                                      </td>
                                        <td>
                                            {{#has_link}}
                                                <a href="{{link}}"><i class="oe-i direction-right-circle pro_theme small pad"></i></a>
                                            {{/has_link}}
                                        </td>
                                    </tr>
                                    {{/pastSurgery.operation}}
                                {{/pastSurgery.operation.length}}
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="group" name="group-systemic-medications">
                    <div class="label">Systemic Medications</div>
                    <div class="data">
                        {{#systemicMedications.nilRecord}}
                            <div class="nil-recorded">Nil recorded</div>
                        {{/systemicMedications.nilRecord}}
                        {{^systemicMedications.nilRecord}}
                            {{#systemicMedications.noPreviousData}}
                                <div class="nil-recorded">Patient is not taking any systemic medications</div>
                            {{/systemicMedications.noPreviousData}}
                            {{^systemicMedications.noPreviousData}}
                                {{#systemicMedications.currentSystemicMeds.length}}
                                    <table id="{{historyMedications.id}}_systemic_current_entry_table">
                                        <colgroup>
                                            <col class="cols-8">
                                            <col>
                                        </colgroup>
                                        <thead style="display:none;">
                                             <!-- These hidden headers are required for Katalon / automated tests to find correct columns -->
                                            <tr>
                                                <th>Drug</th>
                                                <th>Tooltip</th>
                                                <th>Date</th>
                                                <th>Link</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            {{#systemicMedications.currentSystemicMeds}}
                                                <tr data-key="{{index}}">
                                                    <td>
                                                        {{display}}
                                                        {{#historyTooltipContent}}
                                                            <i class="oe-i change small pro_theme js-has-tooltip pad-right" data-tooltip-content="{{historyTooltipContent}}"></i>
                                                        {{/historyTooltipContent}}
                                                    </td>
                                                    <td>
                                                        {{#tooltipContent}}
                                                            <i class="oe-i {{icon}} small pro_theme js-has-tooltip pad-right" data-tooltip-content="{{tooltipContent}}">
                                                            </i>
                                                        {{/tooltipContent}}
                                                    </td>
                                                    <td>
                                                        <span class="oe-date">{{date}}</span>
                                                    </td>
                                                    <td>
                                                        <a href="{{link}}"><span class="js-has-tooltip fa oe-i direction-right-circle small pad pro_theme" data-tooltip-content="{{linkTooltipContent}}"></span></a>
                                                    </td>
                                                </tr>
                                            {{/systemicMedications.currentSystemicMeds}}
                                        </tbody>
                                    </table>
                                {{/systemicMedications.currentSystemicMeds.length}}
                                {{^systemicMedications.currentSystemicMeds.length}}
                                    <div class="data-value not-recorded">
                                        No current Systemic Medications
                                    </div>
                                {{/systemicMedications.currentSystemicMeds.length}}
                                {{#systemicMedications.stoppedSystemicMeds.length}}
                                    <div class="collapse-data">
                                        <div class="collapse-data-header-icon expand">
                                            Stopped
                                            <small>({{systemicMedications.stoppedSystemicMedsSize}})</small>
                                        </div>
                                        <div class="collapse-data-content">
                                            <table id="{{historyMedications.id}}_systemic_stopped_entry_table">
                                                <colgroup>
                                                    <col class="cols-8">
                                                    <col>
                                                </colgroup>
                                                <thead style="display:none;">
                                                     <!-- These hidden headers are required for Katalon / automated tests to find correct columns -->
                                                    <tr>
                                                        <th>Drug</th>
                                                        <th>Tooltip</th>
                                                        <th>Date</th>
                                                        <th>Link</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    {{#systemicMedications.stoppedSystemicMeds}}
                                                        <tr data-key="{{index}}">
                                                            <td>
                                                                {{display}}
                                                                {{#historyTooltipContent}}
                                                                    <i class="oe-i change small pro_theme js-has-tooltip pad-right" data-tooltip-content="{{historyTooltipContent}}"></i>
                                                                {{/historyTooltipContent}}
                                                            </td>
                                                            <td>
                                                                {{#tooltipContent}}
                                                                    <i class="oe-i {{icon}} small pro_theme js-has-tooltip pad-right" data-tooltip-content="{{tooltipContent}}">
                                                                    </i>
                                                                {{/tooltipContent}}
                                                            </td>
                                                            <td>
                                                                <span class="oe-date">{{date}}</span>
                                                            </td>
                                                            <td>
                                                                <i class="oe-i"></i>
                                                            </td>
                                                        </tr>
                                                    {{/systemicMedications.stoppedSystemicMeds}}
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                {{/systemicMedications.stoppedSystemicMeds.length}}
                            {{/systemicMedications.noPreviousData}}
                        {{/systemicMedications.nilRecord}}
                    </div>
                </div>

                <!-- oe-popup-overflow handles scrolling if data overflow height -->
                <div class="oe-popup-overflow quicklook-data-groups">
                    <div class="group" name="group-eye-medications">
                        <div class="label">Eye Medications</div>
                        <div class="data">
                            {{#eyeMedications.nilRecord}}
                                <div class="nil-recorded">Nil recorded</div>
                            {{/eyeMedications.nilRecord}}
                            {{^eyeMedications.nilRecord}}
                                {{#eyeMedications.noPreviousData}}
                                    <div class="nil-recorded">Patient is not taking any systemic medications</div>
                                {{/eyeMedications.noPreviousData}}
                                {{^eyeMedications.noPreviousData}}
                                    {{#eyeMedications.currentEyeMeds.length}}
                                        <table id="{{historyMedications.id}}_eye_current_entry_table">
                                            <colgroup>
                                                <col class="cols-8">
                                                <col>
                                            </colgroup>
                                            <thead style="display:none;">
                                                 <!-- These hidden headers are required for Katalon / automated tests to find correct columns -->
                                                <tr>
                                                    <th>Drug</th>
                                                    <th></th>
                                                    <th>Tooltip</th>
                                                    <th>Date</th>
                                                    <th>Link</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                {{#eyeMedications.currentEyeMeds}}
                                                    <tr data-key="{{index}}">
                                                        <td>
                                                            {{display}}
                                                            {{#comments}}
                                                                <i class="oe-i comments-who small pad js-has-tooltip" data-tt-type="basic" data-tooltip-content="<em>{{comments}}</em>"></i>
                                                            {{/comments}}
                                                            {{#historyTooltipContent}}
                                                                <i class="oe-i change small pro_theme js-has-tooltip pad-right" data-tooltip-content="{{historyTooltipContent}}"></i>
                                                            {{/historyTooltipContent}}
                                                        </td>
                                                        <td>
                                                            {{#tooltipContent}}
                                                                <i class="oe-i {{icon}} small pro_theme js-has-tooltip pad-right" data-tooltip-content="{{tooltipContent}}">
                                                                </i>
                                                            {{/tooltipContent}}
                                                        </td>
                                                        <td class="nowrap">
                                                            <span class="oe-eye-lat-icons">
                                                                <i class="oe-i laterality {{right}}"></i>
                                                                <i class="oe-i laterality {{left}}"></i>
                                                            </span>
                                                            <span class="oe-date">{{date}}</span>
                                                        </td>
                                                        <td>
                                                            <a href="{{link}}"><span class="js-has-tooltip fa oe-i direction-right-circle small pad pro_theme" data-tooltip-content="{{linkTooltipContent}}"></span></a>
                                                        </td>
                                                    </tr>
                                                {{/eyeMedications.currentEyeMeds}}
                                            </tbody>
                                        </table>
                                    {{/eyeMedications.currentEyeMeds.length}}
                                    {{^eyeMedications.currentEyeMeds.length}}
                                        <div class="data-value not-recorded">
                                            No current Eye Medications
                                        </div>
                                    {{/eyeMedications.currentEyeMeds.length}}
                                    {{#eyeMedications.stoppedEyeMeds.length}}
                                        <div class="collapse-data">
                                            <div class="collapse-data-header-icon expand">
                                                Stopped
                                                <small>({{eyeMedications.stoppedEyeMedsSize}})</small>
                                            </div>
                                            <div class="collapse-data-content">
                                                <table id="{{historyMedications.id}}_eye_stopped_entry_table">
                                                    <colgroup>
                                                        <col class="cols-8">
                                                        <col>
                                                    </colgroup>
                                                    <thead style="display:none;">
                                                         <!-- These hidden headers are required for Katalon / automated tests to find correct columns -->
                                                        <tr>
                                                            <th>Drug</th>
                                                            <th>Tooltip</th>
                                                            <th>Date</th>
                                                            <th>Link</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        {{#eyeMedications.stoppedEyeMeds}}
                                                            <tr data-key="{{index}}">
                                                                <td>
                                                                    {{display}}
                                                                    {{#historyTooltipContent}}
                                                                        <i class="oe-i change small pro_theme js-has-tooltip pad-right" data-tooltip-content="{{historyTooltipContent}}"></i>
                                                                    {{/historyTooltipContent}}
                                                                </td>
                                                                <td>
                                                                    {{#tooltipContent}}
                                                                        <i class="oe-i {{icon}} small pro_theme js-has-tooltip pad-right" data-tooltip-content="{{tooltipContent}}">
                                                                        </i>
                                                                    {{/tooltipContent}}
                                                                </td>
                                                                <td class="nowrap">
                                                                    <span class="oe-eye-lat-icons">
                                                                        <i class="oe-i laterality {{right}}"></i>
                                                                        <i class="oe-i laterality {{left}}"></i>
                                                                    </span>
                                                                    <span class="oe-date">{{date}}</span>
                                                                </td>
                                                                <td>
                                                                    <i class="oe-i"></i>
                                                                </td>
                                                            </tr>
                                                        {{/eyeMedications.stoppedEyeMeds}}
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    {{/eyeMedications.stoppedEyeMeds.length}}
                                {{/eyeMedications.noPreviousData}}
                            {{/eyeMedications.nilRecord}}
                        </div>
                    </div>

                    <div class="group">
                        <div class="label">Family History</div>
                        <div class="data">
                            {{#familyHistory.nilRecord}}
                            <div class="nil-recorded">
                                Patient family history is unknown
                            </div>
                            {{/familyHistory.nilRecord}}
                            {{^familyHistory.nilRecord}}
                                <div class="nil-recorded" style="{{#familyHistory.noFamilyHistory}}display: none;{{/familyHistory.noFamilyHistory}}{{^familyHistory.noFamilyHistory}}''{{/familyHistory.noFamilyHistory}}">
                                    Patient has no family history
                                </div>
                                {{#familyHistory.entries.length}}
                                    <table id="{{familyHistory.modelName}}_patient_mode_table" class="plain patient-data">
                                        <thead>
                                            <tr>
                                                <th>Relative</th>
                                                <th>Side</th>
                                                <th>Condition</th>
                                                <th>Comments</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            {{#familyHistory.entries}}
                                                <tr>
                                                    <td>
                                                        {{relativeDisplay}}
                                                    </td>
                                                    <td>
                                                        {{sideDisplay}}
                                                    </td>
                                                    <td>
                                                        {{conditionDisplay}}
                                                    </td>
                                                    <td>
                                                        {{comments}}
                                                    </td>
                                                </tr>
                                            {{/familyHistory.entries}}
                                        </tbody>
                                    </table>
                                {{/familyHistory.entries.length}}
                            {{/familyHistory.nilRecord}}
                        </div>
                    </div>

                    <div class="group">
                        <div class="label">Social History</div>
                        <div class="data">
                            {{#socialHistory.nilRecord}}
                                <div class="nil-recorded">Nil recorded</div>
                            {{/socialHistory.nilRecord}}
                            {{^socialHistory.nilRecord}}
                                <table class="plain patient-data">
                                    <tbody>
                                        {{#socialHistory.occupation}}
                                            <tr>
                                                <td>{{label}}</td>
                                                <td>{{value}}</td>
                                            </tr>
                                        {{/socialHistory.occupation}}
                                        {{#socialHistory.drivingStatuses}}
                                            <tr>
                                                <td>{{label}}</td>
                                                <td>{{value}}</td>
                                            </tr>
                                        {{/socialHistory.drivingStatuses}}
                                        {{#socialHistory.smokingStatus}}
                                            <tr>
                                                <td>{{label}}</td>
                                                <td>{{value}}</td>
                                            </tr>
                                        {{/socialHistory.smokingStatus}}
                                        {{#socialHistory.accommodation}}
                                            <tr>
                                                <td>{{label}}</td>
                                                <td>{{value}}</td>
                                            </tr>
                                        {{/socialHistory.accommodation}}
                                        {{#socialHistory.comments}}
                                            <tr>
                                                <td>{{label}}</td>
                                                <td>{{value}}</td>
                                            </tr>
                                        {{/socialHistory.comments}}
                                        {{#socialHistory.carer}}
                                            <tr>
                                                <td>{{label}}</td>
                                                <td>{{value}}</td>
                                            </tr>
                                        {{/socialHistory.carer}}
                                        {{#socialHistory.alcoholIntake}}
                                            <tr>
                                                <td>{{label}}</td>
                                                <td>{{value}}</td>
                                            </tr>
                                        {{/socialHistory.alcoholIntake}}
                                        {{#socialHistory.substanceMisuse}}
                                            <tr>
                                                <td>{{label}}</td>
                                                <td>{{value}}</td>
                                            </tr>
                                        {{/socialHistory.substanceMisuse}}
                                    </tbody>
                                </table>
                            {{/socialHistory.nilRecord}}
                        </div>
                    </div>
                </div>
            </div>

            <!--patient popup management-->
            <div class="data-group">
                <h3>Management Summaries</h3>
                <table class="management-summaries">
                    <tbody>
                        {{#managementSummaries.length}}
                            {{#managementSummaries}}
                                <tr>
                                    <td>{{service}}</td>
                                    <td>{{& comments}}</td>
                                    <td class="fade">
                                        <span class="oe-date">
                                            <span class="day">{{day}}</span>
                                            <span class="month">{{month}}</span>
                                            <span class="year">{{year}}</span>
                                        </span>
                                    </td>
                                    <td><i class="oe-i info small pro-theme js-has-tooltip"
                                           data-tooltip-content="{{user}}"></i></td>
                                </tr>
                            {{/managementSummaries}}
                        {{/managementSummaries.length}}
                    </tbody>
                </table>
            </div>

            <div class="data-group">
                <h3>Appointments</h3>
                <table class="patient-appointments">
                    <colgroup>
                        <col class="cols-1">
                        <col class="cols-6">
                        <col class="cols-2">
                        <col class="cols-3">
                    </colgroup>
                    <tbody>
                        {{#worklistPatients.length}}
                            {{#worklistPatients}}
                                <tr>
                                    <td><span class="time">{{time}}</span></td>
                                    <td>{{name}}</td>
                                    <td><span class="oe-date">{{date}}</span></td>
                                    <td>{{status}}</td>
                                </tr>
                            {{/worklistPatients}}
                        {{/worklistPatients.length}}
                    </tbody>
                </table>
            </div>
            {{#pastWorklistPatientsCount}}
                <div class="collapse-data">
                    <div class="collapse-data-header-icon expand js-get-past-appointments">
                        <h3>Past Appointments <small>({{pastWorklistPatientsCount}})</small></h3>
                    </div>
                    <div class="collapse-data-content">
                        <div class="restrict-data-shown">
                            <div class="restrict-data-content rows-10">
                                <!-- restrict data height, overflow will scroll -->
                                <table class="patient-appointments">
                                    <colgroup>
                                        <col class="cols-1">
                                        <col class="cols-6">
                                        <col class="cols-2">
                                        <col class="cols-3">
                                    </colgroup>
                                    <tbody class="js-past-appointments-body">
                                        {{#pastWorklistPatients.length}}
                                            {{#pastWorklistPatients}}
                                                <tr>
                                                    <td><span class="time">{{time}}</span></td>
                                                    <td>{{name}}</td>
                                                    <td><span class="oe-date">{{date}}</span></td>
                                                    <td>{{status}}</td>
                                                </tr>
                                            {{/pastWorklistPatients}}
                                        {{/pastWorklistPatients.length}}
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            {{/pastWorklistPatientsCount}}

            <div class="problems-plans">
                <h3>Problems &amp; Plans</h3>
                <ul class="problems-plans-sortable" id="problems-plans-sortable">
                    {{#currentPlanProblems.length}}
                        {{#currentPlanProblems}}
                            <li>
                                <span class="drag-handle"><i class="oe-i menu medium pro-theme"></i></span>
                                {{name}}
                                <div class="metadata">
                                    <i class="oe-i info small pro-theme js-has-tooltip"
                                       data-tooltip-content="{{tooltipContent}}"></i>
                                </div>
                                <div class="remove"><i class="oe-i remove-circle small pro-theme pad" data-plan-id="{{id}}"></i></div>
                            </li>
                        {{/currentPlanProblems}}
                    {{/currentPlanProblems.length}}
                </ul>
                {{#pastPlanProblems.length}}
                    <h3>Past/closed problems</h3>
                    <table class="past-problems-plans">
                        <colgroup>
                            <col class="cols-4">
                            <col class="cols-1">
                            <col class="cols-2">
                        </colgroup>
                        <tbody>
                            {{#pastPlanProblems}}
                                <tr>
                                    <td style="padding: 6px 3px;">{{name}}</td>
                                    <td>
                                        <div class="metadata">
                                            <i class="oe-i info small pro_theme js-has-tooltip"
                                               data-tooltip-content="{{tooltipContent}}">
                                            </i>
                                        </div>
                                    </td>
                                    <td class="oe-date">Removed: {{lastModifiedDate}}</td>
                                </tr>
                            {{/pastPlanProblems}}
                        </tbody>
                    </table>
                {{/pastPlanProblems.length}}
                {{#currentTrials.length}}
                    <div class="data-group">
                        <h3>Current Trials</h3>
                        <table class="patient-trials">
                            <tbody>
                                {{#currentTrials}}
                                    <tr>
                                        <td>Trial</td>
                                        <td>{{&trial}}</td>
                                    </tr>
                                    <tr>
                                        <td>Date</td>
                                        <td>{{date}}</td>
                                    </tr>
                                    <tr>
                                        <td>Study Coordinator</td>
                                        <td>{{&studyCoordinator}}</td>
                                    </tr>
                                    <tr>
                                        <td>Treatment</td>
                                        <td>{{treatment}}</td>
                                    </tr>
                                    <tr>
                                        <td>Trial Type</td>
                                        <td>{{type}}</td>
                                    </tr>
                                    <tr>
                                        <td>Trial Status</td>
                                        <td>{{status}}</td>
                                    </tr>
                                    <tr class="divider"></tr>
                                {{/currentTrials}}
                            </tbody>
                        </table>
                    </div>
                {{/currentTrials.length}}
            </div>
        </div>
    </div>
</script>
<script type="text/javascript">
    $(function () {
        pickmeup('#worklist-date-from', {
            format: 'd b Y',
            hide_on_select: true,
            date: $('#worklist-date-from').val(),
            default_date: false,
        });
        pickmeup('#worklist-date-to', {
            format: 'd b Y',
            hide_on_select: true,
            date: $('#worklist-date-to').val(),
            default_date: false,
        });

        $('#worklist-date-from, #worklist-date-to').on('pickmeup-change change', function () {
            if ((input_validator.validate($(this).val(), ['date']) || $(this).val() === '')) {
                let parameter = this.id.includes('from') ? 'date_from' : 'date_to';
                window.location.href = jQuery.query
                    .set(parameter, $(this).val())
            }else {
                $(this).addClass('error');
            }
        });

        const worklist_selected = $.cookie("worklist_selected");
        if (worklist_selected){
            updateWorkLists(worklist_selected);
            $('.js-worklist-filter').filter('[data-worklist="'+worklist_selected+'"]').addClass('selected');
        }
    });

    $('.js-clear-dates').on('click', () => {
        $('#worklist-date-from').val(null);
        $('#worklist-date-to').val(null);

        window.location.href = '/worklist/cleardates';
    });

    $('.js-worklist-filter').click(function (e) {
        e.preventDefault();
        resetFilters();
        $(this).addClass('selected');
        updateWorkLists($(this).data('worklist'));
        $.cookie('worklist_selected', $(this).data('worklist'));
    });

    function resetFilters() {
        $('.js-worklist-filter').removeClass('selected');
    }

    function updateWorkLists(listID) {
        if (listID == 'all') {
            $('.worklist-group').show();
            $("#wl_print_selected_worklist").val("");
        } else {
            $('.worklist-group').hide();
            $('#' + listID + '-wrapper').show();
            $("#wl_print_selected_worklist").val(listID);
        }
    }

    function goPrint() {
        printIFrameUrl(
            '/worklist/print',
            {
                list_id: $("#wl_print_selected_worklist").val().replace("js-worklist-",""),
                date_from: $("#worklist-date-from").val(),
                date_to: $("#worklist-date-to").val()
            }
        );
    }

    function performSync(display_spinner = false) {
        const $wl_ctn = $('main#js-clinic-manager');
        const $wl_cat_ul = $('ul#js-worklist-category');
        const selected_category = $('ul#js-worklist-category a.selected').data('worklist');
        const $selected_patient = $('.js-check-patient:checked');
        const $popup = $('.oe-popup-wrap.js-add-psd-popup');
        const $last_sync_time = $('.last-sync');

        if (display_spinner) {
            $('.js-apply-filter-btn').prop('disabled', true);
            $('.js-pathway-spinner').show();
        }

        $.get(
            '/worklist/AutoRefresh',
            {
                date_from: $('#worklist-date-from').val(),
                date_to: $('#worklist-date-to').val(),
            },
            function(resp){
                if (display_spinner) {
                    $('.js-apply-filter-btn').prop('disabled', false);
                    $('.js-pathway-spinner').hide();
                }
                if(!resp){
                    return;
                }

                // Ensure the step adder checkboxes maintain their state
                let checkedAdders = $wl_ctn.find('.js-check-patient:checked').map(function() { return this.value; }).get();

                // Cleanup and repopulate worklist sections
                $wl_ctn.find('section').remove();
                $wl_ctn.find('div.oec-adder').after(resp['main']);

                if (picker) {
                    picker.reattachCheckboxHandlers();
                }

                for (id of checkedAdders) {
                    $wl_ctn.find(`.js-check-patient[value="${id}"]`).prop('checked', true);
                }

                $wl_cat_ul.html(resp['filter']);
                if($popup.is(":hidden")){
                    $popup.html(resp['popup']);
                }
                $selected_patient.each(function(index, item){
                    const val = $(item).val();
                    $(`.js-check-patient[value="${val}"]`).prop('checked', true);
                });
                $('.patient-popup-worklist').remove();

                $('.js-select-patient-for-psd').trigger('change');
                $wl_cat_ul.find(`a[data-worklist="${selected_category}"]`).trigger('click');
                $last_sync_time.text(resp['refresh_time']);

                if (worklistFiltersController) {
                    worklistFiltersController.updateCounts(resp['quick_details'], resp['waiting_for_details'], resp['assigned_to_details']);
                    worklistFiltersController.setAvailableLists(resp['all_worklists_in_context'], resp['filtered_worklist_ids'], resp['worklist_definitions']);
                    worklistFiltersController.resetShownLists();
                }

                // At every autorefresh, traverse all the child nodes of the patient popup data and check if any
                // of the patient data HTML needs to be updated.
                $('.js-patient-popup-data').children('div').each(function (index, element) {
                    $.ajax({
                        type: "POST",
                        url: "/worklist/renderPopup",
                        data: {
                            "patientId" : $(element).attr('data-patient-id'),
                            YII_CSRF_TOKEN: YII_CSRF_TOKEN
                        },
                        success: function (resp) {
                            const patientDataObjHash = objectHash(resp);
                            if ($(element).data('data-patient-json-hash') !== patientDataObjHash) {
                                let templateInstance = document.getElementById('oe-patient-quick-overview-template').innerHTML;
                                let text = Mustache.render(templateInstance, resp);
                                $(element).html(text);
                                // Update the arbitrary data with the current hash.
                                $(element).data('data-patient-json-hash', patientDataObjHash);
                            }
                        }
                    });
                });
            }
        );
    }

    function autoSync(count_down){
        init_time--;
        if(init_time === 0){
            // reset timer count
            init_time = count_down;
            performSync();
        }
    }
    // init global timer count
    let init_time = '<?=$sync_interval_value?>';

    // Controller for filtering worklists via right hand side panel / quick filters
    let worklistFiltersController = null;

    // Pathstep picker
    let picker = null;

    // Common data used by the PathwayStepPicker and some step widget views
    const pathwaySetupData = <?=$picker_setup?>;

    // Workaround for the complete option dialog disappearing before the 'ok' event is triggered.
    $(document).off('click', '.js-complete-option-list .li').on('click', '.js-complete-option-list .li', function() {
        $('#selected-complete-option').val($(this).prev().val());
    });

    $(document).ready(function () {
        const picker_setup = pathwaySetupData;
        const path_step_type_ids = <?=$path_step_type_ids?>;
        $('body').on('click', '.collapse-data-header-icon', function () {
            $(this).toggleClass('collapse expand');
            $(this).next('div').toggle();
        });

        // Set up client side controller for filters

        // Gather the set of worklist ids and titles, now from the worklists
        // passed from the controller.
        const extantWorklists = <?= json_encode(array_map(static function ($worklist) {
            return ['id' => $worklist->id, 'title' => $worklist->name];
                                }, $unfiltered_worklists)) ?>;

        const filteredWorklists = <?= json_encode(array_map(static function ($worklist) {
            return $worklist->id;
                                  }, $worklists)) ?>;

        const worklistDefinitions = <?= json_encode($worklist_definitions) ?>;

        const usersList = <?= json_encode(array_map(static function ($user) {
            return ['id' => $user->id, 'label' => $user->getFullName() . ' (' . $user->getInitials() . ')'];
                          }, User::model()->findAll())) ?>;

        const stepsList = <?= json_encode(array_map(static function ($step_type) {
            return ['id' => $step_type->id, 'label' => $step_type->long_name];
                          }, PathwayStepType::model()->findAll())) ?>;

        const controllerOptions = {
            worklistFilterPanelSelector: '#js-worklists-filter-panel',
            saveFilterPopupSelector: '#js-worklist-save-filter-popup',
            saveFilterPopupButtonSelector: '.oe-header .js-favourite',

            // No button selector needed for sort by popup, * it's part of the quick filter panel
            quickFilterPanelSelector: '#js-clinic-filters',
            sortByPopupSelector: '#js-worklist-sort-order-popup',

            worklists: extantWorklists,
            filteredWorklists: filteredWorklists,
            worklistDefinitions: worklistDefinitions,
            users: usersList,
            steps: stepsList,

            initial_selected_filter_type: <?= json_encode($session_filter_info['type']) ?>,
            initial_selected_filter_id: <?= json_encode($session_filter_info['id']) ?>,
            initial_selected_quick_filter: <?= json_encode($session_filter_info['quick']) ?>,
            initial_is_combined: <?= json_encode($session_filter_info['filter']->combined) ?>,

            applyFilter: function() { performSync(true); },

            changeShownLists: function(lists) {
                if (lists === 'all') {
                    $('#js-clinic-manager section').show();
                } else {
                    $('#js-clinic-manager section').hide();

                    for (id of lists) {
                        $(`#js-clinic-manager section[data-definition-id="${id}"]`).show();
                    }
                }
            },

            removeRow: function(pathwayId) {
                const pathway = $(`.pathway[data-visit-id="${pathwayId}"`).closest('tr');
                const pagination = pathway.closest('.oec-patients').find('.pagination');

                pathway.remove();

                // This will change the pagination numbers for the worklist the row is in, since it reflects server PHP state, not client JS state otherwise
                const countRegex = /([0-9]+) - ([0-9]+) of ([0-9]+)/;
                const countText = pagination.contents()[0].data;
                const paginationCounts = countRegex.exec(countText);

                paginationCounts[1] = parseInt(paginationCounts[1]);
                paginationCounts[2] = parseInt(paginationCounts[2]) - 1;
                paginationCounts[3] = parseInt(paginationCounts[3]) - 1;

                pagination.contents()[0].data = `${paginationCounts[1]} - ${paginationCounts[2]} of ${paginationCounts[3]}`;
            }
        };

        worklistFiltersController = new OpenEyes.WorklistFiltersController(controllerOptions);

        worklistFiltersController.updateCounts(
            <?= json_encode($this->getStatusCountsList($initial_filter, $worklists)) ?>,
            <?= json_encode($this->getWaitingForList($initial_filter, $worklists)) ?>,
            <?= json_encode($this->getAssignedToList($initial_filter, $worklists)) ?>
        );

         picker = new OpenEyes.UI.PathwayStepPicker({
            ...path_step_type_ids,
            ...picker_setup,
            pathways: <?= $pathway_json ?>,
            onChangePatientRow: function(changeType, details) {
                worklistFiltersController.updateCountsOnChange(changeType, details);
            },
        });
        picker.init();

        // init timer obj
        let autorefresh_countdown = null;
        if(init_time !== 'off'){
            // if auto sync is not set to off, turn on the timer
            autorefresh_countdown = setInterval(autoSync.bind(null, init_time), 1000);
        }

        let $sync_btn = $('#js-sync-btn');
        let $sync_data = $('#js-sync-data');
        let $sync_opts = $('#js-sync-options');
        $sync_data.off('mouseenter').on('mouseenter', function(){
            $sync_btn.addClass('active');
            $sync_opts.show();
        });
        $sync_data.off('mouseleave').on('mouseleave', function(){
            $sync_btn.removeClass('active');
            $sync_opts.hide();
        });
        $sync_opts.off('click', 'ul button').on('click', 'ul button', function(){
            let selected_key = $(this).data('value-key');
            let selected_value = $(this).data('value');
            // send ajax call to save user's auto sync setting
            $.ajax({
                'type': 'GET',
                'url': "<?= \Yii::app()->createUrl('/profile/changeWorklistSyncInterval') ?>",
                'data': {
                    'sync_interval': selected_key,
                    'key': '<?=$sync_interval_setting_key?>',
                }
            });
            if(selected_key === 'off'){
                $sync_btn.removeClass('on');
                // turn off the timer
                clearInterval(autorefresh_countdown);
            } else {
                $sync_btn.addClass('on');
                // avoid duplicate timers
                if(autorefresh_countdown){
                    clearInterval(autorefresh_countdown);
                }
                init_time = selected_key;
                autorefresh_countdown = setInterval(autoSync.bind(null, selected_key), 1000);
            }
            $sync_btn.find('.sync-interval').text(selected_value);
            autoSync(selected_key);
        });
    })

    const $parentContainerElem = $("body.open-eyes.oe-grid");
    const $patientQuickOverviewElem = $('.oe-patient-quick-overview');
    const POPUP_TOP_OFFSET = 5;
    const POPUP_BOTTOM_OFFSET = 10;
    const POPUP_LEFT_OFFSET = 250;
    const POPUP_HEIGHT_APPROXIMATION = 300;
    let xhr;
    let isOpen = false;
    let isClicked = false;

    function closePatientPop() {
        isClicked = !isClicked
        $patientQuickOverviewElem.find('.close-icon-btn').hide();
        hidePatientQuickOverview();
    }

    function showPatientQuickOverview(element, isClicked) {
        if (typeof isClicked !== 'undefined') {
            if (isClicked) {
                $patientQuickOverviewElem.find('.close-icon-btn').show();
                $patientQuickOverviewElem.show();
                if (isOpen) {
                    return;
                }
            } else {
                $patientQuickOverviewElem.find('.close-icon-btn').hide();
                hidePatientQuickOverview();
                return;
            }
        }
        isOpen = true;
        $patientQuickOverviewElem.show();

        $patientQuickOverviewElem.append('<div class="quick-overview-content"><i class="spinner"></i></div');
        // Get the Patient Id.
        const patientId = $(element).parent().attr('data-patient-id');
        const mode = $patientQuickOverviewElem.attr('data-mode');
        $patientQuickOverviewElem.get(0).style.cssText = " ";

        let rect = element.getBoundingClientRect();

        if( mode === "float"){
            $patientQuickOverviewElem.removeClass('side-panel');
            // Check not too close the bottom of the screen.
            if (window.innerHeight - rect.y > POPUP_HEIGHT_APPROXIMATION) {
                $patientQuickOverviewElem.get(0).style.top = (rect.y + rect.height + POPUP_TOP_OFFSET) + "px";
            } else {
                $patientQuickOverviewElem.get(0).style.bottom = (window.innerHeight - rect.top) + POPUP_BOTTOM_OFFSET + "px";
            }
            $patientQuickOverviewElem.get(0).style.left = (rect.x - POPUP_LEFT_OFFSET +  rect.width/2)  + "px";
        } else {
            $patientQuickOverviewElem.get(0).classList.add("full-panel");
            if( mode.endsWith("to-right")){
                $patientQuickOverviewElem.get(0).style.left = rect.right + 20 + 'px';
            } else {
                $patientQuickOverviewElem.get(0).left = rect.left - (420 + 20) + 'px';
            }
        }

        if ($('.js-patient-popup-data').find(`[data-patient-id=${patientId}]`).length) {
            $patientQuickOverviewElem.find('.quick-overview-content').remove();
            $patientQuickOverviewElem.append($('.js-patient-popup-data').find(`[data-patient-id=${patientId}]`).html());
        } else {
            xhr = $.ajax({
                type: "POST",
                url: "/worklist/renderPopup",
                data: {
                    "patientId" : patientId,
                    YII_CSRF_TOKEN: YII_CSRF_TOKEN
                },
                success: function (resp) {
                    $patientQuickOverviewElem.find('.quick-overview-content').remove();
                    let templateInstance = document.getElementById('oe-patient-quick-overview-template').innerHTML;

                    let text = Mustache.render(templateInstance, resp);
                    $patientQuickOverviewElem.append(text);

                    const $tempElement = $(`<div data-patient-id=${patientId}>${text}</div>`);
                    $tempElement.hide();
                    $('.js-patient-popup-data').append($tempElement);
                    $('.js-patient-popup-data').find(`[data-patient-id=${patientId}]`).data('data-patient-json-hash', objectHash(resp));
                }
            });
        }
    }

    $(document).off('change', '#visual-fields-form input[value="preset_id"]')
        .on('change', '#visual-fields-form input[name="preset_id"]', function () {
            if ($(this).val() === '') {
                $(this).closest('#visual-fields-form').find('.js-field-custom').show();
                // Set the height
                const $pathstepPopup = $(this).closest('.oe-pathstep-popup.arrow-t');
                const currentHeight = $pathstepPopup.height();
                $pathstepPopup.height(currentHeight + 90);
            } else {
                $(this).closest('#visual-fields-form').find('.js-field-custom').hide();
                $(this).closest('#visual-fields-form').find('.js-field-custom input').prop('checked', false);
            }
        });

    function onMouseEnterPatientQuickOverview(element, isClicked) {
        if (!isOpen) {
            showPatientQuickOverview(element, isClicked);
        }
    }

    function hidePatientQuickOverview() {
        if (!isClicked) {
            $patientQuickOverviewElem.find('.oe-patient-meta, .quick-overview-content').remove();
            $patientQuickOverviewElem.hide();
            isOpen = false;
            xhr.abort();
        }
    }

    function onClickPatientQuickOverview(element) {
        isClicked = !isClicked
        showPatientQuickOverview(element, isClicked);
    }

    // auto close patient quick view when scrolling
    document.getElementById('js-clinic-manager').addEventListener('scroll', function() {
        if(isOpen) {
            if(isClicked) {
                closePatientPop();
            } else {
                hidePatientQuickOverview();
            }
        }
    });
</script>
