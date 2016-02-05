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

class m130913_000003_consolidation_for_ophtroperationnote extends OEMigration
{
	private  $element_types;

	public function up()
	{
		if (!$this->consolidate(
			array(
				"m120510_102522_ophtroperationnote_consolidated",
				"m120517_095948_fix_procedurelist_table_foreign_key",
				"m120530_130251_fields_for_fife",
				"m120531_134618_renumber_opnote_element_display_orders",
				"m120531_134910_personnel_element",
				"m120611_065502_additional_cataract_procedure_mapping",
				"m120612_095034_buckle_element_remove_report_field",
				"m120612_103357_add_report_field_to_buckle_and_cataract_elements",
				"m120619_092949_fix_surgeon_foreign_key",
				"m120620_092148_cataract_defaults",
				"m120620_092825_anaesthetic_given_by_default",
				"m120620_094821_revised_iol_type_list",
				"m120620_100817_adjust_cataract_default_details_text",
				"m120621_093839_change_title_of_postop_drugs_element",
				"m120621_094721_new_devices_for_cataract_element",
				"m120625_161647_add_new_procedures",
				"m120627_072411_add_private_iol_types",
				"m120627_075208_fix_double_associated_cataract_procedure",
				"m120628_130847_fix_procedure_element_associations",
				"m120629_130712_fife_preparation_element",
				"m120704_100346_change_new_procedures_to_longname",
				"m120705_094249_fix_missing_procedure_element_assignment",
				"m120706_115557_fix_dupe_procedure_element_row",
				"m120706_133551_increase_length_of_cataract_eyedraw_field",
				"m120709_093130_iol_type_id_should_be_nullable",
				"m120709_093714_add_new_cataract_complications",
				"m120710_062419_new_vr_fields",
				"m120711_141118_default_cataract_field_values",
				"m120719_143700_separate_postop_drug_table",
				"m120809_134618_renumber_more_opnote_element_display_orders",
				"m120810_130413_add_limbal_relaxing_incision_procedure_element",
				"m120816_094302_change_fife_differences_to_use_settings_rather_than_a_config_value",
				"m120917_142345_add_both_to_element_table_eye_for_procedurelist_element",
				"m120919_065814_make_tamponade_volume_field_nullable",
				"m120919_082005_add_new_percentage_values_to_gas_percentage_field",
				"m121030_093559_add_missing_procedure_element_mappings",
				"m121227_095138_soft_deletion_of_postop_drugs",
				"m130116_100158_fluorescein_procedure_element",
				"m130116_101710_fluorescein_element_table",
				"m130116_102441_fix_fluorescein_default",
				"m130121_094133_proc_element_for_icce",
				"m130218_094538_new_proc_elements_oe2661",
				"m130218_125200_new_proc_elements_shouldnt_be_default",
				"m130225_101706_opnote_link_to_booking_event",
				"m130311_133429_dont_require_iol_fields_if_no_iol",
				"m130318_113609_display_order_field_on_postop_drugs",
				"m130403_142345_event_id_indexes",
				"m130425_145858_trabectome_element",
				"m130604_103443_patient_shortcodes",
				"m130624_110551_predicted_refraction",
				"m130809_073841_procedure_assignment_primary_key"
			))
		) {
			$this->createTables();
		} else {
			// Check to see if the out of order migration has ever been run, and if not run it
			$ooo_migration = $this->getDbConnection()->createCommand()
				->select('version')
				->from('tbl_migration')
				->where('version = :version', array(':version' => 'm130722_115732_remove_unnecessary_element_tables'))
				->queryColumn();
			if($ooo_migration) {
				$this->getDbConnection()->createCommand()
					->delete('tbl_migration', array('version = :version', array(':version' => 'm130722_115732_remove_unnecessary_element_tables')));
			} else {
				$this->m130722_115732_remove_unnecessary_element_tables();
			}
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

	public function setData(){
		$this->element_types = array(
			'Element_OphTrOperationnote_ProcedureList' => array('name' => 'Procedure list', 'display_order' => 10, 'default' => 1),
			'Element_OphTrOperationnote_Preparation' => array('name' => 'Preparation', 'display_order' => 15, 'default' => 1),
			'Element_OphTrOperationnote_GenericProcedure' => array('name' => 'Generic procedure', 'display_order' => 20, 'default' => 0),
			'Element_OphTrOperationnote_Cataract' => array('name' => 'Cataract', 'display_order' => 20, 'default' => 0),
			'Element_OphTrOperationnote_Buckle' => array('name' => 'Buckle', 'display_order' => 20, 'default' => 0),
			'Element_OphTrOperationnote_Tamponade' => array('name' => 'Tamponade', 'display_order' => 20, 'default' => 0),
			'Element_OphTrOperationnote_MembranePeel' => array('name' => 'Membrane peel', 'display_order' => 20, 'default' => 0),
			'Element_OphTrOperationnote_Vitrectomy' => array('name' => 'Vitrectomy', 'display_order' => 20, 'default' => 0),
			'Element_OphTrOperationnote_Anaesthetic' => array('name' => 'Anaesthetic', 'display_order' => 30, 'default' => 1),
			'Element_OphTrOperationnote_Surgeon' => array('name' => 'Surgeon', 'display_order' => 40, 'default' => 1),
			'Element_OphTrOperationnote_Personnel' => array('name' => 'Personnel', 'display_order' => 45, 'default' => 1),
			'Element_OphTrOperationnote_PostOpDrugs' => array('name' => 'Per-operative drugs', 'display_order' => 50, 'default' => 1),
			'Element_OphTrOperationnote_Comments' => array('name' => 'Comments', 'display_order' => 60, 'default' => 1),
		);
	}

	protected function createTables()
	{
		$this->setData();
		//disable foreign keys check
		$this->execute("SET foreign_key_checks = 0");

		Yii::app()->cache->flush();

		$event_type_id = $this->insertOEEventType( 'Operation note', 'OphTrOperationnote', 'Tr');
		$this->insertOEElementType($this->element_types , $event_type_id);

		$this->execute("CREATE TABLE `et_ophtroperationnote_anaesthetic` (
			  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			  `event_id` int(10) unsigned NOT NULL,
			  `anaesthetic_type_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `anaesthetist_id` int(10) unsigned NOT NULL DEFAULT '4',
			  `anaesthetic_delivery_id` int(10) unsigned NOT NULL DEFAULT '5',
			  `anaesthetic_comment` varchar(1024)  DEFAULT NULL,
			  `display_order` tinyint(3) unsigned NOT NULL,
			  `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
			  `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
			  `anaesthetic_witness_id` int(10) unsigned DEFAULT NULL,
			  PRIMARY KEY (`id`),
			  KEY `et_ophtroperationnote_ana_type_last_modified_user_id_fk` (`last_modified_user_id`),
			  KEY `et_ophtroperationnote_ana_type_created_user_id_fk` (`created_user_id`),
			  KEY `et_ophtroperationnote_ana_anaesthetic_type_id_fk` (`anaesthetic_type_id`),
			  KEY `et_ophtroperationnote_ana_anaesthetist_id_fk` (`anaesthetist_id`),
			  KEY `et_ophtroperationnote_ana_anaesthetic_delivery_id_fk` (`anaesthetic_delivery_id`),
			  KEY `et_ophtroperationnote_ana_anaesthetic_witness_id_fk` (`anaesthetic_witness_id`),
			  KEY `et_ophtroperationnote_anaesthetic_eid_fk` (`event_id`),
			  CONSTRAINT `et_ophtroperationnote_anaesthetic_eid_fk` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`),
			  CONSTRAINT `et_ophtroperationnote_ana_anaesthetic_delivery_id_fk` FOREIGN KEY (`anaesthetic_delivery_id`) REFERENCES `anaesthetic_delivery` (`id`),
			  CONSTRAINT `et_ophtroperationnote_ana_anaesthetic_type_id_fk` FOREIGN KEY (`anaesthetic_type_id`) REFERENCES `anaesthetic_type` (`id`),
			  CONSTRAINT `et_ophtroperationnote_ana_anaesthetic_witness_id_fk` FOREIGN KEY (`anaesthetic_witness_id`) REFERENCES `user` (`id`),
			  CONSTRAINT `et_ophtroperationnote_ana_anaesthetist_id_fk` FOREIGN KEY (`anaesthetist_id`) REFERENCES `anaesthetist` (`id`),
			  CONSTRAINT `et_ophtroperationnote_ana_type_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
			  CONSTRAINT `et_ophtroperationnote_ana_type_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

		$this->execute("CREATE TABLE `et_ophtroperationnote_buckle` (
			  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			  `event_id` int(10) unsigned NOT NULL,
			  `drainage_type_id` int(10) unsigned NOT NULL,
			  `drain_haem` tinyint(1) unsigned NOT NULL DEFAULT '0',
			  `deep_suture` tinyint(1) unsigned NOT NULL DEFAULT '0',
			  `eyedraw` varchar(4096)  NOT NULL,
			  `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
			  `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
			  `report` varchar(4096)  NOT NULL,
			  `comments` varchar(1024)  NOT NULL,
			  PRIMARY KEY (`id`),
			  KEY `et_ophtroperationnote_bu_drainage_type_id_fk` (`drainage_type_id`),
			  KEY `et_ophtroperationnote_bu_last_modified_user_id_fk` (`last_modified_user_id`),
			  KEY `et_ophtroperationnote_bu_created_user_id_fk` (`created_user_id`),
			  KEY `et_ophtroperationnote_buckle_eid_fk` (`event_id`),
			  CONSTRAINT `et_ophtroperationnote_buckle_eid_fk` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`),
			  CONSTRAINT `et_ophtroperationnote_bu_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
			  CONSTRAINT `et_ophtroperationnote_bu_drainage_type_id_fk` FOREIGN KEY (`drainage_type_id`) REFERENCES `ophtroperationnote_buckle_drainage_type` (`id`),
			  CONSTRAINT `et_ophtroperationnote_bu_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

		$this->execute("CREATE TABLE `et_ophtroperationnote_cataract` (
			  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			  `event_id` int(10) unsigned NOT NULL,
			  `incision_site_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `length` varchar(5)  NOT NULL DEFAULT '2.8',
			  `meridian` varchar(5)  NOT NULL DEFAULT '180',
			  `incision_type_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `eyedraw` text  NOT NULL,
			  `report` varchar(4096)  NOT NULL DEFAULT 'Continuous Circular Capsulorrhexis\nHydrodissection\nPhakoemulsification of lens nucleus\nAspiration of soft lens matter\nViscoelastic removed',
			  `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
			  `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
			  `iol_position_id` int(10) unsigned DEFAULT '1',
			  `complication_notes` varchar(4096)  DEFAULT NULL,
			  `eyedraw2` varchar(4096)  NOT NULL,
			  `iol_power` varchar(5)  NOT NULL,
			  `iol_type_id` int(10) unsigned DEFAULT NULL,
			  `report2` varchar(4096)  NOT NULL,
			  `predicted_refraction` decimal(4,2) NOT NULL DEFAULT '0.00',
			  PRIMARY KEY (`id`),
			  KEY `et_ophtroperationnote_ca_incision_site_id_fk` (`incision_site_id`),
			  KEY `et_ophtroperationnote_ca_incision_type_id_fk` (`incision_type_id`),
			  KEY `et_ophtroperationnote_ca_last_modified_user_id_fk` (`last_modified_user_id`),
			  KEY `et_ophtroperationnote_ca_created_user_id_fk` (`created_user_id`),
			  KEY `et_ophtroperationnote_cataract_iol_position_fk` (`iol_position_id`),
			  KEY `et_ophtroperationnote_cataract_iol_type_id_fk` (`iol_type_id`),
			  KEY `et_ophtroperationnote_cataract_eid_fk` (`event_id`),
			  CONSTRAINT `et_ophtroperationnote_cataract_eid_fk` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`),
			  CONSTRAINT `et_ophtroperationnote_cataract_iol_position_fk` FOREIGN KEY (`iol_position_id`) REFERENCES `ophtroperationnote_cataract_iol_position` (`id`),
			  CONSTRAINT `et_ophtroperationnote_cataract_iol_type_id_fk` FOREIGN KEY (`iol_type_id`) REFERENCES `ophtroperationnote_cataract_iol_type` (`id`),
			  CONSTRAINT `et_ophtroperationnote_ca_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
			  CONSTRAINT `et_ophtroperationnote_ca_incision_site_id_fk` FOREIGN KEY (`incision_site_id`) REFERENCES `ophtroperationnote_cataract_incision_site` (`id`),
			  CONSTRAINT `et_ophtroperationnote_ca_incision_type_id_fk` FOREIGN KEY (`incision_type_id`) REFERENCES `ophtroperationnote_cataract_incision_type` (`id`),
			  CONSTRAINT `et_ophtroperationnote_ca_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

		$this->execute("CREATE TABLE `et_ophtroperationnote_comments` (
			  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			  `event_id` int(10) unsigned NOT NULL,
			  `comments` varchar(4096)  NOT NULL,
			  `postop_instructions` varchar(4096)  NOT NULL,
			  `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
			  `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
			  PRIMARY KEY (`id`),
			  KEY `et_ophtroperationnote_comments_last_modified_user_id_fk` (`last_modified_user_id`),
			  KEY `et_ophtroperationnote_comments_created_user_id_fk` (`created_user_id`),
			  KEY `et_ophtroperationnote_comments_eid_fk` (`event_id`),
			  CONSTRAINT `et_ophtroperationnote_comments_eid_fk` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`),
			  CONSTRAINT `et_ophtroperationnote_comments_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
			  CONSTRAINT `et_ophtroperationnote_comments_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

		$this->execute("CREATE TABLE `et_ophtroperationnote_genericprocedure` (
			  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			  `event_id` int(10) unsigned NOT NULL,
			  `proc_id` int(10) unsigned NOT NULL,
			  `comments` varchar(4096)  NOT NULL,
			  `element_index` tinyint(1) unsigned NOT NULL DEFAULT '0',
			  `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `last_modified_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
			  `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `created_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
			  PRIMARY KEY (`id`),
			  KEY `et_ophtroperationnote_genericprocedure_event_id_fk` (`event_id`),
			  KEY `et_ophtroperationnote_genericprocedure_proc_id_fk` (`proc_id`),
			  KEY `et_ophtroperationnote_genericprocedure_cui_fk` (`created_user_id`),
			  KEY `et_ophtroperationnote_genericprocedure_lmui_fk` (`last_modified_user_id`),
			  CONSTRAINT `et_ophtroperationnote_genericprocedure_event_id_fk` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`),
			  CONSTRAINT `et_ophtroperationnote_genericprocedure_proc_id_fk` FOREIGN KEY (`proc_id`) REFERENCES `proc` (`id`),
			  CONSTRAINT `et_ophtroperationnote_genericprocedure_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
			  CONSTRAINT `et_ophtroperationnote_genericprocedure_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

		$this->execute("CREATE TABLE `et_ophtroperationnote_membrane_peel` (
			  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			  `event_id` int(10) unsigned NOT NULL,
			  `membrane_blue` tinyint(1) unsigned NOT NULL DEFAULT '0',
			  `brilliant_blue` tinyint(1) unsigned NOT NULL DEFAULT '0',
			  `other_dye` varchar(255)  NOT NULL,
			  `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
			  `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
			  `comments` varchar(1024)  NOT NULL,
			  PRIMARY KEY (`id`),
			  KEY `et_ophtroperationnote_mp_last_modified_user_id_fk` (`last_modified_user_id`),
			  KEY `et_ophtroperationnote_mp_created_user_id_fk` (`created_user_id`),
			  KEY `et_ophtroperationnote_membrane_peel_eid_fk` (`event_id`),
			  CONSTRAINT `et_ophtroperationnote_membrane_peel_eid_fk` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`),
			  CONSTRAINT `et_ophtroperationnote_mp_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
			  CONSTRAINT `et_ophtroperationnote_mp_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

		$this->execute("CREATE TABLE `et_ophtroperationnote_personnel` (
			  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			  `event_id` int(10) unsigned NOT NULL,
			  `scrub_nurse_id` int(10) unsigned NOT NULL,
			  `floor_nurse_id` int(10) unsigned NOT NULL,
			  `accompanying_nurse_id` int(10) unsigned NOT NULL,
			  `operating_department_practitioner_id` int(10) unsigned NOT NULL,
			  `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
			  `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
			  PRIMARY KEY (`id`),
			  KEY `et_ophtroperationnote_p_event_id_fk` (`event_id`),
			  KEY `et_ophtroperationnote_p_scrub_nurse_id_fk` (`scrub_nurse_id`),
			  KEY `et_ophtroperationnote_p_floor_nurse_id_fk` (`floor_nurse_id`),
			  KEY `et_ophtroperationnote_p_accompanying_nurse_id_fk` (`accompanying_nurse_id`),
			  KEY `et_ophtroperationnote_p_operating_department_practitioner_id_fk` (`operating_department_practitioner_id`),
			  KEY `et_ophtroperationnote_p_last_modified_user_id_fk` (`last_modified_user_id`),
			  KEY `et_ophtroperationnote_p_created_user_id_fk` (`created_user_id`),
			  CONSTRAINT `et_ophtroperationnote_p_event_id_fk` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`),
			  CONSTRAINT `et_ophtroperationnote_p_scrub_nurse_id_fk` FOREIGN KEY (`scrub_nurse_id`) REFERENCES `contact` (`id`),
			  CONSTRAINT `et_ophtroperationnote_p_floor_nurse_id_fk` FOREIGN KEY (`floor_nurse_id`) REFERENCES `contact` (`id`),
			  CONSTRAINT `et_ophtroperationnote_p_accompanying_nurse_id_fk` FOREIGN KEY (`accompanying_nurse_id`) REFERENCES `contact` (`id`),
			  CONSTRAINT `et_ophtroperationnote_p_operating_department_practitioner_id_fk` FOREIGN KEY (`operating_department_practitioner_id`) REFERENCES `contact` (`id`),
			  CONSTRAINT `et_ophtroperationnote_p_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
			  CONSTRAINT `et_ophtroperationnote_p_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

		$this->execute("CREATE TABLE `et_ophtroperationnote_postop_drugs` (
			  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			  `event_id` int(10) unsigned NOT NULL,
			  `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
			  `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
			  PRIMARY KEY (`id`),
			  KEY `et_ophtroperationnote_postop_drugs_last_modified_user_id_fk` (`last_modified_user_id`),
			  KEY `et_ophtroperationnote_postop_drugs_created_user_id_fk` (`created_user_id`),
			  KEY `et_ophtroperationnote_postop_drugs_eid_fk` (`event_id`),
			  CONSTRAINT `et_ophtroperationnote_postop_drugs_eid_fk` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`),
			  CONSTRAINT `et_ophtroperationnote_postop_drugs_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
			  CONSTRAINT `et_ophtroperationnote_postop_drugs_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

		$this->execute("CREATE TABLE `et_ophtroperationnote_preparation` (
			  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			  `event_id` int(10) unsigned NOT NULL,
			  `spo2` tinyint(1) unsigned NOT NULL DEFAULT '0',
			  `oxygen` tinyint(1) unsigned NOT NULL DEFAULT '0',
			  `pulse` smallint(2) unsigned NOT NULL DEFAULT '0',
			  `skin_preparation_id` int(10) unsigned NOT NULL,
			  `intraocular_solution_id` int(10) unsigned NOT NULL,
			  `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
			  `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
			  PRIMARY KEY (`id`),
			  KEY `et_ophtroperationnote_preparation_event_id_fk` (`event_id`),
			  KEY `et_ophtroperationnote_preparation_last_modified_user_id_fk` (`last_modified_user_id`),
			  KEY `et_ophtroperationnote_preparation_created_user_id_fk` (`created_user_id`),
			  KEY `et_ophtroperationnote_preparation_skin_preparation_id_fk` (`skin_preparation_id`),
			  KEY `et_ophtroperationnote_preparation_intraocular_solution_id_fk` (`intraocular_solution_id`),
			  CONSTRAINT `et_ophtroperationnote_preparation_event_id_fk` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`),
			  CONSTRAINT `et_ophtroperationnote_preparation_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
			  CONSTRAINT `et_ophtroperationnote_preparation_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
			  CONSTRAINT `et_ophtroperationnote_preparation_skin_preparation_id_fk` FOREIGN KEY (`skin_preparation_id`) REFERENCES `ophtroperationnote_preparation_skin_preparation` (`id`),
			  CONSTRAINT `et_ophtroperationnote_preparation_intraocular_solution_id_fk` FOREIGN KEY (`intraocular_solution_id`) REFERENCES `ophtroperationnote_preparation_intraocular_solution` (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

		$this->execute("CREATE TABLE `et_ophtroperationnote_procedurelist` (
			  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			  `event_id` int(10) unsigned NOT NULL,
			  `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `last_modified_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
			  `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `created_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
			  `eye_id` int(10) unsigned NOT NULL,
			  `booking_event_id` int(10) unsigned DEFAULT NULL,
			  PRIMARY KEY (`id`),
			  UNIQUE KEY `event_id` (`event_id`),
			  KEY `et_ophtroperationnote_procedurelist_last_modified_user_id_fk` (`last_modified_user_id`),
			  KEY `et_ophtroperationnote_procedurelist_created_user_id_fk` (`created_user_id`),
			  KEY `et_ophtroperationnote_procedurelist_eye_id_fk` (`eye_id`),
			  KEY `et_ophtroperationnote_procedurelist_bei_fk` (`booking_event_id`),
			  KEY `et_ophtroperationnote_procedurelist_eid_fk` (`event_id`),
			  CONSTRAINT `et_ophtroperationnote_procedurelist_eid_fk` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`),
			  CONSTRAINT `et_ophtroperationnote_procedurelist_bei_fk` FOREIGN KEY (`booking_event_id`) REFERENCES `event` (`id`),
			  CONSTRAINT `et_ophtroperationnote_procedurelist_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
			  CONSTRAINT `et_ophtroperationnote_procedurelist_eye_id_fk` FOREIGN KEY (`eye_id`) REFERENCES `eye` (`id`),
			  CONSTRAINT `et_ophtroperationnote_procedurelist_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

		$this->execute("CREATE TABLE `et_ophtroperationnote_surgeon` (
			  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			  `event_id` int(10) unsigned NOT NULL,
			  `surgeon_id` int(10) unsigned NOT NULL,
			  `assistant_id` int(10) unsigned DEFAULT NULL,
			  `supervising_surgeon_id` int(10) unsigned DEFAULT NULL,
			  `display_order` tinyint(3) unsigned NOT NULL,
			  `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
			  `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
			  PRIMARY KEY (`id`),
			  KEY `et_ophtroperationnote_sur_type_last_modified_user_id_fk` (`last_modified_user_id`),
			  KEY `et_ophtroperationnote_sur_type_created_user_id_fk` (`created_user_id`),
			  KEY `et_ophtroperationnote_sur_surgeon_id_fk` (`surgeon_id`),
			  KEY `et_ophtroperationnote_surgeon_eid_fk` (`event_id`),
			  CONSTRAINT `et_ophtroperationnote_surgeon_eid_fk` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`),
			  CONSTRAINT `et_ophtroperationnote_sur_surgeon_id_fk` FOREIGN KEY (`surgeon_id`) REFERENCES `user` (`id`),
			  CONSTRAINT `et_ophtroperationnote_sur_type_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
			  CONSTRAINT `et_ophtroperationnote_sur_type_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

		$this->execute("CREATE TABLE `et_ophtroperationnote_tamponade` (
			  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			  `event_id` int(10) unsigned NOT NULL,
			  `gas_type_id` int(10) unsigned NOT NULL,
			  `gas_percentage_id` int(10) unsigned NOT NULL DEFAULT '0',
			  `gas_volume_id` int(10) unsigned DEFAULT '1',
			  `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
			  `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
			  PRIMARY KEY (`id`),
			  KEY `et_ophtroperationnote_tp_gas_type_id_fk` (`gas_type_id`),
			  KEY `et_ophtroperationnote_tp_last_modified_user_id_fk` (`last_modified_user_id`),
			  KEY `et_ophtroperationnote_tp_created_user_id_fk` (`created_user_id`),
			  KEY `et_ophtroperationnote_tamponade_pc_id` (`gas_percentage_id`),
			  KEY `et_ophtroperationnote_tamponade_gv_id` (`gas_volume_id`),
			  KEY `et_ophtroperationnote_tamponade_eid_fk` (`event_id`),
			  CONSTRAINT `et_ophtroperationnote_tamponade_eid_fk` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`),
			  CONSTRAINT `et_ophtroperationnote_tamponade_gv_id` FOREIGN KEY (`gas_volume_id`) REFERENCES `ophtroperationnote_gas_volume` (`id`),
			  CONSTRAINT `et_ophtroperationnote_tamponade_pc_id` FOREIGN KEY (`gas_percentage_id`) REFERENCES `ophtroperationnote_gas_percentage` (`id`),
			  CONSTRAINT `et_ophtroperationnote_tp_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
			  CONSTRAINT `et_ophtroperationnote_tp_gas_type_id_fk` FOREIGN KEY (`gas_type_id`) REFERENCES `ophtroperationnote_gas_type` (`id`),
			  CONSTRAINT `et_ophtroperationnote_tp_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

		$this->execute("CREATE TABLE `et_ophtroperationnote_vitrectomy` (
			  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			  `event_id` int(10) unsigned NOT NULL,
			  `gauge_id` int(10) unsigned NOT NULL,
			  `pvd_induced` tinyint(1) unsigned NOT NULL DEFAULT '0',
			  `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
			  `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
			  `eyedraw` text  NOT NULL,
			  `comments` varchar(1024)  NOT NULL,
			  PRIMARY KEY (`id`),
			  KEY `et_ophtroperationnote_vitrectomy_event_id` (`event_id`),
			  KEY `et_ophtroperationnote_vitrectomy_gauge_id` (`gauge_id`),
			  KEY `et_ophtroperationnote_vit_last_modified_user_id_fk` (`last_modified_user_id`),
			  KEY `et_ophtroperationnote_vit_created_user_id_fk` (`created_user_id`),
			  CONSTRAINT `et_ophtroperationnote_vitrectomy_event_fk` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`),
			  CONSTRAINT `et_ophtroperationnote_vitrectomy_gauge_fk` FOREIGN KEY (`gauge_id`) REFERENCES `ophtroperationnote_gauge` (`id`),
			  CONSTRAINT `et_ophtroperationnote_vit_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
			  CONSTRAINT `et_ophtroperationnote_vit_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

		$this->execute("CREATE TABLE `ophtroperationnote_anaesthetic_anaesthetic_agent` (
			  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			  `anaesthetic_agent_id` int(10) unsigned NOT NULL,
			  `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
			  `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
			  `et_ophtroperationnote_anaesthetic_id` int(10) unsigned NOT NULL,
			  PRIMARY KEY (`id`),
			  KEY `ophtroperationnote_paa_last_modified_user_id_fk` (`last_modified_user_id`),
			  KEY `ophtroperationnote_paa_created_user_id_fk` (`created_user_id`),
			  KEY `ophtroperationnote_paa_anaesthetic_agent_id_fk` (`anaesthetic_agent_id`),
			  KEY `ophtroperationnote_paa_anaesthetic_id_fk` (`et_ophtroperationnote_anaesthetic_id`),
			  CONSTRAINT `ophtroperationnote_paa_anaesthetic_id_fk` FOREIGN KEY (`et_ophtroperationnote_anaesthetic_id`) REFERENCES `et_ophtroperationnote_anaesthetic` (`id`),
			  CONSTRAINT `ophtroperationnote_paa_anaesthetic_agent_id_fk` FOREIGN KEY (`anaesthetic_agent_id`) REFERENCES `anaesthetic_agent` (`id`),
			  CONSTRAINT `ophtroperationnote_paa_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
			  CONSTRAINT `ophtroperationnote_paa_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

		$this->execute("CREATE TABLE `ophtroperationnote_anaesthetic_anaesthetic_complication` (
			  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			  `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
			  `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
			  `et_ophtroperationnote_anaesthetic_id` int(10) unsigned NOT NULL,
			  `anaesthetic_complication_id` int(10) unsigned NOT NULL,
			  PRIMARY KEY (`id`),
			  KEY `ophtroperationnote_pac_last_modified_user_id_fk` (`last_modified_user_id`),
			  KEY `ophtroperationnote_pac_created_user_id_fk` (`created_user_id`),
			  KEY `ophtroperationnote_anaesthetic_ac_anaesthetic_id_fk` (`et_ophtroperationnote_anaesthetic_id`),
			  KEY `ophtroperationnote_anaesthetic_aca_complication_id_fk` (`anaesthetic_complication_id`),
			  CONSTRAINT `ophtroperationnote_anaesthetic_aca_complication_id_fk` FOREIGN KEY (`anaesthetic_complication_id`) REFERENCES `ophtroperationnote_anaesthetic_anaesthetic_complications` (`id`),
			  CONSTRAINT `ophtroperationnote_anaesthetic_ac_anaesthetic_id_fk` FOREIGN KEY (`et_ophtroperationnote_anaesthetic_id`) REFERENCES `et_ophtroperationnote_anaesthetic` (`id`),
			  CONSTRAINT `ophtroperationnote_pac_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
			  CONSTRAINT `ophtroperationnote_pac_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

		$this->execute("CREATE TABLE `ophtroperationnote_anaesthetic_anaesthetic_complications` (
			  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			  `name` varchar(64)  NOT NULL,
			  `display_order` tinyint(3) unsigned NOT NULL,
			  `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
			  `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
			  PRIMARY KEY (`id`),
			  KEY `ophtroperationnote_anaesthetic_ac_last_modified_user_id_fk` (`last_modified_user_id`),
			  KEY `ophtroperationnote_anaesthetic_ac_created_user_id_fk` (`created_user_id`),
			  CONSTRAINT `ophtroperationnote_anaesthetic_ac_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
			  CONSTRAINT `ophtroperationnote_anaesthetic_ac_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

		$this->execute("CREATE TABLE `ophtroperationnote_buckle_drainage_type` (
			  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			  `name` varchar(16)  NOT NULL,
			  `display_order` tinyint(3) unsigned NOT NULL,
			  `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
			  `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
			  PRIMARY KEY (`id`),
			  KEY `ophtroperationnote_bdt_last_modified_user_id_fk` (`last_modified_user_id`),
			  KEY `ophtroperationnote_bdt_created_user_id_fk` (`created_user_id`),
			  CONSTRAINT `ophtroperationnote_bdt_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
			  CONSTRAINT `ophtroperationnote_bdt_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

		$this->execute("CREATE TABLE `ophtroperationnote_cataract_complication` (
			  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			  `cataract_id` int(10) unsigned NOT NULL,
			  `complication_id` int(10) unsigned NOT NULL,
			  `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
			  `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
			  PRIMARY KEY (`id`),
			  KEY `ophtroperationnote_cc2_cataract_id_fk` (`cataract_id`),
			  KEY `ophtroperationnote_cc2_complication_id_fk` (`complication_id`),
			  KEY `ophtroperationnote_cc2_last_modified_user_id_fk` (`last_modified_user_id`),
			  KEY `ophtroperationnote_cc2_created_user_id_fk` (`created_user_id`),
			  CONSTRAINT `ophtroperationnote_cc2_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
			  CONSTRAINT `ophtroperationnote_cc2_cataract_id_fk` FOREIGN KEY (`cataract_id`) REFERENCES `et_ophtroperationnote_cataract` (`id`),
			  CONSTRAINT `ophtroperationnote_cc2_complication_id_fk` FOREIGN KEY (`complication_id`) REFERENCES `ophtroperationnote_cataract_complications` (`id`),
			  CONSTRAINT `ophtroperationnote_cc2_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

		$this->execute("CREATE TABLE `ophtroperationnote_cataract_complications` (
			  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			  `name` varchar(64)  NOT NULL,
			  `display_order` tinyint(3) unsigned NOT NULL,
			  `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
			  `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
			  PRIMARY KEY (`id`),
			  KEY `ophtroperationnote_cc_last_modified_user_id_fk` (`last_modified_user_id`),
			  KEY `ophtroperationnote_cc_created_user_id_fk` (`created_user_id`),
			  CONSTRAINT `ophtroperationnote_cc_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
			  CONSTRAINT `ophtroperationnote_cc_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

		$this->execute("CREATE TABLE `ophtroperationnote_cataract_incision_site` (
			  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			  `name` varchar(16)  NOT NULL,
			  `display_order` tinyint(3) unsigned NOT NULL,
			  `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
			  `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
			  PRIMARY KEY (`id`),
			  KEY `ophtroperationnote_cis_last_modified_user_id_fk` (`last_modified_user_id`),
			  KEY `ophtroperationnote_cis_created_user_id_fk` (`created_user_id`),
			  CONSTRAINT `ophtroperationnote_cis_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
			  CONSTRAINT `ophtroperationnote_cis_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

		$this->execute("CREATE TABLE `ophtroperationnote_cataract_incision_type` (
			  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			  `name` varchar(16)  NOT NULL,
			  `display_order` tinyint(3) unsigned NOT NULL,
			  `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
			  `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
			  PRIMARY KEY (`id`),
			  KEY `ophtroperationnote_cit_last_modified_user_id_fk` (`last_modified_user_id`),
			  KEY `ophtroperationnote_cit_created_user_id_fk` (`created_user_id`),
			  CONSTRAINT `ophtroperationnote_cit_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
			  CONSTRAINT `ophtroperationnote_cit_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

		$this->execute("CREATE TABLE `ophtroperationnote_cataract_iol_position` (
			  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			  `name` varchar(32)  NOT NULL,
			  `display_order` tinyint(3) unsigned NOT NULL,
			  `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
			  `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
			  PRIMARY KEY (`id`),
			  KEY `ophtroperationnote_cip_last_modified_user_id_fk` (`last_modified_user_id`),
			  KEY `ophtroperationnote_cip_created_user_id_fk` (`created_user_id`),
			  CONSTRAINT `ophtroperationnote_cip_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
			  CONSTRAINT `ophtroperationnote_cip_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

		$this->execute("CREATE TABLE `ophtroperationnote_cataract_iol_type` (
			  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			  `name` varchar(64)  NOT NULL,
			  `display_order` tinyint(3) unsigned NOT NULL DEFAULT '1',
			  `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
			  `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
			  `private` tinyint(1) NOT NULL DEFAULT '0',
			  PRIMARY KEY (`id`),
			  KEY `ophtroperationnote_cot_last_modified_user_id_fk` (`last_modified_user_id`),
			  KEY `ophtroperationnote_cot_created_user_id_fk` (`created_user_id`),
			  CONSTRAINT `ophtroperationnote_cot_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
			  CONSTRAINT `ophtroperationnote_cot_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

		$this->execute("CREATE TABLE `ophtroperationnote_cataract_operative_device` (
			  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			  `cataract_id` int(10) unsigned NOT NULL,
			  `operative_device_id` int(10) unsigned NOT NULL,
			  `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
			  `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
			  PRIMARY KEY (`id`),
			  KEY `ophtroperationnote_ccd_cataract_id_fk` (`cataract_id`),
			  KEY `ophtroperationnote_ccd_operative_device_id_fk` (`operative_device_id`),
			  KEY `ophtroperationnote_ccd_last_modified_user_id_fk` (`last_modified_user_id`),
			  KEY `ophtroperationnote_ccd_created_user_id_fk` (`created_user_id`),
			  CONSTRAINT `ophtroperationnote_ccd_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
			  CONSTRAINT `ophtroperationnote_ccd_cataract_id_fk` FOREIGN KEY (`cataract_id`) REFERENCES `et_ophtroperationnote_cataract` (`id`),
			  CONSTRAINT `ophtroperationnote_ccd_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
			  CONSTRAINT `ophtroperationnote_ccd_operative_device_id_fk` FOREIGN KEY (`operative_device_id`) REFERENCES `operative_device` (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

		$this->execute("CREATE TABLE `ophtroperationnote_gas_percentage` (
			  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			  `value` tinyint(1) unsigned NOT NULL DEFAULT '0',
			  `display_order` tinyint(3) unsigned NOT NULL,
			  `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
			  `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
			  PRIMARY KEY (`id`),
			  KEY `ophtroperationnote_gas_pc_last_modified_user_id_fk` (`last_modified_user_id`),
			  KEY `ophtroperationnote_gas_pc_created_user_id_fk` (`created_user_id`),
			  CONSTRAINT `ophtroperationnote_gas_pc_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
			  CONSTRAINT `ophtroperationnote_gas_pc_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

		$this->execute("CREATE TABLE `ophtroperationnote_gas_type` (
			  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			  `name` varchar(5)  NOT NULL,
			  `display_order` tinyint(3) unsigned NOT NULL,
			  `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
			  `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
			  PRIMARY KEY (`id`),
			  KEY `ophtroperationnote_gas_type_last_modified_user_id_fk` (`last_modified_user_id`),
			  KEY `ophtroperationnote_gas_type_created_user_id_fk` (`created_user_id`),
			  CONSTRAINT `ophtroperationnote_gas_type_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
			  CONSTRAINT `ophtroperationnote_gas_type_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
			) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

		$this->execute("CREATE TABLE `ophtroperationnote_gas_volume` (
			  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			  `value` varchar(3)  NOT NULL,
			  `display_order` tinyint(3) unsigned NOT NULL,
			  `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
			  `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
			  PRIMARY KEY (`id`),
			  KEY `ophtroperationnote_gas_vol_last_modified_user_id_fk` (`last_modified_user_id`),
			  KEY `ophtroperationnote_gas_vol_created_user_id_fk` (`created_user_id`),
			  CONSTRAINT `ophtroperationnote_gas_vol_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
			  CONSTRAINT `ophtroperationnote_gas_vol_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

		$this->execute("CREATE TABLE `ophtroperationnote_gauge` (
			  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			  `value` varchar(5)  NOT NULL,
			  `display_order` tinyint(3) unsigned NOT NULL,
			  `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
			  `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
			  PRIMARY KEY (`id`),
			  KEY `ophtroperationnote_gauge_last_modified_user_id_fk` (`last_modified_user_id`),
			  KEY `ophtroperationnote_gauge_created_user_id_fk` (`created_user_id`),
			  CONSTRAINT `ophtroperationnote_gauge_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
			  CONSTRAINT `ophtroperationnote_gauge_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

		$this->execute("CREATE TABLE `ophtroperationnote_postop_drug` (
			  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			  `name` varchar(255)  NOT NULL,
			  `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
			  `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
			  `deleted` tinyint(1) unsigned NOT NULL DEFAULT '0',
			  `display_order` int(10) unsigned NOT NULL DEFAULT '0',
			  PRIMARY KEY (`id`),
			  KEY `ophtroperationnote_postop_drug_l_m_u_id_fk` (`last_modified_user_id`),
			  KEY `ophtroperationnote_postop_drug_c_u_id_fk` (`created_user_id`),
			  CONSTRAINT `ophtroperationnote_postop_drug_c_u_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
			  CONSTRAINT `ophtroperationnote_postop_drug_l_m_u_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

		$this->execute("CREATE TABLE `ophtroperationnote_postop_drugs_drug` (
			  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			  `ophtroperationnote_postop_drugs_id` int(10) unsigned NOT NULL,
			  `drug_id` int(10) unsigned NOT NULL,
			  `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
			  `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
			  PRIMARY KEY (`id`),
			  KEY `ophtroperationnote_pdd_created_user_id_fk` (`created_user_id`),
			  KEY `ophtroperationnote_pdd_last_modified_user_id_fk` (`last_modified_user_id`),
			  KEY `ophtroperationnote_pdd_drug_id_fk` (`drug_id`),
			  KEY `ophtroperationnote_pdd_drugs_id_fk` (`ophtroperationnote_postop_drugs_id`),
			  CONSTRAINT `ophtroperationnote_pdd_drugs_id_fk` FOREIGN KEY (`ophtroperationnote_postop_drugs_id`) REFERENCES `et_ophtroperationnote_postop_drugs` (`id`),
			  CONSTRAINT `ophtroperationnote_pdd_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
			  CONSTRAINT `ophtroperationnote_pdd_drug_id_fk` FOREIGN KEY (`drug_id`) REFERENCES `ophtroperationnote_postop_drug` (`id`),
			  CONSTRAINT `ophtroperationnote_pdd_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

		$this->execute("CREATE TABLE `ophtroperationnote_postop_site_subspecialty_drug` (
			  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			  `site_id` int(10) unsigned NOT NULL,
			  `subspecialty_id` int(10) unsigned NOT NULL,
			  `drug_id` int(10) unsigned NOT NULL,
			  `display_order` tinyint(3) unsigned NOT NULL,
			  `default` tinyint(1) unsigned NOT NULL DEFAULT '0',
			  `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
			  `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
			  PRIMARY KEY (`id`),
			  KEY `ophtroperationnote_postop_site_subspecialty_drug_l_m_u_id_fk` (`last_modified_user_id`),
			  KEY `ophtroperationnote_postop_site_subspecialty_drug_c_u_id_fk` (`created_user_id`),
			  KEY `ophtroperationnote_postop_site_subspecialty_drug_site_id_fk` (`site_id`),
			  KEY `ophtroperationnote_postop_site_subspecialty_drug_s_id_fk` (`subspecialty_id`),
			  KEY `ophtroperationnote_postop_site_subspecialty_drug_drug_id_fk` (`drug_id`),
			  CONSTRAINT `ophtroperationnote_postop_site_subspecialty_drug_drug_id_fk` FOREIGN KEY (`drug_id`) REFERENCES `ophtroperationnote_postop_drug` (`id`),
			  CONSTRAINT `ophtroperationnote_postop_site_subspecialty_drug_c_u_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
			  CONSTRAINT `ophtroperationnote_postop_site_subspecialty_drug_l_m_u_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
			  CONSTRAINT `ophtroperationnote_postop_site_subspecialty_drug_site_id_fk` FOREIGN KEY (`site_id`) REFERENCES `site` (`id`),
			  CONSTRAINT `ophtroperationnote_postop_site_subspecialty_drug_s_id_fk` FOREIGN KEY (`subspecialty_id`) REFERENCES `subspecialty` (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

		$this->execute("CREATE TABLE `ophtroperationnote_preparation_intraocular_solution` (
			  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			  `name` varchar(128)  DEFAULT NULL,
			  `display_order` tinyint(3) unsigned DEFAULT '0',
			  `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
			  `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
			  PRIMARY KEY (`id`),
			  KEY `ophtroperationnote_pis_last_modified_user_id_fk` (`last_modified_user_id`),
			  KEY `ophtroperationnote_pis_created_user_id_fk` (`created_user_id`),
			  CONSTRAINT `ophtroperationnote_pis_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
			  CONSTRAINT `ophtroperationnote_pis_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

		$this->execute("CREATE TABLE `ophtroperationnote_preparation_skin_preparation` (
			  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			  `name` varchar(128)  DEFAULT NULL,
			  `display_order` tinyint(3) unsigned DEFAULT '0',
			  `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
			  `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
			  PRIMARY KEY (`id`),
			  KEY `ophtroperationnote_psp_last_modified_user_id_fk` (`last_modified_user_id`),
			  KEY `ophtroperationnote_psp_created_user_id_fk` (`created_user_id`),
			  CONSTRAINT `ophtroperationnote_psp_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
			  CONSTRAINT `ophtroperationnote_psp_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

		$this->execute("CREATE TABLE `ophtroperationnote_procedure_element` (
			  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			  `procedure_id` int(10) unsigned NOT NULL,
			  `element_type_id` int(10) unsigned NOT NULL,
			  `display_order` tinyint(3) unsigned NOT NULL DEFAULT '1',
			  `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
			  `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
			  PRIMARY KEY (`id`),
			  KEY `ophtroperationnote_pe_procedure_fk` (`procedure_id`),
			  KEY `ophtroperationnote_pe_element_type_fk` (`element_type_id`),
			  KEY `ophtroperationnote_pe_created_user_id_fk` (`created_user_id`),
			  KEY `ophtroperationnote_pe_last_modified_user_id_fk` (`last_modified_user_id`),
			  CONSTRAINT `ophtroperationnote_pe_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
			  CONSTRAINT `ophtroperationnote_pe_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
			  CONSTRAINT `ophtroperationnote_pe_element_type_fk` FOREIGN KEY (`element_type_id`) REFERENCES `element_type` (`id`),
			  CONSTRAINT `ophtroperationnote_pe_procedure_fk` FOREIGN KEY (`procedure_id`) REFERENCES `proc` (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

		$this->execute("CREATE TABLE `ophtroperationnote_procedurelist_procedure_assignment` (
			  `procedurelist_id` int(10) unsigned NOT NULL,
			  `proc_id` int(10) unsigned NOT NULL,
			  `display_order` tinyint(3) unsigned DEFAULT '0',
			  `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
			  `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
			  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			  PRIMARY KEY (`id`),
			  KEY `procedurelist_id` (`procedurelist_id`),
			  KEY `procedure_id` (`proc_id`),
			  KEY `ophtroperationnote_plpa_last_modified_user_id_fk` (`last_modified_user_id`),
			  KEY `ophtroperationnote_plpa_created_user_id_fk` (`created_user_id`),
			  KEY `procedurelist_procid_key` (`procedurelist_id`,`proc_id`),
			  CONSTRAINT `et_ophtroperationnote_plpa_proclist_fk` FOREIGN KEY (`procedurelist_id`) REFERENCES `et_ophtroperationnote_procedurelist` (`id`),
			  CONSTRAINT `ophtroperationnote_plpa_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
			  CONSTRAINT `ophtroperationnote_plpa_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
			  CONSTRAINT `ophtroperationnote_procedurelist_procedure_assignment_ibfk_1` FOREIGN KEY (`proc_id`) REFERENCES `proc` (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

		$this->execute("CREATE TABLE `ophtroperationnote_site_subspecialty_postop_instructions` (
			  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			  `site_id` int(10) unsigned NOT NULL,
			  `subspecialty_id` int(10) unsigned NOT NULL,
			  `content` varchar(1024)  NOT NULL,
			  `display_order` tinyint(3) unsigned NOT NULL DEFAULT '1',
			  `last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
			  `created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
			  `created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
			  PRIMARY KEY (`id`),
			  KEY `ophtroperationnote_sspi_site_id_fk` (`site_id`),
			  KEY `ophtroperationnote_sspi_subspecialty_id_fk` (`subspecialty_id`),
			  KEY `ophtroperationnote_sspi_last_modified_user_id_fk` (`last_modified_user_id`),
			  KEY `ophtroperationnote_sspi_created_user_id_fk` (`created_user_id`),
			  CONSTRAINT `ophtroperationnote_sspi_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
			  CONSTRAINT `ophtroperationnote_sspi_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
			  CONSTRAINT `ophtroperationnote_sspi_site_id_fk` FOREIGN KEY (`site_id`) REFERENCES `site` (`id`),
			  CONSTRAINT `ophtroperationnote_sspi_subspecialty_id_fk` FOREIGN KEY (`subspecialty_id`) REFERENCES `subspecialty` (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

		$migrations_path = dirname(__FILE__);
		$this->initialiseData($migrations_path);

		//enable foreign keys check
		$this->execute("SET foreign_key_checks = 1");

	}

	/**
	 * Out of order migration missing from previous release
	 */
	protected function m130722_115732_remove_unnecessary_element_tables()
	{
		$opnote = $this->dbConnection->createCommand()->select("*")->from("event_type")->where("class_name=:class_name",array(':class_name'=>'OphTrOperationnote'))->queryRow();

		$this->insert('element_type',array(
				'class_name' => 'Element_OphTrOperationnote_GenericProcedure',
				'name' => 'Generic procedure',
				'event_type_id' => $opnote['id'],
				'display_order' => 20,
				'default' => 0,
			));

		$this->createTable('et_ophtroperationnote_genericprocedure', array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'event_id' => 'int(10) unsigned NOT NULL',
				'proc_id' => 'int(10) unsigned NOT NULL',
				'comments' => 'varchar(4096) NOT NULL',
				'element_index' => 'tinyint(1) unsigned NOT NULL DEFAULT 0',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
				'created_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'KEY `et_ophtroperationnote_genericprocedure_event_id_fk` (`event_id`)',
				'KEY `et_ophtroperationnote_genericprocedure_proc_id_fk` (`proc_id`)',
				'KEY `et_ophtroperationnote_genericprocedure_cui_fk` (`created_user_id`)',
				'KEY `et_ophtroperationnote_genericprocedure_lmui_fk` (`last_modified_user_id`)',
				'CONSTRAINT `et_ophtroperationnote_genericprocedure_event_id_fk` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`)',
				'CONSTRAINT `et_ophtroperationnote_genericprocedure_proc_id_fk` FOREIGN KEY (`proc_id`) REFERENCES `proc` (`id`)',
				'CONSTRAINT `et_ophtroperationnote_genericprocedure_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `et_ophtroperationnote_genericprocedure_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
			), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci');

		foreach (array(
					 'et_ophtroperationnote_3_snp' => 'ElementPunctoplasty',
					 'et_ophtroperationnote_5fu' => 'ElementApplicationOf5FU',
					 'et_ophtroperationnote_absc_drng' => 'ElementDrainageOfEyelidAbscess',
					 'et_ophtroperationnote_adjust_corneal_suture' => 'ElementAdjustmentToCornealSuture',
					 'et_ophtroperationnote_adjustable' => 'ElementAdjustableSuture',
					 'et_ophtroperationnote_adjustsuture' => 'ElementCornealSutureAdjustment',
					 'et_ophtroperationnote_al_implnt' => 'ElementAllogenicImplant',
					 'et_ophtroperationnote_alt' => 'ElementLaserTrabeculoplasty',
					 'et_ophtroperationnote_amniotigrft' => 'ElementAmnioticMembraneGraft',
					 'et_ophtroperationnote_ant_capsulotomy' => 'ElementAnteriorCapsulotomy',
					 'et_ophtroperationnote_ant_lam_keratop' => 'ElementKeratoplastyAnteriorLamellar',
					 'et_ophtroperationnote_ant_orb_conj' => 'ElementAnteriorOrbitotomyConjunctivalApproach',
					 'et_ophtroperationnote_ant_orb_ll' => 'ElementAnteriorOrbitotomyLowerLidApproach',
					 'et_ophtroperationnote_ant_orb_ul' => 'ElementAnteriorOrbitotomyUpperLidApproach',
					 'et_ophtroperationnote_ant_vity' => 'ElementAnteriorVitrectomy',
					 'et_ophtroperationnote_anterior_synechiolysis' => 'ElementAnteriorSynechiolysis',
					 'et_ophtroperationnote_astig_keratotom' => 'ElementAstigmaticKeratotomy',
					 'et_ophtroperationnote_aur_cart' => 'ElementEarCartilageGraft',
					 'et_ophtroperationnote_b_scan_ultrasound' => 'ElementBScanUltrasound',
					 'et_ophtroperationnote_bandage' => 'ElementBandageContactLens',
					 'et_ophtroperationnote_beta_irradiation' => 'ElementApplicationOfBetaRadation',
					 'et_ophtroperationnote_biopsy_buccal_mucosa' => 'ElementBiopsyOfBuccalMucosa',
					 'et_ophtroperationnote_biopsy_iris' => 'ElementBiopsyOfIris',
					 'et_ophtroperationnote_bleph_both_lids' => 'ElementBlepharoplastyOfBothLids',
					 'et_ophtroperationnote_bleph_lower_lid' => 'ElementBlepharoplastyOfLowerLid',
					 'et_ophtroperationnote_bleph_upper_lid' => 'ElementBlepharoplastyOfUpperLid',
					 'et_ophtroperationnote_blphmosis_canth' => 'ElementBlepharophimosisCanthalSurgery',
					 'et_ophtroperationnote_botox_inj' => 'ElementBotulinumToxinInjectionToEyelid',
					 'et_ophtroperationnote_brow_lift__direct' => 'ElementBrowLiftDirect',
					 'et_ophtroperationnote_brow_lift__indirect' => 'ElementBrowLiftInternal',
					 'et_ophtroperationnote_brow_susp_afl' => 'ElementBrowSuspensionWithFasciaLata',
					 'et_ophtroperationnote_brow_susp_synth' => 'ElementBrowSuspensionWithSyntheticMaterial',
					 'et_ophtroperationnote_btxamuscle' => 'ElementBotulinumInjectionEyeMuscle',
					 'et_ophtroperationnote_bx_cnj' => 'ElementBiopsyOfConjunctivaIncisional',
					 'et_ophtroperationnote_bx_exc_cnj' => 'ElementBiopsyOfConjunctivaExcisional',
					 'et_ophtroperationnote_bx_exc_ld' => 'ElementBiopsyOfLidExcisional',
					 'et_ophtroperationnote_bx_lid' => 'ElementBiopsyOfLidIncisional',
					 'et_ophtroperationnote_canaliculodcr' => 'ElementDacrocystorhinostomyRetrotubes',
					 'et_ophtroperationnote_canltmy' => 'ElementCalaniculotomyForCanaliculitis',
					 'et_ophtroperationnote_capsulectomy' => 'ElementCapsulectomy',
					 'et_ophtroperationnote_capsulotomypost' => 'ElementCapsulotomySurgical',
					 'et_ophtroperationnote_chk_lft' => 'ElementCheekLift',
					 'et_ophtroperationnote_chor_biopsy' => 'ElementBiopsyOfChoroid',
					 'et_ophtroperationnote_closure_cornea' => 'ElementCornealWoundSuture',
					 'et_ophtroperationnote_compression_sut' => 'ElementCompressionSutureOfGraft',
					 'et_ophtroperationnote_conj_inject' => 'ElementSubconjunctivalInjection',
					 'et_ophtroperationnote_conjunctival_swab' => 'ElementConjunctivalSwab',
					 'et_ophtroperationnote_corn_sut_removal' => 'ElementRemovalOfCornealSuture',
					 'et_ophtroperationnote_corndiath' => 'ElementCornealVesselDiathermy',
					 'et_ophtroperationnote_corneal_biopsy' => 'ElementBiopsyOfCornea',
					 'et_ophtroperationnote_corneal_fb' => 'ElementRemovalOfCornealForeignBody',
					 'et_ophtroperationnote_corneal_graft' => 'ElementKeratoplastyPenetrating',
					 'et_ophtroperationnote_corneal_suture' => 'ElementSutureOfCornea',
					 'et_ophtroperationnote_corr_anmly' => 'ElementCongenitalAnomalyCorrection',
					 'et_ophtroperationnote_creatn_conjhood' => 'ElementConjunctivalFlap',
					 'et_ophtroperationnote_crosslinking' => 'ElementCrosslinkingOfCornea',
					 'et_ophtroperationnote_cryo' => 'ElementCryotherapyToLesionOfRetina',
					 'et_ophtroperationnote_cryo2' => 'ElementCryotherapyRetinopexy',
					 'et_ophtroperationnote_cryo_collin' => 'ElementCryotherapyWithCollinCryoprobe',
					 'et_ophtroperationnote_cryo_nitro' => 'ElementCryotherapyWithLiquidNitrogen',
					 'et_ophtroperationnote_cyclocryo' => 'ElementCryotherapyOfCiliaryBody',
					 'et_ophtroperationnote_cyclodiaclftrep' => 'ElementCyclodialysisCleftRepair',
					 'et_ophtroperationnote_cyclodiode' => 'ElementLaserCoagulationCiliaryBody',
					 'et_ophtroperationnote_dacrocystogram' => 'ElementDacrocystogram',
					 'et_ophtroperationnote_dcr' => 'ElementDacrocystorhinostomy',
					 'et_ophtroperationnote_dcr_endo' => 'ElementDacrocystorhinostomyEndonasal',
					 'et_ophtroperationnote_dctmy' => 'ElementDacrocystectomy',
					 'et_ophtroperationnote_deblk_ft' => 'ElementOrbitalFatProlapseTransConjunctivalReduction',
					 'et_ophtroperationnote_debride' => 'ElementCornealDebridement',
					 'et_ophtroperationnote_decmp_2_balanced' => 'ElementDecompressionOfOrbit2WallsBalancedApproach',
					 'et_ophtroperationnote_decmp_lat' => 'ElementDecompressionOfOrbitLateralWall',
					 'et_ophtroperationnote_decmp_med_only' => 'ElementDecompressionOfOrbitMedialWallForNeuropathy',
					 'et_ophtroperationnote_decomp_3' => 'ElementDecompressionOfOrbit3Walls',
					 'et_ophtroperationnote_delam' => 'ElementDelamination',
					 'et_ophtroperationnote_dfg' => 'ElementDermisFatGraft',
					 'et_ophtroperationnote_dfgsock' => 'ElementDermisFatGraftToSocket',
					 'et_ophtroperationnote_dmek' => 'ElementKeratoplastyPosteriorDSAEK',
					 'et_ophtroperationnote_donorsclera' => 'ElementGraftToSclera',
					 'et_ophtroperationnote_drain' => 'ElementExternalDrainageOfSRF',
					 'et_ophtroperationnote_drain_supra' => 'ElementDrainSupra',
					 'et_ophtroperationnote_drmlpm' => 'ElementDermolipomaExcisionMicroscope',
					 'et_ophtroperationnote_dsaek' => 'ElementKeratoplastyPosteriorDMEK',
					 'et_ophtroperationnote_dsaek_reposition' => 'ElementDSAEKRepositioning',
					 'et_ophtroperationnote_ecce' => 'ElementExtracapsularCataractExtraction',
					 'et_ophtroperationnote_ectr' => 'ElementEctropionCorrection',
					 'et_ophtroperationnote_ectr_med' => 'ElementEctropionMedialOnlyCorrection',
					 'et_ophtroperationnote_edta_chelation' => 'ElementChelationOfCornea',
					 'et_ophtroperationnote_elctrlys' => 'ElementElectrolysisOfEyelash',
					 'et_ophtroperationnote_endo_rev_dcr' => 'ElementEndonasalRevisionOfDCR',
					 'et_ophtroperationnote_enscpy' => 'ElementEndoscopy',
					 'et_ophtroperationnote_ent' => 'ElementEntropionCorrectionNoGraft',
					 'et_ophtroperationnote_ent_lower_lid' => 'ElementEntropionCorrectionOfLowerEyelid',
					 'et_ophtroperationnote_ent_upper_lid' => 'ElementEntropionCorrectionOfUpperEyelid',
					 'et_ophtroperationnote_enuc__impnlt' => 'ElementEnucleationAndImplant',
					 'et_ophtroperationnote_enuc_no_implnt' => 'ElementEnucleationNoImplant',
					 'et_ophtroperationnote_epikeratoplasty' => 'ElementEpikeratoplasty',
					 'et_ophtroperationnote_eua' => 'ElementExaminationUnderAnaesthesia',
					 'et_ophtroperationnote_evisc__ball' => 'ElementEviscerationAndImplant',
					 'et_ophtroperationnote_evisc_no_ball' => 'ElementEviscerationNoImplant',
					 'et_ophtroperationnote_ex_lid_lsn' => 'ElementExcisionOfLidLesionNoBiopsy',
					 'et_ophtroperationnote_exc_papilloma' => 'ElementExcisionOfPapilloma',
					 'et_ophtroperationnote_excbxpinguecula' => 'ElementPingueculumExcision',
					 'et_ophtroperationnote_excisconjlesion' => 'ElementExcisionOfLesionOfConjunctiva',
					 'et_ophtroperationnote_excision_cathal_lesion' => 'ElementExcisionOfLesionOfCanthus',
					 'et_ophtroperationnote_excision_eyebrow_lesion' => 'ElementExcisionOfLesionOfEyebrow',
					 'et_ophtroperationnote_excision_of_corneal_lesion' => 'ElementExcisionOfLesionOfCornea',
					 'et_ophtroperationnote_excision_of_lacrimal_gland' => 'ElementExcisionOfLacrimalGland',
					 'et_ophtroperationnote_exent' => 'ElementExenteration',
					 'et_ophtroperationnote_expteryconjaugf' => 'ElementPterygiumExcisionConjAutogrft',
					 'et_ophtroperationnote_eyelid_laceration_full_thickness' => 'ElementEyelidLacerationFullThickness',
					 'et_ophtroperationnote_eyelid_laceration_partial_thickness' => 'ElementEyelidLacerationPartialThickness',
					 'et_ophtroperationnote_fl_harvest' => 'ElementFasciaLataHarvest',
					 'et_ophtroperationnote_fluorescein' => 'ElementFluorescein',
					 'et_ophtroperationnote_fornix_mmg' => 'ElementFornixReconstructionWithMucusMembraneGraft',
					 'et_ophtroperationnote_frag' => 'ElementFragmatomeLensectomy',
					 'et_ophtroperationnote_frnx_recon' => 'ElementReconstructionOfEyeSocketWithMMG',
					 'et_ophtroperationnote_fsak' => 'ElementFemtosecondAstigmaticKeratotomy',
					 'et_ophtroperationnote_ftsg' => 'ElementFullThicknessSkinGraft',
					 'et_ophtroperationnote_gld_wt' => 'ElementInsertionOfGoldWeight',
					 'et_ophtroperationnote_glue' => 'ElementCornealGlue',
					 'et_ophtroperationnote_goniotomy' => 'ElementGoniotomy',
					 'et_ophtroperationnote_graft_tectonic' => 'ElementKeratoplastyTectonic',
					 'et_ophtroperationnote_graft_to_sclera' => 'ElementScleralGraft',
					 'et_ophtroperationnote_haradiito' => 'ElementSuperiorObliqueHaradaIto',
					 'et_ophtroperationnote_hpg' => 'ElementHardpalateGraft',
					 'et_ophtroperationnote_ic' => 'ElementIncisionAndCurettageOfCyst',
					 'et_ophtroperationnote_icce' => 'ElementICCE',
					 'et_ophtroperationnote_ilm' => 'ElementInternalLimitingMembranePeel',
					 'et_ophtroperationnote_indocyanine_ga' => 'ElementICGAngiogram',
					 'et_ophtroperationnote_inj_ac' => 'ElementInjectionOfAnteriorChamberOfEye',
					 'et_ophtroperationnote_inj_eye' => 'ElementInjectionIntoEye',
					 'et_ophtroperationnote_inj_lid' => 'ElementInjectionIntoEyelid',
					 'et_ophtroperationnote_inlay_insert' => 'ElementCornealInlayInsertion',
					 'et_ophtroperationnote_inlay_removal' => 'ElementCornealInlayRemoval',
					 'et_ophtroperationnote_insaqueousshunt' => 'ElementInsertionOfAqueousShunt',
					 'et_ophtroperationnote_insertion_slow_release' => 'ElementInsertionSlowRelease',
					 'et_ophtroperationnote_intacs_' => 'ElementInsertionOfIntacs',
					 'et_ophtroperationnote_intralasik' => 'ElementIntraLASIKEyeSurgery',
					 'et_ophtroperationnote_intrastromal' => 'ElementIntrastromalCornealInjection',
					 'et_ophtroperationnote_intravit' => 'ElementIntravitrealInjection',
					 'et_ophtroperationnote_io_' => 'ElementInferiorObliqueDisinsertion',
					 'et_ophtroperationnote_io_ant_trans' => 'ElementInferiorObliqueAnteriorTransposition',
					 'et_ophtroperationnote_io_faden' => 'ElementInferiorObliqueFaden',
					 'et_ophtroperationnote_iofb' => 'ElementRemovalOfIntraocularForeignBody',
					 'et_ophtroperationnote_ir_faden' => 'ElementInferiorRectusFaden',
					 'et_ophtroperationnote_ir_recess' => 'ElementInferiorRectusRecession',
					 'et_ophtroperationnote_ir_resect' => 'ElementInferiorRectusResection',
					 'et_ophtroperationnote_ir_transposition' => 'ElementInferiorRectusHorizontalTransposition',
					 'et_ophtroperationnote_iridoplasty' => 'ElementIridoplasty',
					 'et_ophtroperationnote_irrigatn_ac' => 'ElementIrrigationOfAnteriorChamber',
					 'et_ophtroperationnote_iv_steroid_injection' => 'ElementIntravenousSteroidInjection',
					 'et_ophtroperationnote_knapp' => 'ElementKnappProcedure',
					 'et_ophtroperationnote_kpro' => 'ElementKeratoprosthesis',
					 'et_ophtroperationnote_lacintub' => 'ElementLacrimalIntubation',
					 'et_ophtroperationnote_lacrimal_gland__other' => 'ElementOtherProcedureOnLacrimalGland',
					 'et_ophtroperationnote_lacrimal_gland_biopsy' => 'ElementBiopsyOfLacrimalGland',
					 'et_ophtroperationnote_lacrimal_sac__excision' => 'ElementExcisionOfLacrimalSac',
					 'et_ophtroperationnote_lasekprk' => 'ElementLASEKPRK',
					 'et_ophtroperationnote_laser' => 'ElementLaserRetinopexy',
					 'et_ophtroperationnote_laser_ophthalmoscopy' => 'ElementScanningLaserOphthalmoscopy',
					 'et_ophtroperationnote_laser_pi' => 'ElementLaserIridotomy',
					 'et_ophtroperationnote_lasik' => 'ElementLASIK',
					 'et_ophtroperationnote_lasik_flap' => 'ElementLASIKFlapReposition',
					 'et_ophtroperationnote_lat_canth_sling' => 'ElementLatCanthSling',
					 'et_ophtroperationnote_lat_cnthplst' => 'ElementCanthoplastyLateral',
					 'et_ophtroperationnote_lat_cnthpxy' => 'ElementCanthopexyLateral',
					 'et_ophtroperationnote_lat_orbitotomy' => 'ElementLateralOrbitotomy',
					 'et_ophtroperationnote_lee' => 'ElementCanthoplastyMedialLee',
					 'et_ophtroperationnote_lid_low_ant' => 'ElementLidLoweringAnteriorApproach',
					 'et_ophtroperationnote_lid_low_post' => 'ElementLidLoweringPosteriorApproach',
					 'et_ophtroperationnote_lid_recon__flaps' => 'ElementReconstructionOfLidLocalFlaps',
					 'et_ophtroperationnote_lid_recon__graft' => 'ElementReconstructionOfLidWithGraft',
					 'et_ophtroperationnote_limbal' => 'ElementLimbalCellTransplant',
					 'et_ophtroperationnote_limbal_relaxing_incision' => 'ElementLimbalRelaxingIncision',
					 'et_ophtroperationnote_lj_tube' => 'ElementLesterJonesTube',
					 'et_ophtroperationnote_ll_elvtn' => 'ElementLowerLidElevationSpecifyGraftMaterial',
					 'et_ophtroperationnote_lr_' => 'ElementLateralRectusRecession',
					 'et_ophtroperationnote_lr_2' => 'ElementLateralRectusResection',
					 'et_ophtroperationnote_lr_faden' => 'ElementLateralRectusFaden',
					 'et_ophtroperationnote_lr_vert_trans' => 'ElementLateralRectusVerticalTransposition',
					 'et_ophtroperationnote_lsac_bx' => 'ElementBiopsyOfLacrimalSac',
					 'et_ophtroperationnote_med_cnthplsty' => 'ElementCanthoplastyMedial',
					 'et_ophtroperationnote_mld' => 'ElementMouldingOfSocket',
					 'et_ophtroperationnote_mmc' => 'ElementApplicationOfMMC',
					 'et_ophtroperationnote_mmg' => 'ElementMucousMembraneGraft',
					 'et_ophtroperationnote_mmg2' => 'ElementMMGToOcularSuface',
					 'et_ophtroperationnote_moria_alk' => 'ElementKeratoplastyAutomatedMoria',
					 'et_ophtroperationnote_mr_' => 'ElementMedialRectusRecession',
					 'et_ophtroperationnote_mr_2' => 'ElementMedialRectusResection',
					 'et_ophtroperationnote_mr_faden' => 'ElementMedialRectusFaden',
					 'et_ophtroperationnote_mr_vert_trans' => 'ElementMedialRectusVerticalTransposition',
					 'et_ophtroperationnote_nasedosc' => 'ElementNasendoscopy',
					 'et_ophtroperationnote_needlingbleb' => 'ElementNeedlingOfBleb',
					 'et_ophtroperationnote_occllacrpunctm' => 'ElementOcclusionOfLacrimalPunctum',
					 'et_ophtroperationnote_ofi' => 'ElementInsertionOfOrbitalFloorImplant',
					 'et_ophtroperationnote_onsdopticdecom' => 'ElementDecompressionOfOpticNerve',
					 'et_ophtroperationnote_opn_eyelid' => 'ElementOperationOnEyelid',
					 'et_ophtroperationnote_optical_coherence_tomography' => 'ElementOpticalCoherenceTomography',
					 'et_ophtroperationnote_orb_absc' => 'ElementDrainageOfOrbitalAbscess',
					 'et_ophtroperationnote_orb_ball_implnt' => 'ElementInsertionOfOrbitalImplant',
					 'et_ophtroperationnote_orb_explrn' => 'ElementExplorationOfOrbit',
					 'et_ophtroperationnote_orb_fb' => 'ElementRemovalOfOrbitalForeignBody',
					 'et_ophtroperationnote_orb_implnt_removal' => 'ElementRemovalOfOrbitalImplant',
					 'et_ophtroperationnote_orb_recn' => 'ElementReconstructionOfOrbit',
					 'et_ophtroperationnote_orbicularismsst' => 'ElementBlepharospasmOrbicularisMuscleStripping',
					 'et_ophtroperationnote_orbit_other' => 'ElementOrbitOther',
					 'et_ophtroperationnote_orbital_biopsy' => 'ElementBiopsyOfOrbit',
					 'et_ophtroperationnote_orbital_fracture' => 'ElementRepairOfOrbitalFracture',
					 'et_ophtroperationnote_other_eyelid' => 'ElementOtherProcedureEyelid',
					 'et_ophtroperationnote_ozurdex' => 'ElementOzurdex',
					 'et_ophtroperationnote_penetrating_inj' => 'ElementRepairOfPenetratingInjury',
					 'et_ophtroperationnote_periocular_steroid' => 'ElementPeriocularSteroidInjection',
					 'et_ophtroperationnote_pi' => 'ElementPeripheralIridectomy',
					 'et_ophtroperationnote_plaque' => 'ElementRadioactivePlaque',
					 'et_ophtroperationnote_post_capsulotomy' => 'ElementPosteriorCapsulotomy',
					 'et_ophtroperationnote_posterior_synechiolysis' => 'ElementPosteriorSynechiolysis',
					 'et_ophtroperationnote_prp' => 'ElementPanretinalPhotocoagulation',
					 'et_ophtroperationnote_pt_ant' => 'ElementPtosisCorrectionApoRepairAnteriorApproach',
					 'et_ophtroperationnote_pt_ant_lev_excn' => 'ElementPtosisCorrectionAnteriorLevatorExcision',
					 'et_ophtroperationnote_pt_post' => 'ElementPtosisCorrectionApoRepairPosteriorApproach',
					 'et_ophtroperationnote_ptk' => 'ElementPTKLaserSuperficialKeratectomy',
					 'et_ophtroperationnote_punctal_occl' => 'ElementPunctumClosure',
					 'et_ophtroperationnote_pupiloplasty' => 'ElementIridoplastyOccluder',
					 'et_ophtroperationnote_pupiloplasty2' => 'ElementIridoplastySuture',
					 'et_ophtroperationnote_redo_dcr' => 'ElementRedoDCR',
					 'et_ophtroperationnote_reformation_ac' => 'ElementReformationOfAC',
					 'et_ophtroperationnote_reformation_ac2' => 'ElementReformationOfAnteriorChamber',
					 'et_ophtroperationnote_rem_aqueous_shunt' => 'ElementRemAqueousShunt',
					 'et_ophtroperationnote_rem_stnt' => 'ElementRemovalOfStent',
					 'et_ophtroperationnote_removal_of_cnv' => 'ElementSubretinalMembranectomy',
					 'et_ophtroperationnote_removal_of_eyelash' => 'ElementRemovalOfEyelash',
					 'et_ophtroperationnote_removal_of_fb_conjunctiva' => 'ElementRemovalOfForeignBodyFromConjunctiva',
					 'et_ophtroperationnote_removal_of_gas' => 'ElementRemovalOfGas',
					 'et_ophtroperationnote_removal_of_intacs' => 'ElementRemovalOfIntacs',
					 'et_ophtroperationnote_removal_of_releasable_suture' => 'ElementRemovalOfReleasableSuture',
					 'et_ophtroperationnote_removal_of_sutu' => 'ElementCornealSutureRemoval',
					 'et_ophtroperationnote_removal_tube' => 'ElementRemovalOfTubeFromNasolacrimalDuct',
					 'et_ophtroperationnote_rep_canaliculus' => 'ElementRepairOfCanaliculus',
					 'et_ophtroperationnote_repair_iris_prolapse' => 'ElementRepairOfProlapsedIris',
					 'et_ophtroperationnote_repair_orbit' => 'ElementRepairOrbit',
					 'et_ophtroperationnote_ret_biopsy' => 'ElementBiopsyOfRetina',
					 'et_ophtroperationnote_retinectomy' => 'ElementRetinectomy',
					 'et_ophtroperationnote_retro_steroid' => 'ElementRetrobulbarSteroidInjection',
					 'et_ophtroperationnote_revaqueousshunt' => 'ElementRevisionOfAqueousShunt',
					 'et_ophtroperationnote_revision_ac' => 'ElementRevisionOfAnteriorChamber',
					 'et_ophtroperationnote_revision_traby' => 'ElementRevisionOfTrabeculectomy',
					 'et_ophtroperationnote_rmv_gld_wt' => 'ElementRemovalOfGoldWeight',
					 'et_ophtroperationnote_ro_buckle' => 'ElementRemovalOfBuckle',
					 'et_ophtroperationnote_roo' => 'ElementRemovalOfOil',
					 'et_ophtroperationnote_rotationlcorgft' => 'ElementKeratoplastyRotationAutograft',
					 'et_ophtroperationnote_scar_rvn' => 'ElementRevisionOfScar',
					 'et_ophtroperationnote_sckt_exp' => 'ElementInsertionOfSocketExpander',
					 'et_ophtroperationnote_skin_crease_reformation' => 'ElementReformationOfSkinCrease',
					 'et_ophtroperationnote_snb' => 'ElementSentinelNodeBiopsy',
					 'et_ophtroperationnote_so_' => 'ElementSuperiorObliqueRecession',
					 'et_ophtroperationnote_so_ant_transposition' => 'ElementSuperiorObliqueAnteriorTransposition',
					 'et_ophtroperationnote_so_disinsertion' => 'ElementSuperiorObliqueDisinsertion',
					 'et_ophtroperationnote_so_faden' => 'ElementSuperiorObliqueFaden',
					 'et_ophtroperationnote_so_tenotomy' => 'ElementSuperiorObliqueTenotomy',
					 'et_ophtroperationnote_so_tuck' => 'ElementSuperiorObliqueTuck',
					 'et_ophtroperationnote_sp' => 'ElementSyringeAndProbeNasolacrimalDuct',
					 'et_ophtroperationnote_split_skin' => 'ElementSplitSkinGraft',
					 'et_ophtroperationnote_squint_op' => 'ElementOperationForSquint',
					 'et_ophtroperationnote_squint_opn' => 'ElementCombinedOperationOnEyeMuscles',
					 'et_ophtroperationnote_sr_' => 'ElementSuperiorRectusRecession',
					 'et_ophtroperationnote_sr_2' => 'ElementSuperiorRectusResection',
					 'et_ophtroperationnote_sr_faden' => 'ElementSuperiorRectusFaden',
					 'et_ophtroperationnote_sr_transposition' => 'ElementSuperiorRectusHorizontalTransposition',
					 'et_ophtroperationnote_superficial_k' => 'ElementSuperficialKeratectomy',
					 'et_ophtroperationnote_surgical_pi' => 'ElementSurgicalIridotomy',
					 'et_ophtroperationnote_tars_lat' => 'ElementTarsorrhaphyLateral',
					 'et_ophtroperationnote_tars_med' => 'ElementTarsorrhaphyMedialPillar',
					 'et_ophtroperationnote_tars_permnt' => 'ElementTarsorrhaphyPermanent',
					 'et_ophtroperationnote_tars_temp' => 'ElementTarsorrhaphyTemporary',
					 'et_ophtroperationnote_tarsoconj_diamond' => 'ElementTarsoconjDiamond',
					 'et_ophtroperationnote_tattooing_corne' => 'ElementTattooingOfCornea',
					 'et_ophtroperationnote_telecnth_wire' => 'ElementTelecanthusCorrectionWithWire',
					 'et_ophtroperationnote_tempartrybiopsy' => 'ElementBiopsyOfTemporalArtery',
					 'et_ophtroperationnote_three_snip' => 'ElementThreeSnip',
					 'et_ophtroperationnote_topical_anaesthetic' => 'ElementTopicalLocalAnaesthetic',
					 'et_ophtroperationnote_trabectome' => 'ElementTrabectome',
					 'et_ophtroperationnote_trabeculotomy' => 'ElementTrabeculotomy',
					 'et_ophtroperationnote_traby' => 'ElementTrabeculectomy',
					 'et_ophtroperationnote_vit_biopsy' => 'ElementBiopsyOfVitreous',
					 'et_ophtroperationnote_yag_caps' => 'ElementCapsulotomyYAG',
				 ) as $table => $element) {
			$element_type = $this->dbConnection->createCommand()->select('id')->from("element_type")->where("event_type_id=:event_type_id and class_name=:class_name",array(':event_type_id'=>$opnote['id'],':class_name'=>$element))->queryRow();
			$pe = $this->dbConnection->createCommand()->select("*")->from("et_ophtroperationnote_procedure_element")->where("element_type_id=:element_type_id",array(':element_type_id'=>$element_type['id']))->queryRow();

			foreach ($this->dbConnection->createCommand()->select("*")->from($table)->order('id asc')->queryAll() as $row) {
				$this->insert('et_ophtroperationnote_genericprocedure',array(
						'event_id' => $row['event_id'],
						'proc_id' => $pe['procedure_id'],
						'comments' => $row['comments'],
						'created_user_id' => $row['created_user_id'],
						'created_date' => $row['created_date'],
						'last_modified_user_id' => $row['last_modified_user_id'],
						'last_modified_date' => $row['last_modified_date'],
					));
			}

			if ($pe['id']) {
				$this->delete('et_ophtroperationnote_procedure_element',"id={$pe['id']}");
			}
			$this->delete('element_type',"id={$element_type['id']}");
			$this->dropTable($table);
		}

		foreach (array(
					 'et_ophtroperationnote_anaesthetic_anaesthetic_agent' => array(
						 'et_ophtroperationnote_paa_last_modified_user_id_fk' => array('last_modified_user_id','user','id'),
						 'et_ophtroperationnote_paa_created_user_id_fk' => array('created_user_id','user','id'),
						 'et_ophtroperationnote_paa_anaesthetic_agent_id_fk' => array('anaesthetic_agent_id','anaesthetic_agent','id'),
						 'et_ophtroperationnote_paa_anaesthetic_id_fk' => array('et_ophtroperationnote_anaesthetic_id','et_ophtroperationnote_anaesthetic','id'),
					 ),
					 'et_ophtroperationnote_anaesthetic_anaesthetic_complication' => array(
						 'et_ophtroperationnote_pac_last_modified_user_id_fk' => array('last_modified_user_id','user','id'),
						 'et_ophtroperationnote_pac_created_user_id_fk' => array('created_user_id','user','id'),
						 'et_ophtroperationnote_anaesthetic_ac_anaesthetic_id_fk' => array('et_ophtroperationnote_anaesthetic_id','et_ophtroperationnote_anaesthetic','id'),
						 'et_ophtroperationnote_anaesthetic_aca_complication_id_fk' => array('anaesthetic_complication_id','et_ophtroperationnote_anaesthetic_anaesthetic_complications','id'),
					 ),
					 'et_ophtroperationnote_anaesthetic_anaesthetic_complications' => array(
						 'et_ophtroperationnote_anaesthetic_ac_last_modified_user_id_fk' => array('last_modified_user_id','user','id'),
						 'et_ophtroperationnote_anaesthetic_ac_created_user_id_fk' => array('created_user_id','user','id'),
					 ),
					 'et_ophtroperationnote_buckle_drainage_type' => array(
						 'et_ophtroperationnote_bdt_last_modified_user_id_fk' => array('last_modified_user_id','user','id'),
						 'et_ophtroperationnote_bdt_created_user_id_fk' => array('created_user_id','user','id'),
					 ),
					 'et_ophtroperationnote_cataract_complication' => array(
						 'et_ophtroperationnote_cc2_cataract_id_fk' => array('cataract_id','et_ophtroperationnote_cataract','id'),
						 'et_ophtroperationnote_cc2_complication_id_fk' => array('complication_id','et_ophtroperationnote_cataract_complications','id'),
						 'et_ophtroperationnote_cc2_last_modified_user_id_fk' => array('last_modified_user_id','user','id'),
						 'et_ophtroperationnote_cc2_created_user_id_fk' => array('created_user_id','user','id'),
					 ),
					 'et_ophtroperationnote_cataract_complications' => array(
						 'et_ophtroperationnote_cc_last_modified_user_id_fk' => array('last_modified_user_id','user','id'),
						 'et_ophtroperationnote_cc_created_user_id_fk' => array('created_user_id','user','id'),
					 ),
					 'et_ophtroperationnote_cataract_incision_site' => array(
						 'et_ophtroperationnote_cis_last_modified_user_id_fk' => array('last_modified_user_id','user','id'),
						 'et_ophtroperationnote_cis_created_user_id_fk' => array('created_user_id','user','id'),
					 ),
					 'et_ophtroperationnote_cataract_incision_type' => array(
						 'et_ophtroperationnote_cit_last_modified_user_id_fk' => array('last_modified_user_id','user','id'),
						 'et_ophtroperationnote_cit_created_user_id_fk' => array('created_user_id','user','id'),
					 ),
					 'et_ophtroperationnote_cataract_operative_device' => array(
						 'et_ophtroperationnote_ccd_cataract_id_fk' => array('cataract_id','et_ophtroperationnote_cataract','id'),
						 'et_ophtroperationnote_ccd_operative_device_id_fk' => array('operative_device_id','operative_device','id'),
						 'et_ophtroperationnote_ccd_last_modified_user_id_fk' => array('last_modified_user_id','user','id'),
						 'et_ophtroperationnote_ccd_created_user_id_fk' => array('created_user_id','user','id'),
					 ),
					 'et_ophtroperationnote_gas_percentage' => array(
						 'et_ophtroperationnote_gas_pc_last_modified_user_id_fk' => array('last_modified_user_id','user','id'),
						 'et_ophtroperationnote_gas_pc_created_user_id_fk' => array('created_user_id','user','id'),
					 ),
					 'et_ophtroperationnote_gas_type' => array(
						 'et_ophtroperationnote_gas_type_last_modified_user_id_fk' => array('last_modified_user_id','user','id'),
						 'et_ophtroperationnote_gas_type_created_user_id_fk' => array('created_user_id','user','id'),
					 ),
					 'et_ophtroperationnote_gas_volume' => array(
						 'et_ophtroperationnote_gas_vol_last_modified_user_id_fk' => array('last_modified_user_id','user','id'),
						 'et_ophtroperationnote_gas_vol_created_user_id_fk' => array('created_user_id','user','id'),
					 ),
					 'et_ophtroperationnote_postop_drug' => array(
						 'et_ophtroperationnote_postop_drug_l_m_u_id_fk' => array('last_modified_user_id','user','id'),
						 'et_ophtroperationnote_postop_drug_c_u_id_fk' => array('created_user_id','user','id'),
					 ),
					 'et_ophtroperationnote_postop_drugs_drug' => array(
						 'et_ophtroperationnote_pdd_created_user_id_fk' => array('created_user_id','user','id'),
						 'et_ophtroperationnote_pdd_last_modified_user_id_fk' => array('last_modified_user_id','user','id'),
						 'et_ophtroperationnote_pdd_drug_id_fk' => array('drug_id','ophtroperationnote_postop_drug','id'),
						 'et_ophtroperationnote_pdd_drugs_id_fk' => array('et_ophtroperationnote_postop_drugs_id','et_ophtroperationnote_postop_drugs','id','ophtroperationnote_postop_drugs_id'),
					 ),
					 'et_ophtroperationnote_postop_site_subspecialty_drug' => array(
						 'et_ophtroperationnote_postop_site_subspecialty_drug_l_m_u_id_fk' => array('last_modified_user_id','user','id'),
						 'et_ophtroperationnote_postop_site_subspecialty_drug_c_u_id_fk' => array('created_user_id','user','id'),
						 'et_ophtroperationnote_postop_site_subspecialty_drug_site_id_fk' => array('site_id','site','id',null,'et_ophtroperationnote_postop_site_subspecialty_drug_site_id'),
						 'et_ophtroperationnote_postop_site_subspecialty_drug_s_id_fk' => array('subspecialty_id','subspecialty','id',null,'et_ophtroperationnote_postop_site_subspecialty_drug_s_id'),
						 'et_ophtroperationnote_postop_site_subspecialty_drug_drug_id_fk' => array('drug_id','ophtroperationnote_postop_drug','id',null,'et_ophtroperationnote_postop_site_subspecialty_drug_drug_id'),
					 ),
					 'et_ophtroperationnote_preparation_intraocular_solution' => array(
						 'et_ophtroperationnote_pis_last_modified_user_id_fk' => array('last_modified_user_id','user','id'),
						 'et_ophtroperationnote_pis_created_user_id_fk' => array('created_user_id','user','id'),
					 ),
					 'et_ophtroperationnote_preparation_skin_preparation' => array(
						 'et_ophtroperationnote_psp_last_modified_user_id_fk' => array('last_modified_user_id','user','id'),
						 'et_ophtroperationnote_psp_created_user_id_fk' => array('created_user_id','user','id'),
					 ),
					 'et_ophtroperationnote_procedurelist_procedure_assignment' => array(
						 'et_ophtroperationnote_plpa_last_modified_user_id_fk' => array('last_modified_user_id','user','id'),
						 'et_ophtroperationnote_plpa_created_user_id_fk' => array('created_user_id','user','id'),
					 ),
					 'et_ophtroperationnote_site_subspecialty_postop_instructions' => array(
						 'et_ophtroperationnote_sspi_site_id_fk' => array('site_id','site','id',null,'et_ophtroperationnote_sspi_site_id'),
						 'et_ophtroperationnote_sspi_subspecialty_id_fk' => array('subspecialty_id','subspecialty','id',null,'et_ophtroperationnote_sspi_subspecialty_id'),
						 'et_ophtroperationnote_sspi_last_modified_user_id_fk' => array('last_modified_user_id','user','id'),
						 'et_ophtroperationnote_sspi_created_user_id_fk' => array('created_user_id','user','id'),
					 ),
					 'et_ophtroperationnote_procedure_element' => array(
						 'et_ophtroperationnote_pe_procedure_fk' => array('procedure_id','proc','id',null,'et_ophtroperationnote_pe_procedure_id'),
						 'et_ophtroperationnote_pe_element_type_fk' => array('element_type_id','element_type','id',null,'et_ophtroperationnote_pe_element_type_id'),
						 'et_ophtroperationnote_pe_created_user_id_fk' => array('created_user_id','user','id'),
						 'et_ophtroperationnote_pe_last_modified_user_id_fk' => array('last_modified_user_id','user','id'),
					 ),
					 'et_ophtroperationnote_cataract_iol_position' => array(
						 'et_ophtroperationnote_cip_last_modified_user_id_fk' => array('last_modified_user_id','user','id'),
						 'et_ophtroperationnote_cip_created_user_id_fk' => array('created_user_id','user','id'),
					 ),
					 'et_ophtroperationnote_cataract_iol_type' => array(
						 'et_ophtroperationnote_cot_last_modified_user_id_fk' => array('last_modified_user_id','user','id'),
						 'et_ophtroperationnote_cot_created_user_id_fk' => array('created_user_id','user','id'),
					 ),
					 'et_ophtroperationnote_gauge' => array(
						 'et_ophtroperationnote_gauge_last_modified_user_id_fk' => array('last_modified_user_id','user','id'),
						 'et_ophtroperationnote_gauge_created_user_id_fk' => array('created_user_id','user','id'),
					 ),
				 ) as $table => $keys) {
			$new_table = preg_replace('/^et_/','',$table);

			$this->renameTable($table,$new_table);

			foreach ($keys as $key => $properties) {
				if (is_array($properties)) {
					$this->dropForeignKey($key,$new_table);

					if (@$properties[4]) {
						$this->dropIndex($properties[4],$new_table);
					} else {
						$this->dropIndex($key,$new_table);
					}

					$new_key = preg_replace('/^et_/','',$key);

					if (@$properties[3]) {
						$this->renameColumn($new_table,$properties[0],$properties[3]);
						$properties[0] = $properties[3];
					}

					$this->createIndex($new_key,$new_table,$properties[0]);
					$this->addForeignKey($new_key,$new_table,$properties[0],$properties[1],$properties[2]);
				}
			}
		}

		$this->update('element_type',array('class_name'=>'Element_OphTrOperationnote_ProcedureList'),"event_type_id = {$opnote['id']} and class_name='ElementProcedureList'");
		$this->update('element_type',array('class_name'=>'Element_OphTrOperationnote_Vitrectomy'),"event_type_id = {$opnote['id']} and class_name='ElementVitrectomy'");
		$this->update('element_type',array('class_name'=>'Element_OphTrOperationnote_MembranePeel'),"event_type_id = {$opnote['id']} and class_name='ElementMembranePeel'");
		$this->update('element_type',array('class_name'=>'Element_OphTrOperationnote_Tamponade'),"event_type_id = {$opnote['id']} and class_name='ElementTamponade'");
		$this->update('element_type',array('class_name'=>'Element_OphTrOperationnote_Buckle'),"event_type_id = {$opnote['id']} and class_name='ElementBuckle'");
		$this->update('element_type',array('class_name'=>'Element_OphTrOperationnote_Cataract'),"event_type_id = {$opnote['id']} and class_name='ElementCataract'");
		$this->update('element_type',array('class_name'=>'Element_OphTrOperationnote_Anaesthetic'),"event_type_id = {$opnote['id']} and class_name='ElementAnaesthetic'");
		$this->update('element_type',array('class_name'=>'Element_OphTrOperationnote_Surgeon'),"event_type_id = {$opnote['id']} and class_name='ElementSurgeon'");
		$this->update('element_type',array('class_name'=>'Element_OphTrOperationnote_PostOpDrugs'),"event_type_id = {$opnote['id']} and class_name='ElementPostOpDrugs'");
		$this->update('element_type',array('class_name'=>'Element_OphTrOperationnote_Comments'),"event_type_id = {$opnote['id']} and class_name='ElementComments'");
		$this->update('element_type',array('class_name'=>'Element_OphTrOperationnote_Personnel'),"event_type_id = {$opnote['id']} and class_name='ElementPersonnel'");
		$this->update('element_type',array('class_name'=>'Element_OphTrOperationnote_Preparation'),"event_type_id = {$opnote['id']} and class_name='ElementPreparation'");
	}

}
