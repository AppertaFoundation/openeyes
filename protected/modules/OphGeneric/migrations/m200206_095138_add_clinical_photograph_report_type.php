<?php

class m200206_095138_add_clinical_photograph_report_type extends CDbMigration
{
    public function safeUp()
    {
        $this->insert('event_subtype', [
            'event_subtype' => 'Clinical Photograph',
            'dicom_modality_code' => 'CP',
            'icon_name' => 'i-ImPhoto',
            'display_name' => 'Clinical Photograph',
        ]);

        $this->insert('attachment_type', [
            'attachment_type' => 'Clinical Photograph',
            'title_full' => 'Clinical Photograph',
            'title_short' => 'Clinical Photograph',
            'title_abbreviated' => 'Clinical Photograph',
            'dicom_modality_code' => 'CP',
        ]);
    }

    public function safeDown()
    {
        $this->delete('event_subtype', 'event_subtype = ?', ['Clinical Photograph']);
        $this->delete('attachment_type', 'attachment_type = ?', ['Clinical Photograph']);
    }
}
