<?php
    $safeguarding_elements = $elements;
?>

<div class="oe-full-header flex-layout">
    <div class="title wordcaps">Safeguarding</div>
</div>
<div class="oe-full-content">
    <div class="cols-9">
        <form action="/Safeguarding/index/">
            <h3>Filter by:</h3>
            <table class="standard">
                <tbody>
                    <tr>
                        <td>
                            Age from:
                            <?= \CHtml::numberField('safeguarding_filters[age_from]', '', array('min'=>'0', 'max'=>'130', 'size'=>'5'))?>
                            to:
                            <?= \CHtml::numberField('safeguarding_filters[age_to]', '', array('min'=>'0', 'max'=>'130', 'size'=>'5'))?>
                            years
                        </td>
                    </tr>
                    <tr id="js-child-fields">
                        <td>
                            <label class="highlight inline">
                                <?= \CHtml::checkBox('safeguarding_filters[has_social_worker]') ?>
                                Has social worker
                            </label>
                            <br>
                            <label class="highlight inline">
                                <?= \CHtml::checkBox('safeguarding_filters[under_protection_plan]') ?>
                                Under protection plan
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            Concern
                            <br>
                            <?=
                            \CHtml::dropDownList(
                                'safeguarding_filters[safeguarding_concern_id]',
                                '',
                                CHtml::listData(
                                    \OEModule\OphCiExamination\models\OphCiExamination_Safeguarding_Concern::model()->findAll(),
                                    'id',
                                    'term'
                                ),
                                array('empty' => '-- Select concern --')
                            );
?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <button class="green hint">Filter</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </form>
    </div>
    <div class="cols-9">
        <table class="standard clickable-rows">
            <colgroup>
                <col span="5">
            </colgroup>

            <thead>
                <tr>
                    <th>Referral Date</th><th>Safeguarding Status</th><th>MRN</th><th>Patient Name</th><th>Date of Birth</th><th>Saved/Assigned by</th>
                </tr>
            </thead>

            <tbody>
                <?php foreach ($safeguarding_elements as $element) { ?>
                    <tr data-event-id=<?= $element->event_id ?>>
                        <?php

                        $event = $element->event;

                        $patient = $event->episode->patient;

                        $date = $element->created_date;
                        $status = $element->outcome ? $element->outcome->term : "New";
                        $MRN =
                            PatientIdentifierHelper::getIdentifierValue(PatientIdentifierHelper::getIdentifierForPatient(
                                'LOCAL',
                                $event->episode->patient->id,
                                $event->institution_id,
                                $event->site_id
                            ));
                        $patient_name = $patient->getFullName();
                        $patient_dob = Helper::convertDate2NHS($patient->dob);
                        $saved_by = $element->createdUser->getFullName();

                        echo "<td>$date</td><td>$status</td><td>$MRN</td><td>$patient_name</td><td>$patient_dob</td><td>$saved_by</td>";
                        ?>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<script type="text/javascript">
    $( ".clickable-rows > tbody > tr" ).on( "click", function() {
        let eventId = $(this).data('event-id');
        window.location.href = `/OphCiExamination/default/view/${eventId}`;
    });

    $("#safeguarding_filters_age_from, #safeguarding_filters_age_to").change(function() {
        let age_from = parseInt($("#safeguarding_filters_age_from").val());
        let age_to = parseInt($("#safeguarding_filters_age_to").val());

        if(age_from > 16) {
            $('#js-child-fields').hide();
        } else {
            $('#js-child-fields').show();
        }

        if(age_from !== '' && age_to !== '') {
            if(age_from > age_to) {
                $("#safeguarding_filters_age_from").val(age_to);
            }
        }
    });
</script>