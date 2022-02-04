<?php

class m211115_170740_add_new_shortcodes extends \OEMigration
{
    public function up()
    {
        $event_type_id = $this->getIdOfEventTypeByClassName('OphDrPrescription');

        $this->insertMultiple('patient_shortcode', [
            ['event_type_id' => $event_type_id, 'method' => 'getLetterLongPrescription', 'default_code' => 'prl', 'code' => 'prl', 'description' => 'Prescription displaying long terms for drug frequency'],
        ]);

    }

    public function down()
    {
        $shortcodes = ['prl'];
        $this->delete('patient_shortcode', '`default_code` IN (' . (implode(', ', $shortcodes)) . ')');
    }
}
