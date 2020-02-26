<?php
/* @var $this CaseSearchController
 * @var $fixedParams array
 * @var $params array
 * @var $paramList array
 * @var $patients CActiveDataProvider
 * @var $trial Trial
 * @var $form CActiveForm
 */
$this->pageTitle = 'Case Search';
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
            endforeach; ?>
            </tbody>
        </table>
        <div class="row align-right">
            <button id="add-to-advanced-search-filters" class="button hint green thin js-add-select-btn" data-popup="add-to-advanced-search-filters">
                <i class="oe-i plus pro-theme"></i>
            </button>
        </div>
        <hr class="divider"/>
        <div class="button-stack">
            <?= CHtml::htmlButton('Search', array('class' => 'cols-full green hint js-search-btn', 'type' => 'submit')) ?>
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
        <?php if ($patients->itemCount > 0) :
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
                )
            );
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
            ?>
        <div class="table-sort-order">
            <div class="sort-by">
                Sort by:
                <span class="sort-options">
                    <span class="direction">
                        <label class="inline highlight">
                            <?= CHtml::radioButton('sort-options', true, array('value' => 'ascend'))?>
                            <i class="oe-i direction-up medium"></i>
                        </label>
                        <label class="inline highlight">
                            <?= CHtml::radioButton('sort-options', false, array('value' => 'descend'))?>
                            <i class="oe-i direction-down medium"></i>
                        </label>
                    </span>
                </span>
            </div>
            <?php $pager->run()?>
        </div>
            <table id="case-search-results" class="standard last-right">
                <tbody>
                <?= $searchResults->renderItems() ?>
                </tbody>
                <tfoot><tr><td colspan="3"><?php $pager->run()?></td></tr></tfoot>
            </table>
        <?php endif; ?>
    </main>
</div>

<script type="text/javascript">
    function addPatientToTrial(patient_id, trial_id) {
        var addSelector = '#add-to-trial-link-' + patient_id;
        var removeSelector = '#remove-from-trial-link-' + patient_id;
        $.ajax({
            url: '<?php echo Yii::app()->createUrl('/OETrial/trial/addPatient'); ?>',
            data: {id: trial_id, patient_id: patient_id, YII_CSRF_TOKEN: $('input[name="YII_CSRF_TOKEN"]').val()},
            type: 'POST',
            success: function (response) {
                $(addSelector).hide();
                $(removeSelector).show();
                $(removeSelector).parent('.result').css('background-color', '#fafad2');
            },
            error: function (response) {
                new OpenEyes.UI.Dialog.Alert({
                    content: "Sorry, an internal error occurred and we were unable to add the patient to the trial.\n\nPlease contact support for assistance."
                }).open();
            },
        });
    }

    function removePatientFromTrial(patient_id, trial_id) {
        var addSelector = '#add-to-trial-link-' + patient_id;
        var removeSelector = '#remove-from-trial-link-' + patient_id;
        $.ajax({
            url: '<?php echo Yii::app()->createUrl('/OETrial/trial/removePatient'); ?>',
            data: {id: trial_id, patient_id: patient_id, YII_CSRF_TOKEN: $('input[name="YII_CSRF_TOKEN"]').val()},
            type: 'POST',
            success: function (response) {
                $(removeSelector).hide();
                $(addSelector).show();
                $(addSelector).parent('.result').css('background-color', '#fafafa');
            },
            error: function (response) {
                new OpenEyes.UI.Dialog.Alert({
                    content: "Sorry, an internal error occurred and we were unable to remove the patient from the trial.\n\nPlease contact support for assistance."
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
      var id_max = -1;
      $('#param-list tbody tr').each(function () {
        if ($(this)[0].id > id_max){
          id_max = $(this)[0].id;
        }
      });
      return id_max;
    }


    $(document).ready(function () {
        //null coalesce the id of the last parameter
        var parameter_id_counter = getMaxId();
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
                            $('#param-list tbody').append(response);
                        }
                    });
                });
            }
        });

        $('.oe-full-side-panel').on('click', '#param-list tbody td .remove-circle', function () {
            this.closest('tr').remove();
        });

        $('#clear-search').click(function () {
            $.ajax({
                url: '<?php echo $this->createUrl('caseSearch/clear')?>',
                type: 'GET',
                success: function () {
                    $('#case-search-results').children().remove();
                    $('#param-list tbody').children().remove();
                }
            });
        });
    });
</script>

<?php if ($this->trialContext) { ?>
  <script type="text/javascript">

    $(document).on('click', '.js-add-to-trial', function () {
      var addLink = this;
      var $removeLink = $(this).closest('.js-add-remove-participant').find('.js-remove-from-trial');
      var trialShortlist = parseInt($(this).closest('.js-oe-patient').find('.trial-shortlist').contents().filter(function() {return this.nodeType == Node.TEXT_NODE;}).text());
      var trialShortListElement = $(this).closest('.js-oe-patient').find('.trial-shortlist');
      var patientId = $(this).closest('.js-oe-patient').data('patient-id');

      $.ajax({
        url: '<?php echo Yii::app()->createUrl('/OETrial/trial/addPatient'); ?>',
        data: {
          id: <?= $this->trialContext->id?>,
          patient_id: patientId,
        },
        success: function (response) {
          $(addLink).hide();
          trialShortlist += 1;
          trialShortListElement.text(' ' + trialShortlist);
          trialShortListElement.prepend('<em>Shortlisted</em>');
          trialShortListElement.show();

        },
        error: function (response) {
          new OpenEyes.UI.Dialog.Alert({
            content: "Sorry, an internal error occurred and we were unable to add the patient to the trial.\n\nPlease contact support for assistance."
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

