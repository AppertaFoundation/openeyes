<?php
    // Glaucoma and Medical Retina filters
    $procedure_filter = null;
    $outcome_eye_filter = null;
    $vf_eye_filter = null;
    $age_filter = null;
    $plot_filter = null;
    $va_unit_filter = null;
    $time_interval_filter = null;
if ($specialty !== 'All') {
    $procedure_filter = $this->renderPartial('./filters/analytics_procedure_filter', array(
        'procedures' => $procedures,
        'default_procedure' => $default_procedure,
        'specialty' => $specialty,
    ), true);
    $outcome_eye_filter = $this->renderPartial('./filters/analytics_outcome_eye_filter', array(), true);
    $vf_eye_filter = $this->renderPartial('./filters/analytics_vf_eye_filter', array(), true);
    $age_filter = $this->renderPartial('./filters/analytics_age_filter', array(), true);
    $plot_filter = $this->renderPartial('./filters/analytics_plot_filter', array(), true);
    $va_unit_filter = $this->renderPartial('./filters/analytics_va_unit_filter', array(
        'va_units' => $va_units,
        'default_va_unit' => $default_va_unit,
    ), true);
    $time_interval_filter = $this->renderPartial('./filters/analytics_time_interval_filter', array(), true);
}
    // common filters
    $user_filter = $this->renderPartial('./filters/analytics_user_filter', array(
        'is_service_manager' => $is_service_manager,
        'current_user' => $current_user,
        'user_list' => $user_list
    ), true);
    $diagnosis_filter = $this->renderPartial('./filters/analytics_diagnosis_filter', array(
        'common_disorders' => $common_disorders,
    ), true);
    ?>
<!-- Clinical / Service Tab START -->
<div class="view-mode flex-layout">
    <button class="analytics-view-mode analytics-section cols-6 <?=$can_view_clinical || $is_service_manager ? '' : 'disabled';?>" id="js-btn-clinical"
            data-section="#js-hs-chart-analytics-clinical-main"
            data-tab="#js-charts-clinical"
            data-options="clinical">
                Clinical
    </button>
    <button class="analytics-view-mode analytics-section cols-6 selected" id="js-btn-service"
            data-section="#js-hs-chart-analytics-service"
            data-tab="#js-charts-service"
            data-filterid="service"
            data-options="service">
        Service
    </button>
</div>
<!-- Clinical / Service Tab END -->

<!-- Clinical Report Type START -->
<h3>Reports</h3>
<div id="js-charts-clinical" style="display: none;">
    <ul class="charts">
        <li>
            <a href="#"
                id="js-hs-clinical-diagnoses"
                data-plotid="#js-hs-chart-analytics-clinical-main"
                data-filterid="clinical"
                data-report="diagnoses"
                class="selected clinical-plot-button js-plot-display-label">
                Diagnoses
            </a>
        </li>
        <?php if ($specialty !== "All") {?>
            <li>
                <a href="#"
                    id="js-hs-clinical-custom"
                    data-plotid="#js-hs-chart-analytics-clinical-others"
                    data-filterid="custom"
                    data-report="outcomes"
                    class="clinical-plot-button js-plot-display-label">
                    Outcomes
                </a>
            </li>
        <?php }?>
    </ul>
</div>
<!-- Clinical Report Type END -->

<!-- Service Report Type START -->
<div id="js-charts-service">
    <ul class="charts">
        <li><a href="#" id="js-hs-app-follow-up-coming" class="js-plot-display-label" data-report="coming">Followups coming due</a></li>
        <li><a href="#" id="js-hs-app-follow-up-overdue" class="selected js-plot-display-label" data-report="overdue">Overdue followups</a></li>
        <li><a href="#" id="js-hs-app-follow-up-waiting" class="js-plot-display-label" data-report="waiting">Waiting time for new patients</a></li>
        <?php if ($specialty === "Glaucoma") { ?>
            <li><a href="#" id="js-hs-app-vf" class="js-plot-display-label" data-report="vf">Progression of Visual Fields</a></li>
        <?php } ?>
    </ul>
</div>
<!-- Service Report Type END -->

<!-- Filters START -->
<h3>Data Filters</h3>
<form id="search-form" autocomplete="off">
    <!-- Specialty Filter START -->
    <input type="hidden" name="specialty" value="<?=$specialty?>">
    <!-- Specialty Filter END -->
    <table class="custom-filters">
        <tbody>
            <?= $user_filter;?>
            <?= $diagnosis_filter;?>
            <?= $procedure_filter;?>
            <?= $outcome_eye_filter;?>
            <?= $vf_eye_filter;?>
            <?= $age_filter;?>
            <?= $plot_filter;?>
            <?= $va_unit_filter;?>
            <?= $time_interval_filter;?>
            
            <!-- Unbooked Filter START -->
            <tr class="service-filter clinical-filter">
                <td>Unbooked Only</td>
                <td colspan="2">
                    <label class="inline highlight">
                        <input class="default" type="radio" value="1" data-name="analytics_unbooked" name="unbooked" checked>Yes
                    </label>
                    <label class="inline highlight">
                        <input type="radio" value="0" data-name="analytics_unbooked" name="unbooked">No
                    </label>
                </td>
            </tr>
            <!-- Unbooked Filter END -->
        </tbody>
    </table>
    <h3>Date Range</h3>
    <!-- Date Filter START -->
    <div class="flex-layout">
        <input id="analytics_datepicker_from" disabled type="text" class="date datepicker-to" placeholder="from" name="from">
        <input id="analytics_datepicker_to" disabled type="text" class="date datepicker-from" placeholder="to" name="to">
        <label class="inline highlight ">
            <input value="1" id="show-all-dates" type="checkbox" checked="checked"> All dates
        </label>
    </div>
    <!-- Date Filter END -->
</form>
<div class="row divider">
    <button type="reset" id="reset-filters" class="cols-full">Reset all Filters</button>
</div>
<!-- Filters END -->

<!-- Download CSV buttons START -->
<div class="button-stack">
    <button class="green hint cols-full update-chart-btn">Update Chart</button>
    <button id="js-download-csv" data-anonymised="0" class="cols-full">Download (CSV)</button>
    <button id="js-download-csv-anonymized" data-anonymised="1" class="cols-full">Download (CSV - Anonymised)</button>
</div>
<!-- Download CSV buttons END -->

<script>
    $(function(){
        $('#show-all-dates').off('change').on('change', function(e){
            $('input[id^="analytics_datepicker"]').attr('disabled', this.checked).val('');
        });
        $('#reset-filters').off('click').on('click', function(e){
            const defaults = document.querySelectorAll('li[data-defaultselected="true"]:not([class="selected"])');
            const addBtns = document.querySelectorAll('div.oe-add-select-search div.add-icon-btn');
            $(defaults).trigger('click');
            $(addBtns).trigger('click');
            $('.custom-filter input[name$="eye"]').each(function(i, item){
                $(item).prop('checked', false).trigger('change');
            })
            $('.custom-filter input[name$="eye"].default').prop('checked', 'checked').trigger('change');
            $('#show-all-dates').trigger('change');
            $('input[data-name$="unbooked"].default').trigger('change');
            $('input[data-name$="plot"].default').trigger('change');
        });
    })
</script>
