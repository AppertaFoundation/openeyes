<?php

class m191009_144720_add_oct_event_sub_type extends CDbMigration
{
    public function up()
    {
        $this->insert('event_subtype', ['event_subtype' => 'OCT',
            'dicom_modality_code' => 'OAM',
            'icon_name' => 'i-ImOCT',
            'display_name' => 'OCT']);
    }

    public function down()
    {
        $this->delete('event_subtype', 'event_subtype = ? AND dicom_modality_code = ?', ['OCT', 'OAM']);
    }
}
