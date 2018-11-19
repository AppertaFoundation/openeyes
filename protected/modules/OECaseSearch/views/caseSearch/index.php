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
<div class="oe-full-header">
    <div class="title wordcaps">
        <?= $this->trialContext === null ?
            'Advanced Search' :
            'Adding Participants to Trial: ' . $this->trialContext->name;
        ?>
    </div>
</div>
<div class="oe-grid oe-full-content pro-theme" style="height: 100%; width: 100%">
    <nav class="oe-full-side-panel">
        <h3>Actions</h3>
        <ul>
            <?php if ($this->trialContext):?>
            <li>
                <a href="/OETrial/trial/view/<?=$this->trialContext->id?>">Back to Trial</a>
            </li>
            <?php endif;?>
        </ul>
    </nav>
    <main class="oe-full-main">
        <div class="main-event" style="padding-top: 0px">
            <div class="element">
                <div>
                    <?php $form = $this->beginWidget('CActiveForm', array('id' => 'search-form')); ?>
                    <div class="sub-element">
                        <table id="param-list" class="cols-full">
                            <tbody>
                            <?php
                            if (isset($params)):
                                foreach ($params as $id => $param):?>
                                    <?php $this->renderPartial('parameter_form', array(
                                        'model' => $param,
                                        'id' => $id,
                                    )); ?>
                                <?php endforeach;
                            endif; ?>
                            </tbody>
                        </table>
                        <?php foreach ($fixedParams as $id => $param):
                            $this->renderPartial('fixed_parameter_form', array(
                                'model' => $param,
                                'id' => $id
                            ));
                        endforeach; ?>
                    </div>
                </div>
                <div class="sub-element">
                    <div class="new-param row field-row">
                        <div class="cols-3 column">
                            <?php echo CHtml::dropDownList(
                                'Add Parameter: ',
                                null,
                                $paramList,
                                array('empty' => '- Add a parameter -', 'id' => 'js-add-param'));
                            ?>
                        </div>
                    </div>
                    <div class="search-actions flex-layout flex-left">
                        <div class="column">
                            <?php echo CHtml::submitButton('Search', array('class' => 'js-search-btn')); ?>
                        </div>
                        <div class="column end" style="padding-left: 5px">
                            <?php echo CHtml::button('Clear',
                                array('id' => 'clear-search', 'class' => 'button event-action cancel')) ?>
                        </div>
                    </div>
                </div>
                <?php $this->endWidget('search-form'); ?>
            </div>
            <div class="element">
                <?php
                if ($patients->itemCount > 0):
                    //Just create the widget here so we can render it's parts separately
                    /** @var $searchResults CListView */
                    $searchResults =
                        $this->createWidget(
                            'zii.widgets.CListView',
                            array(
                                'dataProvider' => $patients,
                                'itemView' => 'search_results',
                                'emptyText' => 'No patients found',
                                'viewData' => array(
                                    'trial' => $this->trialContext
                                )
                            )
                        );
                    $pager = $this->createWidget('LinkPager',
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
                    <table id="case-search-results" class="cols-10">
                        <tbody class=" cols-full">
                        <?= $searchResults->renderItems(); ?>
                        </tbody>
                        <tfoot><tr><th class="flex-right flex-layout"><?php $pager->run()?></th></tr></tfoot>
                    </table>
                <?php endif; ?>

                </div>
        </div>
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

    function removeParam(elm) {
        $(elm).parents('.parameter').remove();
    }

    function refreshValues(elm) {
        if ($(elm).val() === 'BETWEEN') {
            // Display the two text fields.
            $(elm).closest('.js-case-search-param').find('.single-value').find('input').val('');
            $(elm).closest('.js-case-search-param').find('.dual-value').show();
            $(elm).closest('.js-case-search-param').find('.dual-value').css("display", "inline-block");
            $(elm).closest('.js-case-search-param').find('.single-value').hide();
        }
        else {
            // Display the single text field
            $(elm).closest('.js-case-search-param').find('.dual-value').find('input').val('');
            $(elm).closest('.js-case-search-param').find('.dual-value').hide();
            $(elm).closest('.js-case-search-param').find('.single-value').show();
            $(elm).closest('.js-case-search-param').find('.single-value').css("display", "inline-block");
        }
    }


    $(document).ready(function () {
        //null coallese the id of the last parameter
        var parameter_id_counter = $('.parameter').last().attr('id') || -1;
        $('#js-add-param').on('change', function () {
            var dropDown = this;
            if (!dropDown.value) {
                return;
            }
            parameter_id_counter++;
            $.ajax({
                url: '<?php echo $this->createUrl('caseSearch/addParameter')?>',
                data: {param: dropDown.value, id: parameter_id_counter},
                type: 'GET',
                success: function (response) {
                    $('#param-list tbody').append(response);
                    dropDown.value = '';
                }
            });
        });

        $('#clear-search').click(function () {
            $.ajax({
                url: '<?php echo $this->createUrl('caseSearch/clear')?>',
                type: 'GET',
                success: function () {
                    $('#case-search-results').children().remove();
                    $('#param-list').children().remove();
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
      var patientId = $(this).closest('.js-oe-patient').data('patient-id');

      $.ajax({
        url: '<?php echo Yii::app()->createUrl('/OETrial/trial/addPatient'); ?>',
        data: {
          id: <?= $this->trialContext->id?>,
          patient_id: patientId,
        },
        success: function (response) {
          $(addLink).hide();
          $removeLink.show();
        },
        error: function (response) {
          new OpenEyes.UI.Dialog.Alert({
            content: "Sorry, an internal error occurred and we were unable to add the patient to the trial.\n\nPlease contact support for assistance."
          }).open();
        }
      });
    });

    $(document).on('click', '.js-remove-from-trial', function addPatientToTrial() {
        var removeLink = this;
        var $addLink = $(this).closest('.js-add-remove-participant').find('.js-add-to-trial');
        var patientId = $(this).closest('.js-oe-patient').data('patient-id');

        $.ajax({
          url: '<?php echo Yii::app()->createUrl('/OETrial/trial/removePatient'); ?>',
          data: {
            id: <?= $this->trialContext->id?>,
            patient_id: patientId,
          },
          success: function (response) {
            $(removeLink).hide();
            $addLink.show();
          },
          error: function (response) {
            new OpenEyes.UI.Dialog.Alert({
              content: "Sorry, an internal error occurred and we were unable to remove the patient from the trial.\n\nPlease contact support for assistance."
            }).open();
          }
        });
      }
    );
  </script>
<?php } ?>


<?php
$assetPath = Yii::app()->assetManager->publish(Yii::getPathOfAlias('application.assets'), false, -1);
Yii::app()->clientScript->registerScriptFile($assetPath . '/js/toggle-section.js');
?>

