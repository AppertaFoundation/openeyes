<?php
    $card_list = array(
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
                'content' => $data->iol_power,
                'extra_data' => $data->iol_model,
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
                    'content' => 'Element 1',
                ),
                array(
                    'content' => 'Element 2',
                    'small_data' => 'units',
                    'extra_data' => 'Test extra data'
                )
            )
        ),
        'Predicted Outcome' => array(
            'data' => $data->predicted_refractive_outcome,
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
    ?>
<header class="oe-header">
    <?php $this->renderPartial($this->getHeaderTemplate(), array(
        'data' => $data
    ));?>
</header>
<main class="oe-whiteboard">
    <div class="wb3">
        <?php
        foreach ($card_list as $title => $card) {
            $this->widget('WBCard', array(
                'title' => $title,
                'data' => $card['data'],
                'colour' => isset($card['colour']) ? $card['colour'] : null,
                'editable' => isset($card['editable']) ? $card['editable'] : false,
                'event_id' => $data->event_id,
            ));
        }
        ?>
        <div class="oe-wb-widget data-image">
            <h3>Axis</h3>
            <div class="wb-data image-fill">
                <!--Add image here.-->
            </div>
        </div>
        <?php $this->widget('RiskCard', array(
                'data' => $data,
                'whiteboard' => $this->getWhiteboard(),
        )); ?>
    </div>
    <footer class="wb3-actions down">
        <?php $this->renderPartial('footer'); ?>
    </footer>
</main>
