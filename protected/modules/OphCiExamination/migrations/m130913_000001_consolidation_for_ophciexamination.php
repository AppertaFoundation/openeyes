<?php
/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
class m130913_000001_consolidation_for_ophciexamination extends OEMigration
{
    private $patients_shortcodes = array(
        array('hpc', 'getLetterHistory', 'History of presenting complaint'),
        array('ipb', 'getLetterIOPReadingBoth', 'Intraocular pressure in both eyes'),
        array('ipl', 'getLetterIOPReadingLeft', 'Intraocular pressure in the left eye'),
        array('ipp', 'getLetterIOPReadingPrincipal', 'Intraocular pressure in the principal eye'),
        array('ipr', 'getLetterIOPReadingRight', 'Intraocular pressure in the right eye'),
        array('asl', 'getLetterAnteriorSegmentLeft', 'Anterior segment findings in the left eye'),
        array('asp', 'getLetterAnteriorSegmentPrincipal', 'Anterior segment findings in the principal eye'),
        array('asr', 'getLetterAnteriorSegmentRight', 'Anterior segment findings in the right eye'),
        array('psl', 'getLetterPosteriorPoleLeft', 'Posterior pole findings in the left eye'),
        array('psp', 'getLetterPosteriorPolePrincipal', 'Posterior pole findings in the principal eye'),
        array('psr', 'getLetterPosteriorPoleRight', 'Posterior pole findings in the right eye'),
        array('vbb', 'getLetterVisualAcuityBoth', 'Best visual acuity in both eyes'),
        array('vbl', 'getLetterVisualAcuityLeft', 'Best visual acuity in the left eye'),
        array('vbp', 'getLetterVisualAcuityPrincipal', 'Best visual acuity in the principal eye'),
        array('vbr', 'getLetterVisualAcuityRight', 'Best visual acuity in the right eye'),
        array('con', 'getLetterConclusion', 'Conclusion'),
        array('man', 'getLetterManagement', 'Management'),
        array('adr', 'getLetterAdnexalComorbidityRight', 'Adnexal comorbidity in the right eye'),
        array('adl', 'getLetterAdnexalComorbidityLeft', 'Adnexal comorbidity in the left eye'),
        array('nrr', 'getLetterDRRetinopathyRight', 'NSC right retinopathy'),
        array('nlr', 'getLetterDRRetinopathyLeft', 'NSC left retinopathy'),
        array('nrm', 'getLetterDRMaculopathyRight', 'NSC right maculopathy'),
        array('nlm', 'getLetterDRMaculopathyLeft', 'NSC left maculopathy'),
        array('crd', 'getLetterDRClinicalRight', 'Clinical right retinopathy'),
        array('cld', 'getLetterDRClinicalLeft', 'Clinical left retinopathy'),
        array('lmp', 'getLetterLaserManagementPlan', 'Laser management plan'),
        array('lmc', 'getLetterLaserManagementComments', 'Laser management comments'),
        array('fup', 'getLetterOutcomeFollowUpPeriod', 'Follow up period'),
    );

    public function up()
    {
        if (!$this->consolidate(
            array(
                'm120703_130000_initial_migration_for_ophciexamination',
                'm120724_123004_sanitise_rule_values',
                'm120823_091933_put_cataract_assessment_model_data_into_the_database',
                'm120823_103423_put_cataract_assessment_nuclear_values_into_the_database',
                'm120823_105425_put_cataract_assessment_cortical_values_into_the_database',
                'm120823_113230_put_iop_reading_values_into_the_database',
                'm120823_135356_put_cd_ratio_values_into_the_database',
                'm120823_141902_put_visualacuity_method_values_into_the_database',
                'm120823_144728_put_visualacuity_wearing_values_into_the_database',
                'm120824_145601_put_refraction_types_into_the_database',
                'm120828_073821_put_refraction_segmented_field_fractions_into_the_database',
                'm120828_074417_put_refraction_integers_into_the_database',
                'm120828_080317_put_refraction_signs_into_the_database',
                'm120829_105859_set_default_values_for_visualacuity_method_fields',
                'm120829_111324_set_default_values_for_visual_acuity_wearing_fields',
                'm120925_122900_uat_changes',
                'm121005_093511_update_diagnosis_element_to_support_multiple_diagnoses',
                'm121009_080953_element_type_eye_rows_for_diagnoses_element',
                'm121009_141300_more_uat_changes',
                'm121009_162400_anterior_segment',
                'm121012_092744_management_element',
                'm121012_130734_set_management_element_to_be_default_for_cataract_subspecialty',
                'm121015_122500_cataract_data',
                'm121015_123351_add_consultant_to_suitable_dropdown_in_management_element',
                'm121015_140200_attribute_delimiter',
                'm121019_094213_new_adnexal_comorbidity_options',
                'm121019_102355_add_investigation_attribute_option',
                'm121019_102951_managaement_element_previous_refractive_surgery_boolean',
                'm121022_144600_cataract_data_again',
                'm121024_134239_attribute_options',
                'm121026_115500_surely_not_cataract_data',
                'm121030_145511_none_value_for_adnexal_comorbidity',
                'm121108_113852_add_new_options_to_history_dropdown',
                'm121108_125109_add_radio_boolean_for_previous_refractive_surgery',
                'm121111_155600_glaucoma',
                'm121116_082910_add_none_to_adnexal_comorbidity',
                'm121127_171400_add_values_to_eyedraw_lookups',
                'm121210_084931_dilation_element',
                'm121211_115300_new_opticdisc_fields',
                'm121212_155342_cataractmanagement',
                'm121218_121700_add_fks',
                'm130114_094914_dilation_element_eye_id_null',
                'm130115_122300_dilation_changes',
                'm130117_143800_add_option_to_iop',
                'm130117_150400_subspecialty_specific_attributes',
                'm130121_115900_anterior_segment_cct_element',
                'm130122_124400_other_risks',
                'm130123_160400_optic_disc_lenses',
                'm130129_153400_new_management_element',
                'm130205_162400_glaucoma_sets',
                'm130215_132333_vitrectomised_eye_field',
                'm130226_133800_rename_elements',
                'm130226_141700_clinic_outcome_element',
                'm130226_142600_glaucoma_risk_element',
                'm130227_121900_rename_risk_to_comorbidities',
                'm130228_120600_remove_previous_refractive_surgery',
                'm130228_152100_risks_element',
                'm130301_141001_rename_posterior_segment_to_posterior_pole',
                'm130304_131200_workflow',
                'm130305_143600_workflow_sets',
                'm130305_165800_remove_cd_ratio',
                'm130306_093800_posteriorpole_api_change',
                'm130307_110400_clinic_outcome_template',
                'm130308_145200_clinic_outcome_role',
                'm130311_132000_glaucoma_risk_update',
                'm130311_171200_gonioscopy_grade',
                'm130319_110800_clinic_outcome_tweak',
                'm130327_081818_visual_fields_element',
                'm130327_085032_new_dropdown_values',
                'm130404_114857_missing_event_id_fk',
                'm130405_120200_attribute_elements',
                'm130405_132505_first_second_eye_in_cataract_management_element',
                'm130415_115500_new_pupillaryabnormalities_element',
                'm130423_100500_optic_disc_optional_fields',
                'm130423_153100_glaucoma_risk_update',
                'm130521_143339_dilation_times',
                'm130522_135033_new_va_values',
                'm130603_135042_dr_function',
                'm130604_134337_patient_shortcodes',
                'm130617_104158_va_scale_update',
                'm130619_130602_community_patient_checkbox',
                'm130701_131445_dr_function_2',
                'm130725_075406_dr_function_3',
                'm130808_125115_missing_date_and_user_fields',
                'm130808_130328_missing_fields',
                'm130821_144333_injectionmanagement_other',
                'm130822_125938_missing_snellen_va',
            )
        )
        ) {
            $this->createTables();
        }
    }

    public function down()
    {
        echo "You cannot migrate down past a consolidation migration\n";

        return false;
    }

    public function safeUp()
    {
        $this->up();
    }

    public function safeDown()
    {
        $this->down();
    }

    protected function createTables()
    {
        //disable foreign keys check
        $this->execute('SET foreign_key_checks = 0');
        Yii::app()->cache->flush();
        $event_type_id = $this->insertOEEventType('Examination', 'OphCiExamination', 'Ci');

        // Insert element types (in order of display)
        $element_types = array(
            'Element_OphCiExamination_History' => array('name' => 'History', 'display_order' => 10),
            'Element_OphCiExamination_Refraction' => array('name' => 'Refraction', 'display_order' => 20),
            'Element_OphCiExamination_VisualAcuity' => array('name' => 'Visual Acuity', 'display_order' => 30),
            'Element_OphCiExamination_AdnexalComorbidity' => array('name' => 'Adnexal Comorbidity', 'display_order' => 40),
            'Element_OphCiExamination_AnteriorSegment' => array('name' => 'Anterior Segment', 'display_order' => 50),
            'Element_OphCiExamination_IntraocularPressure' => array('name' => 'Intraocular Pressure', 'display_order' => 60),
            'Element_OphCiExamination_PosteriorPole' => array('name' => 'Posterior Pole', 'display_order' => 70),
            'Element_OphCiExamination_Diagnoses' => array('name' => 'Diagnoses', 'display_order' => 80),
            'Element_OphCiExamination_Investigation' => array('name' => 'Investigation', 'display_order' => 90),
            'Element_OphCiExamination_Conclusion' => array('name' => 'Conclusion', 'display_order' => 100),
            'Element_OphCiExamination_Gonioscopy' => array('name' => 'Gonioscopy', 'display_order' => 35),
            'Element_OphCiExamination_OpticDisc' => array('name' => 'Optic Disc', 'display_order' => 65),
            'Element_OphCiExamination_Dilation' => array('name' => 'Dilation', 'display_order' => 65),
            'Element_OphCiExamination_Management' => array('name' => 'Clinical Management', 'display_order' => 95),
            'Element_OphCiExamination_ClinicOutcome' => array('name' => 'Clinic Outcome', 'display_order' => 97),
            'Element_OphCiExamination_Risks' => array('name' => 'Risks', 'display_order' => 96, 'default' => 0),
            'Element_OphCiExamination_PupillaryAbnormalities' => array('name' => 'Pupillary Abnormalities', 'display_order' => 63, 'default' => 0),

            'Element_OphCiExamination_CataractManagement' => array('name' => 'Cataract Management', 'parent_element_type_id' => 'Element_OphCiExamination_Management', 'display_order' => 10),
            'Element_OphCiExamination_Comorbidities' => array('name' => 'Comorbidities', 'parent_element_type_id' => 'Element_OphCiExamination_History', 'display_order' => 10),
            'Element_OphCiExamination_AnteriorSegment_CCT' => array('name' => 'CCT', 'parent_element_type_id' => 'Element_OphCiExamination_AnteriorSegment', 'display_order' => 1),
            'Element_OphCiExamination_GlaucomaRisk' => array('name' => 'Glaucoma Risk Stratification', 'parent_element_type_id' => 'Element_OphCiExamination_Risks', 'display_order' => 10, 'default' => 0),
            'Element_OphCiExamination_DRGrading' => array('name' => 'DR Grading', 'parent_element_type_id' => 'Element_OphCiExamination_PosteriorPole', 'display_order' => 71),
            'Element_OphCiExamination_LaserManagement' => array('name' => 'Laser Management', 'parent_element_type_id' => 'Element_OphCiExamination_Management', 'display_order' => 91),
            'Element_OphCiExamination_InjectionManagement' => array('name' => 'Injection Management', 'parent_element_type_id' => 'Element_OphCiExamination_Management', 'display_order' => 92),
            'Element_OphCiExamination_InjectionManagementComplex' => array('name' => 'Injection Management', 'parent_element_type_id' => 'Element_OphCiExamination_Management', 'display_order' => 92),
            'Element_OphCiExamination_OCT' => array('name' => 'OCT', 'parent_element_type_id' => 'Element_OphCiExamination_Investigation', 'display_order' => 1),
        );

        $this->insertOEElementType($element_types, $event_type_id);

        //add setting_medatada dynamically
        $settingMetadataArray = array(
            array(
                'element_type_id' => $this->getIdOfElementTypeByClassName('Element_OphCiExamination_VisualAcuity'),
                'field_type_id' => 2, // Dropdown
                'key' => 'unit_id', 'name' => 'Units', 'default_value' => 2, ),
            array(
                'element_type_id' => $this->getIdOfElementTypeByClassName('Element_OphCiExamination_VisualAcuity'),
                'field_type_id' => 1, // Checkbox
                'key' => 'notes', 'name' => 'Show Notes', 'default_value' => 1, ),
            array(
                'element_type_id' => $this->getIdOfElementTypeByClassName('Element_OphCiExamination_IntraocularPressure'),
                'field_type_id' => 2, // Checkbox
                'key' => 'default_instrument_id', 'name' => 'Default Instrument', 'default_value' => 1, ),
            array(
                'element_type_id' => $this->getIdOfElementTypeByClassName('Element_OphCiExamination_IntraocularPressure'),
                'field_type_id' => 1, // Checkbox
                'key' => 'show_instruments', 'name' => 'Show instruments', 'default_value' => 1, ),
            array(
                'element_type_id' => $this->getIdOfElementTypeByClassName('Element_OphCiExamination_IntraocularPressure'),
                'field_type_id' => 1, // Checkbox
                'key' => 'link_instruments', 'name' => 'Link Instruments', 'default_value' => 1, ),
            array(
                'element_type_id' => $this->getIdOfElementTypeByClassName('Element_OphCiExamination_Gonioscopy'),
                'field_type_id' => 1, // Checkbox
                'key' => 'expert', 'name' => 'Expert Mode', 'default_value' => 0, ),
        );

        foreach ($settingMetadataArray as $settingMetadata) {
            $this->insert('setting_metadata', array(
                'element_type_id' => $settingMetadata['element_type_id'],
                'field_type_id' => $settingMetadata['field_type_id'], // Checkbox
                'key' => $settingMetadata['key'],
                'name' => $settingMetadata['name'],
                'default_value' => $settingMetadata['default_value'],
            ));
        }

        //load patient_shortcodes
        $this->loadPatientShortcodes();

        // Raw create tables as per last dump
        $this->execute("CREATE TABLE `et_ophciexamination_adnexalcomorbidity` (
				`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
				`event_id` int(10) unsigned NOT NULL,
				`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
				`last_modified_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
				`created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
				`created_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
				`left_description` text,
				`right_description` text,
				`eye_id` int(10) unsigned NOT NULL DEFAULT '3',
				PRIMARY KEY (`id`),
				KEY `et_ophciexamination_adnexalcomorbidity_e_id_fk` (`event_id`),
				KEY `et_ophciexamination_adnexalcomorbidity_c_u_id_fk` (`created_user_id`),
				KEY `et_ophciexamination_adnexalcomorbidity_l_m_u_id_fk` (`last_modified_user_id`),
				KEY `et_ophciexamination_adnexalcomorbidity_eye_id_fk` (`eye_id`),
				CONSTRAINT `et_ophciexamination_adnexalcomorbidity_eye_id_fk` FOREIGN KEY (`eye_id`) REFERENCES `eye` (`id`),
				CONSTRAINT `et_ophciexamination_adnexalcomorbidity_c_u_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
				CONSTRAINT `et_ophciexamination_adnexalcomorbidity_e_id_fk` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`),
				CONSTRAINT `et_ophciexamination_adnexalcomorbidity_l_m_u_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

        $this->execute("CREATE TABLE `et_ophciexamination_anteriorsegment` (
				`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
				`event_id` int(10) unsigned NOT NULL,
				`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
				`last_modified_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
				`created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
				`created_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
				`left_eyedraw` text,
				`left_pupil_id` int(10) unsigned DEFAULT NULL,
				`left_nuclear_id` int(10) unsigned DEFAULT NULL,
				`left_cortical_id` int(10) unsigned DEFAULT NULL,
				`left_pxe` tinyint(1) DEFAULT NULL,
				`left_phako` tinyint(1) DEFAULT NULL,
				`left_description` text,
				`right_eyedraw` text,
				`right_pupil_id` int(10) unsigned DEFAULT NULL,
				`right_nuclear_id` int(10) unsigned DEFAULT NULL,
				`right_cortical_id` int(10) unsigned DEFAULT NULL,
				`right_pxe` tinyint(1) DEFAULT NULL,
				`right_phako` tinyint(1) DEFAULT NULL,
				`right_description` text,
				`eye_id` int(10) unsigned NOT NULL DEFAULT '3',
				PRIMARY KEY (`id`),
				KEY `et_ophciexamination_anteriorsegment_cui_fk` (`created_user_id`),
				KEY `et_ophciexamination_anteriorsegment_lmui_fk` (`last_modified_user_id`),
				KEY `et_ophciexamination_anteriorsegment_ei_fk` (`event_id`),
				KEY `et_ophciexamination_anteriorsegment_rni_fk` (`right_nuclear_id`),
				KEY `et_ophciexamination_anteriorsegment_lni_fk` (`left_nuclear_id`),
				KEY `et_ophciexamination_anteriorsegment_rpi_fk` (`right_pupil_id`),
				KEY `et_ophciexamination_anteriorsegment_lpi_fk` (`left_pupil_id`),
				KEY `et_ophciexamination_anteriorsegment_rci_fk` (`right_cortical_id`),
				KEY `et_ophciexamination_anteriorsegment_lci_fk` (`left_cortical_id`),
				KEY `et_ophciexamination_anteriorsegment_eye_id_fk` (`eye_id`),
				CONSTRAINT `et_ophciexamination_anteriorsegment_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
				CONSTRAINT `et_ophciexamination_anteriorsegment_ei_fk` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`),
				CONSTRAINT `et_ophciexamination_anteriorsegment_eye_id_fk` FOREIGN KEY (`eye_id`) REFERENCES `eye` (`id`),
				CONSTRAINT `et_ophciexamination_anteriorsegment_lci_fk` FOREIGN KEY (`left_cortical_id`) REFERENCES `ophciexamination_anteriorsegment_cortical` (`id`),
				CONSTRAINT `et_ophciexamination_anteriorsegment_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
				CONSTRAINT `et_ophciexamination_anteriorsegment_lni_fk` FOREIGN KEY (`left_nuclear_id`) REFERENCES `ophciexamination_anteriorsegment_nuclear` (`id`),
				CONSTRAINT `et_ophciexamination_anteriorsegment_lpi_fk` FOREIGN KEY (`left_pupil_id`) REFERENCES `ophciexamination_anteriorsegment_pupil` (`id`),
				CONSTRAINT `et_ophciexamination_anteriorsegment_rci_fk` FOREIGN KEY (`right_cortical_id`) REFERENCES `ophciexamination_anteriorsegment_cortical` (`id`),
				CONSTRAINT `et_ophciexamination_anteriorsegment_rni_fk` FOREIGN KEY (`right_nuclear_id`) REFERENCES `ophciexamination_anteriorsegment_nuclear` (`id`),
				CONSTRAINT `et_ophciexamination_anteriorsegment_rpi_fk` FOREIGN KEY (`right_pupil_id`) REFERENCES `ophciexamination_anteriorsegment_pupil` (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

        $this->execute("CREATE TABLE `et_ophciexamination_anteriorsegment_cct` (
				`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
				`event_id` int(10) unsigned NOT NULL,
				`eye_id` int(10) unsigned NOT NULL DEFAULT '3',
				`left_method_id` int(10) unsigned DEFAULT NULL,
				`right_method_id` int(10) unsigned DEFAULT NULL,
				`left_value` int(10) unsigned DEFAULT NULL,
				`right_value` int(10) unsigned DEFAULT NULL,
				`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
				`last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
				`created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
				`created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
				PRIMARY KEY (`id`),
				KEY `et_ophciexamination_anteriorsegment_cct_event_id_fk` (`event_id`),
				KEY `et_ophciexamination_anteriorsegment_cct_eye_id_fk` (`eye_id`),
				KEY `et_ophciexamination_anteriorsegment_cct_last_modified_user_id_fk` (`last_modified_user_id`),
				KEY `et_ophciexamination_anteriorsegment_cct_created_user_id_fk` (`created_user_id`),
				KEY `et_ophciexamination_anteriorsegment_cct_lmi_fk` (`left_method_id`),
				KEY `et_ophciexamination_anteriorsegment_cct_rmi_fk` (`right_method_id`),
				CONSTRAINT `et_ophciexamination_anteriorsegment_cct_event_id_fk` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`),
				CONSTRAINT `et_ophciexamination_anteriorsegment_cct_eye_id_fk` FOREIGN KEY (`eye_id`) REFERENCES `eye` (`id`),
				CONSTRAINT `et_ophciexamination_anteriorsegment_cct_last_modified_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
				CONSTRAINT `et_ophciexamination_anteriorsegment_cct_created_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
				CONSTRAINT `et_ophciexamination_anteriorsegment_cct_lmi_fk` FOREIGN KEY (`left_method_id`) REFERENCES `ophciexamination_anteriorsegment_cct_method` (`id`),
				CONSTRAINT `et_ophciexamination_anteriorsegment_cct_rmi_fk` FOREIGN KEY (`right_method_id`) REFERENCES `ophciexamination_anteriorsegment_cct_method` (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

        $this->execute("CREATE TABLE `et_ophciexamination_cataractmanagement` (
				`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
				`event_id` int(10) unsigned NOT NULL,
				`city_road` tinyint(1) unsigned NOT NULL DEFAULT '0',
				`satellite` tinyint(1) unsigned NOT NULL DEFAULT '0',
				`fast_track` tinyint(1) unsigned NOT NULL DEFAULT '0',
				`target_postop_refraction` decimal(5,1) NOT NULL DEFAULT '0.0',
				`correction_discussed` tinyint(1) unsigned NOT NULL,
				`suitable_for_surgeon_id` int(10) unsigned NOT NULL,
				`supervised` tinyint(1) unsigned NOT NULL DEFAULT '0',
				`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
				`last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
				`created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
				`created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
				`previous_refractive_surgery` tinyint(1) unsigned NOT NULL,
				`vitrectomised_eye` tinyint(1) unsigned NOT NULL DEFAULT '0',
				`eye_id` int(10) unsigned DEFAULT NULL,
				PRIMARY KEY (`id`),
				KEY `et_ophciexamination_management_event_id_fk` (`event_id`),
				KEY `et_ophciexamination_management_suitable_for_surgeon_id_fk` (`suitable_for_surgeon_id`),
				KEY `et_ophciexamination_management_last_modified_user_id_fk` (`last_modified_user_id`),
				KEY `et_ophciexamination_management_created_user_id_fk` (`created_user_id`),
				KEY `et_ophciexamination_cataractmanagement_eye_id_fk` (`eye_id`),
				CONSTRAINT `et_ophciexamination_cataractmanagement_eye_id_fk` FOREIGN KEY (`eye_id`) REFERENCES `ophciexamination_cataractmanagement_eye` (`id`),
				CONSTRAINT `et_ophciexamination_catmanagement_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
				CONSTRAINT `et_ophciexamination_catmanagement_event_id_fk` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`),
				CONSTRAINT `et_ophciexamination_catmanagement_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
				CONSTRAINT `et_ophciexamination_catmanagement_suitable_for_surgeon_id_fk` FOREIGN KEY (`suitable_for_surgeon_id`) REFERENCES `ophciexamination_cataractmanagement_suitable_for_surgeon` (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

        $this->execute("CREATE TABLE `et_ophciexamination_clinicoutcome` (
				`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
				`event_id` int(10) unsigned NOT NULL,
				`status_id` int(10) unsigned NOT NULL,
				`followup_quantity` int(10) unsigned DEFAULT NULL,
				`followup_period_id` int(10) unsigned DEFAULT NULL,
				`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
				`last_modified_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
				`created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
				`created_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
				`role_id` int(10) unsigned DEFAULT NULL,
				`role_comments` varchar(255) DEFAULT NULL,
				`community_patient` tinyint(1) unsigned NOT NULL DEFAULT '0',
				PRIMARY KEY (`id`),
				KEY `et_ophciexamination_clinicoutcome_lmui_fk` (`last_modified_user_id`),
				KEY `et_ophciexamination_clinicoutcome_cui_fk` (`created_user_id`),
				KEY `et_ophciexamination_clinicoutcome_status_fk` (`status_id`),
				KEY `et_ophciexamination_clinicoutcome_fup_p_fk` (`followup_period_id`),
				KEY `et_ophciexamination_clinicoutcome_ri_fk` (`role_id`),
				KEY `et_ophciexamination_clinicoutcome_event_id_fk` (`event_id`),
				CONSTRAINT `et_ophciexamination_clinicoutcome_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
				CONSTRAINT `et_ophciexamination_clinicoutcome_event_id_fk` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`),
				CONSTRAINT `et_ophciexamination_clinicoutcome_fup_p_fk` FOREIGN KEY (`followup_period_id`) REFERENCES `period` (`id`),
				CONSTRAINT `et_ophciexamination_clinicoutcome_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
				CONSTRAINT `et_ophciexamination_clinicoutcome_ri_fk` FOREIGN KEY (`role_id`) REFERENCES `ophciexamination_clinicoutcome_role` (`id`),
				CONSTRAINT `et_ophciexamination_clinicoutcome_status_fk` FOREIGN KEY (`status_id`) REFERENCES `ophciexamination_clinicoutcome_status` (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

        $this->execute("CREATE TABLE `et_ophciexamination_comorbidities` (
				`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
				`event_id` int(10) unsigned NOT NULL,
				`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
				`last_modified_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
				`created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
				`created_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
				`comments` text,
				PRIMARY KEY (`id`),
				KEY `et_ophciexamination_risks_e_id_fk` (`event_id`),
				KEY `et_ophciexamination_risks_c_u_id_fk` (`created_user_id`),
				KEY `et_ophciexamination_risks_l_m_u_id_fk` (`last_modified_user_id`),
				CONSTRAINT `et_ophciexamination_risks_c_u_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
				CONSTRAINT `et_ophciexamination_risks_e_id_fk` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`),
				CONSTRAINT `et_ophciexamination_risks_l_m_u_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

        $this->execute("CREATE TABLE `et_ophciexamination_conclusion` (
				`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
				`event_id` int(10) unsigned NOT NULL,
				`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
				`last_modified_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
				`created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
				`created_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
				`description` text,
				PRIMARY KEY (`id`),
				KEY `et_ophciexamination_conclusion_e_id_fk` (`event_id`),
				KEY `et_ophciexamination_conclusion_c_u_id_fk` (`created_user_id`),
				KEY `et_ophciexamination_conclusion_l_m_u_id_fk` (`last_modified_user_id`),
				CONSTRAINT `et_ophciexamination_conclusion_c_u_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
				CONSTRAINT `et_ophciexamination_conclusion_e_id_fk` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`),
				CONSTRAINT `et_ophciexamination_conclusion_l_m_u_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

        $this->execute("CREATE TABLE `et_ophciexamination_diagnoses` (
				`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
				`event_id` int(10) unsigned NOT NULL,
				`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
				`last_modified_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
				`created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
				`created_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
				PRIMARY KEY (`id`),
				KEY `et_ophciexamination_diagnosis_e_id_fk` (`event_id`),
				KEY `et_ophciexamination_diagnosis_c_u_id_fk` (`created_user_id`),
				KEY `et_ophciexamination_diagnosis_l_m_u_id_fk` (`last_modified_user_id`),
				CONSTRAINT `et_ophciexamination_diagnosis_c_u_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
				CONSTRAINT `et_ophciexamination_diagnosis_e_id_fk` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`),
				CONSTRAINT `et_ophciexamination_diagnosis_l_m_u_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

        $this->execute("CREATE TABLE `et_ophciexamination_dilation` (
				`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
				`event_id` int(10) unsigned NOT NULL,
				`eye_id` int(10) unsigned NOT NULL DEFAULT '3',
				`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
				`last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
				`created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
				`created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
				PRIMARY KEY (`id`),
				KEY `et_ophciexamination_dilation_event_id_fk` (`event_id`),
				KEY `et_ophciexamination_dilation_eye_id_fk` (`eye_id`),
				KEY `et_ophciexamination_dilation_last_modified_user_id_fk` (`last_modified_user_id`),
				KEY `et_ophciexamination_dilation_created_user_id_fk` (`created_user_id`),
				CONSTRAINT `et_ophciexamination_dilation_created_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
				CONSTRAINT `et_ophciexamination_dilation_event_id_fk` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`),
				CONSTRAINT `et_ophciexamination_dilation_eye_id_fk` FOREIGN KEY (`eye_id`) REFERENCES `eye` (`id`),
				CONSTRAINT `et_ophciexamination_dilation_last_modified_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

        $this->execute("CREATE TABLE `et_ophciexamination_drgrading` (
				`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
				`event_id` int(10) unsigned NOT NULL,
				`left_nscretinopathy_id` int(10) unsigned DEFAULT NULL,
				`right_nscretinopathy_id` int(10) unsigned DEFAULT NULL,
				`left_nscmaculopathy_id` int(10) unsigned DEFAULT NULL,
				`right_nscmaculopathy_id` int(10) unsigned DEFAULT NULL,
				`left_nscretinopathy_photocoagulation` tinyint(1) DEFAULT NULL,
				`right_nscretinopathy_photocoagulation` tinyint(1) DEFAULT NULL,
				`left_nscmaculopathy_photocoagulation` tinyint(1) DEFAULT NULL,
				`right_nscmaculopathy_photocoagulation` tinyint(1) DEFAULT NULL,
				`left_clinical_id` int(10) unsigned DEFAULT NULL,
				`right_clinical_id` int(10) unsigned DEFAULT NULL,
				`eye_id` int(10) unsigned DEFAULT '3',
				`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
				`last_modified_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
				`created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
				`created_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
				PRIMARY KEY (`id`),
				KEY `et_ophciexamination_drgrading_e_id_fk` (`event_id`),
				KEY `et_ophciexamination_drgrading_c_u_id_fk` (`created_user_id`),
				KEY `et_ophciexamination_drgrading_l_m_u_id_fk` (`last_modified_user_id`),
				KEY `et_ophciexamination_drgrading_l_nret_fk` (`left_nscretinopathy_id`),
				KEY `et_ophciexamination_drgrading_r_nret_fk` (`right_nscretinopathy_id`),
				KEY `et_ophciexamination_drgrading_l_nmac_fk` (`left_nscmaculopathy_id`),
				KEY `et_ophciexamination_drgrading_r_nmac_fk` (`right_nscmaculopathy_id`),
				KEY `et_ophciexamination_drgrading_l_clinical_fk` (`left_clinical_id`),
				KEY `et_ophciexamination_drgrading_r_clinical_fk` (`right_clinical_id`),
				KEY `et_ophciexamination_drgrading_eye_id_fk` (`eye_id`),
				CONSTRAINT `et_ophciexamination_drgrading_e_id_fk` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`),
				CONSTRAINT `et_ophciexamination_drgrading_c_u_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
				CONSTRAINT `et_ophciexamination_drgrading_l_m_u_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
				CONSTRAINT `et_ophciexamination_drgrading_l_nret_fk` FOREIGN KEY (`left_nscretinopathy_id`) REFERENCES `ophciexamination_drgrading_nscretinopathy` (`id`),
				CONSTRAINT `et_ophciexamination_drgrading_r_nret_fk` FOREIGN KEY (`right_nscretinopathy_id`) REFERENCES `ophciexamination_drgrading_nscretinopathy` (`id`),
				CONSTRAINT `et_ophciexamination_drgrading_l_nmac_fk` FOREIGN KEY (`left_nscmaculopathy_id`) REFERENCES `ophciexamination_drgrading_nscmaculopathy` (`id`),
				CONSTRAINT `et_ophciexamination_drgrading_r_nmac_fk` FOREIGN KEY (`right_nscmaculopathy_id`) REFERENCES `ophciexamination_drgrading_nscmaculopathy` (`id`),
				CONSTRAINT `et_ophciexamination_drgrading_l_clinical_fk` FOREIGN KEY (`left_clinical_id`) REFERENCES `ophciexamination_drgrading_clinical` (`id`),
				CONSTRAINT `et_ophciexamination_drgrading_r_clinical_fk` FOREIGN KEY (`right_clinical_id`) REFERENCES `ophciexamination_drgrading_clinical` (`id`),
				CONSTRAINT `et_ophciexamination_drgrading_eye_id_fk` FOREIGN KEY (`eye_id`) REFERENCES `eye` (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

        $this->execute("CREATE TABLE `et_ophciexamination_glaucomarisk` (
				`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
				`event_id` int(10) unsigned NOT NULL,
				`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
				`last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
				`created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
				`created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
				`risk_id` int(10) unsigned NOT NULL,
				PRIMARY KEY (`id`),
				KEY `et_ophciexamination_glaucomarisk_event_id_fk` (`event_id`),
				KEY `et_ophciexamination_glaucomarisk_last_modified_user_id_fk` (`last_modified_user_id`),
				KEY `et_ophciexamination_glaucomarisk_created_user_id_fk` (`created_user_id`),
				KEY `et_ophciexamination_glaucomarisk_risk_id_fk` (`risk_id`),
				CONSTRAINT `et_ophciexamination_glaucomarisk_risk_id_fk` FOREIGN KEY (`risk_id`) REFERENCES `ophciexamination_glaucomarisk_risk` (`id`),
				CONSTRAINT `et_ophciexamination_glaucomarisk_created_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
				CONSTRAINT `et_ophciexamination_glaucomarisk_event_id_fk` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`),
				CONSTRAINT `et_ophciexamination_glaucomarisk_last_modified_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

        $this->execute("CREATE TABLE `et_ophciexamination_gonioscopy` (
				`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
				`event_id` int(10) unsigned NOT NULL,
				`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
				`last_modified_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
				`created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
				`created_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
				`eye_id` int(10) unsigned NOT NULL DEFAULT '3',
				`left_gonio_sup_id` int(10) unsigned DEFAULT NULL,
				`left_gonio_tem_id` int(10) unsigned DEFAULT NULL,
				`left_gonio_nas_id` int(10) unsigned DEFAULT NULL,
				`left_gonio_inf_id` int(10) unsigned DEFAULT NULL,
				`right_gonio_sup_id` int(10) unsigned DEFAULT NULL,
				`right_gonio_tem_id` int(10) unsigned DEFAULT NULL,
				`right_gonio_nas_id` int(10) unsigned DEFAULT NULL,
				`right_gonio_inf_id` int(10) unsigned DEFAULT NULL,
				`left_van_herick_id` int(10) unsigned DEFAULT NULL,
				`right_van_herick_id` int(10) unsigned DEFAULT NULL,
				`left_description` text,
				`right_description` text,
				`left_eyedraw` text,
				`right_eyedraw` text,
				PRIMARY KEY (`id`),
				KEY `et_ophciexamination_gonioscopy_e_id_fk` (`event_id`),
				KEY `et_ophciexamination_gonioscopy_c_u_id_fk` (`created_user_id`),
				KEY `et_ophciexamination_gonioscopy_l_m_u_id_fk` (`last_modified_user_id`),
				KEY `et_ophciexamination_gonioscopy_eye_id_fk` (`eye_id`),
				KEY `et_ophciexamination_gonioscopy_left_gonio_sup_id_fk` (`left_gonio_sup_id`),
				KEY `et_ophciexamination_gonioscopy_right_gonio_sup_id_fk` (`right_gonio_sup_id`),
				KEY `et_ophciexamination_gonioscopy_left_gonio_tem_id_fk` (`left_gonio_tem_id`),
				KEY `et_ophciexamination_gonioscopy_right_gonio_tem_id_fk` (`right_gonio_tem_id`),
				KEY `et_ophciexamination_gonioscopy_left_gonio_nas_id_fk` (`left_gonio_nas_id`),
				KEY `et_ophciexamination_gonioscopy_right_gonio_nas_id_fk` (`right_gonio_nas_id`),
				KEY `et_ophciexamination_gonioscopy_left_gonio_inf_id_fk` (`left_gonio_inf_id`),
				KEY `et_ophciexamination_gonioscopy_right_gonio_inf_id_fk` (`right_gonio_inf_id`),
				KEY `et_ophciexamination_gonioscopy_left_van_herick_id_fk` (`left_van_herick_id`),
				KEY `et_ophciexamination_gonioscopy_right_van_herick_id_fk` (`right_van_herick_id`),
				CONSTRAINT `et_ophciexamination_gonioscopy_right_van_herick_id_fk` FOREIGN KEY (`right_van_herick_id`) REFERENCES `ophciexamination_gonioscopy_van_herick` (`id`),
				CONSTRAINT `et_ophciexamination_gonioscopy_c_u_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
				CONSTRAINT `et_ophciexamination_gonioscopy_eye_id_fk` FOREIGN KEY (`eye_id`) REFERENCES `eye` (`id`),
				CONSTRAINT `et_ophciexamination_gonioscopy_e_id_fk` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`),
				CONSTRAINT `et_ophciexamination_gonioscopy_left_gonio_inf_id_fk` FOREIGN KEY (`left_gonio_inf_id`) REFERENCES `ophciexamination_gonioscopy_description` (`id`),
				CONSTRAINT `et_ophciexamination_gonioscopy_left_gonio_nas_id_fk` FOREIGN KEY (`left_gonio_nas_id`) REFERENCES `ophciexamination_gonioscopy_description` (`id`),
				CONSTRAINT `et_ophciexamination_gonioscopy_left_gonio_sup_id_fk` FOREIGN KEY (`left_gonio_sup_id`) REFERENCES `ophciexamination_gonioscopy_description` (`id`),
				CONSTRAINT `et_ophciexamination_gonioscopy_left_gonio_tem_id_fk` FOREIGN KEY (`left_gonio_tem_id`) REFERENCES `ophciexamination_gonioscopy_description` (`id`),
				CONSTRAINT `et_ophciexamination_gonioscopy_left_van_herick_id_fk` FOREIGN KEY (`left_van_herick_id`) REFERENCES `ophciexamination_gonioscopy_van_herick` (`id`),
				CONSTRAINT `et_ophciexamination_gonioscopy_l_m_u_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
				CONSTRAINT `et_ophciexamination_gonioscopy_right_gonio_inf_id_fk` FOREIGN KEY (`right_gonio_inf_id`) REFERENCES `ophciexamination_gonioscopy_description` (`id`),
				CONSTRAINT `et_ophciexamination_gonioscopy_right_gonio_nas_id_fk` FOREIGN KEY (`right_gonio_nas_id`) REFERENCES `ophciexamination_gonioscopy_description` (`id`),
				CONSTRAINT `et_ophciexamination_gonioscopy_right_gonio_sup_id_fk` FOREIGN KEY (`right_gonio_sup_id`) REFERENCES `ophciexamination_gonioscopy_description` (`id`),
				CONSTRAINT `et_ophciexamination_gonioscopy_right_gonio_tem_id_fk` FOREIGN KEY (`right_gonio_tem_id`) REFERENCES `ophciexamination_gonioscopy_description` (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

        $this->execute("CREATE TABLE `et_ophciexamination_history` (
				`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
				`event_id` int(10) unsigned NOT NULL,
				`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
				`last_modified_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
				`created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
				`created_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
				`description` text,
				PRIMARY KEY (`id`),
				KEY `et_ophciexamination_history_e_id_fk` (`event_id`),
				KEY `et_ophciexamination_history_c_u_id_fk` (`created_user_id`),
				KEY `et_ophciexamination_history_l_m_u_id_fk` (`last_modified_user_id`),
				CONSTRAINT `et_ophciexamination_history_c_u_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
				CONSTRAINT `et_ophciexamination_history_e_id_fk` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`),
				CONSTRAINT `et_ophciexamination_history_l_m_u_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

        $this->execute("CREATE TABLE `et_ophciexamination_injectionmanagement` (
				`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
				`event_id` int(10) unsigned NOT NULL,
				`injection_status_id` int(10) unsigned NOT NULL,
				`injection_deferralreason_id` int(10) unsigned DEFAULT NULL,
				`injection_deferralreason_other` text,
				`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
				`last_modified_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
				`created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
				`created_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
				PRIMARY KEY (`id`),
				KEY `et_ophciexamination_injectionmanagement_lmui_fk` (`last_modified_user_id`),
				KEY `et_ophciexamination_injectionmanagement_cui_fk` (`created_user_id`),
				KEY `et_ophciexamination_injectionmanagement_injection_fk` (`injection_status_id`),
				KEY `et_ophciexamination_injectionmanagement_ideferral_fk` (`injection_deferralreason_id`),
				KEY `et_ophciexamination_injectionmanagement_event_id_fk` (`event_id`),
				CONSTRAINT `et_ophciexamination_injectionmanagement_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
				CONSTRAINT `et_ophciexamination_injectionmanagement_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
				CONSTRAINT `et_ophciexamination_injectionmanagement_injection_fk` FOREIGN KEY (`injection_status_id`) REFERENCES `ophciexamination_management_status` (`id`),
				CONSTRAINT `et_ophciexamination_injectionmanagement_ideferral_fk` FOREIGN KEY (`injection_deferralreason_id`) REFERENCES `ophciexamination_management_deferralreason` (`id`),
				CONSTRAINT `et_ophciexamination_injectionmanagement_event_id_fk` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

        $this->execute("CREATE TABLE `et_ophciexamination_injectionmanagementcomplex` (
				`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
				`event_id` int(10) unsigned NOT NULL,
				`eye_id` int(10) unsigned DEFAULT '3',
				`no_treatment` tinyint(1) NOT NULL DEFAULT '0',
				`no_treatment_reason_id` int(10) unsigned DEFAULT NULL,
				`left_diagnosis1_id` BIGINT unsigned DEFAULT NULL,
				`right_diagnosis1_id` BIGINT unsigned DEFAULT NULL,
				`left_diagnosis2_id` BIGINT unsigned DEFAULT NULL,
				`right_diagnosis2_id` BIGINT unsigned DEFAULT NULL,
				`left_comments` text,
				`right_comments` text,
				`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
				`last_modified_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
				`created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
				`created_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
				`no_treatment_reason_other` text,
				PRIMARY KEY (`id`),
				KEY `et_ophciexamination_injectionmanagementcomplex_lmui_fk` (`last_modified_user_id`),
				KEY `et_ophciexamination_injectionmanagementcomplex_cui_fk` (`created_user_id`),
				KEY `et_ophciexamination_injectionmanagementcomplex_eye_fk` (`eye_id`),
				KEY `et_ophciexamination_injectionmanagementcomplex_ldiag1_fk` (`left_diagnosis1_id`),
				KEY `et_ophciexamination_injectionmanagementcomplex_rdiag1_fk` (`right_diagnosis1_id`),
				KEY `et_ophciexamination_injectionmanagementcomplex_ldiag2_fk` (`left_diagnosis2_id`),
				KEY `et_ophciexamination_injectionmanagementcomplex_rdiag2_fk` (`right_diagnosis2_id`),
				KEY `et_ophciexamination_injectionmanagementcomplex_event_id_fk` (`event_id`),
				CONSTRAINT `et_ophciexamination_injectionmanagementcomplex_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
				CONSTRAINT `et_ophciexamination_injectionmanagementcomplex_event_id_fk` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`),
				CONSTRAINT `et_ophciexamination_injectionmanagementcomplex_eye_fk` FOREIGN KEY (`eye_id`) REFERENCES `eye` (`id`),
				CONSTRAINT `et_ophciexamination_injectionmanagementcomplex_ldiag1_fk` FOREIGN KEY (`left_diagnosis1_id`) REFERENCES `disorder` (`id`),
				CONSTRAINT `et_ophciexamination_injectionmanagementcomplex_ldiag2_fk` FOREIGN KEY (`left_diagnosis2_id`) REFERENCES `disorder` (`id`),
				CONSTRAINT `et_ophciexamination_injectionmanagementcomplex_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
				CONSTRAINT `et_ophciexamination_injectionmanagementcomplex_rdiag1_fk` FOREIGN KEY (`right_diagnosis1_id`) REFERENCES `disorder` (`id`),
				CONSTRAINT `et_ophciexamination_injectionmanagementcomplex_rdiag2_fk` FOREIGN KEY (`right_diagnosis2_id`) REFERENCES `disorder` (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

        $this->execute("CREATE TABLE `et_ophciexamination_intraocularpressure` (
				`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
				`event_id` int(10) unsigned NOT NULL,
				`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
				`last_modified_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
				`created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
				`created_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
				`left_instrument_id` int(10) unsigned DEFAULT NULL,
				`left_reading_id` int(10) unsigned NOT NULL,
				`right_instrument_id` int(10) unsigned DEFAULT NULL,
				`right_reading_id` int(10) unsigned NOT NULL,
				`eye_id` int(10) unsigned NOT NULL DEFAULT '3',
				PRIMARY KEY (`id`),
				KEY `et_ophciexamination_intraocularpressure_e_id_fk` (`event_id`),
				KEY `et_ophciexamination_intraocularpressure_c_u_id_fk` (`created_user_id`),
				KEY `et_ophciexamination_intraocularpressure_l_m_u_id_fk` (`last_modified_user_id`),
				KEY `et_ophciexamination_intraocularpressure_li_fk` (`left_instrument_id`),
				KEY `et_ophciexamination_intraocularpressure_ri_fk` (`right_instrument_id`),
				KEY `et_ophciexamination_intraocularpressure_lri_fk` (`left_reading_id`),
				KEY `et_ophciexamination_intraocularpressure_rri_fk` (`right_reading_id`),
				KEY `et_ophciexamination_intraocularpressure_eye_id_fk` (`eye_id`),
				CONSTRAINT `et_ophciexamination_intraocularpressure_eye_id_fk` FOREIGN KEY (`eye_id`) REFERENCES `eye` (`id`),
				CONSTRAINT `et_ophciexamination_intraocularpressure_c_u_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
				CONSTRAINT `et_ophciexamination_intraocularpressure_e_id_fk` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`),
				CONSTRAINT `et_ophciexamination_intraocularpressure_li_fk` FOREIGN KEY (`left_instrument_id`) REFERENCES `ophciexamination_instrument` (`id`),
				CONSTRAINT `et_ophciexamination_intraocularpressure_lri_fk` FOREIGN KEY (`left_reading_id`) REFERENCES `ophciexamination_intraocularpressure_reading` (`id`),
				CONSTRAINT `et_ophciexamination_intraocularpressure_l_m_u_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
				CONSTRAINT `et_ophciexamination_intraocularpressure_ri_fk` FOREIGN KEY (`right_instrument_id`) REFERENCES `ophciexamination_instrument` (`id`),
				CONSTRAINT `et_ophciexamination_intraocularpressure_rri_fk` FOREIGN KEY (`right_reading_id`) REFERENCES `ophciexamination_intraocularpressure_reading` (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

        $this->execute("CREATE TABLE `et_ophciexamination_investigation` (
				`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
				`event_id` int(10) unsigned NOT NULL,
				`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
				`last_modified_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
				`created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
				`created_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
				`description` text,
				PRIMARY KEY (`id`),
				KEY `et_ophciexamination_investigation_e_id_fk` (`event_id`),
				KEY `et_ophciexamination_investigation_c_u_id_fk` (`created_user_id`),
				KEY `et_ophciexamination_investigation_l_m_u_id_fk` (`last_modified_user_id`),
				CONSTRAINT `et_ophciexamination_investigation_c_u_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
				CONSTRAINT `et_ophciexamination_investigation_e_id_fk` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`),
				CONSTRAINT `et_ophciexamination_investigation_l_m_u_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

        $this->execute("CREATE TABLE `et_ophciexamination_lasermanagement` (
				`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
				`event_id` int(10) unsigned NOT NULL,
				`laser_status_id` int(10) unsigned NOT NULL,
				`laser_deferralreason_id` int(10) unsigned DEFAULT NULL,
				`laser_deferralreason_other` text,
				`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
				`last_modified_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
				`created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
				`created_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
				`eye_id` int(10) unsigned DEFAULT '3',
				`left_lasertype_id` int(10) unsigned DEFAULT NULL,
				`left_lasertype_other` varchar(128) DEFAULT NULL,
				`left_comments` text,
				`right_lasertype_id` int(10) unsigned DEFAULT NULL,
				`right_lasertype_other` varchar(128) DEFAULT NULL,
				`right_comments` text,
				PRIMARY KEY (`id`),
				KEY `et_ophciexamination_lasermanagement_lmui_fk` (`last_modified_user_id`),
				KEY `et_ophciexamination_lasermanagement_cui_fk` (`created_user_id`),
				KEY `et_ophciexamination_lasermanagement_laser_fk` (`laser_status_id`),
				KEY `et_ophciexamination_lasermanagement_ldeferral_fk` (`laser_deferralreason_id`),
				KEY `et_ophciexamination_lasermanagement_event_id_fk` (`event_id`),
				KEY `et_ophciexamination_lasermanagement_llt_fk` (`left_lasertype_id`),
				KEY `et_ophciexamination_lasermanagement_rlt_fk` (`right_lasertype_id`),
				CONSTRAINT `et_ophciexamination_lasermanagement_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
				CONSTRAINT `et_ophciexamination_lasermanagement_event_id_fk` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`),
				CONSTRAINT `et_ophciexamination_lasermanagement_laser_fk` FOREIGN KEY (`laser_status_id`) REFERENCES `ophciexamination_management_status` (`id`),
				CONSTRAINT `et_ophciexamination_lasermanagement_ldeferral_fk` FOREIGN KEY (`laser_deferralreason_id`) REFERENCES `ophciexamination_management_deferralreason` (`id`),
				CONSTRAINT `et_ophciexamination_lasermanagement_llt_fk` FOREIGN KEY (`left_lasertype_id`) REFERENCES `ophciexamination_lasermanagement_lasertype` (`id`),
				CONSTRAINT `et_ophciexamination_lasermanagement_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
				CONSTRAINT `et_ophciexamination_lasermanagement_rlt_fk` FOREIGN KEY (`right_lasertype_id`) REFERENCES `ophciexamination_lasermanagement_lasertype` (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

        $this->execute("CREATE TABLE `et_ophciexamination_management` (
				`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
				`event_id` int(10) unsigned NOT NULL,
				`comments` text,
				`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
				`last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
				`created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
				`created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
				PRIMARY KEY (`id`),
				KEY `et_ophciexamination_management_event_id_fk` (`event_id`),
				KEY `et_ophciexamination_management_last_modified_user_id_fk` (`last_modified_user_id`),
				KEY `et_ophciexamination_management_created_user_id_fk` (`created_user_id`),
				CONSTRAINT `et_ophciexamination_management_event_id_fk` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`),
				CONSTRAINT `et_ophciexamination_management_last_modified_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
				CONSTRAINT `et_ophciexamination_management_created_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

        $this->execute("CREATE TABLE `et_ophciexamination_oct` (
				`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
				`event_id` int(10) unsigned NOT NULL,
				`eye_id` int(10) unsigned NOT NULL DEFAULT '3',
				`left_method_id` int(10) unsigned DEFAULT NULL,
				`right_method_id` int(10) unsigned DEFAULT NULL,
				`left_crt` int(10) unsigned DEFAULT NULL,
				`right_crt` int(10) unsigned DEFAULT NULL,
				`left_sft` int(10) unsigned DEFAULT NULL,
				`right_sft` int(10) unsigned DEFAULT NULL,
				`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
				`last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
				`created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
				`created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
				PRIMARY KEY (`id`),
				KEY `et_ophciexamination_oct_event_id_fk` (`event_id`),
				KEY `et_ophciexamination_oct_eye_id_fk` (`eye_id`),
				KEY `et_ophciexamination_oct_last_modified_user_id_fk` (`last_modified_user_id`),
				KEY `et_ophciexamination_oct_created_user_id_fk` (`created_user_id`),
				KEY `et_ophciexamination_oct_lmid_fk` (`left_method_id`),
				KEY `et_ophciexamination_oct_rmid_fk` (`right_method_id`),
				CONSTRAINT `et_ophciexamination_oct_event_id_fk` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`),
				CONSTRAINT `et_ophciexamination_oct_eye_id_fk` FOREIGN KEY (`eye_id`) REFERENCES `eye` (`id`),
				CONSTRAINT `et_ophciexamination_oct_last_modified_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
				CONSTRAINT `et_ophciexamination_oct_created_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
				CONSTRAINT `et_ophciexamination_oct_lmid_fk` FOREIGN KEY (`left_method_id`) REFERENCES `ophciexamination_oct_method` (`id`),
				CONSTRAINT `et_ophciexamination_oct_rmid_fk` FOREIGN KEY (`right_method_id`) REFERENCES `ophciexamination_oct_method` (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

        $this->execute("CREATE TABLE `et_ophciexamination_opticdisc` (
				`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
				`event_id` int(10) unsigned NOT NULL,
				`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
				`last_modified_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
				`created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
				`created_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
				`eye_id` int(10) unsigned NOT NULL DEFAULT '3',
				`left_description` text,
				`right_description` text,
				`left_diameter` float(2,1) DEFAULT NULL,
				`right_diameter` float(2,1) DEFAULT NULL,
				`left_eyedraw` text,
				`right_eyedraw` text,
				`left_cd_ratio_id` int(10) unsigned DEFAULT NULL,
				`right_cd_ratio_id` int(10) unsigned DEFAULT NULL,
				`left_lens_id` int(10) unsigned DEFAULT NULL,
				`right_lens_id` int(10) unsigned DEFAULT NULL,
				PRIMARY KEY (`id`),
				KEY `et_ophciexamination_opticdisc_e_id_fk` (`event_id`),
				KEY `et_ophciexamination_opticdisc_c_u_id_fk` (`created_user_id`),
				KEY `et_ophciexamination_opticdisc_l_m_u_id_fk` (`last_modified_user_id`),
				KEY `et_ophciexamination_opticdisc_eye_id_fk` (`eye_id`),
				KEY `et_ophciexamination_opticdisc_left_cd_ratio_id_fk` (`left_cd_ratio_id`),
				KEY `et_ophciexamination_opticdisc_right_cd_ratio_id_fk` (`right_cd_ratio_id`),
				KEY `et_ophciexamination_opticdisc_lli` (`left_lens_id`),
				KEY `et_ophciexamination_opticdisc_rli` (`right_lens_id`),
				CONSTRAINT `et_ophciexamination_opticdisc_c_u_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
				CONSTRAINT `et_ophciexamination_opticdisc_eye_id_fk` FOREIGN KEY (`eye_id`) REFERENCES `eye` (`id`),
				CONSTRAINT `et_ophciexamination_opticdisc_e_id_fk` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`),
				CONSTRAINT `et_ophciexamination_opticdisc_left_cd_ratio_id_fk` FOREIGN KEY (`left_cd_ratio_id`) REFERENCES `ophciexamination_opticdisc_cd_ratio` (`id`),
				CONSTRAINT `et_ophciexamination_opticdisc_lli` FOREIGN KEY (`left_lens_id`) REFERENCES `ophciexamination_opticdisc_lens` (`id`),
				CONSTRAINT `et_ophciexamination_opticdisc_l_m_u_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
				CONSTRAINT `et_ophciexamination_opticdisc_right_cd_ratio_id_fk` FOREIGN KEY (`right_cd_ratio_id`) REFERENCES `ophciexamination_opticdisc_cd_ratio` (`id`),
				CONSTRAINT `et_ophciexamination_opticdisc_rli` FOREIGN KEY (`right_lens_id`) REFERENCES `ophciexamination_opticdisc_lens` (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

        $this->execute("CREATE TABLE `et_ophciexamination_posteriorpole` (
				`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
				`event_id` int(10) unsigned NOT NULL,
				`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
				`last_modified_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
				`created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
				`created_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
				`left_eyedraw` text,
				`left_description` text,
				`right_eyedraw` text,
				`right_description` text,
				`eye_id` int(10) unsigned NOT NULL DEFAULT '3',
				PRIMARY KEY (`id`),
				KEY `et_ophciexamination_posteriorpole_e_id_fk` (`event_id`),
				KEY `et_ophciexamination_posteriorpole_c_u_id_fk` (`created_user_id`),
				KEY `et_ophciexamination_posteriorpole_l_m_u_id_fk` (`last_modified_user_id`),
				KEY `et_ophciexamination_posteriorpole_eye_id_fk` (`eye_id`),
				CONSTRAINT `et_ophciexamination_posteriorpole_c_u_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
				CONSTRAINT `et_ophciexamination_posteriorpole_eye_id_fk` FOREIGN KEY (`eye_id`) REFERENCES `eye` (`id`),
				CONSTRAINT `et_ophciexamination_posteriorpole_e_id_fk` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`),
				CONSTRAINT `et_ophciexamination_posteriorpole_l_m_u_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

        $this->execute("CREATE TABLE `et_ophciexamination_pupillaryabnormalities` (
				`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
				`event_id` int(10) unsigned NOT NULL,
				`eye_id` int(10) unsigned NOT NULL DEFAULT '3',
				`left_abnormality_id` int(10) unsigned DEFAULT NULL,
				`right_abnormality_id` int(10) unsigned DEFAULT NULL,
				`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
				`last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
				`created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
				`created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
				PRIMARY KEY (`id`),
				KEY `et_ophciexamination_pupillaryabnormal_ei_fk` (`event_id`),
				KEY `et_ophciexamination_pupillaryabnormal_lmi_fk` (`last_modified_user_id`),
				KEY `et_ophciexamination_pupillaryabnormal_cui_fk` (`created_user_id`),
				KEY `et_ophciexamination_pupillaryabnormal_lai_fk` (`left_abnormality_id`),
				KEY `et_ophciexamination_pupillaryabnormal_rai_fk` (`right_abnormality_id`),
				CONSTRAINT `et_ophciexamination_pupillaryabnormal_ei_fk` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`),
				CONSTRAINT `et_ophciexamination_pupillaryabnormal_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
				CONSTRAINT `et_ophciexamination_pupillaryabnormal_lmi_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
				CONSTRAINT `et_ophciexamination_pupillaryabnormal_lai_fk` FOREIGN KEY (`left_abnormality_id`) REFERENCES `ophciexamination_pupillaryabnormalities_abnormality` (`id`),
				CONSTRAINT `et_ophciexamination_pupillaryabnormal_rai_fk` FOREIGN KEY (`right_abnormality_id`) REFERENCES `ophciexamination_pupillaryabnormalities_abnormality` (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

        $this->execute("CREATE TABLE `et_ophciexamination_refraction` (
				`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
				`event_id` int(10) unsigned NOT NULL,
				`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
				`last_modified_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
				`created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
				`created_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
				`left_sphere` decimal(5,2) DEFAULT NULL,
				`left_cylinder` decimal(5,2) DEFAULT NULL,
				`left_axis` int(3) DEFAULT NULL,
				`left_axis_eyedraw` text,
				`left_type_id` int(10) unsigned DEFAULT NULL,
				`right_sphere` decimal(5,2) DEFAULT NULL,
				`right_cylinder` decimal(5,2) DEFAULT NULL,
				`right_axis` int(3) DEFAULT NULL,
				`right_axis_eyedraw` text,
				`right_type_id` int(10) unsigned DEFAULT NULL,
				`left_type_other` varchar(100) DEFAULT NULL,
				`right_type_other` varchar(100) DEFAULT NULL,
				`eye_id` int(10) unsigned NOT NULL DEFAULT '3',
				PRIMARY KEY (`id`),
				KEY `et_ophciexamination_refraction_e_id_fk` (`event_id`),
				KEY `et_ophciexamination_refraction_c_u_id_fk` (`created_user_id`),
				KEY `et_ophciexamination_refraction_l_m_u_id_fk` (`last_modified_user_id`),
				KEY `et_ophciexamination_refraction_lti_fk` (`left_type_id`),
				KEY `et_ophciexamination_refraction_rti_fk` (`right_type_id`),
				KEY `et_ophciexamination_refraction_eye_id_fk` (`eye_id`),
				CONSTRAINT `et_ophciexamination_refraction_eye_id_fk` FOREIGN KEY (`eye_id`) REFERENCES `eye` (`id`),
				CONSTRAINT `et_ophciexamination_refraction_c_u_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
				CONSTRAINT `et_ophciexamination_refraction_e_id_fk` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`),
				CONSTRAINT `et_ophciexamination_refraction_lti_fk` FOREIGN KEY (`left_type_id`) REFERENCES `ophciexamination_refraction_type` (`id`),
				CONSTRAINT `et_ophciexamination_refraction_l_m_u_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
				CONSTRAINT `et_ophciexamination_refraction_rti_fk` FOREIGN KEY (`right_type_id`) REFERENCES `ophciexamination_refraction_type` (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

        $this->execute("CREATE TABLE `et_ophciexamination_risks` (
				`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
				`event_id` int(10) unsigned NOT NULL,
				`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
				`last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
				`created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
				`created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
				`comments` text,
				PRIMARY KEY (`id`),
				KEY `et_ophciexamination_risks_event_id_fk` (`event_id`),
				KEY `et_ophciexamination_risks_last_modified_user_id_fk` (`last_modified_user_id`),
				KEY `et_ophciexamination_risks_created_user_id_fk` (`created_user_id`),
				CONSTRAINT `et_ophciexamination_risks_created_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
				CONSTRAINT `et_ophciexamination_risks_event_id_fk` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`),
				CONSTRAINT `et_ophciexamination_risks_last_modified_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

        $this->execute("CREATE TABLE `et_ophciexamination_visual_fields` (
				`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
				`event_id` int(10) unsigned NOT NULL,
				`left_eyedraw` text,
				`right_eyedraw` text,
				`left_description` text,
				`right_description` text,
				`eye_id` int(10) unsigned NOT NULL DEFAULT '3',
				`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
				`last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
				`created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
				`created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
				PRIMARY KEY (`id`),
				KEY `et_ophciexamination_visual_acuity_event_id_fk` (`event_id`),
				KEY `et_ophciexamination_visual_acuity_last_modified_user_id_fk` (`last_modified_user_id`),
				KEY `et_ophciexamination_visual_acuity_created_user_id_fk` (`created_user_id`),
				KEY `et_ophciexamination_visual_acuity_eye_id_fk` (`eye_id`),
				CONSTRAINT `et_ophciexamination_visual_acuity_event_id_fk` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`),
				CONSTRAINT `et_ophciexamination_visual_acuity_last_modified_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
				CONSTRAINT `et_ophciexamination_visual_acuity_created_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
				CONSTRAINT `et_ophciexamination_visual_acuity_eye_id_fk` FOREIGN KEY (`eye_id`) REFERENCES `eye` (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

        $this->execute("CREATE TABLE `et_ophciexamination_visualacuity` (
				`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
				`event_id` int(10) unsigned NOT NULL,
				`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
				`last_modified_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
				`created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
				`created_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
				`left_comments` text,
				`right_comments` text,
				`eye_id` int(10) unsigned NOT NULL DEFAULT '3',
				`unit_id` int(10) unsigned NOT NULL,
				PRIMARY KEY (`id`),
				KEY `et_ophciexamination_visualacuity_e_id_fk` (`event_id`),
				KEY `et_ophciexamination_visualacuity_c_u_id_fk` (`created_user_id`),
				KEY `et_ophciexamination_visualacuity_l_m_u_id_fk` (`last_modified_user_id`),
				KEY `et_ophciexamination_visualacuity_eye_id_fk` (`eye_id`),
				KEY `et_ophciexamination_visualacuity_unit_fk` (`unit_id`),
				CONSTRAINT `et_ophciexamination_visualacuity_unit_fk` FOREIGN KEY (`unit_id`) REFERENCES `ophciexamination_visual_acuity_unit` (`id`),
				CONSTRAINT `et_ophciexamination_visualacuity_c_u_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
				CONSTRAINT `et_ophciexamination_visualacuity_eye_id_fk` FOREIGN KEY (`eye_id`) REFERENCES `eye` (`id`),
				CONSTRAINT `et_ophciexamination_visualacuity_e_id_fk` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`),
				CONSTRAINT `et_ophciexamination_visualacuity_l_m_u_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

        $this->execute("CREATE TABLE `ophciexamination_anteriorsegment_cct_method` (
				`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
				`name` varchar(255) NOT NULL,
				`display_order` int(10) unsigned NOT NULL,
				`created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
				`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
				`created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
				`last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
				PRIMARY KEY (`id`),
				KEY `ophciexamination_anteriorsegment_cct_method_cui_fk` (`created_user_id`),
				KEY `ophciexamination_anteriorsegment_cct_method_lmui_fk` (`last_modified_user_id`),
				CONSTRAINT `ophciexamination_anteriorsegment_cct_method_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
				CONSTRAINT `ophciexamination_anteriorsegment_cct_method_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

        $this->execute("CREATE TABLE `ophciexamination_anteriorsegment_cortical` (
				`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
				`name` varchar(128) DEFAULT NULL,
				`display_order` tinyint(3) unsigned DEFAULT '0',
				`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
				`last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
				`created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
				`created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
				`value` varchar(64) DEFAULT NULL,
				PRIMARY KEY (`id`),
				KEY `ophciexamination_anteriorsegment_cortical_lmui_fk` (`last_modified_user_id`),
				KEY `ophciexamination_anteriorsegment_cortical_cui_fk` (`created_user_id`),
				CONSTRAINT `ophciexamination_anteriorsegment_cortical_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
				CONSTRAINT `ophciexamination_anteriorsegment_cortical_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

        $this->execute("CREATE TABLE `ophciexamination_anteriorsegment_nuclear` (
				`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
				`name` varchar(128) DEFAULT NULL,
				`display_order` tinyint(3) unsigned DEFAULT '0',
				`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
				`last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
				`created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
				`created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
				`value` varchar(64) DEFAULT NULL,
				PRIMARY KEY (`id`),
				KEY `ophciexamination_anteriorsegment_nuclear_lmui_fk` (`last_modified_user_id`),
				KEY `ophciexamination_anteriorsegment_nuclear_cui_fk` (`created_user_id`),
				CONSTRAINT `ophciexamination_anteriorsegment_nuclear_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
				CONSTRAINT `ophciexamination_anteriorsegment_nuclear_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

        $this->execute("CREATE TABLE `ophciexamination_anteriorsegment_pupil` (
				`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
				`name` varchar(128) DEFAULT NULL,
				`display_order` tinyint(3) unsigned DEFAULT '0',
				`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
				`last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
				`created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
				`created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
				`value` varchar(64) DEFAULT NULL,
				PRIMARY KEY (`id`),
				KEY `ophciexamination_anteriorsegment_pupil_lmui_fk` (`last_modified_user_id`),
				KEY `ophciexamination_anteriorsegment_pupil_cui_fk` (`created_user_id`),
				CONSTRAINT `ophciexamination_anteriorsegment_pupil_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
				CONSTRAINT `ophciexamination_anteriorsegment_pupil_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

        $this->execute("CREATE TABLE `ophciexamination_attribute` (
				`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
				`name` varchar(40) NOT NULL,
				`label` varchar(255) NOT NULL,
				`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
				`last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
				`created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
				`created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
				PRIMARY KEY (`id`),
				KEY `ophciexamination_attribute_last_modified_user_id_fk` (`last_modified_user_id`),
				KEY `ophciexamination_attribute_created_user_id_fk` (`created_user_id`),
				CONSTRAINT `ophciexamination_attribute_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
				CONSTRAINT `ophciexamination_attribute_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

        $this->execute("CREATE TABLE `ophciexamination_attribute_element` (
				`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
				`attribute_id` int(10) unsigned NOT NULL,
				`element_type_id` int(10) unsigned NOT NULL,
				`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
				`last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
				`created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
				`created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
				PRIMARY KEY (`id`),
				KEY `ophciexamination_attribute_element_a_fk` (`attribute_id`),
				KEY `ophciexamination_attribute_element_et_fk` (`element_type_id`),
				KEY `ophciexamination_attribute_element_lmui_fk` (`last_modified_user_id`),
				KEY `ophciexamination_attribute_cui_fk` (`created_user_id`),
				CONSTRAINT `ophciexamination_attribute_element_a_fk` FOREIGN KEY (`attribute_id`) REFERENCES `ophciexamination_attribute` (`id`),
				CONSTRAINT `ophciexamination_attribute_element_et_fk` FOREIGN KEY (`element_type_id`) REFERENCES `element_type` (`id`),
				CONSTRAINT `ophciexamination_attribute_element_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
				CONSTRAINT `ophciexamination_attribute_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

        $this->execute("CREATE TABLE `ophciexamination_attribute_option` (
				`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
				`value` varchar(255) NOT NULL,
				`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
				`last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
				`created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
				`created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
				`delimiter` varchar(255) NOT NULL DEFAULT ',',
				`subspecialty_id` int(10) unsigned DEFAULT NULL,
				`attribute_element_id` int(10) unsigned NOT NULL,
				PRIMARY KEY (`id`),
				KEY `ophciexamination_attribute_option_last_modified_user_id_fk` (`last_modified_user_id`),
				KEY `ophciexamination_attribute_option_created_user_id_fk` (`created_user_id`),
				KEY `ophciexamination_attribute_option_ssi_fk` (`subspecialty_id`),
				KEY `ophciexamination_attribute_option_aei_fk` (`attribute_element_id`),
				CONSTRAINT `ophciexamination_attribute_option_aei_fk` FOREIGN KEY (`attribute_element_id`) REFERENCES `ophciexamination_attribute_element` (`id`),
				CONSTRAINT `ophciexamination_attribute_option_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
				CONSTRAINT `ophciexamination_attribute_option_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
				CONSTRAINT `ophciexamination_attribute_option_ssi_fk` FOREIGN KEY (`subspecialty_id`) REFERENCES `subspecialty` (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

        $this->execute("CREATE TABLE `ophciexamination_cataractmanagement_eye` (
				`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
				`name` varchar(64) NOT NULL,
				`display_order` tinyint(1) unsigned NOT NULL,
				`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
				`last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
				`created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
				`created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
				PRIMARY KEY (`id`),
				KEY `ophciexamination_cataractmanagement_eye_lmui_fk` (`last_modified_user_id`),
				KEY `ophciexamination_cataractmanagement_eye_cui_fk` (`created_user_id`),
				CONSTRAINT `ophciexamination_cataractmanagement_eye_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
				CONSTRAINT `ophciexamination_cataractmanagement_eye_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

        $this->execute("CREATE TABLE `ophciexamination_cataractmanagement_suitable_for_surgeon` (
				`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
				`name` varchar(64) DEFAULT NULL,
				`display_order` tinyint(3) unsigned NOT NULL DEFAULT '1',
				`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
				`last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
				`created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
				`created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
				PRIMARY KEY (`id`),
				KEY `ophciexamination_sfs_last_modified_user_id_fk` (`last_modified_user_id`),
				KEY `ophciexamination_sfs_created_user_id_fk` (`created_user_id`),
				CONSTRAINT `ophciexamination_sfs_last_modified_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
				CONSTRAINT `ophciexamination_sfs_created_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

        $this->execute("CREATE TABLE `ophciexamination_clinicoutcome_role` (
				`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
				`name` varchar(64) NOT NULL,
				`display_order` int(10) unsigned NOT NULL DEFAULT '10',
				`requires_comment` int(1) unsigned NOT NULL DEFAULT '0',
				`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
				`last_modified_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
				`created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
				`created_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
				PRIMARY KEY (`id`),
				KEY `ophciexamination_clinicoutcome_role_lmui_fk` (`last_modified_user_id`),
				KEY `ophciexamination_clinicoutcome_role_cui_fk` (`created_user_id`),
				CONSTRAINT `ophciexamination_clinicoutcome_role_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
				CONSTRAINT `ophciexamination_clinicoutcome_role_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

        $this->execute("CREATE TABLE `ophciexamination_clinicoutcome_status` (
				`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
				`name` varchar(128) NOT NULL,
				`display_order` int(10) unsigned NOT NULL DEFAULT '1',
				`followup` tinyint(1) NOT NULL DEFAULT '0',
				`episode_status_id` int(10) unsigned NOT NULL,
				`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
				`last_modified_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
				`created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
				`created_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
				PRIMARY KEY (`id`),
				KEY `ophciexamination_clinicoutcome_laser_lmui_fk` (`last_modified_user_id`),
				KEY `ophciexamination_clinicoutcome_laser_cui_fk` (`created_user_id`),
				KEY `ophciexamination_clinicoutcome_episode_status_fk` (`episode_status_id`),
				CONSTRAINT `ophciexamination_clinicoutcome_laser_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
				CONSTRAINT `ophciexamination_clinicoutcome_laser_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
				CONSTRAINT `ophciexamination_clinicoutcome_episode_status_fk` FOREIGN KEY (`episode_status_id`) REFERENCES `episode_status` (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

        $this->execute("CREATE TABLE `ophciexamination_clinicoutcome_template` (
				`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
				`followup_quantity` int(10) unsigned DEFAULT NULL,
				`clinic_outcome_status_id` int(10) unsigned NOT NULL,
				`followup_period_id` int(10) unsigned DEFAULT NULL,
				`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
				`last_modified_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
				`created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
				`created_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
				PRIMARY KEY (`id`),
				KEY `ophciexamination_clinicoutcome_template_lmui_fk` (`last_modified_user_id`),
				KEY `ophciexamination_clinicoutcome_template_cui_fk` (`created_user_id`),
				KEY `ophciexamination_clinicoutcome_template_cosi_fk` (`clinic_outcome_status_id`),
				KEY `ophciexamination_clinicoutcome_template_fpi_fk` (`followup_period_id`),
				CONSTRAINT `ophciexamination_clinicoutcome_template_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
				CONSTRAINT `ophciexamination_clinicoutcome_template_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
				CONSTRAINT `ophciexamination_clinicoutcome_template_cosi_fk` FOREIGN KEY (`clinic_outcome_status_id`) REFERENCES `ophciexamination_clinicoutcome_status` (`id`),
				CONSTRAINT `ophciexamination_clinicoutcome_template_fpi_fk` FOREIGN KEY (`followup_period_id`) REFERENCES `period` (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

        $this->execute("CREATE TABLE `ophciexamination_comorbidities_assignment` (
				`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
				`element_id` int(10) unsigned NOT NULL,
				`item_id` int(10) unsigned NOT NULL,
				`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
				`last_modified_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
				`created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
				`created_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
				PRIMARY KEY (`id`),
				KEY `ophciexamination_risks_assign_e_id_fk` (`element_id`),
				KEY `ophciexamination_risks_assign_r_id_fk` (`item_id`),
				KEY `ophciexamination_risks_assign_c_u_id_fk` (`created_user_id`),
				KEY `ophciexamination_risks_assign_l_m_u_id_fk` (`last_modified_user_id`),
				CONSTRAINT `ophciexamination_comorbidities_assign_i_id_fk` FOREIGN KEY (`item_id`) REFERENCES `ophciexamination_comorbidities_item` (`id`),
				CONSTRAINT `ophciexamination_comorbidities_assign_e_id_fk` FOREIGN KEY (`element_id`) REFERENCES `et_ophciexamination_comorbidities` (`id`),
				CONSTRAINT `ophciexamination_risks_assign_c_u_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
				CONSTRAINT `ophciexamination_risks_assign_l_m_u_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

        $this->execute("CREATE TABLE `ophciexamination_comorbidities_item` (
				`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
				`name` varchar(64) NOT NULL,
				`display_order` int(10) unsigned NOT NULL,
				`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
				`last_modified_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
				`created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
				`created_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
				PRIMARY KEY (`id`),
				KEY `ophciexamination_risks_risk_c_u_id_fk` (`created_user_id`),
				KEY `ophciexamination_risks_risk_l_m_u_id_fk` (`last_modified_user_id`),
				CONSTRAINT `ophciexamination_risks_risk_c_u_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
				CONSTRAINT `ophciexamination_risks_risk_l_m_u_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

        $this->execute("CREATE TABLE `ophciexamination_diagnosis` (
				`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
				`element_diagnoses_id` int(10) unsigned NOT NULL,
				`disorder_id` BIGINT unsigned NOT NULL,
				`eye_id` int(10) unsigned NOT NULL,
				`principal` tinyint(1) unsigned NOT NULL DEFAULT '0',
				`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
				`last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
				`created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
				`created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
				PRIMARY KEY (`id`),
				KEY `ophciexamination_diagnosis_element_diagnoses_id_fk` (`element_diagnoses_id`),
				KEY `ophciexamination_diagnosis_disorder_id_fk` (`disorder_id`),
				KEY `ophciexamination_diagnosis_eye_id_fk` (`eye_id`),
				KEY `ophciexamination_diagnosis_last_modified_user_id_fk` (`last_modified_user_id`),
				KEY `ophciexamination_diagnosis_created_user_id_fk` (`created_user_id`),
				CONSTRAINT `ophciexamination_diagnosis_element_diagnoses_id_fk` FOREIGN KEY (`element_diagnoses_id`) REFERENCES `et_ophciexamination_diagnoses` (`id`),
				CONSTRAINT `ophciexamination_diagnosis_disorder_id_fk` FOREIGN KEY (`disorder_id`) REFERENCES `disorder` (`id`),
				CONSTRAINT `ophciexamination_diagnosis_eye_id_fk` FOREIGN KEY (`eye_id`) REFERENCES `eye` (`id`),
				CONSTRAINT `ophciexamination_diagnosis_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
				CONSTRAINT `ophciexamination_diagnosis_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

        $this->execute("CREATE TABLE `ophciexamination_dilation_drugs` (
				`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
				`name` varchar(64) DEFAULT NULL,
				`display_order` tinyint(3) unsigned NOT NULL DEFAULT '1',
				`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
				`last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
				`created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
				`created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
				PRIMARY KEY (`id`),
				KEY `ophciexamination_dilation_drugs_last_modified_user_id_fk` (`last_modified_user_id`),
				KEY `ophciexamination_dilation_drugs_created_user_id_fk` (`created_user_id`),
				CONSTRAINT `ophciexamination_dilation_drugs_last_modified_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
				CONSTRAINT `ophciexamination_dilation_drugs_created_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

        $this->execute("CREATE TABLE `ophciexamination_dilation_treatment` (
				`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
				`element_id` int(10) unsigned NOT NULL,
				`side` tinyint(1) unsigned NOT NULL,
				`drug_id` int(10) unsigned NOT NULL,
				`drops` int(10) unsigned NOT NULL,
				`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
				`last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
				`created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
				`created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
				`treatment_time` time NOT NULL,
				PRIMARY KEY (`id`),
				KEY `ophciexamination_dilation_treatment_element_id_fk` (`element_id`),
				KEY `ophciexamination_dilation_treatment_last_modified_user_id_fk` (`last_modified_user_id`),
				KEY `ophciexamination_dilation_treatment_created_user_id_fk` (`created_user_id`),
				KEY `ophciexamination_dilation_treatment_drug_id_fk` (`drug_id`),
				CONSTRAINT `ophciexamination_dilation_treatment_created_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
				CONSTRAINT `ophciexamination_dilation_treatment_drug_id_fk` FOREIGN KEY (`drug_id`) REFERENCES `ophciexamination_dilation_drugs` (`id`),
				CONSTRAINT `ophciexamination_dilation_treatment_element_id_fk` FOREIGN KEY (`element_id`) REFERENCES `et_ophciexamination_dilation` (`id`),
				CONSTRAINT `ophciexamination_dilation_treatment_last_modified_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

        $this->execute("CREATE TABLE `ophciexamination_drgrading_clinical` (
				`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
				`name` varchar(128) NOT NULL,
				`description` text,
				`display_order` int(10) unsigned NOT NULL DEFAULT '1',
				`class` varchar(16) NOT NULL,
				`booking_weeks` int(2) unsigned DEFAULT NULL,
				`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
				`last_modified_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
				`created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
				`created_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
				PRIMARY KEY (`id`),
				KEY `ophciexamination_drgrading_clinical_lmui_fk` (`last_modified_user_id`),
				KEY `ophciexamination_drgrading_clinical_cui_fk` (`created_user_id`),
				CONSTRAINT `ophciexamination_drgrading_clinical_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
				CONSTRAINT `ophciexamination_drgrading_clinical_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

        $this->execute("CREATE TABLE `ophciexamination_drgrading_nscmaculopathy` (
				`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
				`name` varchar(128) NOT NULL,
				`description` text,
				`display_order` int(10) unsigned NOT NULL DEFAULT '1',
				`booking_weeks` int(2) unsigned DEFAULT NULL,
				`class` varchar(16) NOT NULL,
				`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
				`last_modified_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
				`created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
				`created_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
				PRIMARY KEY (`id`),
				KEY `ophciexamination_drgrading_nscmaculopathy_lmui_fk` (`last_modified_user_id`),
				KEY `ophciexamination_drgrading_nscmaculopathy_cui_fk` (`created_user_id`),
				CONSTRAINT `ophciexamination_drgrading_nscmaculopathy_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
				CONSTRAINT `ophciexamination_drgrading_nscmaculopathy_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

        $this->execute("CREATE TABLE `ophciexamination_drgrading_nscretinopathy` (
				`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
				`name` varchar(128) NOT NULL,
				`description` text,
				`display_order` int(10) unsigned NOT NULL DEFAULT '1',
				`booking_weeks` int(2) unsigned DEFAULT NULL,
				`class` varchar(16) NOT NULL,
				`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
				`last_modified_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
				`created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
				`created_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
				PRIMARY KEY (`id`),
				KEY `ophciexamination_drgrading_nscretinopathy_lmui_fk` (`last_modified_user_id`),
				KEY `ophciexamination_drgrading_nscretinopathy_cui_fk` (`created_user_id`),
				CONSTRAINT `ophciexamination_drgrading_nscretinopathy_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
				CONSTRAINT `ophciexamination_drgrading_nscretinopathy_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

        $this->execute("CREATE TABLE `ophciexamination_element_set` (
				`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
				`name` varchar(40) DEFAULT NULL,
				`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
				`last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
				`created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
				`created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
				`position` int(10) unsigned NOT NULL DEFAULT '1',
				`workflow_id` int(10) unsigned NOT NULL,
				PRIMARY KEY (`id`),
				KEY `ophciexamination_element_set_last_modified_user_id_fk` (`last_modified_user_id`),
				KEY `ophciexamination_element_set_created_user_id_fk` (`created_user_id`),
				KEY `ophciexamination_element_set_workflow_id_fk` (`workflow_id`),
				CONSTRAINT `ophciexamination_element_set_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
				CONSTRAINT `ophciexamination_element_set_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
				CONSTRAINT `ophciexamination_element_set_workflow_id_fk` FOREIGN KEY (`workflow_id`) REFERENCES `ophciexamination_workflow` (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

        $this->execute("CREATE TABLE `ophciexamination_element_set_item` (
				`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
				`set_id` int(10) unsigned NOT NULL,
				`element_type_id` int(10) unsigned NOT NULL,
				`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
				`last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
				`created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
				`created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
				PRIMARY KEY (`id`),
				KEY `ophciexamination_element_set_item_set_id_fk` (`set_id`),
				KEY `ophciexamination_element_set_item_element_type_id_fk` (`element_type_id`),
				KEY `ophciexamination_element_set_item_last_modified_user_id_fk` (`last_modified_user_id`),
				KEY `ophciexamination_element_set_item_created_user_id_fk` (`created_user_id`),
				CONSTRAINT `ophciexamination_element_set_item_set_id_fk` FOREIGN KEY (`set_id`) REFERENCES `ophciexamination_element_set` (`id`),
				CONSTRAINT `ophciexamination_element_set_item_element_type_id_fk` FOREIGN KEY (`element_type_id`) REFERENCES `element_type` (`id`),
				CONSTRAINT `ophciexamination_element_set_item_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
				CONSTRAINT `ophciexamination_element_set_item_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

        $this->execute("CREATE TABLE `ophciexamination_element_set_rule` (
				`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
				`parent_id` int(10) unsigned DEFAULT NULL,
				`clause` varchar(255) DEFAULT NULL,
				`value` varchar(255) DEFAULT NULL,
				`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
				`last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
				`created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
				`created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
				`workflow_id` int(10) unsigned DEFAULT NULL,
				PRIMARY KEY (`id`),
				KEY `ophciexamination_element_set_rule_parent_id_fk` (`parent_id`),
				KEY `ophciexamination_element_set_rule_last_modified_user_id_fk` (`last_modified_user_id`),
				KEY `ophciexamination_element_set_rule_created_user_id_fk` (`created_user_id`),
				KEY `ophciexamination_element_set_rule_workflow_id_fk` (`workflow_id`),
				CONSTRAINT `ophciexamination_element_set_rule_workflow_id_fk` FOREIGN KEY (`workflow_id`) REFERENCES `ophciexamination_workflow` (`id`),
				CONSTRAINT `ophciexamination_element_set_rule_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
				CONSTRAINT `ophciexamination_element_set_rule_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
				CONSTRAINT `ophciexamination_element_set_rule_parent_id_fk` FOREIGN KEY (`parent_id`) REFERENCES `ophciexamination_element_set_rule` (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

        $this->execute("CREATE TABLE `ophciexamination_event_elementset_assignment` (
				`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
				`step_id` int(10) unsigned NOT NULL,
				`event_id` int(10) unsigned NOT NULL,
				`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
				`last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
				`created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
				`created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
				PRIMARY KEY (`id`),
				UNIQUE KEY `ophciexamination_event_ea_event_id_unique` (`event_id`),
				KEY `ophciexamination_event_ea_step_id_fk` (`step_id`),
				KEY `ophciexamination_event_ea_event_id_fk` (`event_id`),
				KEY `ophciexamination_event_ea_last_modified_user_id_fk` (`last_modified_user_id`),
				KEY `ophciexamination_event_ea_created_user_id_fk` (`created_user_id`),
				CONSTRAINT `ophciexamination_event_ea_step_id_fk` FOREIGN KEY (`step_id`) REFERENCES `ophciexamination_element_set` (`id`),
				CONSTRAINT `ophciexamination_event_ea_event_id_fk` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`),
				CONSTRAINT `ophciexamination_event_ea_last_modified_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
				CONSTRAINT `ophciexamination_event_ea_created_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

        $this->execute("CREATE TABLE `ophciexamination_glaucomarisk_risk` (
				`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
				`name` varchar(48) NOT NULL,
				`description` text,
				`follow_up` varchar(48) DEFAULT NULL,
				`review` varchar(48) DEFAULT NULL,
				`display_order` int(10) unsigned NOT NULL,
				`class` varchar(16) DEFAULT NULL,
				`clinicoutcome_template_id` int(10) unsigned NOT NULL,
				`created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
				`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
				`created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
				`last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
				PRIMARY KEY (`id`),
				KEY `ophciexamination_glaucomarisk_risk_coti_fk` (`clinicoutcome_template_id`),
				KEY `ophciexamination_glaucomarisk_risk_cui_fk` (`created_user_id`),
				KEY `ophciexamination_glaucomarisk_risk_lmui_fk` (`last_modified_user_id`),
				CONSTRAINT `ophciexamination_glaucomarisk_risk_coti_fk` FOREIGN KEY (`clinicoutcome_template_id`) REFERENCES `ophciexamination_clinicoutcome_template` (`id`),
				CONSTRAINT `ophciexamination_glaucomarisk_risk_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
				CONSTRAINT `ophciexamination_glaucomarisk_risk_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

        $this->execute("CREATE TABLE `ophciexamination_gonioscopy_description` (
				`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
				`name` varchar(40) NOT NULL,
				`display_order` tinyint(3) unsigned DEFAULT '0',
				`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
				`last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
				`created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
				`created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
				`seen` tinyint(1) unsigned NOT NULL DEFAULT '1',
				PRIMARY KEY (`id`),
				KEY `ophciexamination_gonioscopy_description_lmuid_fk` (`last_modified_user_id`),
				KEY `ophciexamination_gonioscopy_description_cuid_fk` (`created_user_id`),
				CONSTRAINT `ophciexamination_gonioscopy_description_cuid_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
				CONSTRAINT `ophciexamination_gonioscopy_description_lmuid_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

        $this->execute("CREATE TABLE `ophciexamination_gonioscopy_van_herick` (
				`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
				`name` varchar(40) NOT NULL,
				`display_order` tinyint(3) unsigned DEFAULT '0',
				`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
				`last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
				`created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
				`created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
				PRIMARY KEY (`id`),
				KEY `ophciexamination_gonioscopy_van_herick_lmuid_fk` (`last_modified_user_id`),
				KEY `ophciexamination_gonioscopy_van_herick_cuid_fk` (`created_user_id`),
				CONSTRAINT `ophciexamination_gonioscopy_van_herick_lmuid_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
				CONSTRAINT `ophciexamination_gonioscopy_van_herick_cuid_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

        $this->execute("CREATE TABLE `ophciexamination_injectmanagecomplex_answer` (
				`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
				`element_id` int(10) unsigned NOT NULL,
				`eye_id` int(10) unsigned NOT NULL,
				`question_id` int(10) unsigned NOT NULL,
				`answer` tinyint(1) NOT NULL,
				`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
				`last_modified_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
				`created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
				`created_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
				PRIMARY KEY (`id`),
				KEY `ophciexamination_injectmanagecomplex_answer_lmui_fk` (`last_modified_user_id`),
				KEY `ophciexamination_injectmanagecomplex_answer_cui_fk` (`created_user_id`),
				KEY `ophciexamination_injectmanagecomplex_answer_eli_fk` (`element_id`),
				KEY `ophciexamination_injectmanagecomplex_answer_eyei_fk` (`eye_id`),
				KEY `ophciexamination_injectmanagecomplex_answer_qi_fk` (`question_id`),
				CONSTRAINT `ophciexamination_injectmanagecomplex_answer_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
				CONSTRAINT `ophciexamination_injectmanagecomplex_answer_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
				CONSTRAINT `ophciexamination_injectmanagecomplex_answer_eli_fk` FOREIGN KEY (`element_id`) REFERENCES `et_ophciexamination_injectionmanagementcomplex` (`id`),
				CONSTRAINT `ophciexamination_injectmanagecomplex_answer_eyei_fk` FOREIGN KEY (`eye_id`) REFERENCES `eye` (`id`),
				CONSTRAINT `ophciexamination_injectmanagecomplex_answer_qi_fk` FOREIGN KEY (`question_id`) REFERENCES `ophciexamination_injectmanagecomplex_question` (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

        $this->execute("CREATE TABLE `ophciexamination_injectmanagecomplex_notreatmentreason` (
				`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
				`name` varchar(128) NOT NULL,
				`display_order` int(10) unsigned NOT NULL DEFAULT '1',
				`enabled` tinyint(1) NOT NULL DEFAULT '1',
				`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
				`last_modified_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
				`created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
				`created_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
				`other` tinyint(1) NOT NULL DEFAULT '0',
				PRIMARY KEY (`id`),
				KEY `ophciexamination_injectmanagecomplex_notreatmentreason_lmui_fk` (`last_modified_user_id`),
				KEY `ophciexamination_injectmanagecomplex_notreatmentreason_cui_fk` (`created_user_id`),
				CONSTRAINT `ophciexamination_injectmanagecomplex_notreatmentreason_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
				CONSTRAINT `ophciexamination_injectmanagecomplex_notreatmentreason_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

        $this->execute("CREATE TABLE `ophciexamination_injectmanagecomplex_question` (
				`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
				`disorder_id` BIGINT unsigned NOT NULL,
				`question` varchar(128) NOT NULL,
				`display_order` int(10) unsigned NOT NULL DEFAULT '1',
				`enabled` tinyint(1) NOT NULL DEFAULT '1',
				`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
				`last_modified_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
				`created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
				`created_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
				PRIMARY KEY (`id`),
				KEY `ophciexamination_injectmanagecomplex_question_lmui_fk` (`last_modified_user_id`),
				KEY `ophciexamination_injectmanagecomplex_question_cui_fk` (`created_user_id`),
				KEY `ophciexamination_injectmanagecomplex_question_disorder_fk` (`disorder_id`),
				CONSTRAINT `ophciexamination_injectmanagecomplex_question_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
				CONSTRAINT `ophciexamination_injectmanagecomplex_question_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
				CONSTRAINT `ophciexamination_injectmanagecomplex_question_disorder_fk` FOREIGN KEY (`disorder_id`) REFERENCES `disorder` (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

        $this->execute("CREATE TABLE `ophciexamination_injectmanagecomplex_risk` (
				`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
				`name` varchar(256) NOT NULL,
				`display_order` int(10) unsigned NOT NULL DEFAULT '1',
				`enabled` tinyint(1) NOT NULL DEFAULT '1',
				`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
				`last_modified_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
				`created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
				`created_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
				PRIMARY KEY (`id`),
				KEY `ophciexamination_injectmanagecomplex_risk_lmui_fk` (`last_modified_user_id`),
				KEY `ophciexamination_injectmanagecomplex_risk_cui_fk` (`created_user_id`),
				CONSTRAINT `ophciexamination_injectmanagecomplex_risk_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
				CONSTRAINT `ophciexamination_injectmanagecomplex_risk_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

        $this->execute("CREATE TABLE `ophciexamination_injectmanagecomplex_risk_assignment` (
				`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
				`element_id` int(10) unsigned NOT NULL,
				`eye_id` int(10) unsigned NOT NULL DEFAULT '3',
				`risk_id` int(10) unsigned NOT NULL,
				`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
				`last_modified_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
				`created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
				`created_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
				PRIMARY KEY (`id`),
				KEY `ophciexamination_injectmanagecomplex_risk_assignment_lmui_fk` (`last_modified_user_id`),
				KEY `ophciexamination_injectmanagecomplex_risk_assignment_cui_fk` (`created_user_id`),
				KEY `ophciexamination_injectmanagecomplex_risk_assignment_ele_fk` (`element_id`),
				KEY `ophciexamination_injectmanagecomplex_risk_assign_eye_id_fk` (`eye_id`),
				KEY `ophciexamination_injectmanagecomplex_risk_assignment_lku_fk` (`risk_id`),
				CONSTRAINT `ophciexamination_injectmanagecomplex_risk_assignment_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
				CONSTRAINT `ophciexamination_injectmanagecomplex_risk_assignment_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
				CONSTRAINT `ophciexamination_injectmanagecomplex_risk_assignment_ele_fk` FOREIGN KEY (`element_id`) REFERENCES `et_ophciexamination_injectionmanagementcomplex` (`id`),
				CONSTRAINT `ophciexamination_injectmanagecomplex_risk_assign_eye_id_fk` FOREIGN KEY (`eye_id`) REFERENCES `eye` (`id`),
				CONSTRAINT `ophciexamination_injectmanagecomplex_risk_assignment_lku_fk` FOREIGN KEY (`risk_id`) REFERENCES `ophciexamination_injectmanagecomplex_risk` (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

        $this->execute("CREATE TABLE `ophciexamination_instrument` (
				`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
				`name` varchar(255) NOT NULL,
				`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
				`last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
				`created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
				`created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
				`display_order` int(10) unsigned DEFAULT '1',
				PRIMARY KEY (`id`),
				KEY `ophciexamination_instrument_last_modified_user_id_fk` (`last_modified_user_id`),
				KEY `ophciexamination_instrument_created_user_id_fk` (`created_user_id`),
				CONSTRAINT `ophciexamination_instrument_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
				CONSTRAINT `ophciexamination_instrument_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

        $this->execute("CREATE TABLE `ophciexamination_intraocularpressure_reading` (
				`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
				`name` varchar(3) DEFAULT NULL,
				`value` int(10) unsigned DEFAULT NULL,
				`display_order` tinyint(3) unsigned DEFAULT '0',
				`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
				`last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
				`created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
				`created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
				PRIMARY KEY (`id`),
				KEY `ophciexamination_intraocularpressure_reading_lmui_fk` (`last_modified_user_id`),
				KEY `ophciexamination_intraocularpressure_reading_cui_fk` (`created_user_id`),
				CONSTRAINT `ophciexamination_intraocularpressure_reading_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
				CONSTRAINT `ophciexamination_intraocularpressure_reading_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

        $this->execute("CREATE TABLE `ophciexamination_lasermanagement_lasertype` (
				`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
				`name` varchar(128) NOT NULL,
				`display_order` int(10) unsigned NOT NULL DEFAULT '1',
				`other` tinyint(1) NOT NULL DEFAULT '0',
				`enabled` tinyint(1) NOT NULL DEFAULT '1',
				`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
				`last_modified_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
				`created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
				`created_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
				PRIMARY KEY (`id`),
				KEY `ophciexamination_lasermanagement_lasertype_lmui_fk` (`last_modified_user_id`),
				KEY `ophciexamination_lasermanagement_lasertype_cui_fk` (`created_user_id`),
				CONSTRAINT `ophciexamination_lasermanagement_lasertype_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
				CONSTRAINT `ophciexamination_lasermanagement_lasertype_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

        $this->execute("CREATE TABLE `ophciexamination_management_deferralreason` (
				`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
				`name` varchar(128) NOT NULL,
				`display_order` int(10) unsigned NOT NULL DEFAULT '1',
				`other` tinyint(1) NOT NULL DEFAULT '0',
				`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
				`last_modified_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
				`created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
				`created_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
				PRIMARY KEY (`id`),
				KEY `ophciexamination_management_ldeferral_lmui_fk` (`last_modified_user_id`),
				KEY `ophciexamination_management_ldeferral_cui_fk` (`created_user_id`),
				CONSTRAINT `ophciexamination_management_ldeferral_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
				CONSTRAINT `ophciexamination_management_ldeferral_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

        $this->execute("CREATE TABLE `ophciexamination_management_status` (
				`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
				`name` varchar(128) NOT NULL,
				`display_order` int(10) unsigned NOT NULL DEFAULT '1',
				`deferred` tinyint(1) NOT NULL DEFAULT '0',
				`book` tinyint(1) NOT NULL DEFAULT '0',
				`event` tinyint(1) NOT NULL DEFAULT '0',
				`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
				`last_modified_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
				`created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
				`created_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
				PRIMARY KEY (`id`),
				KEY `ophciexamination_management_laser_lmui_fk` (`last_modified_user_id`),
				KEY `ophciexamination_management_laser_cui_fk` (`created_user_id`),
				CONSTRAINT `ophciexamination_management_laser_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
				CONSTRAINT `ophciexamination_management_laser_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

        $this->execute("CREATE TABLE `ophciexamination_oct_method` (
				`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
				`name` varchar(255) NOT NULL,
				`display_order` int(10) unsigned NOT NULL,
				`created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
				`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
				`created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
				`last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
				PRIMARY KEY (`id`),
				KEY `ophciexamination_oct_method_cui_fk` (`created_user_id`),
				KEY `ophciexamination_oct_method_lmui_fk` (`last_modified_user_id`),
				CONSTRAINT `ophciexamination_oct_method_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
				CONSTRAINT `ophciexamination_oct_method_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

        $this->execute("CREATE TABLE `ophciexamination_opticdisc_cd_ratio` (
				`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
				`name` varchar(64) NOT NULL,
				`display_order` int(10) unsigned NOT NULL,
				`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
				`last_modified_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
				`created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
				`created_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
				PRIMARY KEY (`id`),
				KEY `ophciexamination_opticdisc_cd_ratio_c_u_id_fk` (`created_user_id`),
				KEY `ophciexamination_opticdisc_cd_ratio_l_m_u_id_fk` (`last_modified_user_id`),
				CONSTRAINT `ophciexamination_opticdisc_cd_ratio_c_u_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
				CONSTRAINT `ophciexamination_opticdisc_cd_ratio_l_m_u_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

        $this->execute("CREATE TABLE `ophciexamination_opticdisc_lens` (
				`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
				`name` varchar(255) NOT NULL,
				`display_order` int(10) unsigned NOT NULL,
				`created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
				`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
				`created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
				`last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
				PRIMARY KEY (`id`),
				KEY `ophciexamination_opticdisc_lens_cui_fk` (`created_user_id`),
				KEY `ophciexamination_opticdisc_lens_lmui_fk` (`last_modified_user_id`),
				CONSTRAINT `ophciexamination_opticdisc_lens_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
				CONSTRAINT `ophciexamination_opticdisc_lens_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

        $this->execute("CREATE TABLE `ophciexamination_pupillaryabnormalities_abnormality` (
				`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
				`name` varchar(255) NOT NULL,
				`display_order` int(10) unsigned NOT NULL DEFAULT '1',
				`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
				`last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
				`created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
				`created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
				PRIMARY KEY (`id`),
				KEY `et_ophciexamination_pupillaryabnormalities_lmui_fk` (`last_modified_user_id`),
				KEY `et_ophciexamination_pupillaryabnormalities_cui_fk` (`created_user_id`),
				CONSTRAINT `et_ophciexamination_pupillaryabnormalities_lmui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
				CONSTRAINT `et_ophciexamination_pupillaryabnormalities_cui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

        $this->execute("CREATE TABLE `ophciexamination_refraction_fraction` (
				`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
				`name` varchar(4) DEFAULT NULL,
				`value` varchar(3) DEFAULT NULL,
				`display_order` tinyint(3) unsigned DEFAULT '0',
				`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
				`last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
				`created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
				`created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
				PRIMARY KEY (`id`),
				KEY `ophciexamination_refraction_fraction_lmui_fk` (`last_modified_user_id`),
				KEY `ophciexamination_refraction_fraction_cui_fk` (`created_user_id`),
				CONSTRAINT `ophciexamination_refraction_fraction_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
				CONSTRAINT `ophciexamination_refraction_fraction_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

        $this->execute("CREATE TABLE `ophciexamination_refraction_integer` (
				`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
				`value` varchar(4) DEFAULT NULL,
				`display_order` tinyint(3) unsigned DEFAULT '0',
				`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
				`last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
				`created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
				`created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
				PRIMARY KEY (`id`),
				KEY `ophciexamination_refraction_integer_lmui_fk` (`last_modified_user_id`),
				KEY `ophciexamination_refraction_integer_cui_fk` (`created_user_id`),
				CONSTRAINT `ophciexamination_refraction_integer_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
				CONSTRAINT `ophciexamination_refraction_integer_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

        $this->execute("CREATE TABLE `ophciexamination_refraction_sign` (
				`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
				`name` varchar(4) DEFAULT NULL,
				`value` varchar(4) DEFAULT NULL,
				`display_order` tinyint(3) unsigned DEFAULT '0',
				`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
				`last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
				`created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
				`created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
				PRIMARY KEY (`id`),
				KEY `ophciexamination_refraction_sign_lmui_fk` (`last_modified_user_id`),
				KEY `ophciexamination_refraction_sign_cui_fk` (`created_user_id`),
				CONSTRAINT `ophciexamination_refraction_sign_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
				CONSTRAINT `ophciexamination_refraction_sign_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

        $this->execute("CREATE TABLE `ophciexamination_refraction_type` (
				`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
				`name` varchar(32) DEFAULT NULL,
				`display_order` tinyint(3) unsigned DEFAULT '0',
				`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
				`last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
				`created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
				`created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
				PRIMARY KEY (`id`),
				KEY `ophciexamination_refraction_type_lmui_fk` (`last_modified_user_id`),
				KEY `ophciexamination_refraction_type_cui_fk` (`created_user_id`),
				CONSTRAINT `ophciexamination_refraction_type_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
				CONSTRAINT `ophciexamination_refraction_type_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

        $this->execute("CREATE TABLE `ophciexamination_visual_acuity_unit` (
				`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
				`name` varchar(40) NOT NULL,
				`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
				`last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
				`created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
				`created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
				`tooltip` tinyint(1) NOT NULL DEFAULT '0',
				`information` text,
				PRIMARY KEY (`id`),
				KEY `ophciexamination_visual_acuity_unit_lmuid_fk` (`last_modified_user_id`),
				KEY `ophciexamination_visual_acuity_unit_cuid_fk` (`created_user_id`),
				CONSTRAINT `ophciexamination_visual_acuity_unit_cuid_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
				CONSTRAINT `ophciexamination_visual_acuity_unit_lmuid_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

        $this->execute("CREATE TABLE `ophciexamination_visual_acuity_unit_value` (
				`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
				`unit_id` int(10) unsigned NOT NULL,
				`value` varchar(255) NOT NULL,
				`base_value` int(10) unsigned NOT NULL,
				`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
				`last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
				`created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
				`created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
				`selectable` tinyint(1) NOT NULL DEFAULT '1',
				PRIMARY KEY (`id`),
				KEY `ophciexamination_visual_acuity_unit_value_unit_id_fk` (`unit_id`),
				KEY `ophciexamination_visual_acuity_unit_value_lmuid_fk` (`last_modified_user_id`),
				KEY `ophciexamination_visual_acuity_unit_value_cuid_fk` (`created_user_id`),
				CONSTRAINT `ophciexamination_visual_acuity_unit_value_cuid_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
				CONSTRAINT `ophciexamination_visual_acuity_unit_value_lmuid_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
				CONSTRAINT `ophciexamination_visual_acuity_unit_value_unit_id_fk` FOREIGN KEY (`unit_id`) REFERENCES `ophciexamination_visual_acuity_unit` (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

        $this->execute("CREATE TABLE `ophciexamination_visualacuity_method` (
				`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
				`name` varchar(32) DEFAULT NULL,
				`display_order` tinyint(3) unsigned DEFAULT '0',
				`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
				`last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
				`created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
				`created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
				PRIMARY KEY (`id`),
				KEY `ophciexamination_visualacuity_method_lmui_fk` (`last_modified_user_id`),
				KEY `ophciexamination_visualacuity_method_cui_fk` (`created_user_id`),
				CONSTRAINT `ophciexamination_visualacuity_method_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
				CONSTRAINT `ophciexamination_visualacuity_method_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

        $this->execute("CREATE TABLE `ophciexamination_visualacuity_reading` (
				`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
				`element_id` int(10) unsigned NOT NULL,
				`value` int(10) unsigned NOT NULL,
				`method_id` int(10) unsigned NOT NULL,
				`side` tinyint(1) unsigned NOT NULL,
				`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
				`last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
				`created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
				`created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
				PRIMARY KEY (`id`),
				KEY `ophciexamination_visualacuity_reading_element_id_fk` (`element_id`),
				KEY `ophciexamination_visualacuity_reading_method_id_fk` (`method_id`),
				KEY `ophciexamination_visualacuity_reading_last_modified_user_id_fk` (`last_modified_user_id`),
				KEY `ophciexamination_visualacuity_reading_created_user_id_fk` (`created_user_id`),
				CONSTRAINT `ophciexamination_visualacuity_reading_element_id_fk` FOREIGN KEY (`element_id`) REFERENCES `et_ophciexamination_visualacuity` (`id`),
				CONSTRAINT `ophciexamination_visualacuity_reading_method_id_fk` FOREIGN KEY (`method_id`) REFERENCES `ophciexamination_visualacuity_method` (`id`),
				CONSTRAINT `ophciexamination_visualacuity_reading_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
				CONSTRAINT `ophciexamination_visualacuity_reading_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

        $this->execute("CREATE TABLE `ophciexamination_workflow` (
				`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
				`name` varchar(64) NOT NULL,
				`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
				`last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
				`created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
				`created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
				PRIMARY KEY (`id`),
				KEY `ophciexamination_workflow_last_modified_user_id_fk` (`last_modified_user_id`),
				KEY `ophciexamination_workflow_created_user_id_fk` (`created_user_id`),
				CONSTRAINT `ophciexamination_workflow_last_modified_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
				CONSTRAINT `ophciexamination_workflow_created_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

        $migrations_path = dirname(__FILE__);
        $this->initialiseData($migrations_path);

        $this->execute('SET foreign_key_checks = 1');
    }

    private function loadPatientShortcodes()
    {
        $event_type = $this->dbConnection->createCommand()->select('*')->from('event_type')->where('class_name = :class_name', array(':class_name' => 'OphCiExamination'))->queryRow();

        foreach ($this->patients_shortcodes as $patient_shortcode) {
            if (!preg_match('/^[a-zA-Z]{3}$/', $patient_shortcode[0])) {
                throw new Exception("Invalid shortcode: $patient_shortcode[0]");
            }

            $code = $default_code = $patient_shortcode[0];

            if ($this->dbConnection->createCommand()->select('*')->from('patient_shortcode')->where('code = :code', array(':code' => strtolower($patient_shortcode[0])))->queryRow()) {
                $n = '00';
                while ($this->dbConnection->createCommand()->select('*')->from('patient_shortcode')->where('code = :code', array(':code' => 'z'.$n))->queryRow()) {
                    $n = str_pad((int) $n + 1, 2, '0', STR_PAD_LEFT);
                }
                $code = "z$n";

                echo "Warning: attempt to register duplicate shortcode '$default_code', replaced with 'z$n'\n";
            }

            $this->insert('patient_shortcode', array(
                'event_type_id' => $event_type['id'],
                'code' => $code,
                'default_code' => $default_code,
                'method' => $patient_shortcode[1],
                'description' => $patient_shortcode[2],
            ));
        }
    }

    private function deletePatientShortcodes()
    {
        foreach ($this->patients_shortcodes as $patient_shortcode) {
            $this->delete('patient_shortcode', 'default_code = \''.$patient_shortcode[0].'\'');
        }
    }
}
