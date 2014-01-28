<?php
/**
 * OpenEyes
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

class m130913_000000_consolidation extends OEMigration
{

	public function up()
	{
		if (!$this->consolidate(
			array(
				"m120223_000000_consolidation",
				"m120223_071223_tweak_available_event_types",
				"m120302_000000_add_patient_date_of_death",
				"m120302_092216_pas_patient_assignment",
				"m120302_132027_episode_status",
				"m120302_135905_last_modified_and_created_fields_for_episode_status_table",
				"m120302_141438_store_episode_status_with_episode",
				"m120302_142758_add_order_column_to_episode_status_table",
				"m120302_171747_drop_first_in_element_field_from_site_element_type",
				"m120302_172414_rename_specialty_table_sub_specialty",
				"m120306_102802_remove_duplicated_site_element_type_rows",
				"m120306_121306_new_specialty_table",
				"m120306_135347_specialty_id_field_on_subspecialty_table",
				"m120306_161357_event_group_table",
				"m120306_170530_class_names_for_event_type_table",
				"m120307_143141_fix_class_names_on_event_type_table",
				"m120307_144421_add_event_type_id_to_element_type_table",
				"m120307_172115_remove_site_element_type_and_possible_element_type_tables",
				"m120308_150621_add_default_field_to_element_type_table",
				"m120312_114412_session_independence",
				"m120313_155343_rename_event_types",
				"m120319_143521_generic_anaesthetic_type_table",
				"m120319_151821_adjust_anaesthetic_type_table",
				"m120319_152650_update_element_type_anaesthetic_type",
				"m120319_161629_fix_existing_element_operation_anaesthetic_type_values",
				"m120319_162220_eo_anaesthetic_type_default_value",
				"m120320_140929_eye_table",
				"m120320_153814_priority_table",
				"m120321_172135_is_doctor_field_on_user_table",
				"m120327_154616_remove_pas_patient_assignment",
				"m120328_161726_event_issue_table",
				"m120328_164911_add_user_and_date_fields_to_issue_and_event_issue_tables",
				"m120328_190925_event_information_field",
				"m120331_131955_generic_anaesthetic_tables",
				"m120402_093653_add_hidden_flag_to_event_table",
				"m120403_153556_drug_tables",
				"m120411_120308_site_subspecialty_drug_defaults_table",
				"m120411_143652_site_subspecialty_anaesthetics",
				"m120412_133957_fix_duplicate_procedure_410563000",
				"m120413_121631_adjust_anaesthetic_delivery",
				"m120413_125641_new_drugs_and_devices",
				"m120418_155652_fix_event_info_missing_data",
				"m120424_094328_remove_default_eye_id_for_diagnosis_and_operation",
				"m120424_101219_refactor_patient_table_with_contact_table",
				"m120427_102856_institution_table_and_contact_mapping",
				"m120427_161025_additional_fields_for_consultant_table",
				"m120510_135100_allergy_table_and_drug_changes",
				"m120511_101458_patient_consultant_assignment_should_be_patient_contact_assignment",
				"m120515_115300_drug_sets",
				"m120515_145100_patient_allergies",
				"m120515_172600_drug_route_options",
				"m120518_130703_site_consultant_assignment_table",
				"m120518_135103_site_insitution_mapping",
				"m120518_142641_site_id_and_insititution_id_fields_in_patient_contact_assignment_table",
				"m120523_102017_new_audit_table",
				"m120523_154923_deleted_field_on_event_table",
				"m120529_154204_add_missing_user_and_date_fields_to_patient_allergy_assignment_table",
				"m120606_172600_add_long_name_to_drug_frequency",
				"m120612_125100_add_index_to_audit_trail",
				"m120613_162100_remove_examination_elements",
				"m120615_102038_gender_table",
				"m120615_102526_language_table",
				"m120615_134904_drop_element_type_name_field_unique_key",
				"m120618_113619_element_settings",
				"m120621_102952_fix_procedure_name",
				"m120627_113710_legacy_event_group",
				"m120629_072425_add_specialist_table",
				"m120630_195352_legacy_tweaks",
				"m120705_070306_specialist_type_and_specialist_site_assignment_tables",
				"m120705_140906_add_index_to_contact_lastname_for_search_speed",
				"m120711_094034_fix_city_road_site_name",
				"m120711_122546_repopulate_event_datetime_from_created",
				"m120717_154100_rationalise_drug_name_cols",
				"m120720_114800_rationalise_drug_specialty_cols",
				"m120806_165500_add_indices_to_non_fk_relations",
				"m120809_070713_northwick_park_changes",
				"m120809_093634_create_a_contact_for_all_users",
				"m120809_142529_add_limbal_relaxing_incision_to_cataract_common_procedures",
				"m120810_095120_fix_moorfields_site_addresses",
				"m120810_103523_upney_lane_site_address",
				"m120813_073133_ucwords_the_site_names",
				"m120816_085927_remove_nightingale_nursing_home_site",
				"m120824_074626_site_table_changes_and_reply_to_addresses",
				"m120830_070915_set_default_eye_for_element_diagnosis",
				"m120830_071031_set_default_eye_for_element_operation",
				"m120917_122307_soft_deleted_flat_for_episode_table",
				"m120918_140159_change_instances_of_char_datatype_to_varchar",
				"m120921_080410_erod_table",
				"m120927_073830_add_discontinued_field_to_drug_table",
				"m120927_075937_add_new_durations_to_drug_duration_table",
				"m121001_144444_add_description_field",
				"m121002_121025_new_multiple_diagnoses_table",
				"m121004_074006_populate_common_systemic_disorders",
				"m121004_083121_add_display_order_to_eye_table",
				"m121004_110700_practices",
				"m121008_135637_audit_event_group",
				"m121009_094438_audit_table_extend_action_field",
				"m121017_142250_outcomes_event_type",
				"m121026_083852_erod_rule_table",
				"m121029_134559_transport_list_indexes",
				"m121031_085020_remove_hyphens_from_nhs_numbers",
				"m121108_165957_multiple_specialism_disorders",
				"m121114_110229_patient_oph_info",
				"m121114_132152_remove_booking_tables",
				"m121204_102309_parent_elements",
				"m121217_150806_add_period_lookup",
				"m130109_153600_required_element_type_field",
				"m130114_101503_procedure_complications",
				"m130114_152007_procedure_additional",
				"m130115_083943_unbooked_procedures",
				"m130116_095906_fluorescein_procedure",
				"m130117_105611_multiple_specialties",
				"m130118_122927_link_procedure_complications_with_services",
				"m130121_083227_benefits_and_risks_should_be_linked_to_subspecialty_rather_than_service",
				"m130121_100122_proc_icce",
				"m130131_161008_disorder_tree",
				"m130218_085437_new_procs_oe2661",
				"m130222_115501_complications_and_benefits",
				"m130228_152358_new_unbooked_procedures",
				"m130301_094914_ozurdex_proc",
				"m130301_113502_drug_frequency_display_order",
				"m130306_105300_add_ethnic_group_to_patient",
				"m130320_141259_user_access_level_field",
				"m130320_144412_contacts_refactoring",
				"m130325_083633_patient_previous_operations",
				"m130325_133841_patient_family_history",
				"m130403_070819_new_family_history_options",
				"m130409_101123_give_non_consultant_users_a_staff_contact_label",
				"m130409_103405_remove_unnecessary_contact_id_field",
				"m130409_175500_drug_table_changes",
				"m130412_091339_ethnic_group_code_varchar",
				"m130412_160300_anaesthetic_type_order",
				"m130424_072109_fluorescein_angiography_should_be_unbooked",
				"m130424_095050_trabectome_procedure",
				"m130424_121820_patient_medication",
				"m130426_082237_trabectome_is_common_for_glaucoma",
				"m130426_172700_add_last_site",
				"m130429_133030_site_institution_import_tables",
				"m130429_150500_asset_table",
				"m130430_141855_person_import_fields",
				"m130430_181000_rename_assets",
				"m130503_135207_new_specialty_table",
				"m130507_145850_missing_contact_location_foreign_key",
				"m130507_151147_missing_person_contact_foreign_key",
				"m130513_132400_nullable_drug_keys",
				"m130514_140800_user_firm_preference",
				"m130517_122245_user_table_contact_id_should_be_nullable",
				"m130520_095926_new_user_fields",
				"m130523_113015_new_access_level",
				"m130528_094310_nhs_choices_import_source",
				"m130528_142301_social_worker_directory_source",
				"m130529_094234_person_remote_id_needs_to_be_40_chars_for_sha1",
				"m130529_133023_specialty_adjectives",
				"m130530_073132_patient_contacts_without_locations",
				"m130530_131019_support_services_firm",
				"m130531_134136_event_type_support_services",
				"m130603_114507_normalise_audit_table",
				"m130604_093335_patient_shortcode_table",
				"m130607_132110_user_site_and_firm_selection",
				"m130613_124300_drug_set_tapering",
				"m130625_141711_trabectome_snomed_code",
				"m130709_090359_dr_patientshortcodes",
				"m130711_133446_commissioning_bodies",
				"m130716_103017_drop_datetime_field",
				"m130717_102933_complications_and_benefits_should_be_linked_to_procedure_only",
				"m130717_142302_specialty_abbreviation",
				"m130726_084841_referrals",
				"m130726_135103_commissioning_body_table_names",
				"m130802_155809_audit_event_type_and_model_name",
				"m130912_153500_remove_unneeded_cols_from_user_session"
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

	public function createTables()
	{
		$this->execute("SET foreign_key_checks = 0");
		$this->execute(
			"CREATE TABLE `address` (
							 `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
							 `address1` varchar(255) DEFAULT NULL,
							 `address2` varchar(255) DEFAULT NULL,
							 `city` varchar(255) DEFAULT NULL,
							 `postcode` varchar(10) DEFAULT NULL,
							 `county` varchar(255) DEFAULT NULL,
							 `country_id` int(10) unsigned NOT NULL,
							 `email` varchar(255) DEFAULT NULL,
							 `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 `parent_class` varchar(40) NOT NULL,
							 `parent_id` int(10) unsigned NOT NULL,
							 `date_start` datetime DEFAULT NULL,
							 `date_end` datetime DEFAULT NULL,
							 `address_type_id` int(10) unsigned DEFAULT NULL,
							 PRIMARY KEY (`id`),
							 KEY `address_country_id_fk` (`country_id`),
							 KEY `address_last_modified_user_id_fk` (`last_modified_user_id`),
							 KEY `address_created_user_id_fk` (`created_user_id`),
							 KEY `address_parent_index` (`parent_class`,`parent_id`),
							 KEY `address_address_type_id_fk` (`address_type_id`),
							 CONSTRAINT `address_address_type_id_fk` FOREIGN KEY (`address_type_id`) REFERENCES `address_type` (`id`),
							 CONSTRAINT `address_country_id_fk` FOREIGN KEY (`country_id`) REFERENCES `country` (`id`),
							 CONSTRAINT `address_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
							 CONSTRAINT `address_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
							)
							ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci"
		);

		$this->execute(
			"CREATE TABLE `address_type` (
							 `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
							 `name` varchar(64) NOT NULL,
							 `last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 PRIMARY KEY (`id`),
							 KEY `address_type_last_modified_user_id_fk` (`last_modified_user_id`),
							 KEY `address_type_created_user_id_fk` (`created_user_id`),
							 CONSTRAINT `address_type_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
							 CONSTRAINT `address_type_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
							)
							ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci"
		);

		$this->execute(
			"CREATE TABLE `allergy` (
							 `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
							 `name` varchar(40) DEFAULT NULL,
							 `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 PRIMARY KEY (`id`),
							 KEY `allergy_last_modified_user_id_fk` (`last_modified_user_id`),
							 KEY `allergy_created_user_id_fk` (`created_user_id`),
							 CONSTRAINT `allergy_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
							 CONSTRAINT `allergy_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)
							)
							ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci"
		);

		$this->execute(
			"CREATE TABLE `anaesthetic_agent` (
							 `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
							 `name` varchar(64) NOT NULL,
							 `display_order` tinyint(3) unsigned NOT NULL,
							 `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 PRIMARY KEY (`id`),
							 KEY `et_ophtroperationnote_agent_last_modified_user_id_fk` (`last_modified_user_id`),
							 KEY `et_ophtroperationnote_agent_created_user_id_fk` (`created_user_id`),
							 CONSTRAINT `et_ophtroperationnote_agent_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
							 CONSTRAINT `et_ophtroperationnote_agent_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
							)
							ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci"
		);

		$this->execute(
			"CREATE TABLE `anaesthetic_complication` (
							 `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
							 `name` varchar(64) NOT NULL,
							 `display_order` tinyint(3) unsigned NOT NULL,
							 `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 PRIMARY KEY (`id`),
							 KEY `et_ophtroperationnote_age_last_modified_user_id_fk` (`last_modified_user_id`),
							 KEY `et_ophtroperationnote_age_created_user_id_fk` (`created_user_id`),
							 CONSTRAINT `et_ophtroperationnote_age_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
							 CONSTRAINT `et_ophtroperationnote_age_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
							)
							ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci"
		);

		$this->execute(
			"CREATE TABLE `anaesthetic_delivery` (
							 `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
							 `name` varchar(64) NOT NULL,
							 `display_order` tinyint(3) unsigned NOT NULL,
							 `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 PRIMARY KEY (`id`),
							 KEY `et_ophtroperationnote_del_last_modified_user_id_fk` (`last_modified_user_id`),
							 KEY `et_ophtroperationnote_del_created_user_id_fk` (`created_user_id`),
							 CONSTRAINT `et_ophtroperationnote_del_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
							 CONSTRAINT `et_ophtroperationnote_del_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
							)
							ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci"
		);

		$this->execute(
			"CREATE TABLE `anaesthetic_type` (
							 `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
							 `name` varchar(255) NOT NULL DEFAULT '',
							 `code` varchar(3) NOT NULL DEFAULT '',
							 PRIMARY KEY (`id`)
							)
							ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci"
		);

		$this->execute(
			"CREATE TABLE `anaesthetist` (
							 `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
							 `name` varchar(64) NOT NULL,
							 `display_order` tinyint(3) unsigned NOT NULL,
							 `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 PRIMARY KEY (`id`),
							 KEY `anaesthetist_last_modified_user_id_fk` (`last_modified_user_id`),
							 KEY `anaesthetist_created_user_id_fk` (`created_user_id`),
							 CONSTRAINT `anaesthetist_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
							 CONSTRAINT `anaesthetist_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
							)
							ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci"
		);

		$this->execute(
			"CREATE TABLE `audit` (
							 `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
							 `action_id` int(10) unsigned NOT NULL,
							 `type_id` int(10) unsigned NOT NULL,
							 `patient_id` int(10) unsigned DEFAULT NULL,
							 `episode_id` int(10) unsigned DEFAULT NULL,
							 `event_id` int(10) unsigned DEFAULT NULL,
							 `user_id` int(10) unsigned DEFAULT NULL,
							 `data` text ,
							 `ipaddr_id` int(10) unsigned DEFAULT NULL,
							 `useragent_id` int(10) unsigned DEFAULT NULL,
							 `server_id` int(10) unsigned DEFAULT NULL,
							 `request_uri` varchar(255) DEFAULT '',
							 `site_id` int(10) unsigned DEFAULT NULL,
							 `firm_id` int(10) unsigned DEFAULT NULL,
							 `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 `event_type_id` int(10) unsigned DEFAULT NULL,
							 `model_id` int(10) unsigned DEFAULT NULL,
							 `module_id` int(10) unsigned DEFAULT NULL,
							 PRIMARY KEY (`id`),
							 KEY `audit_patient_id_fk` (`patient_id`),
							 KEY `audit_episode_id_fk` (`episode_id`),
							 KEY `audit_event_id_fk` (`event_id`),
							 KEY `audit_user_id_fk` (`user_id`),
							 KEY `audit_site_id_fk` (`site_id`),
							 KEY `audit_firm_id_fk` (`firm_id`),
							 KEY `audit_action_id_fk` (`action_id`),
							 KEY `audit_type_id_fk` (`type_id`),
							 KEY `audit_ipaddr_id_fk` (`ipaddr_id`),
							 KEY `audit_useragent_id_fk` (`useragent_id`),
							 KEY `audit_server_id_fk` (`server_id`),
							 KEY `audit_event_type_id_fk` (`event_type_id`),
							 KEY `audit_model_id_fk` (`model_id`),
							 KEY `audit_module_id_fk` (`module_id`),
							 CONSTRAINT `audit_ibfk_1` FOREIGN KEY (`event_type_id`) REFERENCES `event_type` (`id`),
							 CONSTRAINT `audit_ibfk_2` FOREIGN KEY (`model_id`) REFERENCES `audit_model` (`id`),
							 CONSTRAINT `audit_ibfk_3` FOREIGN KEY (`module_id`) REFERENCES `audit_module` (`id`),
							 CONSTRAINT `audit_action_id_fk` FOREIGN KEY (`action_id`) REFERENCES `audit_action` (`id`),
							 CONSTRAINT `audit_episode_id_fk` FOREIGN KEY (`episode_id`) REFERENCES `episode` (`id`),
							 CONSTRAINT `audit_event_id_fk` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`),
							 CONSTRAINT `audit_firm_id_fk` FOREIGN KEY (`firm_id`) REFERENCES `firm` (`id`),
							 CONSTRAINT `audit_ipaddr_id_fk` FOREIGN KEY (`ipaddr_id`) REFERENCES `audit_ipaddr` (`id`),
							 CONSTRAINT `audit_patient_id_fk` FOREIGN KEY (`patient_id`) REFERENCES `patient` (`id`),
							 CONSTRAINT `audit_server_id_fk` FOREIGN KEY (`server_id`) REFERENCES `audit_server` (`id`),
							 CONSTRAINT `audit_site_id_fk` FOREIGN KEY (`site_id`) REFERENCES `site` (`id`),
							 CONSTRAINT `audit_type_id_fk` FOREIGN KEY (`type_id`) REFERENCES `audit_type` (`id`),
							 CONSTRAINT `audit_useragent_id_fk` FOREIGN KEY (`useragent_id`) REFERENCES `audit_useragent` (`id`),
							 CONSTRAINT `audit_user_id_fk` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
							)
							ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci"
		);

		$this->execute(
			"CREATE TABLE `audit_action` (
							 `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
							 `name` varchar(64) NOT NULL,
							 `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `last_modified_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
							 `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `created_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
							 PRIMARY KEY (`id`),
							 KEY `audit_action_lmui_fk` (`last_modified_user_id`),
							 KEY `audit_action_cui_fk` (`created_user_id`),
							 CONSTRAINT `audit_action_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
							 CONSTRAINT `audit_action_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)
							)
							ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci"
		);

		$this->execute(
			"CREATE TABLE `audit_ipaddr` (
							 `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
							 `name` varchar(16) NOT NULL,
							 `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `last_modified_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
							 `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `created_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
							 PRIMARY KEY (`id`),
							 KEY `audit_ipaddr_lmui_fk` (`last_modified_user_id`),
							 KEY `audit_ipaddr_cui_fk` (`created_user_id`),
							 CONSTRAINT `audit_ipaddr_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
							 CONSTRAINT `audit_ipaddr_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)
							)
							ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci"
		);

		$this->execute(
			"CREATE TABLE `audit_model` (
							 `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
							 `name` varchar(64) NOT NULL,
							 `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `last_modified_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
							 `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `created_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
							 PRIMARY KEY (`id`),
							 KEY `audit_model_lmui_fk` (`last_modified_user_id`),
							 KEY `audit_model_cui_fk` (`created_user_id`),
							 CONSTRAINT `audit_model_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
							 CONSTRAINT `audit_model_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)
							)
							ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci"
		);

		$this->execute(
			"CREATE TABLE `audit_module` (
							 `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
							 `name` varchar(64) NOT NULL,
							 `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `last_modified_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
							 `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `created_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
							 PRIMARY KEY (`id`),
							 KEY `audit_module_lmui_fk` (`last_modified_user_id`),
							 KEY `audit_module_cui_fk` (`created_user_id`),
							 CONSTRAINT `audit_module_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
							 CONSTRAINT `audit_module_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)
							)
							ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci"
		);

		$this->execute(
			"CREATE TABLE `audit_server` (
							 `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
							 `name` varchar(64) NOT NULL,
							 `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `last_modified_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
							 `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `created_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
							 PRIMARY KEY (`id`),
							 KEY `audit_server_lmui_fk` (`last_modified_user_id`),
							 KEY `audit_server_cui_fk` (`created_user_id`),
							 CONSTRAINT `audit_server_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
							 CONSTRAINT `audit_server_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)
							)
							ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci"
		);

		$this->execute(
			"CREATE TABLE `audit_type` (
							 `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
							 `name` varchar(64) NOT NULL,
							 `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `last_modified_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
							 `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `created_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
							 PRIMARY KEY (`id`),
							 KEY `audit_type_lmui_fk` (`last_modified_user_id`),
							 KEY `audit_type_cui_fk` (`created_user_id`),
							 CONSTRAINT `audit_type_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
							 CONSTRAINT `audit_type_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)
							)
							ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci"
		);

		$this->execute(
			"CREATE TABLE `audit_useragent` (
							 `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
							 `name` varchar(1024) NOT NULL,
							 `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `last_modified_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
							 `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `created_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
							 PRIMARY KEY (`id`),
							 KEY `audit_useragent_lmui_fk` (`last_modified_user_id`),
							 KEY `audit_useragent_cui_fk` (`created_user_id`),
							 CONSTRAINT `audit_useragent_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
							 CONSTRAINT `audit_useragent_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)
							)
							ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci"
		);

		$this->execute(
			"CREATE TABLE `authassignment` (
							 `itemname` varchar(64) NOT NULL DEFAULT '',
							 `userid` varchar(64) NOT NULL DEFAULT '',
							 `bizrule` text ,
							 `data` text ,
							 `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 PRIMARY KEY (`itemname`,`userid`),
							 KEY `authassignment_last_modified_user_id_fk` (`last_modified_user_id`),
							 KEY `authassignment_created_user_id_fk` (`created_user_id`),
							 CONSTRAINT `authassignment_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
							 CONSTRAINT `authassignment_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
							)
							ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci"
		);

		$this->execute(
			"CREATE TABLE `authitem` (
							 `name` varchar(64) NOT NULL,
							 `type` int(11) NOT NULL,
							 `description` text ,
							 `bizrule` text ,
							 `data` text ,
							 `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 PRIMARY KEY (`name`),
							 KEY `authitem_last_modified_user_id_fk` (`last_modified_user_id`),
							 KEY `authitem_created_user_id_fk` (`created_user_id`),
							 CONSTRAINT `authitem_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
							 CONSTRAINT `authitem_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
							)
							ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci"
		);

		$this->execute(
			"CREATE TABLE `authitemchild` (
							 `parent` varchar(64) NOT NULL,
							 `child` varchar(64) NOT NULL,
							 `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 PRIMARY KEY (`parent`,`child`),
							 KEY `child` (`child`),
							 KEY `authitemchild_last_modified_user_id_fk` (`last_modified_user_id`),
							 KEY `authitemchild_created_user_id_fk` (`created_user_id`),
							 CONSTRAINT `authitemchild_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
							 CONSTRAINT `authitemchild_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
							)
							ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci"
		);

		$this->execute(
			"CREATE TABLE `benefit` (
							 `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
							 `name` varchar(255) DEFAULT NULL,
							 `last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 PRIMARY KEY (`id`),
							 KEY `benefit_last_modified_user_id_fk` (`last_modified_user_id`),
							 KEY `benefit_created_user_id_fk` (`created_user_id`),
							 CONSTRAINT `benefit_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
							 CONSTRAINT `benefit_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
							)
							ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci"
		);

		$this->execute(
			"CREATE TABLE `commissioning_body` (
							 `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
							 `name` varchar(64) NOT NULL,
							 `code` varchar(16) DEFAULT NULL,
							 `commissioning_body_type_id` int(10) unsigned NOT NULL,
							 `contact_id` int(10) unsigned NOT NULL,
							 `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 PRIMARY KEY (`id`),
							 KEY `commissioning_body_last_modified_user_id_fk` (`last_modified_user_id`),
							 KEY `commissioning_body_created_user_id_fk` (`created_user_id`),
							 KEY `commissioning_body_commissioning_body_type_id_fk` (`commissioning_body_type_id`),
							 KEY `commissioning_body_contact_id_fk` (`contact_id`),
							 CONSTRAINT `commissioning_body_contact_id_fk` FOREIGN KEY (`contact_id`) REFERENCES `contact` (`id`),
							 CONSTRAINT `commissioning_body_commissioning_body_type_id_fk` FOREIGN KEY (`commissioning_body_type_id`) REFERENCES `commissioning_body_type` (`id`),
							 CONSTRAINT `commissioning_body_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
							 CONSTRAINT `commissioning_body_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
							)
							ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci"
		);

		$this->execute(
			"CREATE TABLE `commissioning_body_patient_assignment` (
							 `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
							 `commissioning_body_id` int(10) unsigned NOT NULL,
							 `patient_id` int(10) unsigned NOT NULL,
							 `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 PRIMARY KEY (`id`),
							 KEY `commissioning_body_patient_assignment_cbid_fk` (`commissioning_body_id`),
							 KEY `commissioning_body_patient_assignment_created_user_id_fk` (`created_user_id`),
							 KEY `commissioning_body_patient_assignment_last_modified_user_id_fk` (`last_modified_user_id`),
							 KEY `commissioning_body_patient_assignment_pid_fk` (`patient_id`),
							 CONSTRAINT `commissioning_body_patient_assignment_pid_fk` FOREIGN KEY (`patient_id`) REFERENCES `patient` (`id`),
							 CONSTRAINT `commissioning_body_patient_assignment_cbid_fk` FOREIGN KEY (`commissioning_body_id`) REFERENCES `commissioning_body` (`id`),
							 CONSTRAINT `commissioning_body_patient_assignment_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
							 CONSTRAINT `commissioning_body_patient_assignment_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
							)
							ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci"
		);

		$this->execute(
			"CREATE TABLE `commissioning_body_practice_assignment` (
							 `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
							 `commissioning_body_id` int(10) unsigned NOT NULL,
							 `practice_id` int(10) unsigned NOT NULL,
							 `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 PRIMARY KEY (`id`),
							 KEY `commissioning_body_practice_assignment_last_modified_user_id_fk` (`last_modified_user_id`),
							 KEY `commissioning_body_practice_assignment_created_user_id_fk` (`created_user_id`),
							 KEY `commissioning_body_practice_assignment_cbid_fk` (`commissioning_body_id`),
							 KEY `commissioning_body_practice_assignment_pid_fk` (`practice_id`),
							 CONSTRAINT `commissioning_body_practice_assignment_pid_fk` FOREIGN KEY (`practice_id`) REFERENCES `practice` (`id`),
							 CONSTRAINT `commissioning_body_practice_assignment_cbid_fk` FOREIGN KEY (`commissioning_body_id`) REFERENCES `commissioning_body` (`id`),
							 CONSTRAINT `commissioning_body_practice_assignment_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
							 CONSTRAINT `commissioning_body_practice_assignment_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
							)
							ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci"
		);

		$this->execute(
			"CREATE TABLE `commissioning_body_service` (
							 `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
							 `name` varchar(64) NOT NULL,
							 `code` varchar(16) DEFAULT NULL,
							 `commissioning_body_service_type_id` int(10) unsigned NOT NULL,
							 `commissioning_body_id` int(10) unsigned DEFAULT NULL,
							 `contact_id` int(10) unsigned NOT NULL,
							 `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 PRIMARY KEY (`id`),
							 KEY `commissioning_body_service_cbid_fk` (`commissioning_body_id`),
							 KEY `commissioning_body_service_cid_fk` (`contact_id`),
							 KEY `commissioning_body_service_created_user_id_fk` (`created_user_id`),
							 KEY `commissioning_body_service_last_modified_user_id_fk` (`last_modified_user_id`),
							 KEY `commissioning_body_service_tid_fk` (`commissioning_body_service_type_id`),
							 CONSTRAINT `commissioning_body_service_tid_fk` FOREIGN KEY (`commissioning_body_service_type_id`) REFERENCES `commissioning_body_service_type` (`id`),
							 CONSTRAINT `commissioning_body_service_cbid_fk` FOREIGN KEY (`commissioning_body_id`) REFERENCES `commissioning_body` (`id`),
							 CONSTRAINT `commissioning_body_service_cid_fk` FOREIGN KEY (`contact_id`) REFERENCES `contact` (`id`),
							 CONSTRAINT `commissioning_body_service_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
							 CONSTRAINT `commissioning_body_service_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
							)
							ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci"
		);

		$this->execute(
			"CREATE TABLE `commissioning_body_service_type` (
							 `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
							 `name` varchar(64) NOT NULL,
							 `shortname` varchar(16) DEFAULT NULL,
							 `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 PRIMARY KEY (`id`),
							 KEY `commissioning_body_service_type_created_user_id_fk` (`created_user_id`),
							 KEY `commissioning_body_service_type_last_modified_user_id_fk` (`last_modified_user_id`),
							 CONSTRAINT `commissioning_body_service_type_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
							 CONSTRAINT `commissioning_body_service_type_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)
							)
							ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci"
		);

		$this->execute(
			"CREATE TABLE `commissioning_body_type` (
							 `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
							 `name` varchar(64) NOT NULL,
							 `shortname` varchar(16) DEFAULT NULL,
							 `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 PRIMARY KEY (`id`),
							 KEY `commissioning_body_type_created_user_id_fk` (`created_user_id`),
							 KEY `commissioning_body_type_last_modified_user_id_fk` (`last_modified_user_id`),
							 CONSTRAINT `commissioning_body_type_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
							 CONSTRAINT `commissioning_body_type_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)
							)
							ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci"
		);

		$this->execute(
			"CREATE TABLE `common_ophthalmic_disorder` (
							 `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
							 `disorder_id` int(10) unsigned NOT NULL,
							 `subspecialty_id` int(10) unsigned NOT NULL,
							 `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 PRIMARY KEY (`id`),
							 KEY `disorder_id` (`disorder_id`),
							 KEY `common_ophthalmic_disorder_last_modified_user_id_fk` (`last_modified_user_id`),
							 KEY `common_ophthalmic_disorder_created_user_id_fk` (`created_user_id`),
							 KEY `subspecialty_id` (`subspecialty_id`),
							 CONSTRAINT `common_ophthalmic_disorder_ibfk_2` FOREIGN KEY (`subspecialty_id`) REFERENCES `subspecialty` (`id`),
							 CONSTRAINT `common_ophthalmic_disorder_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
							 CONSTRAINT `common_ophthalmic_disorder_ibfk_1` FOREIGN KEY (`disorder_id`) REFERENCES `disorder` (`id`),
							 CONSTRAINT `common_ophthalmic_disorder_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
							)
							ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci"
		);

		$this->execute(
			"CREATE TABLE `common_previous_operation` (
							 `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
							 `name` varchar(1024) NOT NULL,
							 `display_order` tinyint(1) unsigned NOT NULL,
							 `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 PRIMARY KEY (`id`),
							 KEY `common_previous_operation_last_modified_user_id_fk` (`last_modified_user_id`),
							 KEY `common_previous_operation_created_user_id_fk` (`created_user_id`),
							 CONSTRAINT `common_previous_operation_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
							 CONSTRAINT `common_previous_operation_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
							)
							ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci"
		);

		$this->execute(
			"CREATE TABLE `common_systemic_disorder` (
							 `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
							 `disorder_id` int(10) unsigned NOT NULL,
							 `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 PRIMARY KEY (`id`),
							 KEY `disorder_id` (`disorder_id`),
							 KEY `common_systemic_disorder_last_modified_user_id_fk` (`last_modified_user_id`),
							 KEY `common_systemic_disorder_created_user_id_fk` (`created_user_id`),
							 CONSTRAINT `common_systemic_disorder_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
							 CONSTRAINT `common_systemic_disorder_ibfk_1` FOREIGN KEY (`disorder_id`) REFERENCES `disorder` (`id`),
							 CONSTRAINT `common_systemic_disorder_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
							)
							ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci"
		);

		$this->execute(
			"CREATE TABLE `complication` (
							 `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
							 `name` varchar(255) DEFAULT NULL,
							 `last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 PRIMARY KEY (`id`),
							 KEY `complication_last_modified_user_id_fk` (`last_modified_user_id`),
							 KEY `complication_created_user_id_fk` (`created_user_id`),
							 CONSTRAINT `complication_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
							 CONSTRAINT `complication_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
							)
							ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci"
		);

		$this->execute(
			"CREATE TABLE `consultant` (
							 `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
							 `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 `gmc_number` varchar(7) DEFAULT NULL,
							 `practitioner_code` varchar(8) DEFAULT NULL,
							 `gender` varchar(1) DEFAULT NULL,
							 PRIMARY KEY (`id`),
							 KEY `consultant_last_modified_user_id_fk` (`last_modified_user_id`),
							 KEY `consultant_created_user_id_fk` (`created_user_id`),
							 CONSTRAINT `consultant_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
							 CONSTRAINT `consultant_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
							)
							ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci"
		);

		$this->execute(
			"CREATE TABLE `contact` (
							 `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
							 `nick_name` varchar(80) DEFAULT NULL,
							 `primary_phone` varchar(20) DEFAULT NULL,
							 `title` varchar(20) DEFAULT NULL,
							 `first_name` varchar(100) NOT NULL,
							 `last_name` varchar(100) NOT NULL,
							 `qualifications` varchar(200) DEFAULT NULL,
							 `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 `contact_label_id` int(10) unsigned DEFAULT NULL,
							 PRIMARY KEY (`id`),
							 KEY `contact_last_modified_user_id_fk` (`last_modified_user_id`),
							 KEY `contact_created_user_id_fk` (`created_user_id`),
							 KEY `contact_last_name_key` (`last_name`),
							 KEY `contact_contact_label_id_fk` (`contact_label_id`),
							 CONSTRAINT `contact_contact_label_id_fk` FOREIGN KEY (`contact_label_id`) REFERENCES `contact_label` (`id`),
							 CONSTRAINT `contact_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
							 CONSTRAINT `contact_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
							)
							ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci"
		);

		$this->execute(
			"CREATE TABLE `contact_label` (
							 `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
							 `name` varchar(64) NOT NULL,
							 `last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 PRIMARY KEY (`id`),
							 KEY `contact_label_last_modified_user_id_fk` (`last_modified_user_id`),
							 KEY `contact_label_created_user_id_fk` (`created_user_id`),
							 CONSTRAINT `contact_label_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
							 CONSTRAINT `contact_label_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
							)
							ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci"
		);

		$this->execute(
			"CREATE TABLE `contact_location` (
							 `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
							 `contact_id` int(10) unsigned NOT NULL,
							 `site_id` int(10) unsigned DEFAULT NULL,
							 `institution_id` int(10) unsigned DEFAULT NULL,
							 `last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 PRIMARY KEY (`id`),
							 KEY `contact_location_last_modified_user_id_fk` (`last_modified_user_id`),
							 KEY `contact_location_created_user_id_fk` (`created_user_id`),
							 KEY `contact_location_site_id_fk` (`site_id`),
							 KEY `contact_location_institution_id_fk` (`institution_id`),
							 KEY `contact_location_contact_id_fk` (`contact_id`),
							 CONSTRAINT `contact_location_contact_id_fk` FOREIGN KEY (`contact_id`) REFERENCES `contact` (`id`),
							 CONSTRAINT `contact_location_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
							 CONSTRAINT `contact_location_institution_id_fk` FOREIGN KEY (`institution_id`) REFERENCES `institution` (`id`),
							 CONSTRAINT `contact_location_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
							 CONSTRAINT `contact_location_site_id_fk` FOREIGN KEY (`site_id`) REFERENCES `site` (`id`)
							)
							ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci"
		);

		$this->execute(
			"CREATE TABLE `contact_metadata` (
							 `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
							 `contact_id` int(10) unsigned NOT NULL,
							 `key` varchar(64) NOT NULL,
							 `value` varchar(64) NOT NULL,
							 `last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 PRIMARY KEY (`id`),
							 KEY `contact_metadata_last_modified_user_id_fk` (`last_modified_user_id`),
							 KEY `contact_metadata_created_user_id_fk` (`created_user_id`),
							 CONSTRAINT `contact_metadata_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
							 CONSTRAINT `contact_metadata_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
							)
							ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci"
		);

		$this->execute(
			"CREATE TABLE `contact_type` (
							 `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
							 `name` varchar(40) NOT NULL,
							 `letter_template_only` tinyint(4) NOT NULL DEFAULT '0',
							 `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 PRIMARY KEY (`id`),
							 UNIQUE KEY `name` (`name`),
							 KEY `contact_type_last_modified_user_id_fk` (`last_modified_user_id`),
							 KEY `contact_type_created_user_id_fk` (`created_user_id`),
							 CONSTRAINT `contact_type_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
							 CONSTRAINT `contact_type_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
							)
							ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci"
		);

		$this->execute(
			"CREATE TABLE `country` (
							 `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
							 `code` varchar(2) DEFAULT NULL,
							 `name` varchar(50) DEFAULT NULL,
							 `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 PRIMARY KEY (`id`),
							 UNIQUE KEY `code` (`code`),
							 UNIQUE KEY `name` (`name`),
							 KEY `country_last_modified_user_id_fk` (`last_modified_user_id`),
							 KEY `country_created_user_id_fk` (`created_user_id`),
							 CONSTRAINT `country_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
							 CONSTRAINT `country_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
							)
							ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci"
		);

		$this->execute(
			"CREATE TABLE `disorder` (
							 `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
							 `fully_specified_name` varchar(255) NOT NULL,
							 `term` varchar(255) NOT NULL,
							 `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 `specialty_id` int(10) unsigned DEFAULT NULL,
							 PRIMARY KEY (`id`),
							 KEY `term` (`term`),
							 KEY `disorder_last_modified_user_id_fk` (`last_modified_user_id`),
							 KEY `disorder_created_user_id_fk` (`created_user_id`),
							 KEY `disorder_specialty_fk` (`specialty_id`),
							 CONSTRAINT `disorder_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
							 CONSTRAINT `disorder_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
							 CONSTRAINT `disorder_specialty_fk` FOREIGN KEY (`specialty_id`) REFERENCES `specialty` (`id`)
							)
							ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci"
		);

		$this->execute(
			"CREATE TABLE `disorder_tree` (
							 `id` int(10) unsigned NOT NULL,
							 `lft` int(10) unsigned NOT NULL,
							 `rght` int(10) unsigned NOT NULL,
							 KEY `id` (`id`),
							 KEY `lft` (`lft`),
							 KEY `rght` (`rght`)
							)
							ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci"
		);

		$this->execute(
			"CREATE TABLE `drug` (
							 `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
							 `name` varchar(100) DEFAULT NULL,
							 `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 `type_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `form_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `dose_unit` varchar(40) DEFAULT NULL,
							 `default_dose` varchar(40) DEFAULT NULL,
							 `default_route_id` int(10) unsigned DEFAULT NULL,
							 `default_frequency_id` int(10) unsigned DEFAULT NULL,
							 `default_duration_id` int(10) unsigned DEFAULT NULL,
							 `preservative_free` tinyint(1) unsigned NOT NULL DEFAULT '0',
							 `aliases` text ,
							 `discontinued` tinyint(1) unsigned NOT NULL DEFAULT '0',
							 `tallman` varchar(100) DEFAULT NULL,
							 PRIMARY KEY (`id`),
							 KEY `drug_last_modified_user_id_fk` (`last_modified_user_id`),
							 KEY `drug_created_user_id_fk` (`created_user_id`),
							 KEY `drug_type_id_fk` (`type_id`),
							 KEY `drug_form_id_fk` (`form_id`),
							 KEY `drug_default_route_id_fk` (`default_route_id`),
							 KEY `drug_default_frequency_id_fk` (`default_frequency_id`),
							 KEY `drug_default_duration_id_fk` (`default_duration_id`),
							 CONSTRAINT `drug_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
							 CONSTRAINT `drug_default_duration_id_fk` FOREIGN KEY (`default_duration_id`) REFERENCES `drug_duration` (`id`),
							 CONSTRAINT `drug_default_frequency_id_fk` FOREIGN KEY (`default_frequency_id`) REFERENCES `drug_frequency` (`id`),
							 CONSTRAINT `drug_default_route_id_fk` FOREIGN KEY (`default_route_id`) REFERENCES `drug_route` (`id`),
							 CONSTRAINT `drug_form_id_fk` FOREIGN KEY (`form_id`) REFERENCES `drug_form` (`id`),
							 CONSTRAINT `drug_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
							 CONSTRAINT `drug_type_id_fk` FOREIGN KEY (`type_id`) REFERENCES `drug_type` (`id`)
							)
							ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci"
		);

		$this->execute(
			"CREATE TABLE `drug_allergy_assignment` (
							 `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
							 `drug_id` int(10) unsigned NOT NULL,
							 `allergy_id` int(10) unsigned NOT NULL,
							 `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 `created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 PRIMARY KEY (`id`),
							 KEY `drug_allergy_assignment_drug_id_fk` (`drug_id`),
							 KEY `drug_allergy_assignment_allergy_id_fk` (`allergy_id`),
							 KEY `drug_allergy_assignment_lmui_fk` (`last_modified_user_id`),
							 KEY `drug_allergy_assignment_cui_fk` (`created_user_id`),
							 CONSTRAINT `drug_allergy_assignment_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
							 CONSTRAINT `drug_allergy_assignment_allergy_id_fk` FOREIGN KEY (`allergy_id`) REFERENCES `allergy` (`id`),
							 CONSTRAINT `drug_allergy_assignment_drug_id_fk` FOREIGN KEY (`drug_id`) REFERENCES `drug` (`id`),
							 CONSTRAINT `drug_allergy_assignment_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
							)
							ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci"
		);

		$this->execute(
			"CREATE TABLE `drug_duration` (
							 `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
							 `name` varchar(40) DEFAULT NULL,
							 `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 `created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 `display_order` int(10) unsigned NOT NULL DEFAULT '1',
							 PRIMARY KEY (`id`),
							 KEY `drug_duration_lmui_fk` (`last_modified_user_id`),
							 KEY `drug_duration_cui_fk` (`created_user_id`),
							 CONSTRAINT `drug_duration_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
							 CONSTRAINT `drug_duration_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
							)
							ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci"
		);

		$this->execute(
			"CREATE TABLE `drug_form` (
							 `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
							 `name` varchar(40) DEFAULT NULL,
							 `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 `created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 PRIMARY KEY (`id`),
							 KEY `drug_form_lmui_fk` (`last_modified_user_id`),
							 KEY `drug_form_cui_fk` (`created_user_id`),
							 CONSTRAINT `drug_form_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
							 CONSTRAINT `drug_form_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
							)
							ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci"
		);

		$this->execute(
			"CREATE TABLE `drug_frequency` (
							 `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
							 `name` varchar(40) DEFAULT NULL,
							 `long_name` varchar(40) NOT NULL,
							 `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 `created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 `display_order` int(10) unsigned NOT NULL DEFAULT '0',
							 PRIMARY KEY (`id`),
							 KEY `drug_frequency_lmui_fk` (`last_modified_user_id`),
							 KEY `drug_frequency_cui_fk` (`created_user_id`),
							 CONSTRAINT `drug_frequency_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
							 CONSTRAINT `drug_frequency_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
							)
							ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci"
		);

		$this->execute(
			"CREATE TABLE `drug_route` (
							 `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
							 `name` varchar(40) DEFAULT NULL,
							 `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 `created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 `display_order` int(10) unsigned DEFAULT '0',
							 PRIMARY KEY (`id`),
							 KEY `drug_route_lmui_fk` (`last_modified_user_id`),
							 KEY `drug_route_cui_fk` (`created_user_id`),
							 CONSTRAINT `drug_route_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
							 CONSTRAINT `drug_route_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
							)
							ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci"
		);

		$this->execute(
			"CREATE TABLE `drug_route_option` (
							 `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
							 `name` varchar(40) DEFAULT NULL,
							 `drug_route_id` int(10) unsigned NOT NULL,
							 `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 PRIMARY KEY (`id`),
							 KEY `drug_route_option_drug_route_id_fk` (`drug_route_id`),
							 KEY `drug_route_option_last_modified_user_id_fk` (`last_modified_user_id`),
							 KEY `drug_route_option_created_user_id_fk` (`created_user_id`),
							 CONSTRAINT `drug_route_option_drug_route_id_fk` FOREIGN KEY (`drug_route_id`) REFERENCES `drug_route` (`id`),
							 CONSTRAINT `drug_route_option_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
							 CONSTRAINT `drug_route_option_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)
							)
							ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci"
		);

		$this->execute(
			"CREATE TABLE `drug_set` (
							 `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
							 `name` varchar(40) DEFAULT NULL,
							 `subspecialty_id` int(10) unsigned NOT NULL,
							 `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 PRIMARY KEY (`id`),
							 KEY `drug_set_subspecialty_id_fk` (`subspecialty_id`),
							 KEY `drug_set_last_modified_user_id_fk` (`last_modified_user_id`),
							 KEY `drug_set_created_user_id_fk` (`created_user_id`),
							 CONSTRAINT `drug_set_subspecialty_id_fk` FOREIGN KEY (`subspecialty_id`) REFERENCES `subspecialty` (`id`),
							 CONSTRAINT `drug_set_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
							 CONSTRAINT `drug_set_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)
							)
							ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci"
		);

		$this->execute(
			"CREATE TABLE `drug_set_item` (
							 `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
							 `drug_id` int(10) unsigned NOT NULL,
							 `drug_set_id` int(10) unsigned NOT NULL,
							 `frequency_id` int(10) unsigned DEFAULT NULL,
							 `duration_id` int(10) unsigned DEFAULT NULL,
							 `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 `dose` varchar(40) DEFAULT NULL,
							 PRIMARY KEY (`id`),
							 KEY `drug_set_item_drug_id_fk` (`drug_id`),
							 KEY `drug_set_item_drug_set_id_fk` (`drug_set_id`),
							 KEY `drug_set_item_last_modified_user_id_fk` (`last_modified_user_id`),
							 KEY `drug_set_item_created_user_id_fk` (`created_user_id`),
							 KEY `drug_set_item_frequency_id_fk` (`frequency_id`),
							 KEY `drug_set_item_duration_id_fk` (`duration_id`),
							 CONSTRAINT `drug_set_item_duration_id_fk` FOREIGN KEY (`duration_id`) REFERENCES `drug_duration` (`id`),
							 CONSTRAINT `drug_set_item_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
							 CONSTRAINT `drug_set_item_drug_id_fk` FOREIGN KEY (`drug_id`) REFERENCES `drug` (`id`),
							 CONSTRAINT `drug_set_item_drug_set_id_fk` FOREIGN KEY (`drug_set_id`) REFERENCES `drug_set` (`id`),
							 CONSTRAINT `drug_set_item_frequency_id_fk` FOREIGN KEY (`frequency_id`) REFERENCES `drug_frequency` (`id`),
							 CONSTRAINT `drug_set_item_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
							)
							ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci"
		);

		$this->execute(
			"CREATE TABLE `drug_set_item_taper` (
							 `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
							 `item_id` int(10) unsigned NOT NULL,
							 `dose` varchar(40) DEFAULT NULL,
							 `frequency_id` int(10) unsigned DEFAULT NULL,
							 `duration_id` int(10) unsigned DEFAULT NULL,
							 `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `last_modified_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
							 `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `created_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
							 PRIMARY KEY (`id`),
							 KEY `drug_set_item_taper_f_fk` (`frequency_id`),
							 KEY `drug_set_item_taper_d_fk` (`duration_id`),
							 KEY `drug_set_item_taper_lmui_fk` (`last_modified_user_id`),
							 KEY `drug_set_item_taper_cui_fk` (`created_user_id`),
							 CONSTRAINT `drug_set_item_taper_f_fk` FOREIGN KEY (`frequency_id`) REFERENCES `drug_frequency` (`id`),
							 CONSTRAINT `drug_set_item_taper_d_fk` FOREIGN KEY (`duration_id`) REFERENCES `drug_duration` (`id`),
							 CONSTRAINT `drug_set_item_taper_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
							 CONSTRAINT `drug_set_item_taper_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)
							)
							ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci"
		);

		$this->execute(
			"CREATE TABLE `drug_type` (
							 `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
							 `name` varchar(40) DEFAULT NULL,
							 `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 `created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 PRIMARY KEY (`id`),
							 KEY `drug_type_lmui_fk` (`last_modified_user_id`),
							 KEY `drug_type_cui_fk` (`created_user_id`),
							 CONSTRAINT `drug_type_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
							 CONSTRAINT `drug_type_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
							)
							ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci"
		);

		$this->execute(
			"CREATE TABLE `element_type` (
							 `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
							 `name` varchar(255) NOT NULL,
							 `class_name` varchar(255) NOT NULL,
							 `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 `event_type_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `display_order` int(10) unsigned NOT NULL DEFAULT '1',
							 `default` tinyint(1) unsigned NOT NULL DEFAULT '1',
							 `parent_element_type_id` int(10) unsigned DEFAULT NULL,
							 `required` tinyint(1) DEFAULT NULL,
							 PRIMARY KEY (`id`),
							 KEY `element_type_last_modified_user_id_fk` (`last_modified_user_id`),
							 KEY `element_type_created_user_id_fk` (`created_user_id`),
							 KEY `element_type_parent_et_fk` (`parent_element_type_id`),
							 CONSTRAINT `element_type_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
							 CONSTRAINT `element_type_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
							 CONSTRAINT `element_type_parent_et_fk` FOREIGN KEY (`parent_element_type_id`) REFERENCES `element_type` (`id`)
							)
							ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci"
		);

		$this->execute(
			"CREATE TABLE `element_type_anaesthetic_agent` (
							 `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
							 `element_type_id` int(10) unsigned NOT NULL,
							 `anaesthetic_agent_id` int(10) unsigned NOT NULL,
							 `display_order` tinyint(3) unsigned NOT NULL,
							 `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 PRIMARY KEY (`id`),
							 KEY `element_type_anaesthetic_agent_last_modified_user_id_fk` (`last_modified_user_id`),
							 KEY `element_type_anaesthetic_agent_created_user_id_fk` (`created_user_id`),
							 KEY `element_type_anaesthetic_agent_element_type_id_fk` (`element_type_id`),
							 KEY `element_type_anaesthetic_agent_anaesthetic_agent_id_fk` (`anaesthetic_agent_id`),
							 CONSTRAINT `element_type_anaesthetic_agent_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
							 CONSTRAINT `element_type_anaesthetic_agent_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
							 CONSTRAINT `element_type_anaesthetic_agent_element_type_id_fk` FOREIGN KEY (`element_type_id`) REFERENCES `element_type` (`id`),
							 CONSTRAINT `element_type_anaesthetic_agent_anaesthetic_agent_id_fk` FOREIGN KEY (`anaesthetic_agent_id`) REFERENCES `anaesthetic_agent` (`id`)
							)
							ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci"
		);

		$this->execute(
			"CREATE TABLE `element_type_anaesthetic_complication` (
							 `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
							 `element_type_id` int(10) unsigned NOT NULL,
							 `anaesthetic_complication_id` int(10) unsigned NOT NULL,
							 `display_order` tinyint(3) unsigned NOT NULL,
							 `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 PRIMARY KEY (`id`),
							 KEY `element_type_ac_last_modified_user_id_fk` (`last_modified_user_id`),
							 KEY `element_type_ac_created_user_id_fk` (`created_user_id`),
							 KEY `element_type_ac_element_type_id_fk` (`element_type_id`),
							 KEY `element_type_ac_anaesthetic_complication_id_fk` (`anaesthetic_complication_id`),
							 CONSTRAINT `element_type_ac_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
							 CONSTRAINT `element_type_ac_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
							 CONSTRAINT `element_type_ac_element_type_id_fk` FOREIGN KEY (`element_type_id`) REFERENCES `element_type` (`id`),
							 CONSTRAINT `element_type_ac_anaesthetic_complication_id_fk` FOREIGN KEY (`anaesthetic_complication_id`) REFERENCES `anaesthetic_complication` (`id`)
							)
							ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci"
		);

		$this->execute(
			"CREATE TABLE `element_type_anaesthetic_delivery` (
							 `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
							 `element_type_id` int(10) unsigned NOT NULL,
							 `anaesthetic_delivery_id` int(10) unsigned NOT NULL,
							 `display_order` tinyint(3) unsigned NOT NULL,
							 `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 PRIMARY KEY (`id`),
							 KEY `element_type_anaesthetic_delivery_last_modified_user_id_fk` (`last_modified_user_id`),
							 KEY `element_type_anaesthetic_delivery_created_user_id_fk` (`created_user_id`),
							 KEY `element_type_anaesthetic_delivery_element_type_id_fk` (`element_type_id`),
							 KEY `element_type_anaesthetic_delivery_anaesthetic_delivery_id_fk` (`anaesthetic_delivery_id`),
							 CONSTRAINT `element_type_anaesthetic_delivery_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
							 CONSTRAINT `element_type_anaesthetic_delivery_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
							 CONSTRAINT `element_type_anaesthetic_delivery_element_type_id_fk` FOREIGN KEY (`element_type_id`) REFERENCES `element_type` (`id`),
							 CONSTRAINT `element_type_anaesthetic_delivery_anaesthetic_delivery_id_fk` FOREIGN KEY (`anaesthetic_delivery_id`) REFERENCES `anaesthetic_delivery` (`id`)
							)
							ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci"
		);

		$this->execute(
			"CREATE TABLE `element_type_anaesthetic_type` (
							 `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
							 `element_type_id` int(10) unsigned NOT NULL,
							 `anaesthetic_type_id` int(10) unsigned NOT NULL,
							 `display_order` int(10) unsigned NOT NULL DEFAULT '1',
							 PRIMARY KEY (`id`),
							 KEY `element_type_anaesthetic_type_fk1` (`element_type_id`),
							 KEY `element_type_anaesthetic_type_fk2` (`anaesthetic_type_id`),
							 CONSTRAINT `element_type_anaesthetic_type_fk1` FOREIGN KEY (`element_type_id`) REFERENCES `element_type` (`id`),
							 CONSTRAINT `element_type_anaesthetic_type_fk2` FOREIGN KEY (`anaesthetic_type_id`) REFERENCES `anaesthetic_type` (`id`)
							)
							ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci"
		);

		$this->execute(
			"CREATE TABLE `element_type_anaesthetist` (
							 `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
							 `element_type_id` int(10) unsigned NOT NULL,
							 `anaesthetist_id` int(10) unsigned NOT NULL,
							 `display_order` tinyint(3) unsigned NOT NULL,
							 `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 PRIMARY KEY (`id`),
							 KEY `element_type_anaesthetist_last_modified_user_id_fk` (`last_modified_user_id`),
							 KEY `element_type_anaesthetist_created_user_id_fk` (`created_user_id`),
							 KEY `element_type_anaesthetist_element_type_id_fk` (`element_type_id`),
							 KEY `element_type_anaesthetist_anaesthetist_id_fk` (`anaesthetist_id`),
							 CONSTRAINT `element_type_anaesthetist_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
							 CONSTRAINT `element_type_anaesthetist_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
							 CONSTRAINT `element_type_anaesthetist_element_type_id_fk` FOREIGN KEY (`element_type_id`) REFERENCES `element_type` (`id`),
							 CONSTRAINT `element_type_anaesthetist_anaesthetist_id_fk` FOREIGN KEY (`anaesthetist_id`) REFERENCES `anaesthetist` (`id`)
							)
							ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci"
		);

		$this->execute(
			"CREATE TABLE `element_type_eye` (
							 `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
							 `element_type_id` int(10) unsigned NOT NULL,
							 `eye_id` int(10) unsigned NOT NULL,
							 `display_order` tinyint(1) unsigned NOT NULL DEFAULT '1',
							 PRIMARY KEY (`id`),
							 KEY `element_type_eye_fk1` (`element_type_id`),
							 KEY `element_type_eye_fk2` (`eye_id`),
							 CONSTRAINT `element_type_eye_fk2` FOREIGN KEY (`eye_id`) REFERENCES `eye` (`id`),
							 CONSTRAINT `element_type_eye_fk1` FOREIGN KEY (`element_type_id`) REFERENCES `element_type` (`id`)
							)
							ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci"
		);

		$this->execute(
			"CREATE TABLE `element_type_priority` (
							 `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
							 `element_type_id` int(10) unsigned NOT NULL,
							 `priority_id` int(10) unsigned NOT NULL,
							 `display_order` tinyint(1) unsigned NOT NULL DEFAULT '1',
							 PRIMARY KEY (`id`),
							 KEY `element_type_priority_fk1` (`element_type_id`),
							 KEY `element_type_priority_fk2` (`priority_id`),
							 CONSTRAINT `element_type_priority_fk2` FOREIGN KEY (`priority_id`) REFERENCES `priority` (`id`),
							 CONSTRAINT `element_type_priority_fk1` FOREIGN KEY (`element_type_id`) REFERENCES `element_type` (`id`)
							)
							ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci"
		);

		$this->execute(
			"CREATE TABLE `episode` (
							 `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
							 `patient_id` int(10) unsigned NOT NULL,
							 `firm_id` int(10) unsigned DEFAULT NULL,
							 `start_date` datetime NOT NULL,
							 `end_date` datetime DEFAULT NULL,
							 `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 `episode_status_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `legacy` tinyint(1) unsigned DEFAULT '0',
							 `deleted` int(10) unsigned NOT NULL DEFAULT '0',
							 `eye_id` int(10) unsigned DEFAULT NULL,
							 `disorder_id` int(10) unsigned DEFAULT NULL,
							 `support_services` tinyint(1) unsigned NOT NULL DEFAULT '0',
							 PRIMARY KEY (`id`),
							 KEY `episode_1` (`patient_id`),
							 KEY `episode_2` (`firm_id`),
							 KEY `episode_last_modified_user_id_fk` (`last_modified_user_id`),
							 KEY `episode_created_user_id_fk` (`created_user_id`),
							 KEY `episode_episode_status_id_fk` (`episode_status_id`),
							 KEY `episode_eye_id_fk` (`eye_id`),
							 KEY `episode_disorder_id_fk` (`disorder_id`),
							 CONSTRAINT `episode_1` FOREIGN KEY (`patient_id`) REFERENCES `patient` (`id`),
							 CONSTRAINT `episode_2` FOREIGN KEY (`firm_id`) REFERENCES `firm` (`id`),
							 CONSTRAINT `episode_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
							 CONSTRAINT `episode_disorder_id_fk` FOREIGN KEY (`disorder_id`) REFERENCES `disorder` (`id`),
							 CONSTRAINT `episode_episode_status_id_fk` FOREIGN KEY (`episode_status_id`) REFERENCES `episode_status` (`id`),
							 CONSTRAINT `episode_eye_id_fk` FOREIGN KEY (`eye_id`) REFERENCES `eye` (`id`),
							 CONSTRAINT `episode_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
							)
							ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci"
		);

		$this->execute(
			"CREATE TABLE `episode_status` (
							 `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
							 `name` varchar(64) NOT NULL DEFAULT '',
							 `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `last_modified_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
							 `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `created_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
							 `order` int(10) unsigned NOT NULL DEFAULT '0',
							 PRIMARY KEY (`id`),
							 KEY `episode_status_last_modified_user_id_fk` (`last_modified_user_id`),
							 KEY `episode_status_created_user_id_fk` (`created_user_id`),
							 CONSTRAINT `episode_status_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
							 CONSTRAINT `episode_status_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
							)
							ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci"
		);

		$this->execute(
			"CREATE TABLE `ethnic_group` (
							 `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
							 `name` varchar(64) NOT NULL,
							 `code` varchar(1) NOT NULL,
							 `display_order` int(10) unsigned NOT NULL DEFAULT '1',
							 `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 PRIMARY KEY (`id`),
							 KEY `ethnic_group_last_modified_user_id_fk` (`last_modified_user_id`),
							 KEY `ethnic_group_created_user_id_fk` (`created_user_id`),
							 CONSTRAINT `ethnic_group_created_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
							 CONSTRAINT `ethnic_group_last_modified_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)
							)
							ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci"
		);

		$this->execute(
			"CREATE TABLE `event` (
							 `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
							 `episode_id` int(10) unsigned DEFAULT NULL,
							 `created_user_id` int(10) unsigned NOT NULL,
							 `event_type_id` int(10) unsigned NOT NULL,
							 `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 `created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 `info` varchar(1024) DEFAULT NULL,
							 `deleted` tinyint(1) unsigned NOT NULL DEFAULT '0',
							 PRIMARY KEY (`id`),
							 KEY `event_1` (`episode_id`),
							 KEY `event_2` (`created_user_id`),
							 KEY `event_3` (`event_type_id`),
							 KEY `event_last_modified_user_id_fk` (`last_modified_user_id`),
							 KEY `idx_event_episode_id` (`episode_id`),
							 KEY `idx_event_event_type_id` (`event_type_id`),
							 CONSTRAINT `event_1` FOREIGN KEY (`episode_id`) REFERENCES `episode` (`id`),
							 CONSTRAINT `event_3` FOREIGN KEY (`event_type_id`) REFERENCES `event_type` (`id`),
							 CONSTRAINT `event_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
							 CONSTRAINT `event_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
							)
							ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci"
		);

		$this->execute(
			"CREATE TABLE `event_group` (
							 `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
							 `name` varchar(64) NOT NULL,
							 `code` varchar(2) NOT NULL,
							 PRIMARY KEY (`id`)
							)
							ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci"
		);

		$this->execute(
			"CREATE TABLE `event_issue` (
							 `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
							 `event_id` int(10) unsigned NOT NULL,
							 `issue_id` int(10) unsigned NOT NULL,
							 `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `last_modified_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
							 `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `created_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
							 PRIMARY KEY (`id`),
							 KEY `event_issue_event_id` (`event_id`),
							 KEY `event_issue_issue_id` (`issue_id`),
							 KEY `event_issue_last_modified_user_id_fk` (`last_modified_user_id`),
							 KEY `event_issue_created_user_id_fk` (`created_user_id`),
							 CONSTRAINT `event_issue_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
							 CONSTRAINT `event_issue_event_id` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`),
							 CONSTRAINT `event_issue_issue_id` FOREIGN KEY (`issue_id`) REFERENCES `issue` (`id`),
							 CONSTRAINT `event_issue_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
							)
							ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci"
		);

		$this->execute(
			"CREATE TABLE `event_type` (
							 `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
							 `name` varchar(40) NOT NULL,
							 `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 `event_group_id` int(10) unsigned NOT NULL,
							 `class_name` varchar(200) NOT NULL,
							 `support_services` tinyint(1) unsigned NOT NULL DEFAULT '0',
							 PRIMARY KEY (`id`),
							 UNIQUE KEY `name` (`name`),
							 KEY `event_type_last_modified_user_id_fk` (`last_modified_user_id`),
							 KEY `event_type_created_user_id_fk` (`created_user_id`),
							 KEY `event_type_event_group_id_fk` (`event_group_id`),
							 CONSTRAINT `event_type_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
							 CONSTRAINT `event_type_event_group_id_fk` FOREIGN KEY (`event_group_id`) REFERENCES `event_group` (`id`),
							 CONSTRAINT `event_type_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
							)
							ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci"
		);

		$this->execute(
			"CREATE TABLE `eye` (
							 `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
							 `name` varchar(10) DEFAULT NULL,
							 `display_order` int(10) unsigned NOT NULL DEFAULT '1',
							 PRIMARY KEY (`id`)
							)
							ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci"
		);

		$this->execute(
			"CREATE TABLE `family_history` (
							 `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
							 `patient_id` int(10) unsigned NOT NULL,
							 `relative_id` int(10) unsigned NOT NULL,
							 `side_id` int(10) unsigned NOT NULL,
							 `condition_id` int(10) unsigned NOT NULL,
							 `comments` varchar(1024) NOT NULL,
							 `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 PRIMARY KEY (`id`),
							 KEY `family_history_last_modified_user_id_fk` (`last_modified_user_id`),
							 KEY `family_history_created_user_id_fk` (`created_user_id`),
							 KEY `family_history_patient_id_fk` (`patient_id`),
							 KEY `family_history_relative_id_fk` (`relative_id`),
							 KEY `family_history_side_id_fk` (`side_id`),
							 KEY `family_history_condition_id_fk` (`condition_id`),
							 CONSTRAINT `family_history_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
							 CONSTRAINT `family_history_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
							 CONSTRAINT `family_history_patient_id_fk` FOREIGN KEY (`patient_id`) REFERENCES `patient` (`id`),
							 CONSTRAINT `family_history_relative_id_fk` FOREIGN KEY (`relative_id`) REFERENCES `family_history_relative` (`id`),
							 CONSTRAINT `family_history_side_id_fk` FOREIGN KEY (`side_id`) REFERENCES `family_history_side` (`id`),
							 CONSTRAINT `family_history_condition_id_fk` FOREIGN KEY (`condition_id`) REFERENCES `family_history_condition` (`id`)
							)
							ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci"
		);

		$this->execute(
			"CREATE TABLE `family_history_condition` (
							 `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
							 `name` varchar(64) NOT NULL,
							 `display_order` tinyint(1) unsigned NOT NULL,
							 `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 PRIMARY KEY (`id`),
							 KEY `family_history_condition_last_modified_user_id_fk` (`last_modified_user_id`),
							 KEY `family_history_condition_created_user_id_fk` (`created_user_id`),
							 CONSTRAINT `family_history_condition_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
							 CONSTRAINT `family_history_condition_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
							)
							ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci"
		);

		$this->execute(
			"CREATE TABLE `family_history_relative` (
							 `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
							 `name` varchar(64) NOT NULL,
							 `display_order` tinyint(1) unsigned NOT NULL,
							 `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 PRIMARY KEY (`id`),
							 KEY `family_history_relative_last_modified_user_id_fk` (`last_modified_user_id`),
							 KEY `family_history_relative_created_user_id_fk` (`created_user_id`),
							 CONSTRAINT `family_history_relative_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
							 CONSTRAINT `family_history_relative_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
							)
							ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci"
		);

		$this->execute(
			"CREATE TABLE `family_history_side` (
							 `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
							 `name` varchar(64) NOT NULL,
							 `display_order` tinyint(1) unsigned NOT NULL,
							 `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 PRIMARY KEY (`id`),
							 KEY `family_history_side_last_modified_user_id_fk` (`last_modified_user_id`),
							 KEY `family_history_side_created_user_id_fk` (`created_user_id`),
							 CONSTRAINT `family_history_side_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
							 CONSTRAINT `family_history_side_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
							)
							ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci"
		);

		$this->execute(
			"CREATE TABLE `firm` (
							 `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
							 `service_subspecialty_assignment_id` int(10) unsigned DEFAULT NULL,
							 `pas_code` varchar(4) DEFAULT NULL,
							 `name` varchar(40) NOT NULL,
							 `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 `consultant_id` int(10) unsigned DEFAULT NULL,
							 PRIMARY KEY (`id`),
							 KEY `firm_last_modified_user_id_fk` (`last_modified_user_id`),
							 KEY `firm_created_user_id_fk` (`created_user_id`),
							 KEY `service_subspecialty_assignment_id` (`service_subspecialty_assignment_id`),
							 KEY `firm_consultant_id_fk` (`consultant_id`),
							 CONSTRAINT `firm_consultant_id_fk` FOREIGN KEY (`consultant_id`) REFERENCES `user` (`id`),
							 CONSTRAINT `firm_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
							 CONSTRAINT `firm_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
							 CONSTRAINT `service_subspecialty_assignment_id` FOREIGN KEY (`service_subspecialty_assignment_id`) REFERENCES `service_subspecialty_assignment` (`id`)
							)
							ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci"
		);

		$this->execute(
			"CREATE TABLE `firm_user_assignment` (
							 `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
							 `firm_id` int(10) unsigned NOT NULL,
							 `user_id` int(10) unsigned NOT NULL,
							 `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 PRIMARY KEY (`id`),
							 KEY `firm_id` (`firm_id`),
							 KEY `user_id` (`user_id`),
							 KEY `firm_user_assignment_last_modified_user_id_fk` (`last_modified_user_id`),
							 KEY `firm_user_assignment_created_user_id_fk` (`created_user_id`),
							 CONSTRAINT `firm_id` FOREIGN KEY (`firm_id`) REFERENCES `firm` (`id`),
							 CONSTRAINT `firm_user_assignment_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
							 CONSTRAINT `firm_user_assignment_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
							 CONSTRAINT `user_id` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
							)
							ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci"
		);

		$this->execute(
			"CREATE TABLE `gender` (
							 `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
							 `name` varchar(16) NOT NULL,
							 `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 PRIMARY KEY (`id`),
							 KEY `gender_last_modified_user_id_fk` (`last_modified_user_id`),
							 KEY `gender_created_user_id_fk` (`created_user_id`),
							 CONSTRAINT `gender_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
							 CONSTRAINT `gender_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
							)
							ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci"
		);

		$this->execute(
			"CREATE TABLE `gp` (
							 `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
							 `obj_prof` varchar(20) NOT NULL,
							 `nat_id` varchar(20) NOT NULL,
							 `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 `contact_id` int(10) unsigned NOT NULL,
							 PRIMARY KEY (`id`),
							 KEY `gp_last_modified_user_id_fk` (`last_modified_user_id`),
							 KEY `gp_created_user_id_fk` (`created_user_id`),
							 KEY `gp_contact_id_fk` (`contact_id`),
							 CONSTRAINT `gp_contact_id_fk` FOREIGN KEY (`contact_id`) REFERENCES `contact` (`id`),
							 CONSTRAINT `gp_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
							 CONSTRAINT `gp_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
							)
							ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci"
		);

		$this->execute(
			"CREATE TABLE `import_source` (
							 `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
							 `name` varchar(64) NOT NULL,
							 `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 `last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 PRIMARY KEY (`id`),
							 KEY `import_source_created_user_id_fk` (`created_user_id`),
							 KEY `import_source_last_modified_user_id_fk` (`last_modified_user_id`),
							 CONSTRAINT `import_source_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
							 CONSTRAINT `import_source_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
							)
							ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci"
		);

		$this->execute(
			"CREATE TABLE `institution` (
							 `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
							 `name` varchar(255) NOT NULL,
							 `remote_id` varchar(10) NOT NULL,
							 `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 `short_name` varchar(64) NOT NULL,
							 `contact_id` int(10) unsigned NOT NULL,
							 `source_id` int(10) unsigned DEFAULT NULL,
							 PRIMARY KEY (`id`),
							 KEY `institution_last_modified_user_id_fk` (`last_modified_user_id`),
							 KEY `institution_created_user_id_fk` (`created_user_id`),
							 KEY `institution_contact_id_fk` (`contact_id`),
							 KEY `institution_source_id_fk` (`source_id`),
							 CONSTRAINT `institution_contact_id_fk` FOREIGN KEY (`contact_id`) REFERENCES `contact` (`id`),
							 CONSTRAINT `institution_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
							 CONSTRAINT `institution_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
							 CONSTRAINT `institution_source_id_fk` FOREIGN KEY (`source_id`) REFERENCES `import_source` (`id`)
							)
							ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci"
		);

		$this->execute(
			"CREATE TABLE `institution_consultant_assignment` (
							 `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
							 `institution_id` int(10) unsigned NOT NULL,
							 `consultant_id` int(10) unsigned NOT NULL,
							 `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 PRIMARY KEY (`id`),
							 KEY `institution_consultant_assignment_institution_id_fk` (`institution_id`),
							 KEY `institution_consultant_assignment_consultant_id_fk` (`consultant_id`),
							 KEY `institution_consultant_assignment_last_modified_user_id_fk` (`last_modified_user_id`),
							 KEY `institution_consultant_assignment_created_user_id_fk` (`created_user_id`),
							 CONSTRAINT `institution_consultant_assignment_institution_id_fk` FOREIGN KEY (`institution_id`) REFERENCES `institution` (`id`),
							 CONSTRAINT `institution_consultant_assignment_consultant_id_fk` FOREIGN KEY (`consultant_id`) REFERENCES `consultant` (`id`),
							 CONSTRAINT `institution_consultant_assignment_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
							 CONSTRAINT `institution_consultant_assignment_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
							)
							ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci"
		);

		$this->execute(
			"CREATE TABLE `issue` (
							 `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
							 `name` varchar(1024) DEFAULT NULL,
							 `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `last_modified_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
							 `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `created_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
							 PRIMARY KEY (`id`),
							 KEY `issue_last_modified_user_id_fk` (`last_modified_user_id`),
							 KEY `issue_created_user_id_fk` (`created_user_id`),
							 CONSTRAINT `issue_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
							 CONSTRAINT `issue_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
							)
							ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci"
		);

		$this->execute(
			"CREATE TABLE `language` (
							 `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
							 `name` varchar(32) NOT NULL,
							 `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 PRIMARY KEY (`id`),
							 KEY `language_last_modified_user_id_fk` (`last_modified_user_id`),
							 KEY `language_created_user_id_fk` (`created_user_id`),
							 CONSTRAINT `language_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
							 CONSTRAINT `language_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
							)
							ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci"
		);

		$this->execute(
			"CREATE TABLE `letter_template` (
							 `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
							 `subspecialty_id` int(10) unsigned NOT NULL,
							 `name` varchar(64) DEFAULT NULL,
							 `cc` int(10) unsigned NOT NULL,
							 `phrase` text NOT NULL,
							 `send_to` int(10) unsigned NOT NULL,
							 `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 PRIMARY KEY (`id`),
							 KEY `letter_template_ibfk_3` (`cc`),
							 KEY `letter_template_ibfk_2` (`send_to`),
							 KEY `letter_template_last_modified_user_id_fk` (`last_modified_user_id`),
							 KEY `letter_template_created_user_id_fk` (`created_user_id`),
							 KEY `subspecialty_id` (`subspecialty_id`),
							 CONSTRAINT `letter_template_ibfk_1` FOREIGN KEY (`subspecialty_id`) REFERENCES `subspecialty` (`id`),
							 CONSTRAINT `letter_template_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
							 CONSTRAINT `letter_template_ibfk_2` FOREIGN KEY (`send_to`) REFERENCES `contact_type` (`id`),
							 CONSTRAINT `letter_template_ibfk_3` FOREIGN KEY (`cc`) REFERENCES `contact_type` (`id`),
							 CONSTRAINT `letter_template_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
							)
							ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci"
		);

		$this->execute(
			"CREATE TABLE `manual_contact` (
							 `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
							 `contact_type_id` int(10) unsigned NOT NULL,
							 `contact_id` int(10) unsigned NOT NULL,
							 `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 PRIMARY KEY (`id`),
							 KEY `manual_contact_contact_id_fk_1` (`contact_id`),
							 KEY `manual_contact_contact_type_id_fk_2` (`contact_type_id`),
							 KEY `manual_contact_last_modified_user_id_fk` (`last_modified_user_id`),
							 KEY `manual_contact_created_user_id_fk` (`created_user_id`),
							 CONSTRAINT `manual_contact_contact_id_fk_1` FOREIGN KEY (`contact_id`) REFERENCES `contact` (`id`),
							 CONSTRAINT `manual_contact_contact_type_id_fk_2` FOREIGN KEY (`contact_type_id`) REFERENCES `contact_type` (`id`),
							 CONSTRAINT `manual_contact_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
							 CONSTRAINT `manual_contact_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
							)
							ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci"
		);

		$this->execute(
			"CREATE TABLE `medication` (
							 `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
							 `patient_id` int(10) unsigned NOT NULL,
							 `drug_id` int(10) unsigned NOT NULL,
							 `route_id` int(10) unsigned NOT NULL,
							 `option_id` int(10) unsigned DEFAULT NULL,
							 `frequency_id` int(10) unsigned NOT NULL,
							 `start_date` date NOT NULL,
							 `end_date` date DEFAULT NULL,
							 `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `last_modified_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
							 `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `created_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
							 PRIMARY KEY (`id`),
							 KEY `medication_lmui_fk` (`last_modified_user_id`),
							 KEY `medication_cui_fk` (`created_user_id`),
							 KEY `medication_drug_id_fk` (`drug_id`),
							 KEY `medication_route_id_fk` (`route_id`),
							 KEY `medication_option_id_fk` (`option_id`),
							 KEY `medication_frequency_id_fk` (`frequency_id`),
							 CONSTRAINT `medication_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
							 CONSTRAINT `medication_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
							 CONSTRAINT `medication_drug_id_fk` FOREIGN KEY (`drug_id`) REFERENCES `drug` (`id`),
							 CONSTRAINT `medication_route_id_fk` FOREIGN KEY (`route_id`) REFERENCES `drug_route` (`id`),
							 CONSTRAINT `medication_option_id_fk` FOREIGN KEY (`option_id`) REFERENCES `drug_route_option` (`id`),
							 CONSTRAINT `medication_frequency_id_fk` FOREIGN KEY (`frequency_id`) REFERENCES `drug_frequency` (`id`)
							)
							ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci"
		);

		$this->execute(
			"CREATE TABLE `nsc_grade` (
							 `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
							 `name` varchar(3) NOT NULL,
							 `type` tinyint(1) DEFAULT '0',
							 `medical_phrase` varchar(5000) NOT NULL,
							 `layman_phrase` varchar(1000) NOT NULL,
							 `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 PRIMARY KEY (`id`),
							 UNIQUE KEY `name` (`name`),
							 KEY `nsc_grade_last_modified_user_id_fk` (`last_modified_user_id`),
							 KEY `nsc_grade_created_user_id_fk` (`created_user_id`),
							 CONSTRAINT `nsc_grade_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
							 CONSTRAINT `nsc_grade_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
							)
							ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci"
		);

		$this->execute(
			"CREATE TABLE `opcs_code` (
							 `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
							 `name` varchar(255) NOT NULL,
							 `description` varchar(255) NOT NULL,
							 `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 PRIMARY KEY (`id`),
							 KEY `opcs_code_last_modified_user_id_fk` (`last_modified_user_id`),
							 KEY `opcs_code_created_user_id_fk` (`created_user_id`),
							 CONSTRAINT `opcs_code_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
							 CONSTRAINT `opcs_code_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
							)
							ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci"
		);

		$this->execute(
			"CREATE TABLE `operative_device` (
							 `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
							 `name` varchar(64) NOT NULL,
							 `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 PRIMARY KEY (`id`),
							 KEY `operative_device_last_modified_user_id_fk` (`last_modified_user_id`),
							 KEY `operative_device_created_user_id_fk` (`created_user_id`),
							 CONSTRAINT `operative_device_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
							 CONSTRAINT `operative_device_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
							)
							ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci"
		);

		$this->execute(
			"CREATE TABLE `patient` (
							 `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
							 `pas_key` int(10) unsigned DEFAULT NULL,
							 `dob` date DEFAULT NULL,
							 `gender` varchar(1) DEFAULT NULL,
							 `hos_num` varchar(40) DEFAULT NULL,
							 `nhs_num` varchar(40) DEFAULT NULL,
							 `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 `gp_id` int(10) unsigned DEFAULT NULL,
							 `date_of_death` date DEFAULT NULL,
							 `practice_id` int(10) unsigned DEFAULT NULL,
							 `ethnic_group_id` int(10) unsigned DEFAULT NULL,
							 `contact_id` int(10) unsigned NOT NULL,
							 PRIMARY KEY (`id`),
							 KEY `patient_last_modified_user_id_fk` (`last_modified_user_id`),
							 KEY `patient_created_user_id_fk` (`created_user_id`),
							 KEY `patient_gp_id_fk` (`gp_id`),
							 KEY `patient_practice_id_fk` (`practice_id`),
							 KEY `patient_ethnic_group_id_fk` (`ethnic_group_id`),
							 KEY `patient_contact_id_fk` (`contact_id`),
							 CONSTRAINT `patient_contact_id_fk` FOREIGN KEY (`contact_id`) REFERENCES `contact` (`id`),
							 CONSTRAINT `patient_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
							 CONSTRAINT `patient_ethnic_group_id_fk` FOREIGN KEY (`ethnic_group_id`) REFERENCES `ethnic_group` (`id`),
							 CONSTRAINT `patient_gp_id_fk` FOREIGN KEY (`gp_id`) REFERENCES `gp` (`id`),
							 CONSTRAINT `patient_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
							 CONSTRAINT `patient_practice_id_fk` FOREIGN KEY (`practice_id`) REFERENCES `practice` (`id`)
							)
							ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci"
		);

		$this->execute(
			"CREATE TABLE `patient_allergy_assignment` (
							 `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
							 `patient_id` int(10) unsigned NOT NULL,
							 `allergy_id` int(10) unsigned NOT NULL,
							 `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `last_modified_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
							 `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `created_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
							 PRIMARY KEY (`id`),
							 KEY `patient_allergy_assignment_patient_id_fk` (`patient_id`),
							 KEY `patient_allergy_assignment_allergy_id_fk` (`allergy_id`),
							 KEY `patient_allergy_assignment_last_modified_user_id_fk` (`last_modified_user_id`),
							 KEY `patient_allergy_assignment_created_user_id_fk` (`created_user_id`),
							 CONSTRAINT `patient_allergy_assignment_allergy_id_fk` FOREIGN KEY (`allergy_id`) REFERENCES `allergy` (`id`),
							 CONSTRAINT `patient_allergy_assignment_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
							 CONSTRAINT `patient_allergy_assignment_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
							 CONSTRAINT `patient_allergy_assignment_patient_id_fk` FOREIGN KEY (`patient_id`) REFERENCES `patient` (`id`)
							)
							ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci"
		);

		$this->execute(
			"CREATE TABLE `patient_contact_assignment` (
							 `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
							 `patient_id` int(10) unsigned NOT NULL,
							 `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 `location_id` int(10) unsigned DEFAULT NULL,
							 `contact_id` int(10) unsigned DEFAULT NULL,
							 PRIMARY KEY (`id`),
							 KEY `patient_consultant_assignment_patient_id_fk` (`patient_id`),
							 KEY `patient_consultant_assignment_last_modified_user_id_fk` (`last_modified_user_id`),
							 KEY `patient_consultant_assignment_created_user_id_fk` (`created_user_id`),
							 KEY `patient_contact_assignment_location_id_fk` (`location_id`),
							 KEY `patient_contact_assignment_contact_id_fk` (`contact_id`),
							 CONSTRAINT `patient_consultant_assignment_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
							 CONSTRAINT `patient_consultant_assignment_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
							 CONSTRAINT `patient_consultant_assignment_patient_id_fk` FOREIGN KEY (`patient_id`) REFERENCES `patient` (`id`),
							 CONSTRAINT `patient_contact_assignment_contact_id_fk` FOREIGN KEY (`contact_id`) REFERENCES `contact` (`id`),
							 CONSTRAINT `patient_contact_assignment_location_id_fk` FOREIGN KEY (`location_id`) REFERENCES `contact_location` (`id`)
							)
							ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci"
		);

		$this->execute(
			"CREATE TABLE `patient_oph_info` (
							 `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
							 `patient_id` int(10) unsigned NOT NULL,
							 `cvi_status_date` varchar(10) NOT NULL,
							 `cvi_status_id` int(10) unsigned NOT NULL,
							 `last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 PRIMARY KEY (`id`),
							 KEY `patient_oph_info_patient_id_fk` (`patient_id`),
							 KEY `patient_oph_info_cvi_status_id_fk` (`cvi_status_id`),
							 KEY `patient_oph_info_lmui_fk` (`last_modified_user_id`),
							 KEY `patient_oph_info_cui_fk` (`created_user_id`),
							 CONSTRAINT `patient_oph_info_patient_id_fk` FOREIGN KEY (`patient_id`) REFERENCES `patient` (`id`),
							 CONSTRAINT `patient_oph_info_cvi_status_id_fk` FOREIGN KEY (`cvi_status_id`) REFERENCES `patient_oph_info_cvi_status` (`id`),
							 CONSTRAINT `patient_oph_info_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
							 CONSTRAINT `patient_oph_info_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
							)
							ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci"
		);

		$this->execute(
			"CREATE TABLE `patient_oph_info_cvi_status` (
							 `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
							 `name` varchar(128) NOT NULL,
							 `display_order` int(10) unsigned NOT NULL DEFAULT '1',
							 `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `last_modified_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
							 `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `created_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
							 PRIMARY KEY (`id`),
							 KEY `patient_oph_info_cvi_status_lmui_fk` (`last_modified_user_id`),
							 KEY `patient_oph_info_cvi_status_cui_fk` (`created_user_id`),
							 CONSTRAINT `patient_oph_info_cvi_status_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
							 CONSTRAINT `patient_oph_info_cvi_status_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)
							)
							ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci"
		);

		$this->execute(
			"CREATE TABLE `patient_shortcode` (
							 `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
							 `event_type_id` int(10) unsigned DEFAULT NULL,
							 `default_code` varchar(3) NOT NULL,
							 `code` varchar(3) NOT NULL,
							 `method` varchar(64) NOT NULL,
							 `description` varchar(1024) NOT NULL,
							 `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `last_modified_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
							 `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `created_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
							 PRIMARY KEY (`id`),
							 KEY `patient_shortcode_event_type_id_fk` (`event_type_id`),
							 KEY `patient_shortcode_lmui_fk` (`last_modified_user_id`),
							 KEY `patient_shortcode_cui_fk` (`created_user_id`),
							 CONSTRAINT `patient_shortcode_event_type_id_fk` FOREIGN KEY (`event_type_id`) REFERENCES `event_type` (`id`),
							 CONSTRAINT `patient_shortcode_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
							 CONSTRAINT `patient_shortcode_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)
							)
							ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci"
		);

		$this->execute(
			"CREATE TABLE `period` (
							 `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
							 `name` varchar(128) NOT NULL,
							 `display_order` int(10) unsigned NOT NULL DEFAULT '1',
							 `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `last_modified_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
							 `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `created_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
							 PRIMARY KEY (`id`),
							 KEY `period_lmui_fk` (`last_modified_user_id`),
							 KEY `period_cui_fk` (`created_user_id`),
							 CONSTRAINT `period_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
							 CONSTRAINT `period_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)
							)
							ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci"
		);

		$this->execute(
			"CREATE TABLE `person` (
							 `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
							 `contact_id` int(10) unsigned NOT NULL,
							 `last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 `source_id` int(10) unsigned DEFAULT NULL,
							 `remote_id` varchar(40) NOT NULL,
							 PRIMARY KEY (`id`),
							 KEY `person_last_modified_user_id_fk` (`last_modified_user_id`),
							 KEY `person_created_user_id_fk` (`created_user_id`),
							 KEY `person_source_id_fk` (`source_id`),
							 KEY `person_contact_id_fk` (`contact_id`),
							 CONSTRAINT `person_contact_id_fk` FOREIGN KEY (`contact_id`) REFERENCES `contact` (`id`),
							 CONSTRAINT `person_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
							 CONSTRAINT `person_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
							 CONSTRAINT `person_source_id_fk` FOREIGN KEY (`source_id`) REFERENCES `import_source` (`id`)
							)
							ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci"
		);

		$this->execute(
			"CREATE TABLE `phrase` (
							 `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
							 `phrase` text ,
							 `section_id` int(10) unsigned NOT NULL,
							 `display_order` int(10) unsigned DEFAULT NULL,
							 `phrase_name_id` int(10) unsigned NOT NULL,
							 `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 PRIMARY KEY (`id`),
							 KEY `phrase_phrase_name_id_fk` (`phrase_name_id`),
							 KEY `phrase_last_modified_user_id_fk` (`last_modified_user_id`),
							 KEY `phrase_created_user_id_fk` (`created_user_id`),
							 CONSTRAINT `phrase_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
							 CONSTRAINT `phrase_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
							 CONSTRAINT `phrase_phrase_name_id_fk` FOREIGN KEY (`phrase_name_id`) REFERENCES `phrase_name` (`id`)
							)
							ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci"
		);

		$this->execute(
			"CREATE TABLE `phrase_by_firm` (
							 `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
							 `phrase` text ,
							 `section_id` int(10) unsigned NOT NULL,
							 `display_order` int(10) unsigned DEFAULT NULL,
							 `firm_id` int(10) unsigned NOT NULL,
							 `phrase_name_id` int(10) unsigned NOT NULL,
							 `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 PRIMARY KEY (`id`),
							 KEY `phrase_by_firm_section_fk` (`section_id`),
							 KEY `phrase_by_firm_firm_fk` (`firm_id`),
							 KEY `phrase_by_firm_phrase_name_id_fk` (`phrase_name_id`),
							 KEY `phrase_by_firm_last_modified_user_id_fk` (`last_modified_user_id`),
							 KEY `phrase_by_firm_created_user_id_fk` (`created_user_id`),
							 CONSTRAINT `phrase_by_firm_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
							 CONSTRAINT `phrase_by_firm_firm_fk` FOREIGN KEY (`firm_id`) REFERENCES `firm` (`id`),
							 CONSTRAINT `phrase_by_firm_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
							 CONSTRAINT `phrase_by_firm_phrase_name_id_fk` FOREIGN KEY (`phrase_name_id`) REFERENCES `phrase_name` (`id`),
							 CONSTRAINT `phrase_by_firm_section_fk` FOREIGN KEY (`section_id`) REFERENCES `section` (`id`)
							)
							ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci"
		);

		$this->execute(
			"CREATE TABLE `phrase_by_subspecialty` (
							 `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
							 `phrase` text ,
							 `section_id` int(10) unsigned NOT NULL,
							 `display_order` int(10) unsigned DEFAULT NULL,
							 `subspecialty_id` int(10) unsigned NOT NULL,
							 `phrase_name_id` int(10) unsigned NOT NULL,
							 `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 PRIMARY KEY (`id`),
							 KEY `phrase_by_subspecialty_subspecialty_fk` (`subspecialty_id`),
							 KEY `phrase_by_subspecialty_section_fk` (`section_id`),
							 KEY `phrase_by_subspecialty_last_modified_user_id_fk` (`last_modified_user_id`),
							 KEY `phrase_by_subspecialty_created_user_id_fk` (`created_user_id`),
							 KEY `phrase_by_subspecialty_phrase_name_id_fk` (`phrase_name_id`),
							 CONSTRAINT `phrase_by_subspecialty_section_fk` FOREIGN KEY (`section_id`) REFERENCES `section` (`id`),
							 CONSTRAINT `phrase_by_subspecialty_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
							 CONSTRAINT `phrase_by_subspecialty_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
							 CONSTRAINT `phrase_by_subspecialty_phrase_name_id_fk` FOREIGN KEY (`phrase_name_id`) REFERENCES `phrase_name` (`id`),
							 CONSTRAINT `phrase_by_subspecialty_subspecialty_fk` FOREIGN KEY (`subspecialty_id`) REFERENCES `subspecialty` (`id`)
							)
							ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci"
		);

		$this->execute(
			"CREATE TABLE `phrase_name` (
							 `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
							 `name` varchar(255) DEFAULT NULL,
							 `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 PRIMARY KEY (`id`),
							 KEY `phrase_name_last_modified_user_id_fk` (`last_modified_user_id`),
							 KEY `phrase_name_created_user_id_fk` (`created_user_id`),
							 CONSTRAINT `phrase_name_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
							 CONSTRAINT `phrase_name_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
							)
							ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci"
		);

		$this->execute(
			"CREATE TABLE `practice` (
							 `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
							 `code` varchar(64) NOT NULL,
							 `phone` varchar(64) NOT NULL,
							 `last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 `contact_id` int(10) unsigned NOT NULL,
							 PRIMARY KEY (`id`),
							 KEY `practice_last_modified_user_id_fk` (`last_modified_user_id`),
							 KEY `practice_created_user_id_fk` (`created_user_id`),
							 KEY `practice_contact_id_fk` (`contact_id`),
							 CONSTRAINT `practice_contact_id_fk` FOREIGN KEY (`contact_id`) REFERENCES `contact` (`id`),
							 CONSTRAINT `practice_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
							 CONSTRAINT `practice_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
							)
							ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci"
		);

		$this->execute(
			"CREATE TABLE `previous_operation` (
							 `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
							 `patient_id` int(10) unsigned NOT NULL,
							 `side_id` int(10) unsigned DEFAULT NULL,
							 `operation` varchar(1024) NOT NULL,
							 `date` varchar(10) NOT NULL,
							 `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 PRIMARY KEY (`id`),
							 KEY `previous_operation_last_modified_user_id_fk` (`last_modified_user_id`),
							 KEY `previous_operation_created_user_id_fk` (`created_user_id`),
							 KEY `previous_operation_patient_id_fk` (`patient_id`),
							 KEY `previous_operation_side_id_fk` (`side_id`),
							 CONSTRAINT `previous_operation_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
							 CONSTRAINT `previous_operation_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
							 CONSTRAINT `previous_operation_patient_id_fk` FOREIGN KEY (`patient_id`) REFERENCES `patient` (`id`),
							 CONSTRAINT `previous_operation_side_id_fk` FOREIGN KEY (`side_id`) REFERENCES `eye` (`id`)
							)
							ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci"
		);

		$this->execute(
			"CREATE TABLE `priority` (
							 `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
							 `name` varchar(10) DEFAULT NULL,
							 PRIMARY KEY (`id`)
							)
							ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci"
		);

		$this->execute(
			"CREATE TABLE `proc` (
							 `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
							 `term` varchar(255) NOT NULL,
							 `short_format` varchar(255) NOT NULL,
							 `default_duration` smallint(5) unsigned NOT NULL,
							 `snomed_code` varchar(20) NOT NULL,
							 `snomed_term` varchar(255) NOT NULL DEFAULT '0',
							 `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 `unbooked` tinyint(1) unsigned NOT NULL DEFAULT '0',
							 PRIMARY KEY (`id`),
							 KEY `proc_last_modified_user_id_fk` (`last_modified_user_id`),
							 KEY `proc_created_user_id_fk` (`created_user_id`),
							 CONSTRAINT `proc_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
							 CONSTRAINT `proc_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
							)
							ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci"
		);

		$this->execute(
			"CREATE TABLE `proc_opcs_assignment` (
							 `proc_id` int(10) unsigned NOT NULL,
							 `opcs_code_id` int(10) unsigned NOT NULL,
							 `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 PRIMARY KEY (`proc_id`,`opcs_code_id`),
							 KEY `opcs_code_id` (`opcs_code_id`),
							 KEY `procedure_id` (`proc_id`),
							 KEY `proc_opcs_assignment_last_modified_user_id_fk` (`last_modified_user_id`),
							 KEY `proc_opcs_assignment_created_user_id_fk` (`created_user_id`),
							 CONSTRAINT `opcs_code_fk` FOREIGN KEY (`opcs_code_id`) REFERENCES `opcs_code` (`id`),
							 CONSTRAINT `proc_opcs_assignment_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
							 CONSTRAINT `proc_opcs_assignment_ibfk_1` FOREIGN KEY (`proc_id`) REFERENCES `proc` (`id`),
							 CONSTRAINT `proc_opcs_assignment_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
							)
							ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci"
		);

		$this->execute(
			"CREATE TABLE `proc_subspecialty_assignment` (
							 `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
							 `proc_id` int(10) unsigned NOT NULL,
							 `subspecialty_id` int(10) unsigned NOT NULL,
							 `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 PRIMARY KEY (`id`),
							 KEY `proc_subspecialty_assignment_proc_id_fk` (`proc_id`),
							 KEY `proc_subspecialty_assignment_subspecialty_id_fk` (`subspecialty_id`),
							 KEY `proc_subspecialty_assignment_last_modified_user_id_fk` (`last_modified_user_id`),
							 KEY `proc_subspecialty_assignment_created_user_id_fk` (`created_user_id`),
							 CONSTRAINT `proc_subspecialty_assignment_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
							 CONSTRAINT `proc_subspecialty_assignment_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
							 CONSTRAINT `proc_subspecialty_assignment_ibfk_1` FOREIGN KEY (`proc_id`) REFERENCES `proc` (`id`),
							 CONSTRAINT `proc_subspecialty_assignment_ibfk_2` FOREIGN KEY (`subspecialty_id`) REFERENCES `subspecialty` (`id`)
							)
							ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci"
		);

		$this->execute(
			"CREATE TABLE `proc_subspecialty_subsection_assignment` (
							 `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
							 `proc_id` int(10) unsigned NOT NULL,
							 `subspecialty_subsection_id` int(10) unsigned NOT NULL,
							 `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 PRIMARY KEY (`id`),
							 KEY `proc_subspecialty_subsection_assignment_proc_id_fk` (`proc_id`),
							 KEY `pssa_subspecialty_subsection_id_fk` (`subspecialty_subsection_id`),
							 KEY `proc_subspecialty_subsection_assignment_last_modified_user_id_fk` (`last_modified_user_id`),
							 KEY `proc_subspecialty_subsection_assignment_created_user_id_fk` (`created_user_id`),
							 CONSTRAINT `proc_subspecialty_subsection_assignment_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
							 CONSTRAINT `proc_subspecialty_subsection_assignment_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
							 CONSTRAINT `proc_subspecialty_subsection_assignment_proc_id_fk` FOREIGN KEY (`proc_id`) REFERENCES `proc` (`id`),
							 CONSTRAINT `pssa_subspecialty_subsection_id_fk` FOREIGN KEY (`subspecialty_subsection_id`) REFERENCES `subspecialty_subsection` (`id`)
							)
							ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci"
		);

		$this->execute(
			"CREATE TABLE `procedure_additional` (
							 `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
							 `proc_id` int(10) unsigned NOT NULL,
							 `additional_proc_id` int(10) unsigned NOT NULL,
							 `last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 PRIMARY KEY (`id`),
							 KEY `procedure_additional_proc_id_fk` (`proc_id`),
							 KEY `procedure_additional_additional_proc_id_fk` (`additional_proc_id`),
							 KEY `procedure_additional_last_modified_user_id_fk` (`last_modified_user_id`),
							 KEY `procedure_additional_created_user_id_fk` (`created_user_id`),
							 CONSTRAINT `procedure_additional_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
							 CONSTRAINT `procedure_additional_proc_id_fk` FOREIGN KEY (`proc_id`) REFERENCES `proc` (`id`),
							 CONSTRAINT `procedure_additional_additional_proc_id_fk` FOREIGN KEY (`additional_proc_id`) REFERENCES `proc` (`id`),
							 CONSTRAINT `procedure_additional_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
							)
							ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci"
		);

		$this->execute(
			"CREATE TABLE `procedure_benefit` (
							 `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
							 `proc_id` int(10) unsigned NOT NULL,
							 `benefit_id` int(10) unsigned NOT NULL,
							 `last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 PRIMARY KEY (`id`),
							 KEY `procedure_benefit_last_modified_user_id_fk` (`last_modified_user_id`),
							 KEY `procedure_benefit_created_user_id_fk` (`created_user_id`),
							 KEY `procedure_benefit_proc_id_fk` (`proc_id`),
							 KEY `procedure_benefit_benefit_id_fk` (`benefit_id`),
							 CONSTRAINT `procedure_benefit_benefit_id_fk` FOREIGN KEY (`benefit_id`) REFERENCES `benefit` (`id`),
							 CONSTRAINT `procedure_benefit_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
							 CONSTRAINT `procedure_benefit_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
							 CONSTRAINT `procedure_benefit_proc_id_fk` FOREIGN KEY (`proc_id`) REFERENCES `proc` (`id`)
							)
							ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci"
		);

		$this->execute(
			"CREATE TABLE `procedure_complication` (
							 `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
							 `proc_id` int(10) unsigned NOT NULL,
							 `complication_id` int(10) unsigned NOT NULL,
							 `last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 PRIMARY KEY (`id`),
							 KEY `procedure_complication_last_modified_user_id_fk` (`last_modified_user_id`),
							 KEY `procedure_complication_created_user_id_fk` (`created_user_id`),
							 KEY `procedure_complication_proc_id_fk` (`proc_id`),
							 KEY `procedure_complication_complication_id_fk` (`complication_id`),
							 CONSTRAINT `procedure_complication_complication_id_fk` FOREIGN KEY (`complication_id`) REFERENCES `complication` (`id`),
							 CONSTRAINT `procedure_complication_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
							 CONSTRAINT `procedure_complication_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
							 CONSTRAINT `procedure_complication_proc_id_fk` FOREIGN KEY (`proc_id`) REFERENCES `proc` (`id`)
							)
							ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci"
		);

		$this->execute(
			"CREATE TABLE `protected_file` (
							 `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
							 `uid` varchar(64) NOT NULL,
							 `name` varchar(64) NOT NULL,
							 `title` varchar(64) NOT NULL,
							 `description` varchar(64) NOT NULL,
							 `mimetype` varchar(64) NOT NULL,
							 `size` varchar(64) NOT NULL,
							 `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 PRIMARY KEY (`id`),
							 UNIQUE KEY `asset_uid` (`uid`),
							 KEY `asset_last_modified_user_id_fk` (`last_modified_user_id`),
							 KEY `asset_created_user_id_fk` (`created_user_id`),
							 CONSTRAINT `asset_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
							 CONSTRAINT `asset_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
							)
							ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci"
		);

		$this->execute(
			"CREATE TABLE `referral` (
							 `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
							 `refno` varchar(64) NOT NULL,
							 `patient_id` int(10) unsigned NOT NULL,
							 `referral_type_id` int(10) unsigned NOT NULL,
							 `received_date` date NOT NULL,
							 `closed_date` date DEFAULT NULL,
							 `referrer` varchar(32) NOT NULL,
							 `firm_id` int(10) unsigned DEFAULT NULL,
							 `service_subspecialty_assignment_id` int(10) unsigned DEFAULT NULL,
							 `gp_id` int(10) unsigned DEFAULT NULL,
							 `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 PRIMARY KEY (`id`),
							 KEY `referral_last_modified_user_id_fk` (`last_modified_user_id`),
							 KEY `referral_created_user_id_fk` (`created_user_id`),
							 KEY `referral_patient_id_fk` (`patient_id`),
							 KEY `referral_firm_id_fk` (`firm_id`),
							 KEY `referral_gp_id_fk` (`gp_id`),
							 KEY `referral_referral_type_id_fk` (`referral_type_id`),
							 KEY `referral_service_subspecialty_assignment_id_fk` (`service_subspecialty_assignment_id`),
							 CONSTRAINT `referral_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
							 CONSTRAINT `referral_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
							 CONSTRAINT `referral_patient_id_fk` FOREIGN KEY (`patient_id`) REFERENCES `patient` (`id`),
							 CONSTRAINT `referral_firm_id_fk` FOREIGN KEY (`firm_id`) REFERENCES `firm` (`id`),
							 CONSTRAINT `referral_gp_id_fk` FOREIGN KEY (`gp_id`) REFERENCES `gp` (`id`),
							 CONSTRAINT `referral_referral_type_id_fk` FOREIGN KEY (`referral_type_id`) REFERENCES `referral_type` (`id`),
							 CONSTRAINT `referral_service_subspecialty_assignment_id_fk` FOREIGN KEY (`service_subspecialty_assignment_id`) REFERENCES `service_subspecialty_assignment` (`id`)
							)
							ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci"
		);

		$this->execute(
			"CREATE TABLE `referral_episode_assignment` (
							 `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
							 `referral_id` int(10) unsigned NOT NULL,
							 `episode_id` int(10) unsigned NOT NULL,
							 `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 PRIMARY KEY (`id`),
							 KEY `referral_episode_assignment_last_modified_user_id_fk` (`last_modified_user_id`),
							 KEY `referral_episode_assignment_created_user_id_fk` (`created_user_id`),
							 CONSTRAINT `referral_episode_assignment_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
							 CONSTRAINT `referral_episode_assignment_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
							)
							ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci"
		);

		$this->execute(
			"CREATE TABLE `referral_type` (
							 `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
							 `code` varchar(8) NOT NULL,
							 `name` varchar(64) NOT NULL,
							 `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 PRIMARY KEY (`id`),
							 KEY `referral_type_last_modified_user_id_fk` (`last_modified_user_id`),
							 KEY `referral_type_created_user_id_fk` (`created_user_id`),
							 CONSTRAINT `referral_type_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
							 CONSTRAINT `referral_type_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
							)
							ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci"
		);

		$this->execute(
			"CREATE TABLE `secondary_diagnosis` (
							 `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
							 `disorder_id` int(10) unsigned NOT NULL,
							 `eye_id` int(10) unsigned DEFAULT NULL,
							 `patient_id` int(10) unsigned NOT NULL,
							 `date` varchar(10) NOT NULL,
							 `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 PRIMARY KEY (`id`),
							 KEY `secondary_diagnosis_disorder_id_fk` (`disorder_id`),
							 KEY `secondary_diagnosis_eye_id_fk` (`eye_id`),
							 KEY `secondary_diagnosis_patient_id_fk` (`patient_id`),
							 KEY `secondary_diagnosis_last_modified_user_id_fk` (`last_modified_user_id`),
							 KEY `secondary_diagnosis_created_user_id_fk` (`created_user_id`),
							 CONSTRAINT `secondary_diagnosis_disorder_id_fk` FOREIGN KEY (`disorder_id`) REFERENCES `disorder` (`id`),
							 CONSTRAINT `secondary_diagnosis_eye_id_fk` FOREIGN KEY (`eye_id`) REFERENCES `eye` (`id`),
							 CONSTRAINT `secondary_diagnosis_patient_id_fk` FOREIGN KEY (`patient_id`) REFERENCES `patient` (`id`),
							 CONSTRAINT `secondary_diagnosis_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
							 CONSTRAINT `secondary_diagnosis_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
							)
							ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci"
		);

		$this->execute(
			"CREATE TABLE `section` (
							 `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
							 `name` varchar(255) DEFAULT NULL,
							 `section_type_id` int(10) unsigned NOT NULL,
							 `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 PRIMARY KEY (`id`),
							 KEY `section_last_modified_user_id_fk` (`last_modified_user_id`),
							 KEY `section_created_user_id_fk` (`created_user_id`),
							 CONSTRAINT `section_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
							 CONSTRAINT `section_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
							)
							ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci"
		);

		$this->execute(
			"CREATE TABLE `section_type` (
							 `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
							 `name` varchar(255) DEFAULT NULL,
							 `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 PRIMARY KEY (`id`),
							 KEY `section_type_last_modified_user_id_fk` (`last_modified_user_id`),
							 KEY `section_type_created_user_id_fk` (`created_user_id`),
							 CONSTRAINT `section_type_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
							 CONSTRAINT `section_type_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
							)
							ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci"
		);

		$this->execute(
			"CREATE TABLE `service` (
							 `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
							 `name` varchar(40) NOT NULL,
							 `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 PRIMARY KEY (`id`),
							 KEY `service_last_modified_user_id_fk` (`last_modified_user_id`),
							 KEY `service_created_user_id_fk` (`created_user_id`),
							 CONSTRAINT `service_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
							 CONSTRAINT `service_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
							)
							ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci"
		);

		$this->execute(
			"CREATE TABLE `service_subspecialty_assignment` (
							 `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
							 `service_id` int(10) unsigned NOT NULL,
							 `subspecialty_id` int(10) unsigned NOT NULL,
							 `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 PRIMARY KEY (`id`),
							 KEY `service_id` (`service_id`),
							 KEY `subspecialty_id` (`subspecialty_id`),
							 KEY `service_subspecialty_assignment_last_modified_user_id_fk` (`last_modified_user_id`),
							 KEY `service_subspecialty_assignment_created_user_id_fk` (`created_user_id`),
							 CONSTRAINT `service_subspecialty_assignment_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
							 CONSTRAINT `service_subspecialty_assignment_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
							 CONSTRAINT `service_subspecialty_assignment_ibfk_1` FOREIGN KEY (`service_id`) REFERENCES `service` (`id`),
							 CONSTRAINT `service_subspecialty_assignment_ibfk_2` FOREIGN KEY (`subspecialty_id`) REFERENCES `subspecialty` (`id`)
							)
							ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci"
		);

		$this->execute(
			"CREATE TABLE `setting_field_type` (
							 `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
							 `name` varchar(64) NOT NULL,
							 `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 PRIMARY KEY (`id`),
							 KEY `setting_field_type_last_modified_user_id_fk` (`last_modified_user_id`),
							 KEY `setting_field_type_created_user_id_fk` (`created_user_id`),
							 CONSTRAINT `setting_field_type_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
							 CONSTRAINT `setting_field_type_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
							)
							ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci"
		);

		$this->execute(
			"CREATE TABLE `setting_firm` (
							 `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
							 `firm_id` int(10) unsigned NOT NULL,
							 `element_type_id` int(10) unsigned NOT NULL,
							 `key` varchar(64) NOT NULL,
							 `value` varchar(64) NOT NULL,
							 `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 PRIMARY KEY (`id`),
							 KEY `setting_firm_firm_id_fk` (`firm_id`),
							 KEY `setting_firm_element_type_id_fk` (`element_type_id`),
							 KEY `setting_firm_last_modified_user_id_fk` (`last_modified_user_id`),
							 KEY `setting_firm_created_user_id_fk` (`created_user_id`),
							 CONSTRAINT `setting_firm_firm_id_fk` FOREIGN KEY (`firm_id`) REFERENCES `firm` (`id`),
							 CONSTRAINT `setting_firm_element_type_id_fk` FOREIGN KEY (`element_type_id`) REFERENCES `element_type` (`id`),
							 CONSTRAINT `setting_firm_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
							 CONSTRAINT `setting_firm_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
							)
							ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci"
		);

		$this->execute(
			"CREATE TABLE `setting_installation` (
							 `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
							 `element_type_id` int(10) unsigned NOT NULL,
							 `key` varchar(64) NOT NULL,
							 `value` varchar(64) NOT NULL,
							 `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 PRIMARY KEY (`id`),
							 KEY `setting_installation_element_type_id_fk` (`element_type_id`),
							 KEY `setting_installation_last_modified_user_id_fk` (`last_modified_user_id`),
							 KEY `setting_installation_created_user_id_fk` (`created_user_id`),
							 CONSTRAINT `setting_installation_element_type_id_fk` FOREIGN KEY (`element_type_id`) REFERENCES `element_type` (`id`),
							 CONSTRAINT `setting_installation_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
							 CONSTRAINT `setting_installation_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
							)
							ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci"
		);

		$this->execute(
			"CREATE TABLE `setting_institution` (
							 `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
							 `institution_id` int(10) unsigned NOT NULL,
							 `element_type_id` int(10) unsigned NOT NULL,
							 `key` varchar(64) NOT NULL,
							 `value` varchar(64) NOT NULL,
							 `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 PRIMARY KEY (`id`),
							 KEY `setting_institution_institution_id_fk` (`institution_id`),
							 KEY `setting_institution_element_type_id_fk` (`element_type_id`),
							 KEY `setting_institution_last_modified_user_id_fk` (`last_modified_user_id`),
							 KEY `setting_institution_created_user_id_fk` (`created_user_id`),
							 CONSTRAINT `setting_institution_institution_id_fk` FOREIGN KEY (`institution_id`) REFERENCES `institution` (`id`),
							 CONSTRAINT `setting_institution_element_type_id_fk` FOREIGN KEY (`element_type_id`) REFERENCES `element_type` (`id`),
							 CONSTRAINT `setting_institution_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
							 CONSTRAINT `setting_institution_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
							)
							ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci"
		);

		$this->execute(
			"CREATE TABLE `setting_metadata` (
							 `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
							 `element_type_id` int(10) unsigned NOT NULL,
							 `display_order` tinyint(3) unsigned DEFAULT '0',
							 `field_type_id` int(10) unsigned NOT NULL,
							 `key` varchar(64) NOT NULL,
							 `name` varchar(64) NOT NULL,
							 `data` varchar(4096) NOT NULL,
							 `default_value` varchar(64) NOT NULL,
							 `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 PRIMARY KEY (`id`),
							 KEY `setting_metadata_element_type_id_fk` (`element_type_id`),
							 KEY `setting_metadata_field_type_id_fk` (`field_type_id`),
							 KEY `setting_metadata_last_modified_user_id_fk` (`last_modified_user_id`),
							 KEY `setting_metadata_created_user_id_fk` (`created_user_id`),
							 CONSTRAINT `setting_metadata_element_type_id_fk` FOREIGN KEY (`element_type_id`) REFERENCES `element_type` (`id`),
							 CONSTRAINT `setting_metadata_field_type_id_fk` FOREIGN KEY (`field_type_id`) REFERENCES `setting_field_type` (`id`),
							 CONSTRAINT `setting_metadata_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
							 CONSTRAINT `setting_metadata_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
							)
							ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci"
		);

		$this->execute(
			"CREATE TABLE `setting_site` (
							 `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
							 `site_id` int(10) unsigned NOT NULL,
							 `element_type_id` int(10) unsigned NOT NULL,
							 `key` varchar(64) NOT NULL,
							 `value` varchar(64) NOT NULL,
							 `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 PRIMARY KEY (`id`),
							 KEY `setting_site_site_id_fk` (`site_id`),
							 KEY `setting_site_element_type_id_fk` (`element_type_id`),
							 KEY `setting_site_last_modified_user_id_fk` (`last_modified_user_id`),
							 KEY `setting_site_created_user_id_fk` (`created_user_id`),
							 CONSTRAINT `setting_site_site_id_fk` FOREIGN KEY (`site_id`) REFERENCES `site` (`id`),
							 CONSTRAINT `setting_site_element_type_id_fk` FOREIGN KEY (`element_type_id`) REFERENCES `element_type` (`id`),
							 CONSTRAINT `setting_site_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
							 CONSTRAINT `setting_site_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
							)
							ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci"
		);

		$this->execute(
			"CREATE TABLE `setting_specialty` (
							 `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
							 `specialty_id` int(10) unsigned NOT NULL,
							 `element_type_id` int(10) unsigned NOT NULL,
							 `key` varchar(64) NOT NULL,
							 `value` varchar(64) NOT NULL,
							 `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 PRIMARY KEY (`id`),
							 KEY `setting_specialty_specialty_id_fk` (`specialty_id`),
							 KEY `setting_specialty_element_type_id_fk` (`element_type_id`),
							 KEY `setting_specialty_last_modified_user_id_fk` (`last_modified_user_id`),
							 KEY `setting_specialty_created_user_id_fk` (`created_user_id`),
							 CONSTRAINT `setting_specialty_specialty_id_fk` FOREIGN KEY (`specialty_id`) REFERENCES `specialty` (`id`),
							 CONSTRAINT `setting_specialty_element_type_id_fk` FOREIGN KEY (`element_type_id`) REFERENCES `element_type` (`id`),
							 CONSTRAINT `setting_specialty_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
							 CONSTRAINT `setting_specialty_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
							)
							ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci"
		);

		$this->execute(
			"CREATE TABLE `setting_subspecialty` (
							 `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
							 `subspecialty_id` int(10) unsigned NOT NULL,
							 `element_type_id` int(10) unsigned NOT NULL,
							 `key` varchar(64) NOT NULL,
							 `value` varchar(64) NOT NULL,
							 `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 PRIMARY KEY (`id`),
							 KEY `setting_subspecialty_subspecialty_id_fk` (`subspecialty_id`),
							 KEY `setting_subspecialty_element_type_id_fk` (`element_type_id`),
							 KEY `setting_subspecialty_last_modified_user_id_fk` (`last_modified_user_id`),
							 KEY `setting_subspecialty_created_user_id_fk` (`created_user_id`),
							 CONSTRAINT `setting_subspecialty_subspecialty_id_fk` FOREIGN KEY (`subspecialty_id`) REFERENCES `subspecialty` (`id`),
							 CONSTRAINT `setting_subspecialty_element_type_id_fk` FOREIGN KEY (`element_type_id`) REFERENCES `element_type` (`id`),
							 CONSTRAINT `setting_subspecialty_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
							 CONSTRAINT `setting_subspecialty_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
							)
							ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci"
		);

		$this->execute(
			"CREATE TABLE `setting_user` (
							 `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
							 `user_id` int(10) unsigned NOT NULL,
							 `element_type_id` int(10) unsigned NOT NULL,
							 `key` varchar(64) NOT NULL,
							 `value` varchar(64) NOT NULL,
							 `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 PRIMARY KEY (`id`),
							 KEY `setting_user_user_id_fk` (`user_id`),
							 KEY `setting_user_element_type_id_fk` (`element_type_id`),
							 KEY `setting_user_last_modified_user_id_fk` (`last_modified_user_id`),
							 KEY `setting_user_created_user_id_fk` (`created_user_id`),
							 CONSTRAINT `setting_user_user_id_fk` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`),
							 CONSTRAINT `setting_user_element_type_id_fk` FOREIGN KEY (`element_type_id`) REFERENCES `element_type` (`id`),
							 CONSTRAINT `setting_user_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
							 CONSTRAINT `setting_user_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
							)
							ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci"
		);

		$this->execute(
			"CREATE TABLE `site` (
							 `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
							 `name` varchar(255) NOT NULL,
							 `remote_id` varchar(10) NOT NULL,
							 `short_name` varchar(255) NOT NULL,
							 `fax` varchar(255) NOT NULL,
							 `telephone` varchar(255) NOT NULL,
							 `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 `institution_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `location` varchar(64) NOT NULL,
							 `contact_id` int(10) unsigned NOT NULL,
							 `replyto_contact_id` int(10) unsigned DEFAULT NULL,
							 `source_id` int(10) unsigned DEFAULT NULL,
							 PRIMARY KEY (`id`),
							 KEY `site_last_modified_user_id_fk` (`last_modified_user_id`),
							 KEY `site_created_user_id_fk` (`created_user_id`),
							 KEY `site_institution_id_fk` (`institution_id`),
							 KEY `site_contact_id_fk` (`contact_id`),
							 KEY `site_replyto_contact_id_fk` (`replyto_contact_id`),
							 KEY `site_source_id_fk` (`source_id`),
							 CONSTRAINT `site_contact_id_fk` FOREIGN KEY (`contact_id`) REFERENCES `contact` (`id`),
							 CONSTRAINT `site_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
							 CONSTRAINT `site_institution_id_fk` FOREIGN KEY (`institution_id`) REFERENCES `institution` (`id`),
							 CONSTRAINT `site_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
							 CONSTRAINT `site_replyto_contact_id_fk` FOREIGN KEY (`replyto_contact_id`) REFERENCES `contact` (`id`),
							 CONSTRAINT `site_source_id_fk` FOREIGN KEY (`source_id`) REFERENCES `import_source` (`id`)
							)
							ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci"
		);

		$this->execute(
			"CREATE TABLE `site_consultant_assignment` (
							 `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
							 `site_id` int(10) unsigned NOT NULL,
							 `consultant_id` int(10) unsigned NOT NULL,
							 `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 PRIMARY KEY (`id`),
							 KEY `site_consultant_assignment_site_id_fk` (`site_id`),
							 KEY `site_consultant_assignment_consultant_id_fk` (`consultant_id`),
							 KEY `site_consultant_assignment_last_modified_user_id_fk` (`last_modified_user_id`),
							 KEY `site_consultant_assignment_created_user_id_fk` (`created_user_id`),
							 CONSTRAINT `site_consultant_assignment_site_id_fk` FOREIGN KEY (`site_id`) REFERENCES `site` (`id`),
							 CONSTRAINT `site_consultant_assignment_consultant_id_fk` FOREIGN KEY (`consultant_id`) REFERENCES `consultant` (`id`),
							 CONSTRAINT `site_consultant_assignment_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
							 CONSTRAINT `site_consultant_assignment_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
							)
							ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci"
		);

		$this->execute(
			"CREATE TABLE `site_subspecialty_anaesthetic_agent` (
							 `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
							 `site_id` int(10) unsigned NOT NULL,
							 `subspecialty_id` int(10) unsigned NOT NULL,
							 `anaesthetic_agent_id` int(10) unsigned NOT NULL,
							 `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 PRIMARY KEY (`id`),
							 KEY `site_subspecialty_anaesthetic_last_modified_user_id_fk` (`last_modified_user_id`),
							 KEY `site_subspecialty_anaesthetic_created_user_id_fk` (`created_user_id`),
							 KEY `site_subspecialty_anaesthetic_site_id` (`site_id`),
							 KEY `site_subspecialty_anaesthetic_subspecialty_id` (`subspecialty_id`),
							 KEY `site_subspecialty_anaesthetic_anaesthetic_agent_id` (`anaesthetic_agent_id`),
							 CONSTRAINT `site_subspecialty_anaesthetic_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
							 CONSTRAINT `site_subspecialty_anaesthetic_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
							 CONSTRAINT `site_subspecialty_anaesthetic_site_id_fk` FOREIGN KEY (`site_id`) REFERENCES `site` (`id`),
							 CONSTRAINT `site_subspecialty_anaesthetic_subspecialty_id_fk` FOREIGN KEY (`subspecialty_id`) REFERENCES `subspecialty` (`id`),
							 CONSTRAINT `site_subspecialty_anaesthetic_anaesthetic_agent_id_fk` FOREIGN KEY (`anaesthetic_agent_id`) REFERENCES `anaesthetic_agent` (`id`)
							)
							ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci"
		);

		$this->execute(
			"CREATE TABLE `site_subspecialty_anaesthetic_agent_default` (
							 `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
							 `site_id` int(10) unsigned NOT NULL,
							 `subspecialty_id` int(10) unsigned NOT NULL,
							 `anaesthetic_agent_id` int(10) unsigned NOT NULL,
							 `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 PRIMARY KEY (`id`),
							 KEY `site_subspecialty_anaesthetic_def_last_modified_user_id_fk` (`last_modified_user_id`),
							 KEY `site_subspecialty_anaesthetic_def_created_user_id_fk` (`created_user_id`),
							 KEY `site_subspecialty_anaesthetic_def_site_id` (`site_id`),
							 KEY `site_subspecialty_anaesthetic_def_subspecialty_id` (`subspecialty_id`),
							 KEY `site_subspecialty_anaesthetic_def_anaesthetic_agent_id` (`anaesthetic_agent_id`),
							 CONSTRAINT `site_subspecialty_anaesthetic_def_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
							 CONSTRAINT `site_subspecialty_anaesthetic_def_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
							 CONSTRAINT `site_subspecialty_anaesthetic_def_site_id_fk` FOREIGN KEY (`site_id`) REFERENCES `site` (`id`),
							 CONSTRAINT `site_subspecialty_anaesthetic_def_subspecialty_id_fk` FOREIGN KEY (`subspecialty_id`) REFERENCES `subspecialty` (`id`),
							 CONSTRAINT `site_subspecialty_anaesthetic_def_anaesthetic_agent_id_fk` FOREIGN KEY (`anaesthetic_agent_id`) REFERENCES `anaesthetic_agent` (`id`)
							)
							ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci"
		);

		$this->execute(
			"CREATE TABLE `site_subspecialty_drug` (
							 `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
							 `site_id` int(10) unsigned NOT NULL,
							 `subspecialty_id` int(10) unsigned NOT NULL,
							 `drug_id` int(10) unsigned NOT NULL,
							 `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 PRIMARY KEY (`id`),
							 KEY `site_subspecialty_drug_last_modified_user_id_fk` (`last_modified_user_id`),
							 KEY `site_subspecialty_drug_created_user_id_fk` (`created_user_id`),
							 KEY `site_subspecialty_drug_site_id` (`site_id`),
							 KEY `site_subspecialty_drug_subspecialty_id` (`subspecialty_id`),
							 KEY `site_subspecialty_drug_drug_id` (`drug_id`),
							 CONSTRAINT `site_subspecialty_drug_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
							 CONSTRAINT `site_subspecialty_drug_drug_id_fk` FOREIGN KEY (`drug_id`) REFERENCES `drug` (`id`),
							 CONSTRAINT `site_subspecialty_drug_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
							 CONSTRAINT `site_subspecialty_drug_site_id_fk` FOREIGN KEY (`site_id`) REFERENCES `site` (`id`),
							 CONSTRAINT `site_subspecialty_drug_subspecialty_id_fk` FOREIGN KEY (`subspecialty_id`) REFERENCES `subspecialty` (`id`)
							)
							ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci"
		);

		$this->execute(
			"CREATE TABLE `site_subspecialty_operative_device` (
							 `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
							 `site_id` int(10) unsigned NOT NULL,
							 `subspecialty_id` int(10) unsigned NOT NULL,
							 `operative_device_id` int(10) unsigned NOT NULL,
							 `display_order` tinyint(3) unsigned NOT NULL,
							 `default` tinyint(1) unsigned NOT NULL DEFAULT '0',
							 `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 PRIMARY KEY (`id`),
							 KEY `ss_operative_device_site_id_fk` (`site_id`),
							 KEY `ss_operative_device_subspecialty_id_fk` (`subspecialty_id`),
							 KEY `ss_operative_device_operative_device_id` (`operative_device_id`),
							 KEY `ss_operative_device_last_modified_user_id_fk` (`last_modified_user_id`),
							 KEY `ss_operative_device_created_user_id_fk` (`created_user_id`),
							 CONSTRAINT `ss_operative_device_site_id_fk` FOREIGN KEY (`site_id`) REFERENCES `site` (`id`),
							 CONSTRAINT `ss_operative_device_subspecialty_id_fk` FOREIGN KEY (`subspecialty_id`) REFERENCES `subspecialty` (`id`),
							 CONSTRAINT `ss_operative_device_operative_device_id_fk` FOREIGN KEY (`operative_device_id`) REFERENCES `operative_device` (`id`),
							 CONSTRAINT `ss_operative_device_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
							 CONSTRAINT `ss_operative_device_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
							)
							ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci"
		);

		$this->execute(
			"CREATE TABLE `specialty` (
							 `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
							 `name` varchar(255) NOT NULL DEFAULT '',
							 `code` int(10) unsigned NOT NULL,
							 `specialty_type_id` int(10) unsigned NOT NULL,
							 `default_title` varchar(64) NOT NULL,
							 `default_is_surgeon` tinyint(1) unsigned NOT NULL DEFAULT '0',
							 `adjective` varchar(64) NOT NULL,
							 `abbreviation` char(3) NOT NULL,
							 PRIMARY KEY (`id`),
							 UNIQUE KEY `abbreviation` (`abbreviation`),
							 UNIQUE KEY `abbreviation_2` (`abbreviation`),
							 KEY `specialty_specialty_type_id_fk` (`specialty_type_id`),
							 CONSTRAINT `specialty_specialty_type_id_fk` FOREIGN KEY (`specialty_type_id`) REFERENCES `specialty_type` (`id`)
							)
							ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci"
		);

		$this->execute(
			"CREATE TABLE `specialty_type` (
							 `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
							 `name` varchar(64) NOT NULL,
							 `display_order` int(10) unsigned NOT NULL,
							 `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 PRIMARY KEY (`id`),
							 KEY `specialty_type_last_modified_user_id_fk` (`last_modified_user_id`),
							 KEY `specialty_type_created_user_id_fk` (`created_user_id`),
							 CONSTRAINT `specialty_type_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
							 CONSTRAINT `specialty_type_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
							)
							ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci"
		);

		$this->execute(
			"CREATE TABLE `subspecialty` (
							 `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
							 `name` varchar(40) NOT NULL,
							 `ref_spec` varchar(3) NOT NULL,
							 `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 `specialty_id` int(10) unsigned NOT NULL,
							 PRIMARY KEY (`id`),
							 KEY `subspecialty_last_modified_user_id_fk` (`last_modified_user_id`),
							 KEY `subspecialty_created_user_id_fk` (`created_user_id`),
							 KEY `subspecialty_specialty_id_fk` (`specialty_id`),
							 CONSTRAINT `subspecialty_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
							 CONSTRAINT `subspecialty_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
							 CONSTRAINT `subspecialty_specialty_id_fk` FOREIGN KEY (`specialty_id`) REFERENCES `specialty` (`id`)
							)
							ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci"
		);

		$this->execute(
			"CREATE TABLE `subspecialty_subsection` (
							 `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
							 `subspecialty_id` int(10) unsigned NOT NULL,
							 `name` varchar(255) NOT NULL,
							 `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 PRIMARY KEY (`id`),
							 KEY `subspecialty_subsection_subspecialty_id_fk` (`subspecialty_id`),
							 KEY `subspecialty_subsection_last_modified_user_id_fk` (`last_modified_user_id`),
							 KEY `subspecialty_subsection_created_user_id_fk` (`created_user_id`),
							 CONSTRAINT `subspecialty_subsection_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
							 CONSTRAINT `subspecialty_subsection_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
							 CONSTRAINT `subspecialty_subsection_subspecialty_id_fk` FOREIGN KEY (`subspecialty_id`) REFERENCES `subspecialty` (`id`)
							)
							ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci"
		);

		$this->execute(
			"CREATE TABLE `tbl_audit_trail` (
							 `id` int(11) NOT NULL AUTO_INCREMENT,
							 `old_value` text,
							 `new_value` text,
							 `action` varchar(255) NOT NULL,
							 `model` varchar(255) NOT NULL,
							 `field` varchar(255) NOT NULL,
							 `stamp` datetime NOT NULL,
							 `user_id` int(10) DEFAULT NULL,
							 `model_id` int(10) NOT NULL,
							 PRIMARY KEY (`id`),
							 KEY `idx_audit_trail_user_id` (`user_id`),
							 KEY `idx_audit_trail_model_id` (`model_id`),
							 KEY `idx_audit_trail_model` (`model`),
							 KEY `idx_audit_trail_field` (`field`),
							 KEY `idx_audit_trail_action` (`action`),
							 KEY `idx_audit_trail_stamp` (`stamp`)
							)
							ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci"
		);

		$this->execute(
			"CREATE TABLE `user` (
							 `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
							 `username` varchar(40) NOT NULL,
							 `first_name` varchar(40) NOT NULL,
							 `last_name` varchar(40) NOT NULL,
							 `email` varchar(80) NOT NULL,
							 `active` tinyint(1) NOT NULL,
							 `global_firm_rights` tinyint(1) unsigned NOT NULL DEFAULT '0',
							 `title` varchar(40) NOT NULL,
							 `qualifications` varchar(255) NOT NULL,
							 `role` varchar(255) NOT NULL,
							 `code` varchar(255) DEFAULT NULL,
							 `password` varchar(40) DEFAULT NULL,
							 `salt` varchar(10) DEFAULT NULL,
							 `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 `last_firm_id` int(11) unsigned DEFAULT NULL,
							 `is_doctor` tinyint(1) unsigned NOT NULL DEFAULT '0',
							 `access_level` tinyint(1) unsigned NOT NULL DEFAULT '0',
							 `contact_id` int(10) unsigned DEFAULT NULL,
							 `last_site_id` int(10) unsigned DEFAULT NULL,
							 `is_clinical` tinyint(1) unsigned NOT NULL DEFAULT '0',
							 `is_consultant` tinyint(1) unsigned NOT NULL DEFAULT '0',
							 `is_surgeon` tinyint(1) unsigned NOT NULL DEFAULT '0',
							 `has_selected_firms` tinyint(1) unsigned NOT NULL,
							 PRIMARY KEY (`id`),
							 KEY `user_last_modified_user_id_fk` (`last_modified_user_id`),
							 KEY `user_created_user_id_fk` (`created_user_id`),
							 KEY `user_last_firm_id_fk` (`last_firm_id`),
							 KEY `user_contact_id_fk` (`contact_id`),
							 KEY `user_last_site_id_fk` (`last_site_id`),
							 CONSTRAINT `user_contact_id_fk` FOREIGN KEY (`contact_id`) REFERENCES `contact` (`id`),
							 CONSTRAINT `user_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
							 CONSTRAINT `user_last_firm_id_fk` FOREIGN KEY (`last_firm_id`) REFERENCES `firm` (`id`),
							 CONSTRAINT `user_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
							 CONSTRAINT `user_last_site_id_fk` FOREIGN KEY (`last_site_id`) REFERENCES `site` (`id`)
							)
							ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci"
		);

		$this->execute(
			"CREATE TABLE `user_firm` (
							 `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
							 `user_id` int(10) unsigned NOT NULL,
							 `firm_id` int(10) unsigned NOT NULL,
							 `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `last_modified_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
							 `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `created_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
							 PRIMARY KEY (`id`),
							 KEY `user_firm_user_id_fk` (`user_id`),
							 KEY `user_firm_firm_id_fk` (`firm_id`),
							 KEY `user_firm_lmui_fk` (`last_modified_user_id`),
							 KEY `user_firm_cui_fk` (`created_user_id`),
							 CONSTRAINT `user_firm_user_id_fk` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`),
							 CONSTRAINT `user_firm_firm_id_fk` FOREIGN KEY (`firm_id`) REFERENCES `firm` (`id`),
							 CONSTRAINT `user_firm_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
							 CONSTRAINT `user_firm_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)
							)
							ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci"
		);

		$this->execute(
			"CREATE TABLE `user_firm_preference` (
							 `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
							 `user_id` int(10) unsigned NOT NULL,
							 `firm_id` int(10) unsigned NOT NULL,
							 `position` int(10) unsigned NOT NULL,
							 `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 PRIMARY KEY (`id`),
							 KEY `user_firm_preference_last_modified_user_id_fk` (`last_modified_user_id`),
							 KEY `user_firm_preference_created_user_id_fk` (`created_user_id`),
							 CONSTRAINT `user_firm_preference_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
							 CONSTRAINT `user_firm_preference_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
							)
							ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci"
		);

		$this->execute(
			"CREATE TABLE `user_firm_rights` (
							 `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
							 `user_id` int(10) unsigned NOT NULL,
							 `firm_id` int(10) unsigned NOT NULL,
							 `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 PRIMARY KEY (`id`),
							 KEY `user_firm_rights_fk_1` (`user_id`),
							 KEY `user_firm_rights_fk_2` (`firm_id`),
							 KEY `user_firm_rights_last_modified_user_id_fk` (`last_modified_user_id`),
							 KEY `user_firm_rights_created_user_id_fk` (`created_user_id`),
							 CONSTRAINT `user_firm_rights_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
							 CONSTRAINT `user_firm_rights_fk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`),
							 CONSTRAINT `user_firm_rights_fk_2` FOREIGN KEY (`firm_id`) REFERENCES `firm` (`id`),
							 CONSTRAINT `user_firm_rights_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
							)
							ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci"
		);

		$this->execute(
			"CREATE TABLE `user_service_rights` (
							 `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
							 `user_id` int(10) unsigned NOT NULL,
							 `service_id` int(10) unsigned NOT NULL,
							 `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
							 PRIMARY KEY (`id`),
							 KEY `user_service_rights_fk_1` (`user_id`),
							 KEY `user_service_rights_fk_2` (`service_id`),
							 KEY `user_service_rights_last_modified_user_id_fk` (`last_modified_user_id`),
							 KEY `user_service_rights_created_user_id_fk` (`created_user_id`),
							 CONSTRAINT `user_service_rights_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
							 CONSTRAINT `user_service_rights_fk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`),
							 CONSTRAINT `user_service_rights_fk_2` FOREIGN KEY (`service_id`) REFERENCES `service` (`id`),
							 CONSTRAINT `user_service_rights_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
							)
							ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci"
		);

		$this->execute(
			"CREATE TABLE `user_session` (
							 `id` varchar(32) NOT NULL,
							 `expire` int(11) DEFAULT NULL,
							 `data` text,
							 PRIMARY KEY (`id`)
							)
							ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci"
		);

		$this->execute(
			"CREATE TABLE `user_site` (
							 `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
							 `user_id` int(10) unsigned NOT NULL,
							 `site_id` int(10) unsigned NOT NULL,
							 `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `last_modified_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
							 `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
							 `created_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
							 PRIMARY KEY (`id`),
							 KEY `user_site_user_id_fk` (`user_id`),
							 KEY `user_site_site_id_fk` (`site_id`),
							 KEY `user_site_lmui_fk` (`last_modified_user_id`),
							 KEY `user_site_cui_fk` (`created_user_id`),
							 CONSTRAINT `user_site_user_id_fk` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`),
							 CONSTRAINT `user_site_site_id_fk` FOREIGN KEY (`site_id`) REFERENCES `site` (`id`),
							 CONSTRAINT `user_site_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
							 CONSTRAINT `user_site_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)
							)
							ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci"
		);

		$this->initialiseData($this->getMigrationPath());
		$this->execute("SET foreign_key_checks = 1");
	}

}
