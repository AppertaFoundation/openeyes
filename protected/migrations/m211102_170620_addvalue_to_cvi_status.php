<?php

class m211102_170620_addvalue_to_cvi_status extends OEMigration
{
    public function up()
    {
        $this->insert('patient_oph_info_cvi_status', [
            'name' => 'Not recorded',
            'display_order' => 7,
            'active' => 1
        ]);
    }

    public function down()
    {
        $this->delete('patient_oph_info_cvi_status', 'name ="Not recorded"');
    }
}
