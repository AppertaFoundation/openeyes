<?php

$is_step_instance = $step instanceof PathwayStep;
$is_requested = (int)$step->status === PathwayStep::STEP_REQUESTED;
$is_config = (int)$step->status === PathwayStep::STEP_CONFIG;

$subspecialty = Subspecialty::model()->findByPk($step->getState('subspecialty_id'));
$service_firm = Firm::model()->findByPk($step->getState('service_id'));
$context_firm = Firm::model()->findByPk($step->getState('firm_id'));
$elementset = \OEModule\OphCiExamination\models\OphCiExamination_ElementSet::model()->findByPk($step->getState('workflow_step_id'));

// Deal with legacy data by showing using either the service firm for an existing episode
// or choosing a default iwth find (this mirrors the existing behaviour in modules/OphCiExamination/components/PathstepObserver).
// This entire behaviour of looking for an episode and choosing a default if no episode is found mirrors the priority of choice
// in the Worklist controller.
$episode = null;

if (!$service_firm && $subspecialty && isset($patient)) {
    $episode = $patient->getOpenEpisodeOfSubspecialty($subspecialty->id);

    $service_firm = $episode ? $episode->firm : Firm::getDefaultServiceFirmForSubspecialty($subspecialty);
}

if ($is_step_instance) {
    $is_last_step = $step->isLastRequestedStep();
    $is_first_requested_step = $step->isFirstRequestedStep();
} else {
    $is_last_step = $step->id === $step->pathway_type->default_steps[count($step->pathway_type->default_steps) - 1]->id;
    $is_first_requested_step = $step->id === $step->pathway_type->default_steps[0]->id;
}

?>

<div class="slide-open">
    <?php if ($is_step_instance) { ?>
        <div class="patient">
            <?= strtoupper($patient->last_name) . ', ' . $patient->first_name . ' (' . $patient->title . ')' ?>
        </div>
    <?php } ?>
    <h3 class="title"><?= $step->long_name ?></h3>
    <div class="step-content">
        <p>added by <b><?= $step->created_user->getFullName() ?></b></p>
        <form class="js-examination-change-form">
            <table>
                <tbody>
                    <colgroup>
                        <col class="cols-5">
                        <col class="cols-7">
                    </colgroup>
                    <tr>
                        <th>Service</th>
                        <td>
                            <?php
                            if ($is_config) {
                                if ($subspecialty) {
                                    $episode = $episode ?? $patient->getOpenEpisodeOfSubspecialty($subspecialty->id);

                                    if (!$episode) {
                                        $service_firms = Firm::model()->with('serviceSubspecialtyAssignment')
                                                                      ->findAll('can_own_an_episode = 1 AND subspecialty_id = :subspecialty AND institution_id = :institution',
                                                                                [':subspecialty' => $subspecialty->id, ':institution' => Yii::app()->session->getSelectedInstitution()->id]);

                                        echo CHtml::dropDownList('service_id', $service_firm->id ?? null, CHtml::listData($service_firms, 'id', 'name'),
                                                                 ['class' => 'cols-12 js-examination-change-service']);
                                    } else {
                                        echo CHtml::dropDownList('service_id', $episode->firm->id, [$episode->firm->id => $episode->firm->name], ['class' => 'cols-12 js-examination-change-service']);
                                    }
                                } else {
                                    echo CHtml::dropDownList('service_id', null, [], ['class' => 'cols-12 js-examination-change-service', 'prompt' => 'Subspecialty unassigned', 'disabled' => true]);
                                }
                            } else {
                                echo $service_firm->name ?? 'Unassigned';
                            } ?>
                        </td>
                    <tr>
                        <th>Context</th>
                        <td>
                            <?php
                            if ($is_config) {
                                if ($subspecialty) {
                                    $context_firms = Firm::model()->getList(Yii::app()->session['selected_institution_id'], $subspecialty->id, null, true);

                                    echo CHtml::dropDownList('firm_id', $context_firm->id ?? null, $context_firms, ['class' => 'cols-12 js-examination-change-context']);
                                } else {
                                    echo CHtml::dropDownList('firm_id', null, [], ['class' => 'cols-12 js-examination-change-context', 'prompt' => 'Subspecialty unassigned', 'disabled' => true]);
                                }
                            } else {
                                echo $context_firm->name ?? 'Unassigned';
                            }
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <th>Subspecialty</th>
                        <td>
                            <?php if ($is_config) {
                                $subspecialties = Subspecialty::model()->getList();

                                echo CHtml::dropDownList('subspecialty_id', $subspecialty->id ?? null, $subspecialties, ['class' => 'cols-12 js-examination-change-subspecialty']);
                            } else {
                                echo $subspecialty->name ?? 'Unassigned';
                            } ?>
                        </td>
                    </tr>
                    <tr>
                        <th>Workflow Step</th>
                        <td>
                            <?php if ($is_config) {
                                $workflow_steps = [];

                                if ($context_firm) {
                                    $workflow = \OEModule\OphCiExamination\models\OphCiExamination_Workflow_Rule::model()->findWorkflowCascading($context_firm->id, null);
                                    $workflow_steps = CHtml::listData($workflow->steps, 'id', 'name');
                                } elseif ($elementset) {
                                    $workflow_steps = [$elementset->id => $elementset->name];
                                }

                                echo CHtml::dropDownList('workflow_step_id', $elementset->id ?? null, $workflow_steps, ['class' => 'cols-12 js-examination-change-workflow-step', 'empty' => 'None']);
                            } else {
                                echo $elementset->name ?? 'None';
                            } ?>
                        </td>
                    </tr>
                </tbody>
            </table>
        </form>
    </div>
    <div class="step-comments">
        <?php if (isset($worklist_patient) && !$partial) { ?>
            <div class="flex js-comments-edit" style="<?= $step instanceof PathwayStep && $step->comment ? 'display: none;' : '' ?>">
                <div class="cols-11">
                    <input class="cols-full js-step-comments" type="text" maxlength="80" placeholder="Comments"
                    <?= $step instanceof PathwayStep && $step->comment ? 'value="' . $step->comment->comment . '"' : '' ?>/>
                    <div class="character-counter">
                        <span class="percent-bar"
                              style="width: <?= $step instanceof PathwayStep && $step->comment ? strlen($step->comment->comment) / 0.8 : 0 ?>%;"></span>
                    </div>
                </div>
                <i class="oe-i save-plus js-save"></i>
            </div>
        <?php } ?>
            <?php if ($is_step_instance) { ?>
            <div class="flex js-comments-view" style="<?= !$step->comment ? 'display: none;' : '' ?>">
                <div class="cols-11">
                    <i class="oe-i comments small pad-right no-click"></i>
                    <em class="comment"><?= $step->comment->comment ?? '' ?></em>
                </div>
                <?php if (!$partial && (int)$step->status !== PathwayStep::STEP_COMPLETED) { ?>
                    <i class="oe-i medium-icon pencil js-edit"></i>
                <?php } ?>
            </div>
            <?php } ?>
    </div>
    <?php if (!$partial) { ?>
        <div class="step-actions">
            <?php if (isset($worklist_patient)) { ?>
                <button class="green hint <?= $is_config ? 'js-change-examination' : 'js-ps-popup-btn' ?>"
                        data-action="next"<?= (int)$step->status === PathwayStep::STEP_COMPLETED ? 'style="display: none;"' : '' ?>>
                    <?php if ((int)$step->status === PathwayStep::STEP_CONFIG) {
                        echo 'Set options';
                    } else {
                        echo (int)$step->status === PathwayStep::STEP_STARTED ? 'Complete' : 'Start';
                    } ?>
                </button>
                <button class="blue hint js-ps-popup-btn"
                        data-action="prev" <?= $is_config ? 'style="display: none;"' : '' ?>>
                    <?php if ((int)$step->status === PathwayStep::STEP_COMPLETED) {
                        echo 'Undo complete';
                    } elseif ((int)$step->status === PathwayStep::STEP_STARTED) {
                        echo 'Cancel';
                    } else {
                        echo 'Change';
                    } ?>
                </button>
            <?php } ?>
            <?php if ($is_requested) { ?>
                <button class="blue i-btn left hint js-ps-popup-btn" data-action="left"<?= $is_first_requested_step ? ' disabled' : ''?>></button>
                <button class="blue i-btn right hint js-ps-popup-btn" data-action="right"<?= $is_last_step ? ' disabled' : ''?>></button>
                <button class="red i-btn trash hint js-ps-popup-btn" data-action="remove"></button>
            <?php } ?>
        </div>
    <?php } ?>
</div>
<?php if ($is_config) { ?>
<script>
    $(document).ready(function () {
        function appendOption(idNamePair, into) {
            const option = document.createElement("option");

            option.value = idNamePair.id;
            option.text = idNamePair.name;

            into.append(option);
        }

        function updateWorkflowSteps() {
            const workflow_steps = JSON.parse(pathwaySetupData.workflows);
            const workflow_steps_list = $('.js-examination-change-workflow-step');
            const chosen_context = $('.js-examination-change-context').val();

            workflow_steps_list.children('option[value != ""]').remove();

            if (chosen_context) {
                workflow_steps[chosen_context].forEach(element => appendOption(element, workflow_steps_list));
            }
        }

        $('.js-examination-change-subspecialty').on('change', function () {

            const subspecialty_id = $(this).val();
            const subspecialty = pathwaySetupData.subspecialties.find(i => i.id === String(subspecialty_id));

            $.ajax('/patientEvent/hasServiceFirmForSubspecialty', {
                method: 'GET',
                data: {
                    'patient_id': <?= (int)$patient->id ?>,
                    'subspecialty_id': subspecialty_id,
                },
                success: function (response) {
                    const services_list = $('.js-examination-change-service');
                    const contexts_list = $('.js-examination-change-context');

                    response = JSON.parse(response);

                    services_list.attr('disabled', false);
                    contexts_list.attr('disabled', false);

                    services_list.children().remove();
                    contexts_list.children().remove();

                    if (response !== null) {
                        const only_service = subspecialty.services.find(i => i.id === String(response));

                        appendOption(only_service, services_list);
                    } else {
                        subspecialty.services.forEach(element => appendOption(element, services_list));
                    }

                    subspecialty.contexts.forEach(element => appendOption(element, contexts_list));

                    updateWorkflowSteps();
                }
            });
        });

        $('.js-examination-change-context').on('change', updateWorkflowSteps);
    });
</script>
<?php } ?>
