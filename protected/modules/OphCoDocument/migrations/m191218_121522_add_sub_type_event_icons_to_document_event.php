<?php

class m191218_121522_add_sub_type_event_icons_to_document_event extends OEMigration
{
    public function safeUp()
    {
        $sub_types = [
            'General' => 'i-CoDocument',
            'Biometry Report' => 'i-InBiometry',
            'Referral Letter' => 'i-CoReferral',
            'OCT' => 'i-ImOCT',
            'Electrocardiogram' => 'i-genCiDocHole',
            'Photograph' => 'i-ImPhoto',
            'Consent Form' => 'i-CoPatientConsent',
            'Visual Field Report' => 'i-InVisualField',
            'Lids Photo' => 'i-genCiDocHole',
            'Orbit Photo' => 'i-genCiDocHole',
            'Video' => 'i-CoMedia',
            'Refraction' => 'i-CiRefraction',
            'Retcam' => 'i-genCiDocHole',
            'Toric IOL Calculation' => 'i-ImToricIOL',
            'Ultrasound' => 'i-ImUltraSound',
        ];

        $this->addOEColumn('ophcodocument_sub_types', 'sub_type_event_icon_id', 'int(11)', true );
        $this->addForeignKey('document_sub_type_event_icon_id_fk', 'ophcodocument_sub_types', 'sub_type_event_icon_id', 'event_icon', 'id');


        foreach ($sub_types as $sub_type => $icon) {  //set default values for document sub types
            $event_icon = $this->dbConnection->createCommand('SELECT id FROM event_icon WHERE name = :name')
            ->bindValue(':name', $icon)
            ->queryScalar();
            $this->update('ophcodocument_sub_types', ['sub_type_event_icon_id' => $event_icon], 'name="'.$sub_type .'"');
        }
    }

    public function safeDown()
    {
        $this->dropForeignKey('document_sub_type_event_icon_id_fk', 'ophcodocument_sub_types');
        $this->dropOEColumn('ophcodocument_sub_types', 'sub_type_event_icon_id', true);
    }

}
