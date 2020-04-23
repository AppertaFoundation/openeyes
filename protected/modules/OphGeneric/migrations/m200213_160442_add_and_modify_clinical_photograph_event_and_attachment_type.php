<?php

class m200213_160442_add_and_modify_clinical_photograph_event_and_attachment_type extends CDbMigration
{
    public function safeUp()
    {
        $this->insert('event_subtype', [
            'event_subtype' => 'Clinical Photograph Colour',
            'dicom_modality_code' => 'CP',
            'icon_name' => 'i-ImPhoto',
            'display_name' => 'Clinical Photograph Colour',
        ]);

        $this->insert('attachment_type', [
            'attachment_type' => 'Clinical Photograph Colour',
            'title_full' => 'Clinical Photograph Colour',
            'title_short' => 'Clinical Photograph Colour',
            'title_abbreviated' => 'Clinical Photograph Colour',
            'dicom_modality_code' => 'CP',
        ]);

        $this->insert('event_subtype', [
            'event_subtype' => 'Clinical Photograph Black/White',
            'dicom_modality_code' => 'CP',
            'icon_name' => 'i-ImPhoto',
            'display_name' => 'Clinical Photograph Black/White',
        ]);

        $this->insert('attachment_type', [
            'attachment_type' => 'Clinical Photograph Black/White',
            'title_full' => 'Clinical Photograph Black/White',
            'title_short' => 'Clinical Photograph Black/White',
            'title_abbreviated' => 'Clinical Photograph Black/White',
            'dicom_modality_code' => 'CP',
        ]);

        $clinical_photograph_event_subtype_items = EventSubTypeItem::model()->findAll('event_subtype = ?', ['Clinical Photograph']);
        foreach ($clinical_photograph_event_subtype_items as $clinical_photograph_event_subtype_item) {
            $clinical_photograph_event_subtype_item->event_subtype = 'Clinical Photograph Colour';
            $clinical_photograph_event_subtype_item->save();
        }

        $clinical_photograph_attachment_data_items = AttachmentData::model()->findAll('attachment_type = ?', ['Clinical Photograph']);
        foreach ($clinical_photograph_attachment_data_items as $clinical_photograph_attachment_data_item) {
            $clinical_photograph_attachment_data_item->attachment_type = 'Clinical Photograph Colour';
            $clinical_photograph_attachment_data_item->save();
        }

        $this->delete('event_subtype', 'event_subtype = ?', ['Clinical Photograph']);
        $this->delete('attachment_type', 'attachment_type = ?', ['Clinical Photograph']);
    }

    public function safeDown()
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

        $clinical_photograph_event_subtype_items = EventSubTypeItem::model()->findAll('event_subtype = ?', ['Clinical Photograph Colour']);
        foreach ($clinical_photograph_event_subtype_items as $clinical_photograph_event_subtype_item) {
            $clinical_photograph_event_subtype_item->event_subtype = 'Clinical Photograph';
            $clinical_photograph_event_subtype_item->save();
        }

        $clinical_photograph_attachment_data_items = AttachmentData::model()->findAll('attachment_type = ?', ['Clinical Photograph Colour']);
        foreach ($clinical_photograph_attachment_data_items as $clinical_photograph_attachment_data_item) {
            $clinical_photograph_attachment_data_item->event_subtype = 'Clinical Photograph';
            $clinical_photograph_attachment_data_item->save();
        }

        $this->delete('event_subtype', 'event_subtype = ?', ['Clinical Photograph Colour']);
        $this->delete('event_subtype', 'event_subtype = ?', ['Clinical Photograph Black/White']);
        $this->delete('attachment_type', 'attachment_type = ?', ['Clinical Photograph Colour']);
        $this->delete('attachment_type', 'attachment_type = ?', ['Clinical Photograph Black/White']);
    }
}
