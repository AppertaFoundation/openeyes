<?php
/**
 * @var $booking_id int
 * @var $cataract_opnote ElementType
 */
$complexity_colour = 'green';
$aconst = ((float)$data->aconst === (int)$data->aconst ? (float)$data->aconst . '.0' : (float)$data->aconst);

$procedureTerms = [];
$procedureShortTerms = [];
foreach ($data->procedure_assignments as $procedure_assignment) {
    $procedureTerms[] = $procedure_assignment->proc->term;
    $procedureShortTerms[] = $procedure_assignment->proc->short_format;
}

$longText = implode(', ', $procedureTerms);
// short text should be a maximum of 100 characters, so in case it is bigger than that get first 100 characters.
$shortText = substr(implode(', ', $procedureShortTerms), 0, 100);

switch ($data->complexity) {
    case Element_OphTrOperationbooking_Operation::COMPLEXITY_LOW:
        $complexity_colour = 'green';
        break;
    case Element_OphTrOperationbooking_Operation::COMPLEXITY_MEDIUM:
        $complexity_colour = 'orange';
        break;
    case Element_OphTrOperationbooking_Operation::COMPLEXITY_HIGH:
        $complexity_colour = 'red';
        break;
}

$is_deleted = ((int)$data->booking->status->id === OphTrOperationbooking_Operation_Status::STATUS_COMPLETED
    || (int)$data->booking->status->id === OphTrOperationbooking_Operation_Status::STATUS_CANCELLED);

$institution = Institution::model()->getCurrent();
$event = Event::model()->findByPk($data->event_id);
$display_primary_number_usage_code = Yii::app()->params['display_primary_number_usage_code'];
$primary_identifier = PatientIdentifierHelper::getIdentifierForPatient($display_primary_number_usage_code, $event->episode->patient->id, $institution->id, Yii::app()->session['selected_site_id']);

$cataract_card_list = array(
    'Patient' => array(
        'data' => array(
            $data->patient_name,
            date_create_from_format('Y-m-d', $data->date_of_birth)->format('j M Y'),
            PatientIdentifierHelper::getIdentifierValue($primary_identifier)
        )
    ),
    'Procedure' => array(
        'data' => array(
            'content' => $data->eye->name,
            'extra_data' => $shortText,
            'deleted' => $is_deleted,
            'dataAttribute' => array(
                'name' => 'procedure',
                'value' => ['shortName' => $shortText, 'fullName' => $data->eye->name . ' - ' . $longText]
            ),
            'is_overflow_btn_required' => true,
        ),
        'colour' => $complexity_colour,
    ),
    'Lens' => array(
        'data' => array(
            'content' => $data->iol_model ? ((float)$data->iol_power >= 0.0 && !in_array($data->iol_power, ['Unknown', 'None']) ? '+' : null) . $data->iol_power : '',
            'extra_data' => $data->iol_model ? ($data->iol_model
                . ' '
                . ($data->iol_model !== 'Unknown' ? $aconst : null)) : 'Lens not selected',
        )
    ),
    'Anaesthesia' => array(
        'data' => implode(
            ', ',
            array_map(
                static function ($elem) {
                    switch (trim($elem->name)) {
                        case 'LA':
                            return 'Local';
                            break;
                        case 'GA':
                            return 'General';
                            break;
                        case 'No Anaesthetic':
                            return 'None';
                            break;
                        default:
                            return $elem->name;
                            break;
                    }
                },
                $data->booking->anaesthetic_type
            )
        )
    ),
    'Biometry' => array(
        'data' => array(
            array(
                'content' => $data->axial_length,
                'small_data' => $data->axial_length !== 'Unknown' && isset($data->axial_length) ? 'mm' : null,
                'extra_data' => isset($data->axial_length) ? 'Axial Length' : null,
            ),
            array(
                'content' => $data->acd,
                'small_data' => $data->acd !== 'Unknown' && isset($data->acd) ? 'mm' : null,
                'extra_data' => isset($data->acd) ? 'ACD' : null,
            )
        )
    ),
    'Predicted Outcome' => array(
        'data' => array(
            'content' => $data->iol_model ? ($data->predicted_refractive_outcome !== 'Unknown' ?
                $data->predicted_refractive_outcome . ' D' :
                $data->predicted_refractive_outcome) : '',
            'extra_data' => $data->iol_model ? $data->formula : 'Lens not selected',
        )
    ),
    'Equipment' => array(
        'data' => $data->predicted_additional_equipment ? explode("\n", $data->predicted_additional_equipment) : array('None'),
        'editable' => $data->booking->isEditable(),
    ),
    'Comments' => array(
        'data' => explode("\n", $data->comments),
        'editable' => $data->booking->isEditable(),
    )
);

$other_card_list = array(
    'Patient' => array(
        'data' => array(
            $data->patient_name,
            date_create_from_format('Y-m-d', $data->date_of_birth)->format('j M Y'),
            PatientIdentifierHelper::getIdentifierValue($primary_identifier),
        )
    ),
    (int)$data->eye_id === Eye::BOTH ? 'Procedure (1st)' : 'Procedure' => array(
        'data' => array(
            'content' => (int)$data->eye_id === Eye::BOTH ? 'Left' : $data->eye->name,
            'extra_data' => $shortText,
            'deleted' => $is_deleted,
            'dataAttribute' => array(
                'name' => 'procedure',
                'value' => ['shortName' => $shortText, 'fullName' => $data->eye->name . ' - ' . $longText]
            ),
            'is_overflow_btn_required' => true,
        ),
        'colour' => $complexity_colour,
    ),
    'Procedure (2nd)' => array(
        'data' => (int)$data->eye_id === Eye::BOTH ? array(
            'content' => 'Right',
            'extra_data' => $shortText,
            'deleted' => $is_deleted,
            'dataAttribute' => array(
                'name' => 'procedure',
                'value' => ['shortName' => $shortText, 'fullName' => $data->eye->name . ' - ' . $longText]
            ),
            'is_overflow_btn_required' => true,
        ) : null,
        'colour' => $complexity_colour,
    ),
    'Anaesthesia' => array(
        'data' => implode(
            ', ',
            array_map(
                static function ($elem) {
                    switch (trim($elem->name)) {
                        case 'LA':
                            return 'Local';
                            break;
                        case 'GA':
                            return 'General';
                            break;
                        case 'No Anaesthetic':
                            return 'None';
                            break;
                        default:
                            return $elem->name;
                            break;
                    }
                },
                $data->booking->anaesthetic_type
            )
        )
    ),
    'Biometry' => array(
        'data' => (int)$data->eye->id === Eye::BOTH ? null : array(
            array(
                'content' => $data->axial_length,
                'small_data' => $data->axial_length !== 'Unknown' ? 'mm' : null,
                'extra_data' => 'Axial Length',
            ),
            array(
                'content' => $data->acd,
                'small_data' => $data->acd !== 'Unknown' ? 'mm' : null,
                'extra_data' => 'ACD',
            )
        ),
    ),
    'Predicted Outcome' => array(
        'data' => null,
    ),
    'Equipment' => array(
        'data' => $data->predicted_additional_equipment ? explode("\n", $data->predicted_additional_equipment) : array('None'),
        'editable' => $data->booking->isEditable(),
    ),
    'Comments' => array(
        'data' => explode("\n", $data->comments),
        'editable' => $data->booking->isEditable(),
    ),
);
?>
<header class="oe-header">
    <?php $this->renderPartial($this->getHeaderTemplate(), array(
        'data' => $data
    )); ?>
</header>
<main class="oe-whiteboard">
    <div class="wb3">
        <?php
        if (in_array($cataract_opnote, $data->booking->getAllProcedureOpnotes(), false)) {
            foreach ($cataract_card_list as $title => $card) {
                $this->widget('WBCard', array(
                    'title' => $title,
                    'data' => $card['data'],
                    'colour' => isset($card['colour']) ? $card['colour'] : null,
                    'editable' => isset($card['editable']) ? $card['editable'] : false,
                    'event_id' => $data->event_id,
                ));
            }
        } else {
            foreach ($other_card_list as $title => $card) {
                $this->widget('WBCard', array(
                    'title' => $title,
                    'data' => $card['data'],
                    'colour' => isset($card['colour']) ? $card['colour'] : null,
                    'editable' => isset($card['editable']) ? $card['editable'] : false,
                    'event_id' => $data->event_id,
                ));
            }
        }
        if (in_array($cataract_opnote, $data->booking->getAllProcedureOpnotes(), false)) {
            $this->widget('EDCard', array(
                'title' => 'Axis',
                'eye' => $data->eye,
                'doodles' => $data->steep_k ? array(
                    'AntSegSteepAxis',
                    array('axis' => $data->axis, 'flatK' => $data->flat_k, 'steepK' => $data->steep_k)
                ) : null,
            ));
        } else {
            $this->widget('WBCard', array(
                'title' => null,
                'data' => null,
                'event_id' => $data->event_id,
            ));
        }
        $this->widget('RiskCard', array(
            'data' => $data,
        )); ?>
    </div>
    <!--
    Manually specifying a high z-index here as the open/close button and footer
    should appear above everything else on screen, especially the EyeDraw widget.
    -->
    <footer class="wb3-actions down" style="z-index: 9999">
        <?php $this->renderPartial('footer', array(
            'biometry' => false,
            'consent' => false,
            'booking_id' => $booking_id,
        )); ?>
    </footer>
</main>

<script src="<?= Yii::app()->assetManager->createUrl('/newblue/dist/js/whiteboardJS/wb_procedure_name.js')?>"></script>
