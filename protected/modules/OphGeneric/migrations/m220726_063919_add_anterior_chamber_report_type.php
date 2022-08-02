<?php

class m220726_063919_add_anterior_chamber_report_type extends CDbMigration
{
    public function safeUp()
    {
        $this->insert('event_subtype', [
            'event_subtype' => 'Anterior Chamber',
            'dicom_modality_code' => '',
            'icon_name' => 'i-ImOCT',
            'display_name' => 'Anterior Chamber',
        ]);

        $this->insert('attachment_type', [
            'attachment_type' => 'Anterior Chamber',
            'title_full' => 'Anterior Chamber',
            'title_short' => 'Anterior Chamber',
            'title_abbreviated' => 'Anterior Chamber',
            'dicom_modality_code' => 'Anterior Chamber',
        ]);
    }

    public function safeDown()
    {
        $this->delete('event_subtype', 'event_subtype = ?', ['Anterior Chamber']);
        $this->delete('attachment_type', 'attachment_type = ?', ['Anterior Chamber']);
    }
}
