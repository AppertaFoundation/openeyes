<?php

class m191021_111452_add_event_subtypes extends CDbMigration
{
    public function safeUp()
    {
        $this->insert('event_subtype', [
            'event_subtype' => '3D Disc Report w/ Topography',
            'dicom_modality_code' => 'SC',
            'icon_name' => 'i-ImUltraSound',
            'display_name' => '3D Disc Report w/ Topography',
        ]);

        $this->insert('attachment_type', [
            'attachment_type' => '3D Disc Report w/ Topography',
            'title_full' => '3D Disc Report w/ Topography',
            'title_short' => '3D Disc Report',
            'title_abbreviated' => 'Disc Report',
            'dicom_modality_code' => 'SC',
        ]);


        $this->insert('event_subtype', [
            'event_subtype' => '3D Macula Report',
            'dicom_modality_code' => 'SC',
            'icon_name' => 'i-ImToricIOL',
            'display_name' => '3D Macula Report',
        ]);

        $this->insert('attachment_type', [
            'attachment_type' => '3D Macula Report',
            'title_full' => '3D Macula Report',
            'title_short' => '3D Macula Report',
            'title_abbreviated' => 'Macula Report',
            'dicom_modality_code' => 'SC',
        ]);

        $this->insert('event_subtype', [
            'event_subtype' => 'Radial Report',
            'dicom_modality_code' => 'SC',
            'icon_name' => 'i-InCornealTopography',
            'display_name' => 'Radial Report',
        ]);

        $this->insert('attachment_type', [
            'attachment_type' => 'Radial Report',
            'title_full' => 'Radial Report',
            'title_short' => 'Radial Report',
            'title_abbreviated' => 'Radial Report',
            'dicom_modality_code' => 'SC',
        ]);
    }

    public function safeDown()
    {
        $this->delete('event_subtype', 'event_subtype = ?', ['3D Macula Report']);
        $this->delete('attachment_type', 'attachment_type = ?', ['3D Macula Report']);
        $this->delete('event_subtype', 'event_subtype = ?', ['3D Disc Report w/ Topography']);
        $this->delete('attachment_type', 'attachment_type = ?', ['3D Disc Report w/ Topography']);
        $this->delete('event_subtype', 'event_subtype = ?', ['Radial Report']);
        $this->delete('attachment_type', 'attachment_type = ?', ['Radial Report']);
    }
}
