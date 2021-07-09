<?php

class m190923_094126_add_diagnosis_columns_to_disorder_section_and_disorder extends OEMigration
{
    public function up()
    {
        $to_add = [
            ['ophcocvi_clinicinfo_disorder_section', 'patient_type', 'tinyint(1) unsigned NOT NULL DEFAULT 0'],
            ['ophcocvi_clinicinfo_disorder', 'patient_type', 'tinyint(1) unsigned NOT NULL DEFAULT 0'],
            ['ophcocvi_clinicinfo_disorder_section', 'event_type_version', 'int(10) unsigned NOT NULL DEFAULT 0'],
            ['ophcocvi_clinicinfo_disorder', 'event_type_version', 'int(10) unsigned NOT NULL DEFAULT 0'],
            ['ophcocvi_clinicinfo_disorder', 'group_id', 'int(10) DEFAULT NULL'],
            ['ophcocvi_clinicinfo_disorder', 'main_cause_pdf_id', 'tinyint(1) unsigned'],
        ];

        $this->execute("ALTER TABLE ophcocvi_clinicinfo_disorder_section MODIFY comments_label VARCHAR(128) DEFAULT NULL");

        foreach($to_add as $data) {
            list($table, $column, $type) = $data;
            $table_info = $this->dbConnection->schema->getTable($table, true);

            echo '<pre>' . print_r(array_keys($table_info->columns), true) . '</pre>';

            if (!isset($table_info->columns[$column])) {
                $this->addColumn($table, $column, $type);
                $this->addColumn("{$table}_version", $column, $type);
            }
        }
        $this->dbConnection->schema->refresh();

        $this->insertIfNotExist('ophcocvi_clinicinfo_disorder_section', array( 'name' => 'Retina', 'display_order' => 1, 'active' => 1, 'event_type_version' => 1, 'comments_allowed' => 0));
        $this->insertIfNotExist('ophcocvi_clinicinfo_disorder_section', array( 'name' => 'Glaucoma', 'display_order' => 2, 'active' => 1, 'event_type_version' => 1, 'comments_allowed' => 0));
        $this->insertIfNotExist('ophcocvi_clinicinfo_disorder_section', array( 'name' => 'Globe', 'display_order' => 3, 'active' => 1, 'event_type_version' => 1, 'comments_allowed' => 0));
        $this->insertIfNotExist('ophcocvi_clinicinfo_disorder_section', array( 'name' => 'Neurological', 'display_order' => 4, 'active' => 1, 'event_type_version' => 1, 'comments_allowed' => 0));
        $this->insertIfNotExist('ophcocvi_clinicinfo_disorder_section', array( 'name' => 'Choroid', 'display_order' => 5, 'active' => 1, 'event_type_version' => 1, 'comments_allowed' => 0));
        $this->insertIfNotExist('ophcocvi_clinicinfo_disorder_section', array( 'name' => 'Lens', 'display_order' => 6, 'active' => 1, 'event_type_version' => 1, 'comments_allowed' => 0));
        $this->insertIfNotExist('ophcocvi_clinicinfo_disorder_section', array( 'name' => 'Cornea', 'display_order' => 7, 'active' => 1, 'event_type_version' => 1, 'comments_allowed' => 0));
        $this->insertIfNotExist('ophcocvi_clinicinfo_disorder_section', array( 'name' => 'Neoplasia', 'display_order' => 8, 'active' => 1, 'event_type_version' => 1, 'comments_allowed' => 0));

        $retina_id = $this->getSectionId('Retina', 0);

        $this->insertIfNotExist('ophcocvi_clinicinfo_disorder', array( 'name' => 'Age-related macular degeneration – choroidal neovascularisation (wet)', 'code' => 'H35.32', 'section_id' => $retina_id, 'active' => 1, 'display_order' => 1, 'event_type_version' => 1, 'main_cause_pdf_id' => 0));
        $this->insertIfNotExist('ophcocvi_clinicinfo_disorder', array( 'name' => 'Age-related macular degeneration – choroidal neovascularisation (dry)', 'code' => 'H35.31', 'section_id' => $retina_id, 'active' => 1, 'display_order' => 2, 'event_type_version' => 1, 'main_cause_pdf_id' => 1));
        $this->insertIfNotExist('ophcocvi_clinicinfo_disorder', array( 'name' => 'Age-related macular degeneration – unspecified (mixed)', 'code' => 'H35.30', 'section_id' => $retina_id, 'active' => 1, 'display_order' => 3, 'event_type_version' => 1, 'main_cause_pdf_id' => 2));

        $this->insertIfNotExist('ophcocvi_clinicinfo_disorder', array( 'name' => 'Diabetic retinopathy', 'disorder_id' => 4855003, 'code' => 'E10.3-E14.3 H36.0', 'section_id' => $retina_id, 'active' => 1, 'display_order' => 4, 'event_type_version' => 1, 'main_cause_pdf_id' => 3));
        $this->insertIfNotExist('ophcocvi_clinicinfo_disorder', array( 'name' => 'Diabetic maculopathy', 'disorder_id' => 232020009, 'code' => 'H36.0A', 'section_id' => $retina_id, 'active' => 1, 'display_order' => 5, 'event_type_version' => 1, 'main_cause_pdf_id' => 4));
        $this->insertIfNotExist('ophcocvi_clinicinfo_disorder', array( 'name' => 'Hereditary retinal dystrophy', 'disorder_id' => 41799005, 'code' => 'H35.5', 'section_id' => $retina_id, 'active' => 1, 'display_order' => 6, 'event_type_version' => 1, 'main_cause_pdf_id' => 5));
        $this->insertIfNotExist('ophcocvi_clinicinfo_disorder', array( 'name' => 'Retinal vascular occlusion', 'disorder_id' => 73757007, 'code' => 'H34', 'section_id' => $retina_id, 'active' => 1, 'display_order' => 7, 'event_type_version' => 1, 'main_cause_pdf_id' => 6));

        $glaucoma_id = $this->getSectionId('Glaucoma', 0);
        $this->insertIfNotExist('ophcocvi_clinicinfo_disorder', array( 'name' => 'Primary open angle glaucoma', 'disorder_id' => 77075001, 'code' => 'H40.1', 'section_id' => $glaucoma_id, 'active' => 1, 'display_order' => 8, 'event_type_version' => 1, 'main_cause_pdf_id' => 8));
        $this->insertIfNotExist('ophcocvi_clinicinfo_disorder', array( 'name' => 'Primary angle-closure glaucoma', 'disorder_id' => 392288006, 'code' => 'H40.2', 'section_id' => $glaucoma_id, 'active' => 1, 'display_order' => 9, 'event_type_version' => 1, 'main_cause_pdf_id' => 9));
        $this->insertIfNotExist('ophcocvi_clinicinfo_disorder', array( 'name' => 'Secondary glaucoma', 'disorder_id' => 95717004, 'code' => 'H40.5', 'section_id' => $glaucoma_id, 'active' => 1, 'display_order' => 10, 'event_type_version' => 1, 'main_cause_pdf_id' => 10));

        $globe_id = $this->getSectionId('Globe', 0);
        $this->insertIfNotExist('ophcocvi_clinicinfo_disorder', array( 'name' => 'Degenerative progressive high myopia', 'disorder_id' => 32022003, 'code' => 'H44.2', 'section_id' => $globe_id, 'active' => 1, 'display_order' => 11, 'event_type_version' => 1, 'main_cause_pdf_id' => 12));

        $neurological_id = $this->getSectionId('Neurological', 0);
        $this->insertIfNotExist('ophcocvi_clinicinfo_disorder', array( 'name' => 'Optic atrophy', 'disorder_id' => 76976005, 'code' => 'H47.2', 'section_id' => $neurological_id, 'active' => 1, 'display_order' => 12, 'event_type_version' => 1, 'main_cause_pdf_id' => 13));
        $this->insertIfNotExist('ophcocvi_clinicinfo_disorder', array( 'name' => 'Disorder of visual cortex', 'disorder_id' => 128329001, 'code' => 'H47.6', 'section_id' => $neurological_id, 'active' => 1, 'display_order' => 13, 'event_type_version' => 1, 'main_cause_pdf_id' => 14));
        $this->insertIfNotExist('ophcocvi_clinicinfo_disorder', array( 'name' => 'Cerebrovascular disease', 'disorder_id' => 62914000, 'code' => 'I60-I69', 'section_id' => $neurological_id, 'active' => 1, 'display_order' => 14, 'event_type_version' => 1, 'main_cause_pdf_id' => 15));

        $choroid_id = $this->getSectionId('Choroid', 0);
        $this->insertIfNotExist('ophcocvi_clinicinfo_disorder', array( 'name' => 'Chorioretinitis', 'disorder_id' => 46627006, 'code' => 'H30.9', 'section_id' => $choroid_id, 'active' => 1, 'display_order' => 15, 'event_type_version' => 1, 'main_cause_pdf_id' => 16));
        $this->insertIfNotExist('ophcocvi_clinicinfo_disorder', array( 'name' => 'Choroidal degeneration', 'disorder_id' => 406446000, 'code' => 'H 31.1', 'section_id' => $choroid_id, 'active' => 1, 'display_order' => 16, 'event_type_version' => 1, 'main_cause_pdf_id' => 17));

        $lens_id = $this->getSectionId('Lens', 0);
//        $snomed_id = $this->getSnomedId('cataract (excludes congenital)');
        $this->insertIfNotExist('ophcocvi_clinicinfo_disorder', array( 'name' => 'Cataract (excludes congenital)', 'code' => 'H25.9', 'section_id' => $lens_id, 'active' => 1, 'display_order' => 17, 'event_type_version' => 1, 'main_cause_pdf_id' => 18));

        $cornea_id = $this->getSectionId('Cornea', 0);
 //       $snomed_id = $this->getSnomedId('corneal scars and opacities');
        $this->insertIfNotExist('ophcocvi_clinicinfo_disorder', array( 'name' => 'Corneal scars and opacities', 'code' => 'H17', 'section_id' => $cornea_id, 'active' => 1, 'display_order' => 18, 'event_type_version' => 1, 'main_cause_pdf_id' => 19));

        $this->insertIfNotExist('ophcocvi_clinicinfo_disorder', array( 'name' => 'Keratitis', 'disorder_id' => 5888003, 'code' => 'H16', 'section_id' => $cornea_id, 'active' => 1, 'display_order' => 19, 'event_type_version' => 1, 'main_cause_pdf_id' => 20));

        $neoplasia_id = $this->getSectionId('Neoplasia', 0);
        $this->insertIfNotExist('ophcocvi_clinicinfo_disorder', array( 'name' => 'Eye neoplasia', 'code' => 'C69', 'section_id' => $neoplasia_id, 'active' => 1, 'display_order' => 20, 'event_type_version' => 1, 'main_cause_pdf_id' => 21));
        $this->insertIfNotExist('ophcocvi_clinicinfo_disorder', array( 'name' => 'Brain and CNS neoplasia', 'code' => 'C70-C72, D43-D44', 'section_id' => $neoplasia_id, 'active' => 1, 'display_order' => 21, 'event_type_version' => 1, 'main_cause_pdf_id' => 22));

        $this->insertIfNotExist('ophcocvi_clinicinfo_disorder_section', array( 'name' => 'Central Visual Pathway Problems', 'active' => 1, 'event_type_version' => 1, 'patient_type' => 1, 'display_order' => 22, 'comments_allowed' => 0));
        $this->insertIfNotExist('ophcocvi_clinicinfo_disorder_section', array( 'name' => 'Whole Globe and Anterior Segment', 'active' => 1, 'event_type_version' => 1, 'patient_type' => 1, 'display_order' => 23, 'comments_allowed' => 0));
        $this->insertIfNotExist('ophcocvi_clinicinfo_disorder_section', array( 'name' => 'Amblyopia', 'active' => 1, 'event_type_version' => 1, 'patient_type' => 1, 'display_order' => 24, 'comments_allowed' => 0));
        $this->insertIfNotExist('ophcocvi_clinicinfo_disorder_section', array( 'name' => 'Cornea', 'active' => 1, 'event_type_version' => 1, 'patient_type' => 1, 'display_order' => 25, 'comments_allowed' => 0));
        $this->insertIfNotExist('ophcocvi_clinicinfo_disorder_section', array( 'name' => 'Cataract', 'active' => 1, 'event_type_version' => 1, 'patient_type' => 1, 'display_order' => 26, 'comments_allowed' => 0));
        $this->insertIfNotExist('ophcocvi_clinicinfo_disorder_section', array( 'name' => 'Uvea', 'active' => 1, 'event_type_version' => 1, 'patient_type' => 1, 'display_order' => 27, 'comments_allowed' => 0));
        $this->insertIfNotExist('ophcocvi_clinicinfo_disorder_section', array( 'name' => 'Retina', 'active' => 1, 'event_type_version' => 1, 'patient_type' => 1, 'display_order' => 28, 'comments_allowed' => 0));
        $this->insertIfNotExist('ophcocvi_clinicinfo_disorder_section', array( 'name' => 'Optic Nerve', 'active' => 1, 'event_type_version' => 1, 'patient_type' => 1, 'display_order' => 29, 'comments_allowed' => 0));

        $central_id = $this->getSectionId('Central Visual Pathway Problems', 1);
        $this->insertIfNotExist('ophcocvi_clinicinfo_disorder', array( 'name' => 'Cerebral/cortical pathology affecting mainly acuity', 'code' => 'H47.6', 'section_id' => $central_id, 'active' => 1, 'display_order' => 1, 'event_type_version' => 1, 'patient_type' => 1, 'group_id' => 1, 'main_cause_pdf_id' => 0));
        $this->insertIfNotExist('ophcocvi_clinicinfo_disorder', array( 'name' => 'Cerebral/cortical pathology affecting mainly fields', 'code' => 'H47.6', 'section_id' => $central_id, 'active' => 1, 'display_order' => 1, 'event_type_version' => 1, 'patient_type' => 1, 'group_id' => 1, 'main_cause_pdf_id' => 0));
        $this->insertIfNotExist('ophcocvi_clinicinfo_disorder', array( 'name' => 'Cerebral/cortical pathology affecting mainly visual perception', 'code' => 'H47.6', 'section_id' => $central_id, 'active' => 1, 'display_order' => 1, 'event_type_version' => 1, 'patient_type' => 1, 'group_id' => 1, 'main_cause_pdf_id' => 0));
        $this->insertIfNotExist('ophcocvi_clinicinfo_disorder', array( 'name' => 'Nystagmus', 'code' => 'H55', 'section_id' => $central_id, 'active' => 1, 'display_order' => 2, 'event_type_version' => 1, 'patient_type' => 1, 'main_cause_pdf_id' => 1));

        $wholeglobe_id = $this->getSectionId('Whole Globe and Anterior Segment', 1);
        $this->insertIfNotExist('ophcocvi_clinicinfo_disorder', array( 'name' => 'Microphthalmos', 'disorder_id' => 61142002, 'code' => 'Q11', 'section_id' => $wholeglobe_id, 'active' => 1, 'display_order' => 3, 'event_type_version' => 1, 'patient_type' => 1,'group_id' =>2, 'main_cause_pdf_id' => 3));
        $this->insertIfNotExist('ophcocvi_clinicinfo_disorder', array( 'name' => 'Anophthalmos', 'disorder_id' => 7183006, 'code' => 'Q11', 'section_id' => $wholeglobe_id, 'active' => 1, 'display_order' => 3, 'event_type_version' => 1, 'patient_type' => 1,'group_id' =>2, 'main_cause_pdf_id' => 3));
        $this->insertIfNotExist('ophcocvi_clinicinfo_disorder', array( 'name' => 'Disorganised globe', 'code' => 'H44', 'section_id' => $wholeglobe_id, 'active' => 1, 'display_order' => 4, 'event_type_version' => 1, 'patient_type' => 1,'group_id' =>3, 'main_cause_pdf_id' => 4));
        $this->insertIfNotExist('ophcocvi_clinicinfo_disorder', array( 'name' => 'Phthisis', 'code' => 'H44', 'section_id' => $wholeglobe_id, 'active' => 1, 'display_order' => 4, 'event_type_version' => 1, 'patient_type' => 1,'group_id' =>3, 'main_cause_pdf_id' => 4));
        $this->insertIfNotExist('ophcocvi_clinicinfo_disorder', array( 'name' => 'Anterior segment anomaly', 'code' => 'Q13', 'section_id' => $wholeglobe_id, 'active' => 1, 'display_order' => 5, 'event_type_version' => 1, 'patient_type' => 1, 'main_cause_pdf_id' => 5));
        $this->insertIfNotExist('ophcocvi_clinicinfo_disorder', array( 'name' => 'Primary congenital glaucoma', 'disorder_id' => 415176004, 'code' => 'Q15, H40.1-H40.2', 'section_id' => $wholeglobe_id, 'active' => 1, 'display_order' => 6, 'event_type_version' => 1, 'patient_type' => 1, 'main_cause_pdf_id' => 6));
        $this->insertIfNotExist('ophcocvi_clinicinfo_disorder', array( 'name' => 'Primary infantile glaucoma', 'disorder_id' => 415176004, 'code' => 'Q15, H40.1-H40.2', 'section_id' => $wholeglobe_id, 'active' => 1, 'display_order' => 6, 'event_type_version' => 1, 'patient_type' => 1, 'main_cause_pdf_id' => 6));

        $this->insertIfNotExist('ophcocvi_clinicinfo_disorder', array( 'name' => 'Other glaucoma', 'code' => 'H40.8-H40.9', 'section_id' => $wholeglobe_id, 'active' => 1, 'display_order' => 6, 'event_type_version' => 1, 'patient_type' => 1, 'main_cause_pdf_id' => 6));

        $amblyopia_id = $this->getSectionId('Amblyopia', 1);
        $this->insertIfNotExist('ophcocvi_clinicinfo_disorder', array( 'name' => 'Stimulus deprivation amblyopia', 'disorder_id' => 193638002, 'code' => 'H53.0', 'section_id' => $amblyopia_id, 'active' => 1, 'display_order' => 7, 'event_type_version' => 1, 'patient_type' => 1, 'main_cause_pdf_id' => 8));
        $this->insertIfNotExist('ophcocvi_clinicinfo_disorder', array( 'name' => 'Strabismic amblyopia', 'disorder_id' => 35600002, 'code' => 'H53.0', 'section_id' => $amblyopia_id, 'active' => 1, 'display_order' => 8, 'event_type_version' => 1, 'patient_type' => 1, 'main_cause_pdf_id' => 9));
        $this->insertIfNotExist('ophcocvi_clinicinfo_disorder', array( 'name' => 'Refractive amblyopia', 'disorder_id' => 90927000, 'code' => 'H53.0', 'section_id' => $amblyopia_id, 'active' => 1, 'display_order' => 9, 'event_type_version' => 1, 'patient_type' => 1, 'main_cause_pdf_id' => 10));

        $cornea_id = $this->getSectionId('Cornea', 1);
        $this->insertIfNotExist('ophcocvi_clinicinfo_disorder', array( 'name' => 'Corneal opacity', 'disorder_id' => 64634000, 'code' => 'H17', 'section_id' => $cornea_id, 'active' => 1, 'display_order' => 10, 'event_type_version' => 1, 'patient_type' => 1, 'main_cause_pdf_id' => 11));
        $this->insertIfNotExist('ophcocvi_clinicinfo_disorder', array( 'name' => 'Corneal dystrophy', 'disorder_id' => 5587004, 'code' => 'H18.4', 'section_id' => $cornea_id, 'active' => 1, 'display_order' => 10, 'event_type_version' => 1, 'patient_type' => 1, 'main_cause_pdf_id' => 12));

        $cataract_id = $this->getSectionId('Cataract', 1);
        $this->insertIfNotExist('ophcocvi_clinicinfo_disorder', array( 'name' => 'Congenital cataract', 'disorder_id' => 79410001, 'code' => 'Q12.0', 'section_id' => $cataract_id, 'active' => 1, 'display_order' => 11, 'event_type_version' => 1, 'patient_type' => 1, 'main_cause_pdf_id' => 13));

        $this->insertIfNotExist('ophcocvi_clinicinfo_disorder', array( 'name' => 'Developmental cataract', 'code' => 'H26.9', 'section_id' => $cataract_id, 'active' => 1, 'display_order' => 12, 'event_type_version' => 1, 'patient_type' => 1, 'main_cause_pdf_id' => 14));
        $this->insertIfNotExist('ophcocvi_clinicinfo_disorder', array( 'name' => 'Secondary cataract', 'code' => 'H26.4', 'section_id' => $cataract_id, 'active' => 1, 'display_order' => 13, 'event_type_version' => 1, 'patient_type' => 1, 'main_cause_pdf_id' => 15));

        $uvea_id = $this->getSectionId('Uvea', 1);
        $this->insertIfNotExist('ophcocvi_clinicinfo_disorder', array( 'name' => 'Aniridia', 'disorder_id' => 69278003, 'code' => 'Q13 .1', 'section_id' => $uvea_id, 'active' => 1, 'display_order' => 14, 'event_type_version' => 1, 'patient_type' => 1, 'main_cause_pdf_id' => 16));
        $this->insertIfNotExist('ophcocvi_clinicinfo_disorder', array( 'name' => 'Coloboma', 'code' => 'Q12.2, Q13.0', 'section_id' => $uvea_id, 'active' => 1, 'display_order' => 15, 'event_type_version' => 1, 'patient_type' => 1, 'main_cause_pdf_id' => 17));
        $this->insertIfNotExist('ophcocvi_clinicinfo_disorder', array( 'name' => 'Uveitis', 'disorder_id' => 128473001, 'code' => 'H20', 'section_id' => $uvea_id, 'active' => 1, 'display_order' => 16, 'event_type_version' => 1, 'patient_type' => 1, 'main_cause_pdf_id' => 18));

        $retina_id = $this->getSectionId('Retina', 1);
        $this->insertIfNotExist('ophcocvi_clinicinfo_disorder', array( 'name' => 'Retinopathy of Prematurity', 'disorder_id' => 415297005, 'code' => 'H35.1', 'section_id' => $retina_id, 'active' => 1, 'display_order' => 17, 'event_type_version' => 1, 'patient_type' => 1, 'main_cause_pdf_id' => 19));
        $this->insertIfNotExist('ophcocvi_clinicinfo_disorder', array( 'name' => 'Retinal dystrophy', 'disorder_id' => 314407005, 'code' => 'H35.5', 'section_id' => $retina_id, 'active' => 1, 'display_order' => 18, 'event_type_version' => 1, 'patient_type' => 1, 'main_cause_pdf_id' => 19));
        $this->insertIfNotExist('ophcocvi_clinicinfo_disorder', array( 'name' => 'Retinitis', 'disorder_id' => 399463004, 'code' => 'H30', 'section_id' => $retina_id, 'active' => 1, 'display_order' => 19, 'event_type_version' => 1, 'patient_type' => 1, 'main_cause_pdf_id' => 20));

        $this->insertIfNotExist('ophcocvi_clinicinfo_disorder', array( 'name' => 'Other retinopathy', 'code' => 'H35.2', 'section_id' => $retina_id, 'active' => 1, 'display_order' => 20, 'event_type_version' => 1, 'patient_type' => 1, 'main_cause_pdf_id' => 21));

        $this->insertIfNotExist('ophcocvi_clinicinfo_disorder', array( 'name' => 'Retinoblastoma', 'disorder_id' => 370967009, 'code' => 'C69.2', 'section_id' => $retina_id, 'active' => 1, 'display_order' => 21, 'event_type_version' => 1, 'patient_type' => 1, 'main_cause_pdf_id' => 22));
        $this->insertIfNotExist('ophcocvi_clinicinfo_disorder', array( 'name' => 'Albinism', 'disorder_id' => 15890002, 'code' => 'E70.3', 'section_id' => $retina_id, 'active' => 1, 'display_order' => 22, 'event_type_version' => 1, 'patient_type' => 1, 'main_cause_pdf_id' => 23));
        $this->insertIfNotExist('ophcocvi_clinicinfo_disorder', array( 'name' => 'Retinal detachment', 'disorder_id' => 42059000, 'code' => 'H33', 'section_id' => $retina_id, 'active' => 1, 'display_order' => 23, 'event_type_version' => 1, 'patient_type' => 1, 'main_cause_pdf_id' => 24));

        $optic_id = $this->getSectionId('Optic Nerve', 1);
        $this->insertIfNotExist('ophcocvi_clinicinfo_disorder', array( 'name' => 'Hypoplasia of the optic nerve', 'disorder_id' => 95499004, 'code' => 'Q11. 2', 'section_id' => $optic_id, 'active' => 1, 'display_order' => 24, 'event_type_version' => 1, 'patient_type' => 1, 'main_cause_pdf_id' => 26));

        $this->insertIfNotExist('ophcocvi_clinicinfo_disorder', array( 'name' => 'Other congenital anomaly', 'code' => 'Q14. 2', 'section_id' => $optic_id, 'active' => 1, 'display_order' => 25, 'event_type_version' => 1, 'patient_type' => 1, 'main_cause_pdf_id' => 27));

        $this->insertIfNotExist('ophcocvi_clinicinfo_disorder', array( 'name' => 'Optic atrophy', 'disorder_id' => 76976005, 'code' => 'H47.2', 'section_id' => $optic_id, 'active' => 1, 'display_order' => 26, 'event_type_version' => 1, 'patient_type' => 1, 'main_cause_pdf_id' => 28));
        $this->insertIfNotExist('ophcocvi_clinicinfo_disorder', array( 'name' => 'Disorder of optic nerve', 'disorder_id' => 77157004, 'code' => 'H47.0', 'section_id' => $optic_id, 'active' => 1, 'display_order' => 27, 'event_type_version' => 1, 'patient_type' => 1, 'main_cause_pdf_id' => 29));

        $event_type = $this->dbConnection->createCommand()->select('id')->from('event_type')->where('class_name=:class_name', array(':class_name'=>'OphCoCvi'))->queryRow();
        $last_event_id = $this->dbConnection->createCommand()->select('id')->from('element_type')->where('name=:name and event_type_id=:eventTypeId and version=:version', array(':name'=>'Clinical Info',':eventTypeId'=>$event_type['id'],':version'=>1))->queryRow();
        if ($last_event_id) {
            $this->delete('element_type', 'id = '.$last_event_id['id']);
        }
        $this->insertIfNotExist('element_type', array('name' => 'Clinical Info','class_name' => 'OEModule\OphCoCvi\models\Element_OphCoCvi_ClinicalInfo_V1', 'event_type_id' => $event_type['id'], 'version' => 1, 'display_order' => 30, 'required' => 1));
        $this->addColumn('et_ophcocvi_clinicinfo', 'patient_type', 'tinyint(1) unsigned NOT NULL DEFAULT 0');
        $this->addColumn('et_ophcocvi_clinicinfo_version', 'patient_type', 'tinyint(1) unsigned NOT NULL DEFAULT 0');
    }

    public function down()
    {
        $this->dropColumn('ophcocvi_clinicinfo_disorder', 'main_cause_pdf_id');
        $this->dropColumn('ophcocvi_clinicinfo_disorder_version', 'main_cause_pdf_id');
    }

    public function getSectionId($name, $patient_type = 1)
    {
        $getsection = $this->dbConnection->createCommand('SELECT id FROM ophcocvi_clinicinfo_disorder_section WHERE name LIKE "%'.$name.'%" AND event_type_version = 1 AND patient_type = "'.$patient_type.'"')->queryRow();
        return $getsection['id'];
    }

    public function getSnomedId($name)
    {
        $getsection = $this->dbConnection->createCommand('SELECT id FROM disorder WHERE fully_specified_name LIKE "%'.$name.'%"')->queryRow();
        return $getsection['id'];
    }

}
