<?php
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
            'colour' => 'green',
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
        'colour' => 'orange',
    ),
    'Procedure (2nd)' => array(
        'data' => $data->eye_id === Eye::BOTH ? array(
            'content' => 'Right',
            'extra_data' => $data->procedure,
        ) : null,
        'colour' => 'orange',
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
        'data' => $data->eye_id !== Eye::BOTH ? array(
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
        ) : null,
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
        ?>
        <?php if ($data->event->episode->firm->getSubspecialty()->name === 'Cataract') : ?>
        <div class="oe-wb-widget data-image">
            <h3>Axis</h3>
            <div class="wb-data image-fill">
                <!--Add image here.-->
            </div>
        </div>
        <?php endif; ?>
        <?php $this->widget('RiskCard', array(
                'data' => $data,
                'whiteboard' => $this->getWhiteboard(),
        )); ?>
    </div>
    <footer class="wb3-actions down">
        <?php $this->renderPartial('footer'); ?>
    </footer>
</main>
