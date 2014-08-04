<?php

class m140220_235959_table_versioning extends OEMigration
{
	public function up()
	{
		$this->update('drug',array('default_frequency_id' => null),"default_frequency_id = 0");
		$this->update('drug',array('default_duration_id' => null),"default_duration_id = 0");
		$this->update('drug',array('default_route_id' => null),"default_route_id = 0");

		$proc_ids = array();
		foreach ($this->dbConnection->createCommand()->select("id")->from("proc")->queryAll() as $row) {
			$proc_ids[] = $row['id'];
		}

		if (!empty($proc_ids)) {
			$this->delete('proc_opcs_assignment',"proc_id not in (".implode(',',$proc_ids).")");
		}

		$this->renameColumn('disorder_tree','id','disorder_id');

		$this->addColumn('disorder_tree','id','int(10) unsigned NOT NULL');

		foreach ($this->dbConnection->createCommand()->select("*")->from("disorder_tree")->queryAll() as $i => $row) {
			$this->update('disorder_tree',array('id' => $i+1),"disorder_id = {$row['disorder_id']} and lft = {$row['lft']} and rght = {$row['rght']}");
		}

		$this->addPrimaryKey("id","disorder_tree","id");
		$this->alterColumn('disorder_tree','id','int(10) unsigned NOT NULL AUTO_INCREMENT');

		$this->addColumn('allergy','active','boolean not null default true');
		$this->addColumn('anaesthetic_agent','active','boolean not null default true');
		$this->addColumn('anaesthetic_complication','active','boolean not null default true');
		$this->addColumn('anaesthetic_delivery','active','boolean not null default true');
		$this->addColumn('anaesthetic_type','active','boolean not null default true');
		$this->addColumn('anaesthetist','active','boolean not null default true');
		$this->addColumn('benefit','active','boolean not null default true');
		$this->addColumn('complication','active','boolean not null default true');
		$this->addColumn('contact_label','active','boolean not null default true');
		$this->addColumn('country','active','boolean not null default true');
		$this->addColumn('disorder','active','boolean not null default true');
		$this->addColumn('drug','active','boolean not null default true');
		$this->addColumn('drug_duration','active','boolean not null default true');
		$this->addColumn('drug_form','active','boolean not null default true');
		$this->addColumn('drug_frequency','active','boolean not null default true');
		$this->addColumn('drug_route','active','boolean not null default true');
		$this->addColumn('drug_route_option','active','boolean not null default true');
		$this->addColumn('drug_set','active','boolean not null default true');
		$this->addColumn('drug_type','active','boolean not null default true');
		$this->addColumn('firm','active','boolean not null default true');
		$this->addColumn('institution','active','boolean not null default true');
		$this->addColumn('nsc_grade','active','boolean not null default true');
		$this->addColumn('opcs_code','active','boolean not null default true');
		$this->addColumn('operative_device','active','boolean not null default true');
		$this->addColumn('patient_oph_info_cvi_status','active','boolean not null default true');
		$this->addColumn('proc','active','boolean not null default true');
		$this->addColumn('site','active','boolean not null default true');
		$this->addColumn('specialty_type','active','boolean not null default true');
		$this->addColumn('subspecialty_subsection','active','boolean not null default true');

		$this->update('drug', array('active' => new CDbExpression('not (discontinued)')));
		$this->dropColumn('drug', 'discontinued');

		$this->versionExistingTable('address');
		$this->versionExistingTable('address_type');
		$this->versionExistingTable('allergy');
		$this->versionExistingTable('anaesthetic_agent');
		$this->versionExistingTable('anaesthetic_complication');
		$this->versionExistingTable('anaesthetic_delivery');
		$this->versionExistingTable('anaesthetic_type');
		$this->versionExistingTable('anaesthetist');
		$this->versionExistingTable('benefit');
		$this->versionExistingTable('commissioning_body');
		$this->versionExistingTable('commissioning_body_patient_assignment');
		$this->versionExistingTable('commissioning_body_practice_assignment');
		$this->versionExistingTable('commissioning_body_service');
		$this->versionExistingTable('commissioning_body_service_type');
		$this->versionExistingTable('commissioning_body_type');
		$this->versionExistingTable('common_ophthalmic_disorder');
		$this->versionExistingTable('common_previous_operation');
		$this->versionExistingTable('common_systemic_disorder');
		$this->versionExistingTable('complication');
		$this->versionExistingTable('contact');
		$this->versionExistingTable('contact_label');
		$this->versionExistingTable('contact_location');
		$this->versionExistingTable('contact_metadata');
		$this->versionExistingTable('country');
		$this->versionExistingTable('disorder');
		$this->versionExistingTable('disorder_tree');
		$this->versionExistingTable('drug');
		$this->versionExistingTable('drug_allergy_assignment');
		$this->versionExistingTable('drug_duration');
		$this->versionExistingTable('drug_form');
		$this->versionExistingTable('drug_frequency');
		$this->versionExistingTable('drug_route');
		$this->versionExistingTable('drug_route_option');
		$this->versionExistingTable('drug_set');
		$this->versionExistingTable('drug_set_item');
		$this->versionExistingTable('drug_set_item_taper');
		$this->versionExistingTable('drug_type');
		$this->versionExistingTable('element_type');
		$this->versionExistingTable('episode');
		$this->versionExistingTable('episode_status');
		$this->versionExistingTable('ethnic_group');
		$this->versionExistingTable('event');
		$this->versionExistingTable('event_group');
		$this->versionExistingTable('event_issue');
		$this->versionExistingTable('event_type');
		$this->versionExistingTable('family_history');
		$this->versionExistingTable('family_history_condition');
		$this->versionExistingTable('family_history_relative');
		$this->versionExistingTable('family_history_side');
		$this->versionExistingTable('firm');
		$this->versionExistingTable('firm_user_assignment');
		$this->versionExistingTable('gp');
		$this->versionExistingTable('institution');
		$this->versionExistingTable('issue');
		$this->versionExistingTable('language');
		$this->versionExistingTable('medication');
		$this->versionExistingTable('nsc_grade');
		$this->versionExistingTable('opcs_code');
		$this->versionExistingTable('operative_device');
		$this->versionExistingTable('patient');
		$this->versionExistingTable('patient_allergy_assignment');
		$this->versionExistingTable('patient_contact_assignment');
		$this->versionExistingTable('patient_oph_info');
		$this->versionExistingTable('patient_oph_info_cvi_status');
		$this->versionExistingTable('patient_shortcode');
		$this->versionExistingTable('period');
		$this->versionExistingTable('person');
		$this->versionExistingTable('practice');
		$this->versionExistingTable('previous_operation');
		$this->versionExistingTable('priority');
		$this->versionExistingTable('proc');
		$this->versionExistingTable('proc_opcs_assignment');
		$this->versionExistingTable('proc_subspecialty_assignment');
		$this->versionExistingTable('proc_subspecialty_subsection_assignment');
		$this->versionExistingTable('procedure_additional');
		$this->versionExistingTable('procedure_benefit');
		$this->versionExistingTable('procedure_complication');
		$this->versionExistingTable('protected_file');
		$this->versionExistingTable('referral');
		$this->versionExistingTable('referral_episode_assignment');
		$this->versionExistingTable('referral_type');
		$this->versionExistingTable('secondary_diagnosis');
		$this->versionExistingTable('service');
		$this->versionExistingTable('service_subspecialty_assignment');
		$this->versionExistingTable('setting_field_type');
		$this->versionExistingTable('setting_firm');
		$this->versionExistingTable('setting_installation');
		$this->versionExistingTable('setting_institution');
		$this->versionExistingTable('setting_metadata');
		$this->versionExistingTable('setting_site');
		$this->versionExistingTable('setting_specialty');
		$this->versionExistingTable('setting_subspecialty');
		$this->versionExistingTable('setting_user');
		$this->versionExistingTable('site');
		$this->versionExistingTable('site_subspecialty_anaesthetic_agent');
		$this->versionExistingTable('site_subspecialty_anaesthetic_agent_default');
		$this->versionExistingTable('site_subspecialty_drug');
		$this->versionExistingTable('site_subspecialty_operative_device');
		$this->versionExistingTable('specialty');
		$this->versionExistingTable('specialty_type');
		$this->versionExistingTable('subspecialty');
		$this->versionExistingTable('subspecialty_subsection');
		$this->versionExistingTable('user');
		$this->versionExistingTable('user_firm');
		$this->versionExistingTable('user_firm_preference');
		$this->versionExistingTable('user_firm_rights');
		$this->versionExistingTable('user_service_rights');
		$this->versionExistingTable('user_site');

		$null_ids = array();

		$limit = 10000;
		$offset = 0;

		while (1) {
			$data = $this->dbConnection->createCommand()->select("id,data")->from("audit")->where("data is not null and data != :blank",array(":blank" => ""))->order("id asc")->limit($limit)->offset($offset)->queryAll();

			if (empty($data)) break;

			foreach ($data as $row) {
				if (@unserialize($row['data'])) {
					$null_ids[] = $row['id'];

					if (count($null_ids) >= 1000) {
						$this->resetData($null_ids);
						$null_ids = array();
					}
				}
			}

			$offset += $limit;
		}

		if (!empty($null_ids)) {
			$this->resetData($null_ids);
		}

		$this->update('audit',array('data' => null),"data = ''");
	}

	public function resetData($null_ids)
	{
		$this->update('audit',array('data' => null),"id in (".implode(",",$null_ids).")");
	}

	public function down()
	{
		$this->dropTable('address_version');
		$this->dropTable('address_type_version');
		$this->dropTable('allergy_version');
		$this->dropTable('anaesthetic_agent_version');
		$this->dropTable('anaesthetic_complication_version');
		$this->dropTable('anaesthetic_delivery_version');
		$this->dropTable('anaesthetic_type_version');
		$this->dropTable('anaesthetist_version');
		$this->dropTable('benefit_version');
		$this->dropTable('commissioning_body_version');
		$this->dropTable('commissioning_body_patient_assignment_version');
		$this->dropTable('commissioning_body_practice_assignment_version');
		$this->dropTable('commissioning_body_service_version');
		$this->dropTable('commissioning_body_service_type_version');
		$this->dropTable('commissioning_body_type_version');
		$this->dropTable('common_ophthalmic_disorder_version');
		$this->dropTable('common_previous_operation_version');
		$this->dropTable('common_systemic_disorder_version');
		$this->dropTable('complication_version');
		$this->dropTable('contact_version');
		$this->dropTable('contact_label_version');
		$this->dropTable('contact_location_version');
		$this->dropTable('contact_metadata_version');
		$this->dropTable('country_version');
		$this->dropTable('disorder_version');
		$this->dropTable('disorder_tree_version');
		$this->dropTable('drug_version');
		$this->dropTable('drug_allergy_assignment_version');
		$this->dropTable('drug_duration_version');
		$this->dropTable('drug_form_version');
		$this->dropTable('drug_frequency_version');
		$this->dropTable('drug_route_version');
		$this->dropTable('drug_route_option_version');
		$this->dropTable('drug_set_version');
		$this->dropTable('drug_set_item_version');
		$this->dropTable('drug_set_item_taper_version');
		$this->dropTable('drug_type_version');
		$this->dropTable('element_type_version');
		$this->dropTable('episode_version');
		$this->dropTable('episode_status_version');
		$this->dropTable('ethnic_group_version');
		$this->dropTable('event_version');
		$this->dropTable('event_group_version');
		$this->dropTable('event_issue_version');
		$this->dropTable('event_type_version');
		$this->dropTable('family_history_version');
		$this->dropTable('family_history_condition_version');
		$this->dropTable('family_history_relative_version');
		$this->dropTable('family_history_side_version');
		$this->dropTable('firm_version');
		$this->dropTable('firm_user_assignment_version');
		$this->dropTable('gp_version');
		$this->dropTable('institution_version');
		$this->dropTable('issue_version');
		$this->dropTable('language_version');
		$this->dropTable('medication_version');
		$this->dropTable('nsc_grade_version');
		$this->dropTable('opcs_code_version');
		$this->dropTable('operative_device_version');
		$this->dropTable('patient_version');
		$this->dropTable('patient_allergy_assignment_version');
		$this->dropTable('patient_contact_assignment_version');
		$this->dropTable('patient_oph_info_version');
		$this->dropTable('patient_oph_info_cvi_status_version');
		$this->dropTable('patient_shortcode_version');
		$this->dropTable('period_version');
		$this->dropTable('person_version');
		$this->dropTable('practice_version');
		$this->dropTable('previous_operation_version');
		$this->dropTable('priority_version');
		$this->dropTable('proc_version');
		$this->dropTable('proc_opcs_assignment_version');
		$this->dropTable('proc_subspecialty_assignment_version');
		$this->dropTable('proc_subspecialty_subsection_assignment_version');
		$this->dropTable('procedure_additional_version');
		$this->dropTable('procedure_benefit_version');
		$this->dropTable('procedure_complication_version');
		$this->dropTable('protected_file_version');
		$this->dropTable('referral_version');
		$this->dropTable('referral_episode_assignment_version');
		$this->dropTable('referral_type_version');
		$this->dropTable('secondary_diagnosis_version');
		$this->dropTable('service_version');
		$this->dropTable('service_subspecialty_assignment_version');
		$this->dropTable('setting_field_type_version');
		$this->dropTable('setting_firm_version');
		$this->dropTable('setting_installation_version');
		$this->dropTable('setting_institution_version');
		$this->dropTable('setting_metadata_version');
		$this->dropTable('setting_site_version');
		$this->dropTable('setting_specialty_version');
		$this->dropTable('setting_subspecialty_version');
		$this->dropTable('setting_user_version');
		$this->dropTable('site_version');
		$this->dropTable('site_subspecialty_anaesthetic_agent_version');
		$this->dropTable('site_subspecialty_anaesthetic_agent_default_version');
		$this->dropTable('site_subspecialty_drug_version');
		$this->dropTable('site_subspecialty_operative_device_version');
		$this->dropTable('specialty_version');
		$this->dropTable('specialty_type_version');
		$this->dropTable('subspecialty_version');
		$this->dropTable('subspecialty_subsection_version');
		$this->dropTable('user_version');
		$this->dropTable('user_firm_version');
		$this->dropTable('user_firm_preference_version');
		$this->dropTable('user_firm_rights_version');
		$this->dropTable('user_service_rights_version');
		$this->dropTable('user_site_version');

		$this->addColumn('drug', 'discontinued', 'tinyint(1) unsigned not null');
		$this->update('drug', array('discontinued' => new CDbExpression('not (active)')));

		$this->dropColumn('allergy','active');
		$this->dropColumn('anaesthetic_agent','active');
		$this->dropColumn('anaesthetic_complication','active');
		$this->dropColumn('anaesthetic_delivery','active');
		$this->dropColumn('anaesthetic_type','active');
		$this->dropColumn('anaesthetist','active');
		$this->dropColumn('benefit','active');
		$this->dropColumn('complication','active');
		$this->dropColumn('contact_label','active');
		$this->dropColumn('country','active');
		$this->dropColumn('disorder','active');
		$this->dropColumn('drug','active');
		$this->dropColumn('drug_duration','active');
		$this->dropColumn('drug_form','active');
		$this->dropColumn('drug_frequency','active');
		$this->dropColumn('drug_route','active');
		$this->dropColumn('drug_route_option','active');
		$this->dropColumn('drug_set','active');
		$this->dropColumn('drug_type','active');
		$this->dropColumn('firm','active');
		$this->dropColumn('institution','active');
		$this->dropColumn('nsc_grade','active');
		$this->dropColumn('opcs_code','active');
		$this->dropColumn('operative_device','active');
		$this->dropColumn('patient_oph_info_cvi_status','active');
		$this->dropColumn('proc','active');
		$this->dropColumn('site','active');
		$this->dropColumn('specialty_type','active');
		$this->dropColumn('subspecialty_subsection','active');

		$this->alterColumn('disorder_tree','id','int(10) unsigned NOT NULL');
		$this->dropPrimaryKey('id','disorder_tree');

		$this->dropColumn('disorder_tree','id');

		$this->renameColumn('disorder_tree','disorder_id','id');
	}
}
