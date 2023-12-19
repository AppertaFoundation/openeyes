<?php

class m230928_111515_add_ophthalmic_photography extends CDbMigration
{
    public function safeUp()
    {
        $this->insert('event_subtype', [
            'event_subtype' => 'Ophthalmic Photography',
            'dicom_modality_code' => 'OP',
            'icon_name' => 'i-ImPhoto',
            'display_name' => 'Ophthalmic Photography',
        ]);

        $this->insert('attachment_type', [
            'attachment_type' => 'Ophthalmic Photography',
            'title_full' => 'Ophthalmic Photography',
            'title_short' => 'Ophthalmic Photography',
            'title_abbreviated' => 'Ophthalmic Photography',
            'dicom_modality_code' => 'OP',
        ]);
    }

    public function safeDown()
    {
        $this->delete('event_subtype', 'event_subtype = ?', ['Ophthalmic Photography']);
        $this->delete('attachment_type', 'attachment_type = ?', ['Ophthalmic Photography']);
    }
}
