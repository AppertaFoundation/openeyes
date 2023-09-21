<?php
/* @var $this CaseSearchController
 * @var $params array
 * @var $paramList array
 * @var $patients CActiveDataProvider
 * @var $trial Trial
 * @var $form CActiveForm
 * @var $saved_searches array
 * @var $search_label string
 * @var $variables CaseSearchVariable[]
 * @var $variableList CaseSearchVariable[]
 * @var $variableData array
 * @var $from_date string
 * @var $to_date string
 * @var $show_all_dates bool
 */
$this->pageTitle = 'Advanced Search';

$user_searches = array_map(
    static function ($item) {
        return array('id' => $item->id, 'name' => $item->name);
    },
    $saved_searches
);

?>
<div class="oe-full-header flex-layout">
    <div class="title wordcaps">
        <?= $this->trialContext === null ?
            'Advanced Search' :
            'Adding Participants to Trial: ' . $this->trialContext->name
        ?>
    </div>
</div>
<div class="oe-full-content subgrid wide-side-panel oe-query-search">
    <nav class="oe-full-side-panel">
        <button id="load-saved-search" class="cols-full" data-test="load-saved-search">Previous searches</button>
        <h3>Search criteria</h3>
        <p id="criteria-initial" <?= isset($params) ? 'style="display: none;"' : null ?>>Select criteria for search...</p>
        <?php $form = $this->beginWidget('CActiveForm', array('id' => 'search-form')); ?>
        <table id="param-list" class="standard normal-text last-right">
            <tbody>
            <?php
            if (isset($params)) :
                ksort($params);
                foreach ($params as $id => $param) :?>
                    <?php $this->renderPartial('parameter_form', array(
                        'model' => $param,
                        'id' => $id,
                        'readonly' => !$param->isSaved
                    )); ?>
                <?php endforeach;
            endif; ?>
            </tbody>
        </table>
        <div class="flex-layout flex-right row">
            <button id="add-to-advanced-search-filters" class="button hint green js-add-select-btn"
                    data-popup="add-to-search-queries" data-test="add-to-search-queries">
                Add criteria
            </button>
        </div>
        <input type="hidden" name="YII_CSRF_TOKEN" value="<?php echo Yii::app()->request->csrfToken ?>"/>
        <input type="hidden" name="var" value="<?= isset($variables[0]) ? $variables[0]->field_name : null ?>"/>
        <hr class="divider"/>
        <h3>Date range</h3>
        <div class="flex-layout">
            <?= CHtml::textField('from_date', CHtml::encode($from_date), array('placeholder' => 'from', 'disabled' => $show_all_dates, 'class' => 'date datepicker-from')) ?>
            <?= CHtml::textField('to_date', CHtml::encode($to_date), array('placeholder' => 'to', 'disabled' => $show_all_dates, 'class' => 'date datepicker-to')) ?>
            <label class="inline highlight ">
                <?= CHtml::checkBox('show-all-dates', $show_all_dates) ?> All available dates
            </label>
        </div>
        <hr class="divider"/>
        <div class="button-stack">
            <?= CHtml::htmlButton('Search', array('class' => 'cols-full green hint js-search-btn', 'type' => 'submit')) ?>
            <button class="js-save-search-dialog-btn cols-full" data-test="save-search">Save search</button>
            <?= CHtml::htmlButton('Clear search', array('id' => 'clear-search', 'class' => 'cols-full')) ?>
            <?= (!$patients || $patients->totalItemCount === 0) ? null : CHtml::htmlButton(
                'Download CSV BASIC',
                array(
                        'id' => 'download-csv-basic',
                        'type' => 'submit',
                        'class' => 'cols-full',
                        'formaction' => '/OECaseSearch/caseSearch/downloadCSV?mode=BASIC',
                    )
            ) ?>
            <?= (!$patients || $patients->totalItemCount === 0) ? null : CHtml::htmlButton(
                'Download CSV Advanced',
                array(
                        'id' => 'download-csv-advanced',
                        'type' => 'submit',
                        'class' => 'cols-full',
                        'formaction' => '/OECaseSearch/caseSearch/downloadCSV?mode=ADVANCED',
                    )
            ) ?>
            <?php if ($this->trialContext) : ?>
                <button class="cols-full" onclick="backToTrial()">Back to Trial</button>
            <?php endif; ?>
        </div>
        <?php $this->endWidget('search-form'); ?>
    </nav>
    <main class="oe-full-main">
        <?php if ($patients->itemCount > 0) {
            $view = SettingMetadata::model()->getSetting('oecasesearch_default_view');
            $this->widget('CaseSearchPlot', array(
                'variable_data' => $variableData,
                'variables' => $variables,
                'total_patients' => $patients->totalItemCount,
                'list_selector' => '.oe-search-results',
                'display' => ($view === 'plot'),
            ));
            $this->renderPartial('patient_drill_down_list', array(
                'patients' => $patients,
                'display_class' => 'oe-search-results',
                'display' => ($view === 'list'),
            ));
        } else { ?>
            <div class="alert-box info">No patients found.</div>
        <?php } ?>
        <div id="js-analytics-spinner" style="display: none;"><i class="spinner"></i></div>
    </main>
</div>
<script type="text/html" id="save-search-template">
    <?php $form = $this->beginWidget('CActiveForm', array('id' => 'save-form')); ?>
    <div class="flex-layout flex-top">
        <div class="search-queries">
            <h3>Search queries</h3>
            <table>
                {{{queryTable}}}
            </table>
        </div>
        <div class="show-query">
            <h3>Save search as</h3>
            <?= CHtml::textField('search_name', $search_label, array('placeholder' => 'Search name description', 'class' => 'cols-full', 'maxlength' => 50, 'data-test' => 'search-name')) ?>
            <div class="row align-right">
                <?= CHtml::htmlButton(
                    'Save search',
                    array(
                        'class' => 'js-save-search-btn hint green',
                        'type' => 'submit',
                        'formaction' => $this->createUrl('caseSearch/saveSearch'),
                        'data-test' => 'save-search-btn'
                    )
                ) ?>
            </div>
        </div>
    </div>
    <?php $this->endWidget('save-form'); ?>
</script>
<script type="text/html" id="load-saved-search-template">
    <div class="flex-layout flex-top">
        <div class="all-searches">
            <table class="searches" data-test="searches">
                <tbody>
                {{#allSearches}}
                <tr>
                    <td>{{name}}</td>
                    <td class="nowrap">
                        <button class="js-use-query" data-id="{{id}}">Use</button>
                        <button class="js-show-query" data-id="{{id}}">Show queries</button>
                    </td>
                    <td>
                        <i class="oe-i trash large pro-theme" data-id="{{id}}"></i>
                    </td>
                </tr>
                {{/allSearches}}
                </tbody>
            </table>
        </div>
        <div class="show-query">
            <h3>Current search queries</h3>
            <table class="query-list">
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
</script>
<script type="text/html" id="search-contents-template">
    <tr>{{{searchContents}}}</tr>
</script>
<script type="text/javascript">
    function addPatientToTrial(patient_id, trial_id) {
        const addSelector = '#add-to-trial-link-' + patient_id;
        const removeSelector = '#remove-from-trial-link-' + patient_id;
        $.ajax({
            url: '<?php echo $this->createUrl('/OETrial/trial/addPatient'); ?>',
            data: {id: trial_id, patient_id: patient_id, YII_CSRF_TOKEN: $('input[name="YII_CSRF_TOKEN"]').val()},
            type: 'POST',
            success: function () {
                $(addSelector).hide();
                $(removeSelector).show();
                $(removeSelector).parent('.result').css('background-color', '#fafad2');
            },
            error: function () {
                new OpenEyes.UI.Dialog.Alert({
                    content: "Sorry, an internal error occurred and we were unable to add the patient to the trial." +
                        "\n\nPlease contact support for assistance."
                }).open();
            },
        });
    }

    function removePatientFromTrial(patient_id, trial_id) {
        let addSelector = '#add-to-trial-link-' + patient_id;
        let removeSelector = '#remove-from-trial-link-' + patient_id;
        $.ajax({
            url: '<?php echo $this->createUrl('/OETrial/trial/removePatient'); ?>',
            data: {id: trial_id, patient_id: patient_id, YII_CSRF_TOKEN: $('input[name="YII_CSRF_TOKEN"]').val()},
            type: 'POST',
            success: function () {
                $(removeSelector).hide();
                $(addSelector).show();
                $(addSelector).parent('.result').css('background-color', '#fafafa');
            },
            error: function () {
                new OpenEyes.UI.Dialog.Alert({
                    content: "Sorry, an internal error occurred and we were unable to remove the patient from the trial." +
                        "\n\nPlease contact support for assistance."
                }).open();
            }
        });
    }

    function backToTrial() {
        event.preventDefault();
        window.location.href = '/OETrial/trial/view/<?= $this->trialContext ? $this->trialContext->id : null?>';
    }

    function removeParam(elm) {
        $(elm).parents('.parameter').remove();
    }

    function getMaxId() {
        let id_max = -1;
        $('#param-list tbody tr.parameter').each(function () {
            if ($(this).data('id') > id_max) {
                id_max = $(this).data('id');
            }
        });
        return id_max;
    }

    function performSort(field, $container) {
        let $field = $container.find('#sort-field option[value="' + field + '"]');
        let direction = $container.find("input[name='sort-options']").filter("input[checked='checked']").val();
        $('#js-analytics-spinner').show();
        if (direction === 'ascend') {
            $.get($field.data('sort-ascend')).done(function (response) {
                $container.html(response);
                $('#js-analytics-spinner').hide();
            });
        } else if (direction === 'descend') {
            $.get($field.data('sort-descend')).done(function (response) {
                $container.html(response);
                $('#js-analytics-spinner').hide();
            });
        }
    }

    $(document).ready(function () {
        let $main = $('.oe-full-main');
        //null coalesce the id of the last parameter
        let parameter_id_counter = getMaxId();

        pickmeup('.datepicker-from', {
            format: 'Y-m-d',
            hide_on_select: true,
            default_date: false,
        });

        pickmeup('.datepicker-to', {
            format: 'Y-m-d',
            hide_on_select: true,
            default_date: false
        });

        new OpenEyes.UI.AdderDialog.QuerySearchDialog({
            itemSets: [
                new OpenEyes.UI.AdderDialog.ItemSet(
                    <?= CJSON::encode($paramList) ?>,
                    {'multiSelect': false, 'id': 'param-type-list', 'deselectOnReturn': true,}
                ),
            ],
            openButton: $('#add-to-advanced-search-filters'),
            parentContainer: 'body',
            id: 'add-parameter-dialog',
            onReturn: function (dialog, selectedValues) {
                let operator = null;
                let value = -1;
                let valueList = [];
                let type = null;
                $.each(selectedValues, function (index, item) {
                    switch (item.type) {
                        case 'operator':
                            operator = item.id;
                            break;
                        case 'number':
                            if (value !== -1) {
                                // Second digit, so multiply the existing value by 10 first before adding the next number.
                                value = (value * 10) + parseInt(item.id);
                            } else {
                                // First digit.
                                value = parseInt(item.id)
                            }
                            break;
                        case 'template_string_lookup':
                        case 'lookup':
                            // Selected value
                            valueList.push({id: item.id, field: item.field});
                            break;
                        case 'template_string':
                            // If the value has already been set, don't bother using the template string.
                            if (value === -1) {
                                value = item.id;
                            }
                            break;
                        default:
                            // Parameter type.
                            type = item.type;
                            break;
                    }
                });
                let parameter = {
                    type: type,
                    operation: operator,
                    value: (value !== -1 ? value.toString() : valueList),
                    id: ++parameter_id_counter
                };
                $.ajax({
                    url: '<?= $this->createUrl('caseSearch/addParameter') ?>',
                    data: {
                        parameter: parameter
                    },
                    type: 'GET',
                    success: function (response) {
                        // Append the dynamic parameter HTML before the first fixed parameter.
                        const $criteria_initial = $('#criteria-initial');
                        $('#param-list tbody').append(response);
                        if ($criteria_initial.is(':visible')) {
                            $criteria_initial.hide();
                        }
                    },
                    error: function (xhr) {
                        new OpenEyes.UI.Dialog.Alert({
                            content: '<ul>' + xhr.responseText + '</ul>',
                        }).open();
                    }
                });
            }
        });

        $('.oe-full-side-panel').on('click', '#param-list tbody td .remove-circle', function () {
            if ($('#param-list tbody tr').length === 1) {
                $('#criteria-initial').show();
            }
            this.closest('tr').remove();
        });

        $('input[name="show-all-dates"]').change(function () {
            // Toggle disable/enable of date fields.
            let $from_date = $('input[name="from_date"]');
            let $to_date = $('input[name="to_date"]');
            if ($(this).is(':checked')) {
                $from_date.val('');
                $to_date.val('');
                $from_date.prop('disabled', true);
                $to_date.prop('disabled', true)
            } else {
                $from_date.prop('disabled', false);
                $to_date.prop('disabled', false)
            }
        });

        $('#load-saved-search').click(function (e) {
            e.preventDefault();
            new OpenEyes.UI.Dialog.LoadSavedSearch({
                id: 'load-saved-search-dialog',
                title: 'All searches',
                user_id: <?= Yii::app()->user->id ?>,
                all_searches: <?= json_encode($user_searches) ?>
            }).open();
        });

        $('.js-save-search-dialog-btn').click(function (e) {
            e.preventDefault();
            new OpenEyes.UI.Dialog.SaveSearch({
                id: 'save-search-dialog',
                title: 'Save search'
            }).open();
        });

        $main.on('change', '#sort-field', function () {
            let $container = $(this).parent().parent().parent().parent();
            let value = $container.find('#sort-field').val();
            performSort(value, $container);
        });

        $main.on('change', "input[name='sort-options']", function () {
            let $container = $(this).parent().parent().parent().parent().parent().parent();
            let value = $container.find('#sort-field').val();
            performSort(value, $container);
        });

        $('#clear-search').click(function () {
            $.ajax({
                url: '<?php echo $this->createUrl('caseSearch/clear')?>',
                type: 'GET',
                success: function () {
                    $('#case-search-results').children().remove();
                    $('#param-list tbody tr').remove();
                    $('.date').val('');
                    $('.js-plotly-plot').remove();
                    $('.results-options').hide();
                    $('.oe-full-main').append('<div class="alert-box info">No patients found.</div>');
                    $('#download-csv-basic').hide();
                    $('#download-csv-advanced').hide();
                    $('#criteria-initial').show();
                },
                error: function () {
                    new OpenEyes.UI.Dialog.Alert({
                        content: 'Unable to clear search results.'
                    }).open();
                }
            });
        });

        $main.on('click', '.oe-search-drill-down-list .pagination a', function (e) {
            e.preventDefault();
            e.stopPropagation();
            $('#js-analytics-spinner').show();
            $.get($(this).attr("href")).done(function (response) {
                $('.oe-search-drill-down-list').html(response);
                $('#js-analytics-spinner').hide();
            });
        });

        $main.on('click', '.oe-search-results .pagination a', function (e) {
            e.preventDefault();
            e.stopPropagation();
            $('#js-analytics-spinner').show();
            $.get($(this).attr("href")).done(function (response) {
                $('.oe-search-results').html(response);
                $('#js-analytics-spinner').hide();
            });
        });
    });
</script>

<?php if ($this->trialContext) { ?>
    <script type="text/javascript">

        $(document).on('click', '.js-add-to-trial', function () {
            const addLink = this;
            //const $removeLink = $(this).closest('.js-add-remove-participant').find('.js-remove-from-trial');
            let trialShortlist = parseInt($(this).closest('.js-oe-patient').find('.trial-shortlist').contents().filter(function () {
                return this.nodeType === Node.TEXT_NODE;
            }).text());
            const trialShortListElement = $(this).closest('.js-oe-patient').find('.trial-shortlist');
            const patientId = $(this).closest('.js-oe-patient').data('patient-id');

            $.ajax({
                url: '<?php echo $this->createUrl('/OETrial/trial/addPatient'); ?>',
                data: {
                    id: <?= $this->trialContext->id?>,
                    patient_id: patientId,
                },
                success: function () {
                    $(addLink).hide();
                    trialShortlist += 1;
                    trialShortListElement.text(' ' + trialShortlist);
                    trialShortListElement.prepend('<em>Shortlisted</em>');
                    trialShortListElement.show();

                },
                error: function () {
                    new OpenEyes.UI.Dialog.Alert({
                        content: "Sorry, an internal error occurred and we were unable to add the patient to the trial." +
                            "\n\nPlease contact support for assistance."
                    }).open();
                }
            });
        });
    </script>
<?php } ?>

<?php
    $patientsID = [];
foreach ($patients->getData() as $i => $SearchPatient) {
    $patientsID[] = $SearchPatient->id;
}
    $assetPath = Yii::app()->assetManager->publish(Yii::getPathOfAlias('application.assets'), true, -1);
    Yii::app()->clientScript->registerScriptFile($assetPath . '/js/toggle-section.js');
    $assetManager = Yii::app()->getAssetManager();
    Yii::app()->clientScript->registerScriptFile(Yii::app()->assetManager->getPublishedPathOfAlias('application.widgets.js') . '/PatientPanelPopupMulti.js');
?>

<script type="text/javascript">
    $(document).ready(function () {
        let ids = <?= json_encode($patientsID)?>;
        if (ids[0]) {
            $.ajax({
                'type': "POST",
                'data': "patientsID=" + ids + "&YII_CSRF_TOKEN=" + YII_CSRF_TOKEN,
                'url': "/OECaseSearch/caseSearch/renderPopups",
                success: function (resp) {
                    $("body.open-eyes.oe-grid").append(resp);
                }
            });
            $('body').on('click', '.collapse-data-header-icon', function () {
                $(this).toggleClass('collapse expand');
                $(this).next('div').toggle();
            });
        }
    });
</script>
