<?php
/* @var $this PatientController */
?>
<?php
$patient_overview_popup_mode = SettingMetadata::model()->getSetting('patient_overview_popup_mode');
?>

<?php
if (in_array($this->controller->id, ['caseSearch','trial','worklist'])) {
    $this->render('application.modules.OETrial.widgets.views.patient_trial_summary_side', []);
} else { ?>
    <div class="cols-left">
        <div class="subtitle">Trial Participation</div>
        <table class="patient-trials js-patient-trials-table">
            <tbody>
                <?php if (count($this->patient->trials) === 0) { ?>
                <tr class="divider js-patient-trials-empty">
                    <td>No trial participation for this patient</td>
                </tr>
                    <?php
                } else {
                    foreach ($this->patient->trials as $trialPatient) :
                        $coordinators = array_map(
                            static function ($coordinator) {
                                return $coordinator->user->getFullName();
                            },
                            $trialPatient->trial->getTrialStudyCoordinators()
                        );

                        $coordinators = implode(', ', $coordinators);
                        ?>
                <tr class="divider">
                    <td style="display: none"></td>
                    <td colspan="2">
                        <?php if (!is_null($trialPatient->trial->getUserPermission(Yii::app()->user->id)) && (Yii::app()->user->checkAccess('TaskViewTrial'))) {
                            echo CHtml::link(
                                CHtml::encode($trialPatient->trial->name),
                                Yii::app()->controller->createUrl(
                                    '/OETrial/trial/view',
                                    array('id' => $trialPatient->trial_id)
                                )
                            );
                        } else {
                            echo CHtml::encode($trialPatient->trial->name);
                        } ?>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <ul class="dot-list">
                            <li>
                                <div>
                                    <span data-trial-patient-status="<?= $trialPatient->status->name; ?>"><?= $trialPatient->status->name; ?></span>
                                    <?php if (isset($trialPatient->status_update_date)) : ?>
                                    <small class="fade">on</small>
                                        <?= Helper::formatFuzzyDate($trialPatient->status_update_date) ?>
                                    <?php endif; ?>
                                </div>
                            </li>
                            <li><?= $trialPatient->trial->trialType->name ?></li>
                            <li><?= $trialPatient->treatmentType->name ?></li>
                            <li>
                                <div class="flex-l">
                                    <?= $trialPatient->trial->getStartedDateForDisplay() ?>
                                    <i class="oe-i range small pad disabled"></i>
                                    <?= $trialPatient->trial->getClosedDateForDisplay() ?>
                                </div>
                            </li>
                            <li><?= $coordinators ?></li>
                        </ul>
                    </td>
                </tr>
                        <?php
                    endforeach;
                }
                ?>
            </tbody>
        </table>
    </div>
    <div class="cols-right">
        <div class="subtitle">Shortlist patient for Active Trial</div>
        <div class="flex">
            <?php $this->widget('application.widgets.AutoCompleteSearch', ['field_name' => 'select_new_trial_name', 'layoutColumns' => ['outer' => 'full', 'label' => '', 'field' => 'full']]); ?>
        </div>
        <div class="flex">
            <ul class="multi-filter-list js-trial-shortlist-candidates"></ul>
        </div>
        <div class="flex-c small-row"><button class="green hint js-shortlist-patient-btn">Shortlist patient</button></div>
    </div>

    <script type="text/template" id="js-patient-trial-summary-entry-template">
        <tr class="divider">
            <td style="display: none"></td>
            <td colspan="2"><a href="{{name.link}}">{{name.name}}</a></td>
        </tr>
        <tr>
            <td colspan="2">
                <ul class="dot-list">
                    <li>
                        <div>
                            <span data-trial-patient-status={{status-name}}>{{status-name}}</span>
                            {{#status-update-date}}
                            <small class="fade">on</small>
                            {{status-update-date}}
                            {{/status-update-date}}
                        </div>
                    </li>
                    <li>{{trial-type}}</li>
                    <li>{{treatment-type}}</li>
                    <li>
                        <div class="flex-l">
                            {{started-date}}
                            <i class="oe-i range small pad disabled"></i>
                            {{closed-date}}
                        </div>
                    </li>
                    <li>{{coordinators}}</li>
                </ul>
            </td>
        </tr>
    </script>

    <script type="text/javascript" src="<?= Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('application.widgets.js') . '/AutoCompleteSearch.js', true, -1); ?>"></script>

    <script>
        $(document).ready(function() {
            let params = {
                'patient_id': function() { return <?= $this->patient->id ?>; },
                'already_selected_ids': function() {
                    const trial_ids = $('.js-trial-shortlist-candidates li').map(function() { return this.dataset.trialId; }).get();

                    return JSON.stringify(trial_ids);
                }
            };

            OpenEyes.UI.AutoCompleteSearch.init({
                input: $('#select_new_trial_name'),
                url: '/OETrial/trial/trialAutocomplete',
                params: params,
                onSelect: function () {
                    let AutoCompleteResponse = OpenEyes.UI.AutoCompleteSearch.getResponse();

                    $('.js-trial-shortlist-candidates').empty().append(`<li data-trial-id="${AutoCompleteResponse.id}">${AutoCompleteResponse.label}</li>`);

                    $('.js-trial-shortlist-candidates li').off('click').on('click', function() { $(this).remove(); })
                    return false;
                }
            });

            $('.js-shortlist-patient-btn').click(function() {

                <?php if ($this->patient->isDeceased()) { ?>
                    new OpenEyes.UI.Dialog.Confirm({
                        content: "This patient has been deceased. Please confirm if you would like to shortlist this patient to a trial.",
                    }).on('ok', function () {
                        shortlistPatient();
                    }).open();
                <?php } else { ?>
                    shortlistPatient();
                <?php } ?>

            });

            function shortlistPatient() {
                const trial_id = $('.js-trial-shortlist-candidates li').attr('data-trial-id');

                $.get(
                    '/OETrial/trial/addPatient',
                    {
                        patient_id: <?= $this->patient->id ?>,
                        id: trial_id,
                    },
                    function(result) {
                        if (result) {
                            const template = $('#js-patient-trial-summary-entry-template').text();
                            const into = $('.js-patient-trials-table tbody');

                            into.prepend(Mustache.render(template, result));

                            $('.js-patient-trials-empty').remove();
                            $('.js-trial-shortlist-candidates li').remove();
                        }
                    }
                )
                .fail(function(err) {
                    let content = "Something went wrong trying to shortlist the patient, please try again or contact support for assistance.";
                    switch(err.status) {
                        case 403:
                            content = "You are not authorized to shortlist the patient to the target Trial"
                            break;
                        default:
                            // leave blank for now for potential future use.
                            break;
                    }
                    let dlg = new OpenEyes.UI.Dialog.Alert({content});
                    dlg.open();
                });
            }
        });
    </script>
<?php } ?>

