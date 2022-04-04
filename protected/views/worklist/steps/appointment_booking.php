<?php
/**
 * @var $step PathwayStep|PathwayTypeStep
 * @var $patient Patient
 */

$is_step_instance = $step instanceof PathwayStep;
$is_requested = (int)$step->status === PathwayStep::STEP_REQUESTED;
$is_config = (int)$step->status === PathwayStep::STEP_CONFIG;

$selected_site = Site::model()->findByPk($step->getState('site_id'));
$selected_service = Subspecialty::model()->findByPk($step->getState('service_id'));
$selected_context = Firm::model()->findByPk($step->getState('firm_id'));
$selected_duration_value = ($step->getState('duration_value') || $step->getState('duration_value') != 0) ? $step->getState('duration_value') : 'N/A';
$selected_duration_period = ($step->getState('duration_value') !== 'N/A') ? $step->getState('duration_period') : ''; //No point in showing period if value has not been selected at all

$sites_list = Site::model()->getListForCurrentInstitution('name');
$services_list = CHtml::listData(Subspecialty::model()->with(['serviceSubspecialtyAssignment' => ['with' => 'firms']])->findAll('firms.active = 1'), 'id', 'name');
$contexts_list = Firm::model()->getList(Yii::app()->session['selected_institution_id'], $selected_service ?$selected_service->id : null);
$structured_list = json_encode(NewEventDialogHelper::structureAllSubspecialties(), JSON_THROW_ON_ERROR);
$duration_values = array_combine(range(1, 18), range(1, 18));
$duration_period = [
    'days' => 'days',
    'weeks' => 'weeks',
    'months' => 'months',
    'years' => 'years',
];
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
        <form id="appointment-booking-form">
            <table>
                <tbody>
                <colgroup>
                    <col class="cols-3">
                    <col class="cols-9">
                </colgroup>
                <tr>
                    <th>Site</th>
                    <td>
                        <?php if ($is_config) {
                            echo CHtml::dropDownList('site_id', $selected_site->id ?? null, $sites_list, ['class' => 'cols-8', 'empty' => '- Select Site -']);
                        } else {
                            echo $selected_site->name ?? 'N/A';
                        } ?>
                    </td>
                </tr>
                <tr>
                    <th>Service</th>
                    <td>
                        <?php if ($is_config) {
                            echo CHtml::dropDownList('service_id', $selected_service->id ?? null, $services_list,
                                ['class' => 'cols-8 js-booking-service', 'empty' => '- Select Service -']);
                        } else {
                            echo $selected_service->name ?? 'N/A';
                        } ?>
                    </td>
                </tr>
                <tr>
                    <th>Context</th>
                    <td>
                        <?php if ($is_config) {
                            echo CHtml::dropDownList('firm_id', $selected_context->id ?? null, $contexts_list,
                                ['class' => 'cols-8 js-booking-firm', 'empty' => '- Select Context -']);
                        } else {
                            echo $selected_context->name ?? 'N/A';
                        } ?>
                    </td>
                </tr>
                <tr>
                    <th>Duration</th>
                    <td>
                        <?php if ($is_config) {
                            echo CHtml::dropDownList('duration_value', $selected_duration_value, $duration_values,
                                ['class' => 'cols-3', 'empty' => 'Time']);
                            echo CHtml::dropDownList('duration_period', $selected_duration_period, $duration_period,
                                ['class' => 'cols-5', 'empty' => 'Duration']);
                        } else {
                            echo $selected_duration_value . ' ' . $selected_duration_period;
                        } ?>
                    </td>
                </tr>
                </tbody>
            </table>
        </form>
    </div>
    <?php if (isset($worklist_patient)) {
        $this->renderPartial(
            'step_components/_comment',
            array(
                'partial' => $partial,
                'model' => $step,
                'pathway' => $pathway,
            )
        );
    } ?>
    <?php if (!$partial) { ?>
        <div class="step-actions">
            <?php if (isset($worklist_patient)) { ?>
                <button class="green hint <?= $is_config ? 'js-change-book-appointment' : 'js-ps-popup-btn' ?>"
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
                <button class="blue i-btn left hint js-ps-popup-btn" data-action="left"></button>
                <button class="blue i-btn right hint js-ps-popup-btn" data-action="right"></button>
                <button class="red i-btn trash hint js-ps-popup-btn" data-action="remove"></button>
            <?php } ?>
        </div>
    <?php } ?>
    <?php if ($is_step_instance) { ?>
        <div class="step-status <?= $step->getStatusString() ?>">
            <?php switch ((int)$step->status) {
                case PathwayStep::STEP_STARTED:
                    echo 'Currently active';
                    break;
                case PathwayStep::STEP_COMPLETED:
                    echo 'Completed';
                    break;
                default:
                    echo 'Waiting to be done';
                    break;
            } ?>
        </div>
    <?php } ?>
</div>
<?php if (!$partial) { ?>
    <div class="close-icon-btn">
        <i class="oe-i remove-circle medium-icon"></i>
    </div>
<?php } ?>
<script>
    $(document).ready(
        $('.js-booking-service').on('change', function () {
            $('.js-booking-firm').empty();
            let service_id = $(this).val();
            let subspecialties = <?= $structured_list ?>;
            let contexts = subspecialties.find(i => i.id === String(service_id)).contexts;
            let contextList = [];
            contexts.forEach(element => {
                var option = document.createElement("option");
                option.value = element.id;
                option.text = element.name;
                $('.js-booking-firm').append(option);
            });
        })
    )
</script>
