<?php

class m201019_131246_create_new_report_and_attachment_types extends CDbMigration
{
    public function safeUp()
    {
        $this->insert('event_subtype', [
            'event_subtype' => 'OCT Images',
            'dicom_modality_code' => 'OAM',
            'icon_name' => 'i-ImOCT',
            'display_name' => 'OCT Images',
        ]);

        $this->insert('attachment_type', [
            'attachment_type' => 'OCT Images',
            'title_full' => 'OCT Images',
            'title_short' => 'OCT Images',
            'title_abbreviated' => 'OCT Images',
            'dicom_modality_code' => 'OCT Images',
        ]);

        $this->insert('event_subtype', [
            'event_subtype' => 'B SCAN Images',
            'dicom_modality_code' => 'BSCAN',
            'icon_name' => 'i-ImUltraSound',
            'display_name' => 'B SCAN Images',
        ]);

        $this->insert('attachment_type', [
            'attachment_type' => 'B SCAN Images',
            'title_full' => 'B SCAN Images',
            'title_short' => 'B SCAN Images',
            'title_abbreviated' => 'B SCAN Images',
            'dicom_modality_code' => 'B SCAN Images',
        ]);

        $this->insert('event_subtype', [
            'event_subtype' => 'Visual Field Images',
            'dicom_modality_code' => 'VF',
            'icon_name' => 'i-InVisualField',
            'display_name' => 'Visual Field Images',
        ]);

        $this->insert('attachment_type', [
            'attachment_type' => 'Visual Field Images',
            'title_full' => 'Visual Field Images',
            'title_short' => 'Visual Field Images',
            'title_abbreviated' => 'Visual Field Images',
            'dicom_modality_code' => 'VF',
        ]);

        $this->insert('event_subtype', [
            'event_subtype' => 'Topography Images',
            'dicom_modality_code' => 'TG',
            'icon_name' => 'i-InCornealTopography',
            'display_name' => 'Topography Images',
        ]);

        $this->insert('attachment_type', [
            'attachment_type' => 'Topography Images',
            'title_full' => 'Topography Images',
            'title_short' => 'Topography Images',
            'title_abbreviated' => 'Topography Images',
            'dicom_modality_code' => 'TG',
        ]);

        $this->insert('event_subtype', [
            'event_subtype' => 'Fundus Images',
            'dicom_modality_code' => 'FD',
            'icon_name' => 'i-ImPhoto',
            'display_name' => 'Fundus Images',
        ]);

        $this->insert('attachment_type', [
            'attachment_type' => 'Fundus Images',
            'title_full' => 'Fundus Images',
            'title_short' => 'Fundus Images',
            'title_abbreviated' => 'Fundus Images',
            'dicom_modality_code' => 'FD',
        ]);
    }

    public function safeDown()
    {
        $this->delete('event_subtype', 'event_subtype = ?', ['OCT Images']);
        $this->delete('attachment_type', 'attachment_type = ?', ['OCT Images']);

        $this->delete('event_subtype', 'event_subtype = ?', ['B SCAN Images']);
        $this->delete('attachment_type', 'attachment_type = ?', ['B SCAN Images']);

        $this->delete('event_subtype', 'event_subtype = ?', ['Visual Field Images']);
        $this->delete('attachment_type', 'attachment_type = ?', ['Visual Field Images']);

        $this->delete('event_subtype', 'event_subtype = ?', ['Topography Images']);
        $this->delete('attachment_type', 'attachment_type = ?', ['Topography Images']);

        $this->delete('event_subtype', 'event_subtype = ?', ['Fundus Images']);
        $this->delete('attachment_type', 'attachment_type = ?', ['Fundus Images']);
    }
}
