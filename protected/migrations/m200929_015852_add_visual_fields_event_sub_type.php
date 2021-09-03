<?php

class m200929_015852_add_visual_fields_event_sub_type extends CDbMigration
{
    public function up()
    {
        $this->insert('event_subtype', ['event_subtype' => 'Visual Fields',
            'dicom_modality_code' => 'OPV',
            'icon_name' => 'i-InVisualField',
            'display_name' => 'Visual Fields']);
    }

    public function down()
    {
        $this->delete('event_subtype', 'event_subtype = ? AND dicom_modality_code = ?', ['Visual Fields', 'OPV']);
    }
}
