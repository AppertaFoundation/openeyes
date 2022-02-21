<?php

class m210817_064330_create_investigation_element_options_with_codes extends OEMigration
{

    private $codes = [

        ["A/C tap intravitreal tap","363255004","Paracentesis of eye (procedure)","363255004"],
        ["Swabs for virology","312855001","Taking conjunctival swab (procedure)","401294003"],
        ["Visual fields","86944008","Visual field study (procedure)","86944008"],
        ["Visual Acuity","16830007","Visual acuity testing (procedure)","16830007"],
        ["Ultrasound","19731001","Ultrasound study of eye (procedure)","16310003"],
        ["Urinalysis/Urine Dipstix","27171005","Urinalysis","27171005"],
        ["Thyroid function tests","35650009","Thyroid function test","35650009"],
        ["Slit Lamp Examination","55468007","Ocular slit lamp examination","16830007"],
        ["Refraction","252886007","Refraction assessment (procedure)","86944007"],
        ["Orthoptics","252847008","Orthoptic test","86944008"],
        ["OCT","392010000","OCT - Optical coherence tomography","392010000"],
        ["MRI","113091000","MRI","113091000"],
        ["Liver Function tests","26958001","Liver Function test","26958001"],
        ["Ishihara","7510005","Color vision examination (procedure)","16830006"],
        ["Fundoscopy (posterior segment examination)","53524009","Ophthalmoscopy (procedure)","16830007"],
        ["Fluorescein","172581008","Fluorescein angiography of eye","282096008"],
        ["Fundoscopy","53524009","Fundoscopy","16830007"],
        ["Full Blood Count","26604007","Full blood count","252167001"],
        ["Erythrocyte Sedimentation Rate (ESR)","416838001","Erythrocyte sedimentation rate measurement","416838001"],
        ["ECG","29303009","Electrocardiographic procedure","29303009"],
        ["CT Scan","77477000","Computerized axial tomography (procedure)","77477000"],
        ["Corneal scrape","172410004","Debridement of corneal lesion (procedure)","363255004"],
        ["Swabs for chlamydia","285586000","Chlamydia swab (procedure)","401294003"],
        ["Blood Glucose","33747003","| Glucose measurement, blood (procedure) |","104686004"],
        ["Blood Tests","396550006","Blood test","252167001"],
        ["Anterior Segment Photography","16306001","Ocular photography for medical evaluation and documentation, slit lamp photography (procedure)","16830007"],
        ["X ray","5675001","Diagnostic radiography of orbits (procedure)","168537006"],
        ["X-ray plain film","168537006","Plain radiography","168537006"],
        ["Ocular photography","69167002","Ocular photography, close up","282096008"],
        ["Intra-ocular fluid sampling vitreous tap","19875009","Diagnostic aspiration of vitreous (procedure)","363255004"],
        ["Tonometry","164729009","Tonometry","164729009"],
        ["Visual acuity testing","16830007","Visual acuity testing","16830007"],
        ["Blood culture","30088009","Blood culture (procedure)","30088009"],
        ["Swab for culture and sensitivities","312855001","Taking conjunctival swab (procedure)","401294003"],
        ["Bacteriology (urine)","275721003","Bacteriology - general","168338000"],
        ["Clotting studies","3116009","Blood coagulation panel","3116009"],
        ["Haematology","252275004","Hematology test (procedure)","26604007"],
        ["Serology","68793005","Serologic test","68793005"],
        ["Creatine kinase","397798009","Creatine kinase measurement","397798009"],
        ["C reactive protein (CRP)","55235003","C-reactive protein measurement (procedure)","55235003"],
        ["Glycosolated haemoglobin (HbA1c)","43396009","Hemoglobin A1c measurement","43396009"],
        ["Lipid profile","16254007","Lipid profile","16254007"],
        ["Pregnancy test","167252002","Urine pregnancy test (procedure)","67900009"],
        ["Toxicology","314076009","Toxicology screening test (procedure)","269874008"],
        ["Bone profile","167036008","Bone profile","167036008"],
        ["Liver function tests (LFTs)","26958001","Liver function tests","26958001"],
        ["Biochemistry","412890008","Biochemical test battery","252167001"],
        ["Dementia screening test","165320004","Dementia test","165320004"],
        ["Lactate","270982000","Serum lactate measurement","270982000"],
        ["Arterial / capillary blood gas","60170009","Analysis of arterial blood gases and pH","60170009"],
        ["Venous blood gas","61911006","Blood gases, venous measurement","61911006"],
        ["Peak expiratory flow","29893006","Peak expiratory flow measurement","29893006"],
        ["Swabs for bacteria, chlamydia, virology","285586000","Chlamydia swab (procedure)","168338000"],
        ["Swabs for bacteriology","275892006","Conjunctival swab taken (situation) |","168338000"],
        ["Retinal photography","282096008","Retinal photography","16830007"],
        ["Ocular fundus photography","20067007","Ocular fundus photography","282096008"],
        ["Image intensifier",null,null,"179929004"],
        ["Full Orthoptic Assessment",null,null,"16830007"],
        ["Refraction, orthoptic tests and computerised visual fields",null,null,"86944008"],
        ["Confocal Microscopy",null,null,"16830007"]
    ];

    public function safeUp()
    {
        $this->createOETable('et_ophciexamination_investigation_codes', array(
            'id' => 'pk',
            'name' => 'varchar(256) NOT NULL',
            'snomed_code' => 'varchar(20)',
            'snomed_term' => 'varchar(256)',
            'ecds_code' => 'varchar(20) NOT NULL',
            'specialty_id' => 'int(10) unsigned'
        ), true);
        $this->addForeignKey('investigation_specialty_fk', 'et_ophciexamination_investigation_codes', 'specialty_id', 'specialty', 'id');

        $statement = "INSERT INTO et_ophciexamination_investigation_codes (name, snomed_code, snomed_term, ecds_code, specialty_id) VALUES ";
        $values = [];
        foreach ($this->codes as $code) {
            $values[] = "(".Yii::app()->db->quoteValue($code[0]).","
                        . Yii::app()->db->quoteValue($code[1]).","
                        . Yii::app()->db->quoteValue($code[2]).","
                        . Yii::app()->db->quoteValue($code[3]).", null )";
        }
        $statement.=implode(", ", $values);
        $this->execute($statement);
    }

    public function safeDown()
    {
        foreach ($this->codes as $code) {
            $this->execute("DELETE FROM et_ophciexamination_investigation_codes WHERE name = :code", [":code" => $code[0]]);
        }
        $this->dropForeignKey('investigation_specialty_fk', 'et_ophciexamination_investigation_codes');
        $this->dropOETable('et_ophciexamination_investigation_codes', true);
    }
}
