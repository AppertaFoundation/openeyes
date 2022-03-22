<?php

class m211117_152220_add_ae_event_shortcodes extends OEMigration
{
    public function up()
    {
        $event_type_id = $this->getIdOfEventTypeByClassName('OphCiExamination');
        $this->insertMultiple('patient_shortcode', [
            ['event_type_id' => $event_type_id, 'method' => 'getLatestAEEventDay', 'default_code' => 'ady', 'code' => 'ady', 'description' => 'Latest Examination saved day in A&E subspecialty'],
            ['event_type_id' => $event_type_id, 'method' => 'getLatestAEEventDate', 'default_code' => 'adt', 'code' => 'adt', 'description' => 'Latest Examination saved date in A&E subspecialty'],
            ['event_type_id' => $event_type_id, 'method' => 'getLastAEChiefComplaints', 'default_code' => 'cco', 'code' => 'cco', 'description' => 'Most recent Triage element\'s chief complaint in A&E subspecialty'],
            ['event_type_id' => $event_type_id, 'method' => 'getLastAEChiefComplaintsEye', 'default_code' => 'cce', 'code' => 'cce', 'description' => 'Most recent Triage element\'s chief complaint\'s eye in A&E subspecialty'],
            ['event_type_id' => $event_type_id, 'method' => 'getLastAETriageComment', 'default_code' => 'ccc', 'code' => 'ccc', 'description' => 'Most recent Triage element\'s comment in A&E subspecialty'],
            ['event_type_id' => $event_type_id, 'method' => 'getLastAETriagePriority', 'default_code' => 'pri', 'code' => 'pri', 'description' => 'Most recent Triage element\'s chief complaint\'s priority in A&E subspecialty'],
            ['event_type_id' => $event_type_id, 'method' => 'getLastAEPain', 'default_code' => 'aps', 'code' => 'aps', 'description' => 'Most recent Examination Event pain in A&E subspecialty'],
            ['event_type_id' => $event_type_id, 'method' => 'getLastAESafeguardingConcern', 'default_code' => 'asc', 'code' => 'asc', 'description' => 'Most recent Examination Event safeguarding concerns in A&E subspecialty'],
            ['event_type_id' => $event_type_id, 'method' => 'getLastAEFreehandAntSegComment', 'default_code' => 'aas', 'code' => 'aas', 'description' => 'Most recent Examination Event freehand Ant Seg drawing comment'],
            ['event_type_id' => $event_type_id, 'method' => 'getLastAEFreehandFundusComment', 'default_code' => 'afu', 'code' => 'afu', 'description' => 'Most recent Examination Event freehand Fundus drawing comment'],
            ['event_type_id' => $event_type_id, 'method' => 'getLastAEAdvicesGivenComment', 'default_code' => 'aag', 'code' => 'aag', 'description' => 'Most recent Examination Event Advice Given element\'s comment'],
            ['event_type_id' => $event_type_id, 'method' => 'getLastAEDischargeManagement', 'default_code' => 'ado', 'code' => 'ado', 'description' => 'Most recent Examination Event Follow up element\'s discharge info'],
            ['event_type_id' => $event_type_id, 'method' => 'getLastAETreatments', 'default_code' => 'atr', 'code' => 'atr', 'description' => 'Most recent Examination Clinic Procedures in A&E subspecialty'],
            ['event_type_id' => $event_type_id, 'method' => 'getLastAETreatmentsComments', 'default_code' => 'atc', 'code' => 'atc', 'description' => 'Most recent Examination Clinic Procedures with comments in A&E subspecialty'],
            ['event_type_id' => $event_type_id, 'method' => 'getLastAEInvestigation', 'default_code' => 'ain', 'code' => 'ain', 'description' => 'Most recent Examination Investigations in A&E subspecialty'],
            ['event_type_id' => $event_type_id, 'method' => 'getAEPrincipalDiagnosis', 'default_code' => 'apd', 'code' => 'apd', 'description' => 'Most recent Examination Ophthalmic principal Diagnosis in A&E subspecialty'],
            ['event_type_id' => $event_type_id, 'method' => 'getAEOtherDiagnosis', 'default_code' => 'asd', 'code' => 'asd', 'description' => 'Most recent Examination Ophthalmic Diagnosis (without principal) in A&E subspecialty'],
            ['event_type_id' => $event_type_id, 'method' => 'getLastAEIntraocularPressureFirstValue', 'default_code' => 'aif', 'code' => 'aif', 'description' => 'Most recent Intraocular Pressure element\'s first value in A&E subspecialty'],
            ['event_type_id' => $event_type_id, 'method' => 'getLastAEVisualAcuity', 'default_code' => 'aev', 'code' => 'aev', 'description' => 'Gets all previous (stopped) medications'],
            ['event_type_id' => $event_type_id, 'method' => 'getLastAEVisualAcuitySnellen', 'default_code' => 'ava', 'code' => 'ava', 'description' => 'Most recent Examination VA element Snellen metre readings in A&E subspecialty'],
            ['event_type_id' => $event_type_id, 'method' => 'getLastAEStatMedications', 'default_code' => 'asm', 'code' => 'asm', 'description' => 'Gets medications with frequency: immediately (stat)'],
            ['event_type_id' => $event_type_id, 'method' => 'getLastAEPressureReleasingStatMedication', 'default_code' => 'apl', 'code' => 'apl', 'description' => 'Gets Pressure releasing stat medication in A&E subspecialty'],

            // if we are already here we can also add some non A&E shotcodes
            ['event_type_id' => $event_type_id, 'method' => 'getCurrentMedications', 'default_code' => 'mec', 'code' => 'mec', 'description' => 'Gets current (not stopped) medications'],
            ['event_type_id' => $event_type_id, 'method' => 'getPreviousMedications', 'default_code' => 'mep', 'code' => 'mep', 'description' => 'Gets all previous (stopped) medications'],
        ]);
    }

    public function down()
    {
        $shortcodes = [
            'ady', 'adt', 'cco', 'cce', 'ccc', 'pri',
            'aps', 'asc', 'aas', 'afu', 'aag', 'ado',
            'atr', 'atc', 'ain', 'apd', 'asd', 'aif',
            'ava', 'apl', 'mec', 'mep',
        ];

        $this->delete('patient_shortcode', 'code IN (' . ("'" . implode("', '", $shortcodes) . "'") . ')');
    }
}
