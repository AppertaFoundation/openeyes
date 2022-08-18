<?php

class m220817_133721_add_pachymetry_report_type extends CDbMigration
{
    public function safeUp()
    {
        $this->insert('event_subtype', [
            'event_subtype' => 'Pachymetry',
            'dicom_modality_code' => '',
            'icon_name' => 'i-ImOCT',
            'display_name' => 'Pachymetry',
        ]);

        $this->insert('attachment_type', [
            'attachment_type' => 'Pachymetry',
            'title_full' => 'Pachymetry',
            'title_short' => 'Pachymetry',
            'title_abbreviated' => 'Pachymetry',
            'dicom_modality_code' => 'Pachymetry',
        ]);
    }

    public function safeDown()
    {
        $this->delete('event_subtype', 'event_subtype = ?', ['Pachymetry']);
        $this->delete('attachment_type', 'attachment_type = ?', ['Pachymetry']);
    }
}
