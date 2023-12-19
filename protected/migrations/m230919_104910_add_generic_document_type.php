<?php

class m230919_104910_add_generic_document_type extends CDbMigration
{

    public function safeUp()
    {
        $this->insert('event_subtype', [
            'event_subtype' => 'Generic Document',
            'dicom_modality_code' => 'GD',
            'icon_name' => 'i-CoDocument',
            'display_name' => 'Generic Document',
        ]);

        $this->insert('attachment_type', [
            'attachment_type' => 'Generic Document',
            'title_full' => 'Generic Document',
            'title_short' => 'Generic Document',
            'title_abbreviated' => 'Generic Document',
            'dicom_modality_code' => 'GD',
        ]);
    }

    public function safeDown()
    {
        $this->delete('event_subtype', 'event_subtype = ?', ['Generic Document']);
        $this->delete('attachment_type', 'attachment_type = ?', ['Generic Document']);
    }
}
