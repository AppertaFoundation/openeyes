<?php

class m200131_110104_add_report_types_from_topcon extends CDbMigration
{
    public function safeUp()
    {
        $this->insert('event_subtype', [
            'event_subtype' => 'Anterior Line Report',
            'dicom_modality_code' => 'ALR',
            'icon_name' => 'i-genImTarget',
            'display_name' => 'Anterior Line Report',
        ]);

        $this->insert('attachment_type', [
            'attachment_type' => 'Anterior Line Report',
            'title_full' => 'Anterior Line Report',
            'title_short' => 'Anterior Line Report',
            'title_abbreviated' => 'Anterior Line Report',
            'dicom_modality_code' => 'ALR',
        ]);

        $this->insert('event_subtype', [
            'event_subtype' => 'Anterior Radial Report',
            'dicom_modality_code' => 'ARR',
            'icon_name' => 'i-genImTarget',
            'display_name' => 'Anterior Radial Report',
        ]);

        $this->insert('attachment_type', [
            'attachment_type' => 'Anterior Radial Report',
            'title_full' => 'Anterior Radial Report',
            'title_short' => 'Anterior Radial Report',
            'title_abbreviated' => 'Anterior Radial Report',
            'dicom_modality_code' => 'ARR',
        ]);

        $this->insert('event_subtype', [
            'event_subtype' => 'Glaucoma Analysis - Macula',
            'dicom_modality_code' => 'ARR',
            'icon_name' => 'i-ImFFA"',
            'display_name' => 'Glaucoma Analysis - Macula',
        ]);

        $this->insert('attachment_type', [
            'attachment_type' => 'Glaucoma Analysis - Macula',
            'title_full' => 'Glaucoma Analysis - Macula',
            'title_short' => 'Glaucoma Analysis - Macula',
            'title_abbreviated' => 'Glaucoma Analysis - Macula',
            'dicom_modality_code' => 'GAM',
        ]);

        $this->insert('event_subtype', [
            'event_subtype' => '3D Wide Report',
            'dicom_modality_code' => 'WR',
            'icon_name' => 'i-ImFFA"',
            'display_name' => '3D Wide Report',
        ]);

        $this->insert('attachment_type', [
            'attachment_type' => '3D Wide Report',
            'title_full' => '3D Wide Report',
            'title_short' => '3D Wide Report',
            'title_abbreviated' => '3D Wide Report',
            'dicom_modality_code' => 'WR',
        ]);
    }

    public function safeDown()
    {
        $this->delete('event_subtype', 'event_subtype = ?', ['Anterior Line Report']);
        $this->delete('attachment_type', 'attachment_type = ?', ['Anterior Line Report']);
        $this->delete('event_subtype', 'event_subtype = ?', ['Anterior Radial Report']);
        $this->delete('attachment_type', 'attachment_type = ?', ['Anterior Radial Report']);
        $this->delete('event_subtype', 'event_subtype = ?', ['Glaucoma Analysis - Macula']);
        $this->delete('attachment_type', 'attachment_type = ?', ['Glaucoma Analysis - Macula']);
        $this->delete('event_subtype', 'event_subtype = ?', ['3D Wide Report']);
        $this->delete('attachment_type', 'attachment_type = ?', ['3D Wide Report']);
    }
}
