<?php

class m191125_091623_create_event_icon_table extends CDbMigration
{

    public function up()
    {
        $icon_names = ['i-CiAnaestheticExam', 'i-CiCommunityData', 'i-CiDilation', ' i-CiExamination',
            'i-CiOrthoptics', 'i-CiPatientAdmission', 'i-CiPhasing', 'i-CiRefraction', 'i-CiVisualAcuity',
            'i-CoCatPROM5', 'i-CoCertificate', 'i-CoCorrespondence', 'i-CoDocument', 'i-CoIVTApplication',
            'i-CoInternalReferral', 'i-CoLetterIn', 'i-CoLetterOut', 'i-CoMedia', 'i-CoPatientConsent',
            'i-CoReferral', 'i-CoTelephoneCall', 'i-DrDrops', 'i-DrPills', 'i-DrPrescription',
            'i-DrSpecsPrescription', 'i-ImOCT', 'i-ImPhoto', 'i-ImToricIOL', 'i-ImUltraSound',
            'i-InBiometry', 'i-InBlood', 'i-InCornealTopography', 'i-InDNAExtraction', 'i-InDNAResults',
            'i-InDNASample', 'i-InERG', 'i-InLabRequest', 'i-InMRI-CT', 'i-InStereoPair', 'i-InVisualField',
            'i-Message', 'i-MiPatientEducation', 'i-MiSafetyChecklist', 'i-NuEducation', 'i-NuPreOpCheck',
            'i-OuAnaestheticSatisfaction', 'i-OuInfectedEye', 'i-OuPatientSatisfaction', 'i-Patient',
            'i-PatientDNA', 'i-TrIntravitrealInjection', 'i-TrLaser', 'i-TrNeedling', 'i-TrOperation',
            'i-TrOperationNotes', 'i-TrOperationProcedure'];

        $sub_types = [
            'General' => 'i-CoDocument',
            'Biometry Report' => 'i-InBiometry',
            'Referral Letter' => 'i-CoReferral',
            'OCT' => 'i-ImOCT',
            'Electrocardiogram' => 'i-CoDocument',
            'Photograph' => 'i-ImPhoto',
            'Consent Form' => 'i-CoPatientConsent',
            'Visual Field Report' => 'i-InVisualField',
            'Lids Photo' => 'i-CoDocument',
            'Orbit Photo' => 'i-CoDocument',
            'Video' => 'i-CoMedia',
            'Refraction' => 'i-CiRefraction',
            'Retcam' => 'i-CoDocument',
            'Toric IOL Calculation' => 'i-ImToricIOL',
            'Ultrasound' => 'i-ImUltraSound',
        ];

        $this->createTable('event_icon', [
            'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY',
            'name' => 'varchar(64) not null',
            'display_order' => 'int unsigned not null'
        ]);

        foreach ($icon_names as $key => $event_icon) {
            $key *= 10;
            $this->insert('event_icon', ['name' => $event_icon, 'display_order' => $key]);
        }

        $this->addColumn('ophcodocument_sub_types', 'event_icon_id', 'int(10) unsigned');
        $this->addForeignKey('document_event_icon_id_fk', 'ophcodocument_sub_types', 'event_icon_id', 'event_icon', 'id');

        $this->addColumn('ophcodocument_sub_types_version', 'event_icon_id', 'int(10) unsigned');
        $this->addForeignKey('document_event_icon_id_version_fk', 'ophcodocument_sub_types_version', 'event_icon_id', 'event_icon', 'id');

        foreach ($sub_types as $sub_type => $icon) {  //set default values for document sub types
            $this->update('ophcodocument_sub_types', ['event_icon_id' => EventIcon::model()->find('name = ?', [$icon])->id], 'name="'.$sub_type .'"');
        }

    }

    public function down()
    {
        $this->dropForeignKey('document_event_icon_id_fk', 'ophcodocument_sub_types');
        $this->dropColumn('ophcodocument_sub_types', 'event_icon_id');
        $this->dropForeignKey('document_event_icon_id_version_fk', 'ophcodocument_sub_types_version');
        $this->dropColumn('ophcodocument_sub_types_version', 'event_icon_id');
        $this->dropTable('event_icon');
    }

}