<?php

class m220719_140028_add_report_types extends CDbMigration
{
    public function safeUp()
    {
        $report_types = [
            'Anterior Segment 5 Line Raster' => 'High Definition Images: Anterior Segment 5 Line Raster',
            'HD Cornea' => 'HD Cornea Analysis',
            'Optic Disc Cube 200x200' => 'Optic Disc Cube',
            'Macular Cube 512x128' => 'Macular Cube',
            'Angiography 6x6 mm' => 'Angiography Analysis',
            '5 Line Raster' => 'High Definition Images: 5 Line Raster',
            'HD Cross' => 'High Definition Images: HD Cross',
        ];

        foreach ($report_types as $report_type => $display_name) {
            $this->insert('event_subtype', [
                'event_subtype' => $report_type,
                'dicom_modality_code' => '',
                'icon_name' => 'i-ImOCT',
                'display_name' => $display_name,
            ]);

            $this->insert('attachment_type', [
                'attachment_type' => $report_type,
                'title_full' => $report_type,
                'title_short' => $report_type,
                'title_abbreviated' => $report_type,
                'dicom_modality_code' => '',
            ]);
        }
    }

    public function safeDown()
    {
        $this->delete('event_subtype', 'event_subtype = ?', ['Clinical Photograph']);
        $this->delete('attachment_type', 'attachment_type = ?', ['Clinical Photograph']);
    }
}
