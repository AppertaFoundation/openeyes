<?php

class m140205_104754_remove_soft_deletion_from_tables_that_dont_need_it extends CDbMigration
{
	public $tables = array(
		'address',
		'commissioning_body_patient_assignment',
		'commissioning_body_practice_assignment',
		'common_ophthalmic_disorder',
		'common_previous_operation',
		'common_systemic_disorder',
		'contact',
		'contact_location',
		'contact_metadata',
		'disorder_tree',
		'drug_allergy_assignment',
		'drug_set',
		'drug_set_item',
		'drug_set_item_taper',
		'element_type',
		'element_type_anaesthetic_agent',
		'element_type_anaesthetic_complication',
		'element_type_anaesthetic_delivery',
		'element_type_anaesthetic_type',
		'element_type_anaesthetist',
		'element_type_eye',
		'element_type_priority',
		'episode_status',
		'event_group',
		'event_issue',
		'event_type',
		'family_history',
		'firm_user_assignment',
		'gp',
		'institution_consultant_assignment',
		'issue',
		'language',
		'manual_contact',
		'pas_assignment',
		'pas_patient_merged',
		'patient_allergy_assignment',
		'patient_contact_assignment',
		'patient_oph_info',
		'patient_shortcode',
		'period',
		'practice',
		'previous_operation',
		'priority',
		'proc_opcs_assignment',
		'proc_subspecialty_assignment',
		'proc_subspecialty_subsection_assignment',
		'procedure_additional',
		'procedure_benefit',
		'procedure_complication',
		'protected_file',
		'referral',
		'referral_episode_assignment',
		'secondary_diagnosis',
		'service',
		'service_subspecialty_assignment',
		'setting_field_type',
		'setting_firm',
		'setting_installation',
		'setting_institution',
		'setting_metadata',
		'setting_site',
		'setting_specialty',
		'setting_subspecialty',
		'setting_user',
		'site_consultant_assignment',
		'site_subspecialty_anaesthetic_agent',
		'site_subspecialty_anaesthetic_agent_default',
		'site_subspecialty_drug',
		'site_subspecialty_operative_device',
		'specialty',
		'subspecialty',
		'user_firm',
		'user_firm_preference',
		'user_firm_rights',
		'user_service_rights',
		'user_site',
	);

	public function up()
	{
		foreach ($this->tables as $table) {
			$this->dropColumn($table,'deleted');
			$this->dropColumn($table."_version",'deleted');

			$this->dropForeignKey("{$table}_aid_fk",$table."_version");
		}
	}

	public function down()
	{
		foreach ($this->tables as $table) {
			$this->addColumn($table,'deleted','tinyint(1) unsigned not null');
			$this->addColumn($table."_version",'deleted','tinyint(1) unsigned not null');

			$this->addForeignKey("{$table}_aid_fk",$table."_version","id",$table,"id");
		}
	}
}
