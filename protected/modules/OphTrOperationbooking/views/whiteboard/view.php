<?php
    $complexity_colour = 'green';

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
$cataract_card_list = array(
    'Patient' => array(
        'data' => array(
            $data->patient_name,
            date_create_from_format('Y-m-d', $data->date_of_birth)->format('j M Y'),
            $data->hos_num
        )
    ),
    'Procedure' => array(
        'data' => array(
            'content' => $data->eye->name,
            'extra_data' => $data->procedure,
        ),
        'colour' => $complexity_colour,
    ),
    'Lens' => array(
        'data' => array(
            'content' => ((float) $data->iol_power > 0.0 ? '+' : '-') . $data->iol_power,
            'extra_data' => $data->iol_model
                . ' '
                . ((float)$data->aconst === (int)$data->aconst ? (float)$data->aconst . '.0' : (float)$data->aconst),
        )
    ),
    'Anaesthesia' => array(
        'data' => implode(
            ', ',
            array_map(
                static function ($elem) {
                    if ($elem->name === 'LA') {
                        return 'Local';
                    }
                    if ($elem->name === 'GA') {
                        return 'General';
                    }
                    return $elem->name;
                },
                $data->booking->anaesthetic_type
            )
        )
    ),
    'Biometry' =>array(
        'data' => array(
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
        )
    ),
    'Predicted Outcome' => array(
        'data' => array(
            'content' => $data->predicted_refractive_outcome !== 'Unknown' ?
                $data->predicted_refractive_outcome . ' D' :
                $data->predicted_refractive_outcome,
            'extra_data' => $data->formula,
        )
    ),
    'Equipment' => array(
        'data' => $data->predicted_additional_equipment ? explode("\n", $data->predicted_additional_equipment) : array('None'),
        'editable' => true,
    ),
    'Comments' => array(
        'data' => explode("\n", $data->comments),
        'editable' => true,
    )
);

$other_card_list = array(
    'Patient' => array(
        'data' => array(
            $data->patient_name,
            date_create_from_format('Y-m-d', $data->date_of_birth)->format('j M Y'),
            $data->hos_num
        )
    ),
    $data->eye_id === 3 ? 'Procedure (1st)' : 'Procedure' => array(
        'data' => array(
            'content' =>  $data->eye_id === Eye::BOTH ? 'Left' : $data->eye->name,
            'extra_data' => $data->procedure,
        ),
        'colour' => $complexity_colour,
    ),
    'Procedure (2nd)' => array(
        'data' => $data->eye_id === Eye::BOTH ? array(
            'content' => 'Right',
            'extra_data' => $data->procedure,
        ) : null,
        'colour' => $complexity_colour,
    ),
    'Anaesthesia' => array(
        'data' => implode(
            ', ',
            array_map(
                static function ($elem) {
                    if ($elem->name === 'LA') {
                        return 'Local';
                    }
                    if ($elem->name === 'GA') {
                        return 'General';
                    }
                    return $elem->name;
                },
                $data->booking->anaesthetic_type
            )
        )
    ),
    'Biometry' => array(
        'data' => null,
    ),
    'Predicted Outcome' => array(
        'data' => null,
    ),
    'Equipment' => array(
        'data' => $data->predicted_additional_equipment ? explode("\n", $data->predicted_additional_equipment) : array('None'),
        'editable' => true,
    ),
    'Comments' => array(
        'data' => explode("\n", $data->comments),
        'editable' => true,
    ),
);
?>
<header class="oe-header">
    <?php $this->renderPartial($this->getHeaderTemplate(), array(
        'data' => $data
    ));?>
</header>
<main class="oe-whiteboard">
    <div class="wb3">
        <?php
        if ($data->event->episode->firm->getSubspecialty()->name === 'Cataract') {
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
        if ($data->event->episode->firm->getSubspecialty()->name === 'Cataract') {
            $criteria = new CDbCriteria();
            $criteria->with = 'event.episode.patient';
            $criteria->params = array('event.episode.patient.id' => $data->event->episode->patient->id);
            $criteria->order = 't.last_modified_date DESC';
            $criteria->limit = 1;
            $cataract_element = Element_OphTrOperationnote_Cataract::model()->find($criteria);

            $this->widget('ImageCard', array(
                'title' => 'Axis',
                'eye' => $data->eye,
                'doodles' => array('AntSegSteepAxis', array('axis' => $data->axis, 'flatK' => $data->flat_k, 'steepK' => $data->steep_k)),
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
                'whiteboard' => $this->getWhiteboard(),
        )); ?>
    </div>
    <footer class="wb3-actions down">
        <?php $this->renderPartial('footer'); ?>
    </footer>
</main>
