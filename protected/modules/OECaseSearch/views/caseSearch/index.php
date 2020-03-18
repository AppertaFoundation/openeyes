<?php
/* @var $this CaseSearchController
 * @var $fixedParams array
 * @var $params array
 * @var $paramList array
 * @var $patients CActiveDataProvider
 * @var $trial Trial
 * @var $form CActiveForm
 * @var $saved_searches array
 * @var $user_list array
 * @var $search_label string
 * @var $variables CaseSearchVariable[]
 * @var $variableList CaseSearchVariable[]
 * @var $variableData array
 */
$this->pageTitle = 'Advanced Search';
$sort_field = 'last_name';
$sort_direction = 'ascend';
if (isset($_GET['Patient_sort'])) {
    if (!strpos($_GET['Patient_sort'], '.desc')) {
        $sort_direction = 'ascend';
    } else {
        $sort_direction = 'descend';
    }
    $sort_field = str_replace('.desc', '', $_GET['Patient_sort']);
}

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
            'Search' :
            'Adding Participants to Trial: ' . $this->trialContext->name
        ?>
    </div>
</div>
<div class="oe-full-content subgrid wide-side-panel oe-query-search">
    <nav class="oe-full-side-panel">
        <h3>Custom Search</h3>
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
                    )); ?>
                <?php endforeach;
            endif; ?>
            </tbody>
        </table>
        <div class="flex-layout row">
            <div>
                <button id="load-saved-search">All searches</button>
                <?= CHtml::htmlButton(
                    'Save',
                    array(
                        'class' => 'js-save-search-dialog-btn',
                    )
                ) ?>
            </div>
            <button id="add-to-advanced-search-filters" class="button hint green js-add-select-btn"
                    data-popup="add-to-search-queries">Add Query</button>
        </div>
        <input type="hidden" name="YII_CSRF_TOKEN" value="<?php echo Yii::app()->request->csrfToken ?>"/>
        <hr class="divider"/>
        <h3>Variables</h3>
        <table id="js-variable-table" class="standard normal-text last-right">
            <tbody>
                <?php foreach ($variables as $variable) { ?>
                    <tr class="search-var" data-id="<?= $variable->field_name ?>">
                        <td>
                            <?= $variable->label ?>
                        </td>
                        <td>
                            <i class="oe-i remove-circle small"></i>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
        <?= CHtml::hiddenField('variable_list', implode(',', array_map(
                static function ($item) {
                    return $item->field_name;
                }, $variables)), array('id' => 'js-variable-list')) ?>
        <button id="add-variable" class="button hint green js-add-select-btn"
                data-popup="add-to-variable-list">
            <i class="oe-i plus pro-theme"></i>
        </button>
        <hr class="divider"/>
        <h3>Date range</h3>
        <div class="flex-layout">
            <input type="text" name="from_date" placeholder="from" class="date datepicker-from"/>
            <input type="text" name="to_date" class="date datepicker-to" placeholder="to"/>
            <label class="inline highlight ">
                <input value="All available dates" name="show-all-datas" type="checkbox" checked=""> All available dates
            </label>
        </div>
        <hr class="divider"/>
        <div class="button-stack">
            <?= CHtml::htmlButton('Search', array('class' => 'cols-full green hint js-search-btn', 'type' => 'submit')) ?>
            <?= CHtml::htmlButton('Clear all filters', array('id' => 'clear-search', 'class' => 'cols-full')) ?>
            <?= CHtml::htmlButton('Download CSV BASIC', array('id' => 'download-basic-csv', 'class' => 'cols-full')) ?>
            <?= CHtml::htmlButton('Download CSV Advanced', array('id' => 'download-advanced-csv', 'class' => 'cols-full')) ?>
            <?php if ($this->trialContext) : ?>
                <button class="cols-full" onclick="backToTrial()">Back to Trial</button>
            <?php endif; ?>
        </div>
        <?php $this->endWidget('search-form'); ?>
    </nav>
    <main class="oe-full-main">
        <?php if ($patients->itemCount > 0) { ?>
        <?php $this->widget('CaseSearchPlot', array(
                    'variable_data' => $variableData,
                    'variables' => $variables,
                    'total_patients' => $patients->totalItemCount,
                    'list_selector' => '.oe-search-results'
                ));
        ?>
        <div class="oe-search-results" style="display: none;">
            <?php
                //Just create the widget here so we can render it's parts separately
                /** @var $searchResults CListView */
                $searchResults = $this->createWidget(
                    'zii.widgets.CListView',
                    array(
                        'dataProvider' => $patients,
                        'itemView' => 'search_results',
                        'emptyText' => 'No patients found',
                        'viewData' => array(
                            'trial' => $this->trialContext
                        ),
                        'enableSorting' => true,
                        'sortableAttributes' => array(
                            'last_name',
                            'first_name',
                            'age',
                            'gender',
                        )
                    )
                );
                $sort = $patients->getSort();
                /**
                 * @var $pager LinkPager
                 */
                $pager = $this->createWidget(
                    'LinkPager',
                    array(
                        'pages' => $patients->getPagination(),
                        'maxButtonCount' => 15,
                        'cssFile' => false,
                        'nextPageCssClass' => 'oe-i arrow-right-bold medium pad',
                        'previousPageCssClass' => 'oe-i arrow-left-bold medium pad',
                        'htmlOptions' => array(
                            'class' => 'pagination',
                        ),
                    )
                );
                // Build up the list of sort fields and the relevant ascending/descending sort URLs for each option.
                $sort_fields = array();
                $sort_field_options = array();
                foreach ($sort->attributes as $key => $attribute) {
                    $sort_fields[$key] = $attribute['label'];
                    $sort_field_options[$key]['data-sort-ascend'] = $sort->createUrl($this, array($key => $sort::SORT_ASC));
                    $sort_field_options[$key]['data-sort-descend'] = $sort->createUrl($this, array($key => $sort::SORT_DESC));
                }
                ?>
                <div class="table-sort-order">
                    <div class="sort-by">
                        Sort by:
                        <span class="sort-options">
                        <?= CHtml::dropDownList('sort', $sort_field, $sort_fields, array('id' => 'sort-field', 'options' => $sort_field_options)) ?>
                        <span class="direction">
                            <label class="inline highlight">
                                <?= CHtml::radioButton('sort-options', ($sort_direction === 'ascend'), array('value' => 'ascend')) ?>
                                <i class="oe-i direction-up medium"></i>
                            </label>
                            <label class="inline highlight">
                                <?= CHtml::radioButton('sort-options', ($sort_direction === 'descend'), array('value' => 'descend')) ?>
                                <i class="oe-i direction-down medium"></i>
                            </label>
                        </span>
                    </span>
                    </div>
                    <?php $pager->run(); ?>
                </div>
                <table id="case-search-results" class="standard last-right">
                    <tbody>
                    <?= $searchResults->renderItems() ?>
                    </tbody>
                    <tfoot>
                    <tr>
                        <td colspan="3"><?php $pager->run(); ?></td>
                    </tr>
                    </tfoot>
                </table>
            <?php } ?>
        </div>
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
            <h3>Variables</h3>
            <table>
                {{{variableTable}}}
            </table>
            <input type="hidden" name="variable_list" value="{{variableList}}"/>
        </div>
        <div class="show-query">
            <h3>Save search as</h3>
            <?= CHtml::textField('search_name', $search_label, array('placeholder' => 'Search name description', 'class' => 'cols-full')) ?>
            <div class="row align-right">
                <?= CHtml::htmlButton(
                    'Save search',
                    array(
                        'class' => 'js-save-search-btn hint green',
                        'type' => 'submit',
                        'formaction' => $this->createUrl('caseSearch/saveSearch')
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
            <table class="searches">
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
            <h3>Current variables</h3>
            <table class="var-list">
                <tbody></tbody>
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
            if ($(this)[0].id > id_max) {
                id_max = $(this)[0].id;
            }
        });
        return id_max;
    }

    function performSort(field) {
        let $field = $('#sort-field option[value=' + field + ']');
        let direction = $("input[name='sort-options']").filter("input[checked='checked']").val();
        if (direction === 'ascend') {
            window.location.href = $($field).data('sort-ascend');
        } else if (direction === 'descend') {
            window.location.href = $($field).data('sort-descend');
        }
    }


    $(document).ready(function () {
        //null coalesce the id of the last parameter
        let parameter_id_counter = getMaxId();
        new OpenEyes.UI.AdderDialog({
            itemSets: [
                new OpenEyes.UI.AdderDialog.ItemSet(
                    <?= json_encode(array_map(
                            static function ($item) {
                                return array('id' => $item['id'], 'label' => $item['label']);
                            }, $variableList)) ?>,
                    {'multiSelect': true, 'id': 'variable-type-list', 'deselectOnReturn': true,}
                ),
            ],
            openButton: $('#add-variable'),
            onReturn: function (dialog, selectedValues) {
                let $variableList = $('#js-variable-list');
                $('#js-variable-table tbody').empty();
                $variableList.val('');
                // Add a row to the table for each selected variable, and add each one to the hidden field as a CSV list.
                $.each(selectedValues, function(index, item) {
                    $('#js-variable-table tbody').append('<tr class="search-variable" data-id="' + item.id + '"><td>' + item.label + '</td><td><i class="oe-i remove-circle small"></i></td></tr>');
                    if ($variableList.val()) {
                        $variableList.val($variableList.val() + ',' + item.id);
                    } else {
                        $variableList.val(item.id);
                    }
                });
            }
        });
        new OpenEyes.UI.AdderDialog.QuerySearchDialog({
            itemSets: [
                new OpenEyes.UI.AdderDialog.ItemSet(
                    <?= CJSON::encode($paramList) ?>,
                    {'multiSelect': false, 'id': 'param-type-list', 'deselectOnReturn': true,}
                ),
            ],
            openButton: $('#add-to-advanced-search-filters'),
            onReturn: function (dialog, selectedValues) {
                let operator = null;
                let value = null;
                let valueList = [];
                let type = null;
                $.each(selectedValues, function(index, item) {
                    switch (item.type) {
                        case 'operator':
                            operator = item.id;
                            break;
                        case 'number':
                            if (value) {
                                // Second digit, so multiply the existing value by 10 first before adding the next number.
                                value = (value * 10) + item.id;
                            } else {
                                // First digit.
                                value = item.id
                            }
                            break;
                        case 'lookup':
                            // Selected value
                            valueList.push({id: item.id, field: item.field});
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
                    value: value || valueList,
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
                        $('#param-list tbody').append(response);
                    }
                });
            }
        });

        $('.oe-full-side-panel').on('click', '#param-list tbody td .remove-circle', function () {
            this.closest('tr').remove();
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

        $('.js-save-search-dialog-btn').click(function () {
            new OpenEyes.UI.Dialog.SaveSearch({
                id: 'save-search-dialog',
                title: 'Save search'
            }).open();
        });

        $('#sort-field').change(function () {
            let value = $('#sort-field').val();
            performSort(value)
        });

        $("input[name='sort-options']").change(function () {
            let value = $('#sort-field').val();
            performSort(value);
        });

        $('#js-variable-table i.remove-circle').click(function() {
            let newList = '';
            $(this).closest('tr').remove();
            $.each($('#js-variable-table tr'), function(index, item) {
                if (index !== 0) {
                    newList = newList + ',';
                }
                newList = newList + $(item).data('id');
            });
            $('input[name="variable_list"]').val(newList);
        });

        $('#clear-search').click(function () {
            $.ajax({
                url: '<?php echo $this->createUrl('caseSearch/clear')?>',
                type: 'GET',
                success: function () {
                    $('#case-search-results').children().remove();
                    $('#param-list tbody tr.parameter').remove();
                },
                error: function () {
                    new OpenEyes.UI.Dialog.Alert({
                        content: 'Unable to clear search results.'
                    }).open();
                }
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
$assetPath = Yii::app()->assetManager->publish(Yii::getPathOfAlias('application.assets'), true, -1);
Yii::app()->clientScript->registerScriptFile($assetPath . '/js/toggle-section.js');
?>

