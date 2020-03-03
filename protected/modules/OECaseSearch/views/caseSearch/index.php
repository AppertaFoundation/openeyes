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
)
?>
<div class="oe-full-header flex-layout">
    <div class="title wordcaps">
        <?= $this->trialContext === null ?
            'Advanced Search' :
            'Adding Participants to Trial: ' . $this->trialContext->name
        ?>
    </div>
</div>
<div class="oe-full-content subgrid wide-side-panel oe-advanced-search">
    <nav class="oe-full-side-panel">
        <h3>Search Filters</h3>
        <?php $form = $this->beginWidget('CActiveForm', array('id' => 'search-form')); ?>
        <table id="param-list" class="standard last-right normal-text">
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
            <?php foreach ($fixedParams as $id => $param) :
                $this->renderPartial('fixed_parameter_form', array(
                    'model' => $param,
                    'id' => $id
                ));
            endforeach;?>
            <tr id="search-label-row">
                <td><?= CHtml::textField('search_name', null, array('placeholder' => 'Search label', 'class' => 'cols-full')) ?></td>
            </tr>
            </tbody>
        </table>
        <div class="row align-right">
            <a href="#" id="load-saved-search">Load saved search</a>
            <button id="add-to-advanced-search-filters" class="button hint green thin js-add-select-btn" data-popup="add-to-advanced-search-filters">
                <i class="oe-i plus pro-theme"></i>
            </button>
        </div>
        <input type="hidden" name="YII_CSRF_TOKEN" value="<?php echo Yii::app()->request->csrfToken ?>"/>
        <hr class="divider"/>
        <div class="button-stack">
            <?= CHtml::htmlButton('Search', array('class' => 'cols-full green hint js-search-btn', 'type' => 'submit')) ?>
            <?= CHtml::htmlButton('Save search', array('class' => 'cols-full green hint js-save-search-btn', 'type' => 'submit', 'formaction' => $this->createUrl('caseSearch/saveSearch'))) ?>
            <?= CHtml::htmlButton('Clear all filters', array('id' => 'clear-search', 'class' => 'cols-full')) ?>
            <?= CHtml::htmlButton('Download CSV BASIC', array('id' => 'download-basic-csv', 'class' => 'cols-full')) ?>
            <?= CHtml::htmlButton('Download CSV Advanced', array('id' => 'download-advanced-csv', 'class' => 'cols-full')) ?>
            <?php if ($this->trialContext) :?>
                <button class="cols-full" onclick="backToTrial()">Back to Trial</button>
            <?php endif;?>
        </div>
        <?php $this->endWidget('search-form'); ?>
    </nav>
    <main class="oe-full-main">
        <?php if ($patients->itemCount > 0) {
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
                            <?= CHtml::radioButton('sort-options', ($sort_direction === 'ascend'), array('value' => 'ascend'))?>
                            <i class="oe-i direction-up medium"></i>
                        </label>
                        <label class="inline highlight">
                            <?= CHtml::radioButton('sort-options', ($sort_direction === 'descend'), array('value' => 'descend'))?>
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
                <tfoot><tr><td colspan="3"><?php $pager->run(); ?></td></tr></tfoot>
            </table>
        <?php } ?>
    </main>
</div>
<script type="text/html" id="load-saved-search-template">
    <table style="width: 100%;">
        <tbody>
        <tr>
            <td style="width: 25%;">
                <h3>My searches</h3>
                <ul id="current-user-search-list">
                    {{#currentUserSearches}}
                    <li data-id="{{id}}">{{name}}</li>
                    {{/currentUserSearches}}
                </ul>
            </td>
            <td style="width: 25%;">
                <h3>Searches by user</h3>
                <ul id="other-user-list">
                    {{#otherUsers}}
                    <li data-id="{{id}}">{{name}}</li>
                    {{/otherUsers}}
                </ul>
            </td>
            <td style="width: 25%;">
                <h3>Selected user search</h3>
                <ul id="other-user-search-list">
                    {{#otherUserSearches}}
                    <li data-id="{{>id}}">{{>name}}</li>
                    {{/otherUserSearches}}
                </ul>
            </td>
            <td style="width: 25%;">
                <h3>Search contents</h3>
                <ul id="search-contents-list">
                    {{#searchContents}}
                    <li>{{.}}</li>
                    {{/searchContents}}
                </ul>
            </td>
        </tr>
        </tbody>
    </table>
    <hr class="divider"/>
    <button id="load-selected-search">Load</button>
</script>
<script type="text/html" id="search-contents-template">
    <ul>
        {{#searchContents}}
        <li>{{.}}</li>
        {{/searchContents}}
    </ul>

</script>

<script type="text/javascript">
    function addPatientToTrial(patient_id, trial_id) {
        const addSelector = '#add-to-trial-link-' + patient_id;
        const removeSelector = '#remove-from-trial-link-' + patient_id;
        $.ajax({
            url: '<?php echo Yii::app()->createUrl('/OETrial/trial/addPatient'); ?>',
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
            url: '<?php echo Yii::app()->createUrl('/OETrial/trial/removePatient'); ?>',
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

    function getMaxId(){
        let id_max = -1;
        $('#param-list tbody tr.parameter').each(function () {
            if ($(this)[0].id > id_max){
                id_max = $(this)[0].id;
            }
        });
        return id_max;
    }

    function performSort(field) {
        let $field = $('#sort-field option[value=' + field +']');
        let direction = $("input[name='sort-options']").filter("input[checked='checked']").val();
        if (direction === 'ascend') {
            window.location.href = $($field).data('sort-ascend');
        } else if (direction === 'descend') {
            window.location.href =  $($field).data('sort-descend');
        }
    }


    $(document).ready(function () {
        //null coalesce the id of the last parameter
        let parameter_id_counter = getMaxId();
        new OpenEyes.UI.AdderDialog({
            itemSets: [
                new OpenEyes.UI.AdderDialog.ItemSet(
                    <?= CJSON::encode($paramList) ?>,
                    {'multiSelect': true, 'id': 'param-type-list', 'deselectOnReturn': true,}
                )
            ],
            openButton: $('#add-to-advanced-search-filters'),
            onReturn: function(dialog, selectedValues) {
                $.each(selectedValues, function(index, item) {
                    parameter_id_counter++;
                    $.ajax({
                        url: '<?= $this->createUrl('caseSearch/addParameter') ?>',
                        data: {
                            param: item.type,
                            id: parameter_id_counter
                        },
                        type: 'GET',
                        success: function (response) {
                            // Append the dynamic parameter HTML before the first fixed parameter.
                            $('#param-list tbody tr.fixed-parameter:first').before(response);
                        }
                    });
                });
            }
        });

        $('.oe-full-side-panel').on('click', '#param-list tbody td .remove-circle', function () {
            this.closest('tr').remove();
        });

        $('#load-saved-search').click(function() {
            var savedSearchDialog = new OpenEyes.UI.Dialog.LoadSavedSearch({
                id: 'load-saved-search-dialog',
                user_id: <?= Yii::app()->user->id ?>,
                user_searches: <?= json_encode($user_searches) ?>,
                users: <?= json_encode($user_list) ?>
            }).open();
        });

        $('#sort-field').change(function() {
            let value = $('#sort-field').val();
            performSort(value)
        });

        $("input[name='sort-options']").change(function() {
            let value = $('#sort-field').val();
            performSort(value);
        });

        $('#clear-search').click(function () {
            $.ajax({
                url: '<?php echo $this->createUrl('caseSearch/clear')?>',
                type: 'GET',
                success: function () {
                    $('#case-search-results').children().remove();
                    $('#param-list tbody tr.parameter').remove();
                },
                error: function() {
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
        const $removeLink = $(this).closest('.js-add-remove-participant').find('.js-remove-from-trial');
        let trialShortlist = parseInt($(this).closest('.js-oe-patient').find('.trial-shortlist').contents().filter(function () {
            return this.nodeType === Node.TEXT_NODE;
        }).text());
        const trialShortListElement = $(this).closest('.js-oe-patient').find('.trial-shortlist');
        const patientId = $(this).closest('.js-oe-patient').data('patient-id');

        $.ajax({
            url: '<?php echo Yii::app()->createUrl('/OETrial/trial/addPatient'); ?>',
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

