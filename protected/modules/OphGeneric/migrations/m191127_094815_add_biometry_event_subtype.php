<?php

class m191127_094815_add_biometry_event_subtype extends CDbMigration
{
    public function safeUp()
    {
        $this->insert('event_subtype', [
            'event_subtype' => 'Biometry Report',
            'dicom_modality_code' => 'BR',
            'icon_name' => 'i-InBiometry',
            'display_name' => 'Biometry Report',
        ]);

        $this->insert('attachment_type', [
            'attachment_type' => 'Biometry Report',
            'title_full' => 'Biometry Report',
            'title_short' => 'Biometry Report',
            'title_abbreviated' => 'Biometry Report',
            'dicom_modality_code' => 'BR',
        ]);
    }

    public function safeDown()
    {
        $this->delete('event_subtype', 'event_subtype = ?', ['Biometry Report']);
        $this->delete('attachment_type', 'attachment_type = ?', ['Biometry Report']);
    }
}
