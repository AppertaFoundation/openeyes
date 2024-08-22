<?php

class m220721_140849_add_konan_subtypes extends CDbMigration
{
    public function safeUp()
    {
        $this->insert('event_subtype', [
            'event_subtype' => 'Corneal Endothelial Cell Count',
            'dicom_modality_code' => '',
            'icon_name' => 'i-ImOCT',
            'display_name' => 'Corneal Endothelial Cell Count',
        ]);

        $this->insert('attachment_type', [
            'attachment_type' => 'Corneal Endothelial Cell Count',
            'title_full' => 'Corneal Endothelial Cell Count',
            'title_short' => 'Corneal Endothelial Cell Count',
            'title_abbreviated' => 'Corneal Endothelial Cell Count',
            'dicom_modality_code' => 'Corneal Endothelial Cell Count',
        ]);
    }

    public function safeDown()
    {
        $this->delete('event_subtype', 'event_subtype = ?', ['Corneal Endothelial Cell Count']);
        $this->delete('attachment_type', 'attachment_type = ?', ['Corneal Endothelial Cell Count']);
    }
}
