<?php

class m200207_121240_add_angiography_report_type extends CDbMigration
{
    public function safeUp()
    {
        $this->insert('event_subtype', [
            'event_subtype' => 'OCTAngiography Report',
            'dicom_modality_code' => 'AG',
            'icon_name' => 'i-ImFFA"',
            'display_name' => 'OCT Angiography Report',
        ]);

        $this->insert('attachment_type', [
            'attachment_type' => 'OCTAngiography Report',
            'title_full' => 'OCT Angiography Report',
            'title_short' => 'OCT Angiography Report',
            'title_abbreviated' => 'OCT Angiography Report',
            'dicom_modality_code' => 'AG',
        ]);
    }

    public function safeDown()
    {
        $this->delete('event_subtype', 'event_subtype = ?', ['OCTAngiography Report']);
        $this->delete('attachment_type', 'attachment_type = ?', ['OCTAngiography Report']);
    }
}
