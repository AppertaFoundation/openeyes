<?php

class m191125_091623_create_event_icon_table extends CDbMigration
{

    public function up()
    {
        $icon_names = ['i-genCiDiamond', 'i-genCiDoc', 'i-genCiDocHole', 'i-genCiFour', 'i-genCiGrid',
            'i-genCiNut', 'i-genCiSquare', 'i-genCiSquareDot', 'i-genCiSquareSplit', 'i-genCiSquareWin',
            'i-genCiStack', 'i-genCiTarget', 'i-genCoDiamond', 'i-genCoDoc', 'i-genCoDocHole', 'i-genCoFour',
            'i-genCoGrid', 'i-genCoNut', 'i-genCoSquare', 'i-genCoSquareDot', 'i-genCoSquareSplit',
            'i-genCoSquareWin', 'i-genCoStack', 'i-genCoTarget', 'i-genImDiamond', 'i-genImDoc',
            'i-genImDocHole', 'i-genImFour', 'i-genImGrid', 'i-genImNut', 'i-genImSquare', 'i-genImSquareDot',
            'i-genImSquareSplit', 'i-genImSquareWin', 'i-genImStack', 'i-genImTarget', 'i-genMiDiamond',
            'i-genMiDoc', 'i-genMiDocHole', 'i-genMiFour', 'i-genMiGrid', 'i-genMiNut', 'i-genMiSquare',
            'i-genMiSquareDot', 'i-genMiSquareSplit', 'i-genMiSquareWin', 'i-genMiStack', 'i-genMiTarget',
            'i-genNuDiamond', 'i-genNuDoc', 'i-genNuDocHole', 'i-genNuFour', 'i-genNuGrid', 'i-genNuNut', 'i-genNuSquare',
            'i-genNuSquareDot', 'i-genNuSquareSplit', 'i-genNuSquareWin', 'i-genNuStack', 'i-genNuTarget',
            'i-CiAnaestheticExam', 'i-CiCommunityData', 'i-CiDilation', ' i-CiExamination', 'i-CiOrthoptics',
            'i-CiPatientAdmission', 'i-CiPhasing', 'i-CiRefraction', 'i-CiVisualAcuity', 'i-CoCatPROM5',
            'i-CoCertificate', 'i-CoCorrespondence', 'i-CoDocument', 'i-CoIVTApplication', 'i-CoInternalReferral',
            'i-CoLetterIn', 'i-CoLetterOut', 'i-CoMedia', 'i-CoPatientConsent', 'i-CoReferral', 'i-CoScan', 'i-CoTelephoneCall',
            'i-DrDrops', 'i-DrPills', 'i-DrPrescription', 'i-DrSpecsPrescription', 'i-ImFFA', 'i-ImOCT', 'i-ImPhoto',
            'i-ImToricIOL', 'i-ImUltraSound', 'i-InBiometry', 'i-InBlood', 'i-InCornealTopography',
            'i-InDNAExtraction', 'i-InDNAResults', 'i-InDNASample', 'i-InERG', 'i-InLabRequest', 'i-InMRI-CT',
            'i-InStereoPair', 'i-InVisualField', 'i-Message', 'i-MiPatientEducation', 'i-MiSafetyChecklist',
            'i-NuEducation', 'i-NuPreOpCheck', 'i-OuAnaestheticSatisfaction', 'i-OuInfectedEye',
            'i-OuPatientSatisfaction', 'i-Patient', 'i-PatientDNA', 'i-TrIntravitrealInjection',
            'i-TrLaser', 'i-TrNeedling', 'i-TrOperation', 'i-TrOperationNotes', 'i-TrOperationProcedure'];

        $this->createTable('event_icon', [
            'id' => 'pk',
            'name' => 'varchar(64) not null',
            'display_order' => 'int unsigned not null'
        ]);

        foreach ($icon_names as $key => $event_icon) {
            $key = ($key + 1) * 10;
            $this->insert('event_icon', ['name' => $event_icon, 'display_order' => $key]);
        }
    }

    public function down()
    {
        $this->dropTable('event_icon');
    }

}
