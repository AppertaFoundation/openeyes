<?php

class m120302_172414_rename_specialty_table_sub_specialty extends CDbMigration
{
	public function up()
	{
		$this->renameTable('specialty','subspecialty');

		$this->dropForeignKey('specialty_created_user_id_fk','subspecialty');
		$this->dropForeignKey('specialty_last_modified_user_id_fk','subspecialty');
		$this->dropIndex('specialty_last_modified_user_id_fk','subspecialty');
		$this->dropIndex('specialty_created_user_id_fk','subspecialty');
		$this->createIndex('subspecialty_last_modified_user_id_fk','subspecialty','last_modified_user_id');
		$this->createIndex('subspecialty_created_user_id_fk','subspecialty','created_user_id');
		$this->addForeignKey('subspecialty_created_user_id_fk','subspecialty','created_user_id','user','id');
		$this->addForeignKey('subspecialty_last_modified_user_id_fk','subspecialty','last_modified_user_id','user','id');

		$this->dropForeignKey('service_specialty_assignment_ibfk_2','service_specialty_assignment');
		$this->dropForeignKey('service_specialty_assignment_created_user_id_fk','service_specialty_assignment');
		$this->dropForeignKey('service_specialty_assignment_ibfk_1','service_specialty_assignment');
		$this->dropForeignKey('service_specialty_assignment_last_modified_user_id_fk','service_specialty_assignment');
		$this->dropIndex('specialty_id','service_specialty_assignment');
		$this->dropIndex('service_specialty_assignment_last_modified_user_id_fk','service_specialty_assignment');
		$this->dropIndex('service_specialty_assignment_created_user_id_fk','service_specialty_assignment');
		$this->renameTable('service_specialty_assignment','service_subspecialty_assignment');
		$this->renameColumn('service_subspecialty_assignment','specialty_id','subspecialty_id');
		$this->createIndex('subspecialty_id','service_subspecialty_assignment','subspecialty_id');
		$this->createIndex('service_subspecialty_assignment_last_modified_user_id_fk','service_subspecialty_assignment','last_modified_user_id');
		$this->createIndex('service_subspecialty_assignment_created_user_id_fk','service_subspecialty_assignment','created_user_id');
		$this->addForeignKey('service_subspecialty_assignment_ibfk_2','service_subspecialty_assignment','subspecialty_id','subspecialty','id');
		$this->addForeignKey('service_subspecialty_assignment_created_user_id_fk','service_subspecialty_assignment','created_user_id','user','id');
		$this->addForeignKey('service_subspecialty_assignment_ibfk_1','service_subspecialty_assignment','service_id','service','id');
		$this->addForeignKey('service_subspecialty_assignment_last_modified_user_id_fk','service_subspecialty_assignment','last_modified_user_id','user','id');

		$this->dropForeignKey('common_ophthalmic_disorder_ibfk_2','common_ophthalmic_disorder');
		$this->dropIndex('specialty_id','common_ophthalmic_disorder');
		$this->renameColumn('common_ophthalmic_disorder','specialty_id','subspecialty_id');
		$this->createIndex('subspecialty_id','common_ophthalmic_disorder','subspecialty_id');
		$this->addForeignKey('common_ophthalmic_disorder_ibfk_2','common_ophthalmic_disorder','subspecialty_id','subspecialty','id');

		$this->dropForeignKey('service_specialty_assignment_id','firm');
		$this->dropIndex('service_specialty_assignment_id','firm');
		$this->renameColumn('firm','service_specialty_assignment_id','service_subspecialty_assignment_id');
		$this->createIndex('service_subspecialty_assignment_id','firm','service_subspecialty_assignment_id');
		$this->addForeignKey('service_subspecialty_assignment_id','firm','service_subspecialty_assignment_id','service_subspecialty_assignment','id');

		$this->dropForeignKey('letter_template_ibfk_1','letter_template');
		$this->dropIndex('specialty_id','letter_template');
		$this->renameColumn('letter_template','specialty_id','subspecialty_id');
		$this->createIndex('subspecialty_id','letter_template','subspecialty_id');
		$this->addForeignKey('letter_template_ibfk_1','letter_template','subspecialty_id','subspecialty','id');

		$this->dropForeignKey('phrase_by_specialty_specialty_fk','phrase_by_specialty');
		$this->dropForeignKey('phrase_by_specialty_created_user_id_fk','phrase_by_specialty');
		$this->dropForeignKey('phrase_by_specialty_last_modified_user_id_fk','phrase_by_specialty');
		$this->dropForeignKey('phrase_by_specialty_phrase_name_id_fk','phrase_by_specialty');
		$this->dropForeignKey('phrase_by_specialty_section_fk','phrase_by_specialty');
		$this->dropIndex('phrase_by_specialty_specialty_fk','phrase_by_specialty');
		$this->dropIndex('phrase_by_specialty_section_fk','phrase_by_specialty');
		$this->dropIndex('phrase_by_specialty_last_modified_user_id_fk','phrase_by_specialty');
		$this->dropIndex('phrase_by_specialty_created_user_id_fk','phrase_by_specialty');
		$this->dropIndex('phrase_by_specialty_phrase_name_id_fk','phrase_by_specialty');
		$this->renameTable('phrase_by_specialty','phrase_by_subspecialty');
		$this->renameColumn('phrase_by_subspecialty','specialty_id','subspecialty_id');
		$this->createIndex('phrase_by_subspecialty_subspecialty_fk','phrase_by_subspecialty','subspecialty_id');
		$this->createIndex('phrase_by_subspecialty_section_fk','phrase_by_subspecialty','section_id');
		$this->createIndex('phrase_by_subspecialty_last_modified_user_id_fk','phrase_by_subspecialty','last_modified_user_id');
		$this->createIndex('phrase_by_subspecialty_created_user_id_fk','phrase_by_subspecialty','created_user_id');
		$this->createIndex('phrase_by_subspecialty_phrase_name_id_fk','phrase_by_subspecialty','phrase_name_id');
		$this->addForeignKey('phrase_by_subspecialty_subspecialty_fk','phrase_by_subspecialty','subspecialty_id','subspecialty','id');
		$this->addForeignKey('phrase_by_subspecialty_created_user_id_fk','phrase_by_subspecialty','created_user_id','user','id');
		$this->addForeignKey('phrase_by_subspecialty_last_modified_user_id_fk','phrase_by_subspecialty','last_modified_user_id','user','id');
		$this->addForeignKey('phrase_by_subspecialty_phrase_name_id_fk','phrase_by_subspecialty','phrase_name_id','phrase_name','id');
		$this->addForeignKey('phrase_by_subspecialty_section_fk','phrase_by_subspecialty','section_id','section','id');

		$this->dropForeignKey('proc_specialty_assignment_created_user_id_fk','proc_specialty_assignment');
		$this->dropForeignKey('proc_specialty_assignment_ibfk_1','proc_specialty_assignment');
		$this->dropForeignKey('proc_specialty_assignment_ibfk_2','proc_specialty_assignment');
		$this->dropForeignKey('proc_specialty_assignment_last_modified_user_id_fk','proc_specialty_assignment');
		$this->dropIndex('proc_id','proc_specialty_assignment');
		$this->dropIndex('specialty_id','proc_specialty_assignment');
		$this->dropIndex('proc_specialty_assignment_last_modified_user_id_fk','proc_specialty_assignment');
		$this->dropIndex('proc_specialty_assignment_created_user_id_fk','proc_specialty_assignment');
		$this->renameTable('proc_specialty_assignment','proc_subspecialty_assignment');
		$this->renameColumn('proc_subspecialty_assignment','specialty_id','subspecialty_id');
		$this->createIndex('proc_subspecialty_assignment_proc_id_fk','proc_subspecialty_assignment','proc_id');
		$this->createIndex('proc_subspecialty_assignment_subspecialty_id_fk','proc_subspecialty_assignment','subspecialty_id');
		$this->createIndex('proc_subspecialty_assignment_last_modified_user_id_fk','proc_subspecialty_assignment','last_modified_user_id');
		$this->createIndex('proc_subspecialty_assignment_created_user_id_fk','proc_subspecialty_assignment','created_user_id');
		$this->addForeignKey('proc_subspecialty_assignment_created_user_id_fk','proc_subspecialty_assignment','created_user_id','user','id');
		$this->addForeignKey('proc_subspecialty_assignment_ibfk_1','proc_subspecialty_assignment','proc_id','proc','id');
		$this->addForeignKey('proc_subspecialty_assignment_ibfk_2','proc_subspecialty_assignment','subspecialty_id','subspecialty','id');
		$this->addForeignKey('proc_subspecialty_assignment_last_modified_user_id_fk','proc_subspecialty_assignment','last_modified_user_id','user','id');

		$this->dropForeignKey('proc_specialty_subsection_assignment_created_user_id_fk','proc_specialty_subsection_assignment');
		$this->dropForeignKey('proc_specialty_subsection_assignment_ibfk_1','proc_specialty_subsection_assignment');
		$this->dropForeignKey('proc_specialty_subsection_assignment_ibfk_2','proc_specialty_subsection_assignment');
		$this->dropForeignKey('proc_specialty_subsection_assignment_last_modified_user_id_fk','proc_specialty_subsection_assignment');
		$this->dropIndex('proc_id','proc_specialty_subsection_assignment');
		$this->dropIndex('specialty_subsection_id','proc_specialty_subsection_assignment');
		$this->dropIndex('proc_specialty_subsection_assignment_last_modified_user_id_fk','proc_specialty_subsection_assignment');
		$this->dropIndex('proc_specialty_subsection_assignment_created_user_id_fk','proc_specialty_subsection_assignment');
		$this->renameTable('proc_specialty_subsection_assignment','proc_subspecialty_subsection_assignment');
		$this->renameColumn('proc_subspecialty_subsection_assignment','specialty_subsection_id','subspecialty_subsection_id');
		$this->createIndex('proc_subspecialty_subsection_assignment_proc_id_fk','proc_subspecialty_subsection_assignment','proc_id');
		$this->createIndex('pssa_subspecialty_subsection_id_fk','proc_subspecialty_subsection_assignment','subspecialty_subsection_id');
		$this->createIndex('proc_subspecialty_subsection_assignment_last_modified_user_id_fk','proc_subspecialty_subsection_assignment','last_modified_user_id');
		$this->createIndex('proc_subspecialty_subsection_assignment_created_user_id_fk','proc_subspecialty_subsection_assignment','created_user_id');
		$this->addForeignKey('proc_subspecialty_subsection_assignment_proc_id_fk','proc_subspecialty_subsection_assignment','proc_id','proc','id');
		$this->addForeignKey('pssa_subspecialty_subsection_id_fk','proc_subspecialty_subsection_assignment','subspecialty_subsection_id','specialty_subsection','id');
		$this->addForeignKey('proc_subspecialty_subsection_assignment_last_modified_user_id_fk','proc_subspecialty_subsection_assignment','last_modified_user_id','user','id');
		$this->addForeignKey('proc_subspecialty_subsection_assignment_created_user_id_fk','proc_subspecialty_subsection_assignment','created_user_id','user','id');

		$this->dropForeignKey('referral_ibfk_1','referral');
		$this->dropIndex('referral_ibfk_1','referral');
		$this->renameColumn('referral','service_specialty_assignment_id','service_subspecialty_assignment_id');
		$this->createIndex('referral_service_subspecialty_assignment_id_fk','referral','service_subspecialty_assignment_id');
		$this->addForeignKey('referral_service_subspecialty_assignment_id_fk','referral','service_subspecialty_assignment_id','service_subspecialty_assignment','id');

		$this->dropForeignKey('site_element_type_ibfk_2','site_element_type');
		$this->dropIndex('specialty_id','site_element_type');
		$this->renameColumn('site_element_type','specialty_id','subspecialty_id');
		$this->createIndex('site_element_type_subspecialty_id_fk','site_element_type','subspecialty_id');
		$this->addForeignKey('site_element_type_subspecialty_id_fk','site_element_type','subspecialty_id','subspecialty','id');

		$this->dropForeignKey('specialty_fk','specialty_subsection');
		$this->dropForeignKey('specialty_subsection_created_user_id_fk','specialty_subsection');
		$this->dropForeignKey('specialty_subsection_last_modified_user_id_fk','specialty_subsection');
		$this->dropIndex('service_id','specialty_subsection');
		$this->dropIndex('specialty_subsection_last_modified_user_id_fk','specialty_subsection');
		$this->dropIndex('specialty_subsection_created_user_id_fk','specialty_subsection');
		$this->renameTable('specialty_subsection','subspecialty_subsection');
		$this->renameColumn('subspecialty_subsection','specialty_id','subspecialty_id');
		$this->createIndex('subspecialty_subsection_subspecialty_id_fk','subspecialty_subsection','subspecialty_id');
		$this->createIndex('subspecialty_subsection_last_modified_user_id_fk','subspecialty_subsection','last_modified_user_id');
		$this->createIndex('subspecialty_subsection_created_user_id_fk','subspecialty_subsection','created_user_id');
		$this->addForeignKey('subspecialty_subsection_subspecialty_id_fk','subspecialty_subsection','subspecialty_id','subspecialty','id');
		$this->addForeignKey('subspecialty_subsection_last_modified_user_id_fk','subspecialty_subsection','last_modified_user_id','user','id');
		$this->addForeignKey('subspecialty_subsection_created_user_id_fk','subspecialty_subsection','created_user_id','user','id');
	}

	public function down()
	{
		$this->renameTable('subspecialty','specialty');

		$this->dropForeignKey('subspecialty_created_user_id_fk','specialty');
		$this->dropForeignKey('subspecialty_last_modified_user_id_fk','specialty');
		$this->dropIndex('subspecialty_last_modified_user_id_fk','specialty');
		$this->dropIndex('subspecialty_created_user_id_fk','specialty');
		$this->createIndex('specialty_last_modified_user_id_fk','specialty','last_modified_user_id');
		$this->createIndex('specialty_created_user_id_fk','specialty','created_user_id');
		$this->addForeignKey('specialty_created_user_id_fk','specialty','created_user_id','user','id');
		$this->addForeignKey('specialty_last_modified_user_id_fk','specialty','last_modified_user_id','user','id');

		$this->dropForeignKey('service_subspecialty_assignment_ibfk_2','service_subspecialty_assignment');
		$this->dropForeignKey('service_subspecialty_assignment_created_user_id_fk','service_subspecialty_assignment');
		$this->dropForeignKey('service_subspecialty_assignment_ibfk_1','service_subspecialty_assignment');
		$this->dropForeignKey('service_subspecialty_assignment_last_modified_user_id_fk','service_subspecialty_assignment');
		$this->dropIndex('subspecialty_id','service_subspecialty_assignment');
		$this->dropIndex('service_subspecialty_assignment_last_modified_user_id_fk','service_subspecialty_assignment');
		$this->dropIndex('service_subspecialty_assignment_created_user_id_fk','service_subspecialty_assignment');
		$this->renameTable('service_subspecialty_assignment','service_specialty_assignment');
		$this->renameColumn('service_specialty_assignment','subspecialty_id','specialty_id');
		$this->createIndex('specialty_id','service_specialty_assignment','specialty_id');
		$this->createIndex('service_specialty_assignment_last_modified_user_id_fk','service_specialty_assignment','last_modified_user_id');
		$this->createIndex('service_specialty_assignment_created_user_id_fk','service_specialty_assignment','created_user_id');
		$this->addForeignKey('service_specialty_assignment_ibfk_2','service_specialty_assignment','specialty_id','specialty','id');
		$this->addForeignKey('service_specialty_assignment_created_user_id_fk','service_specialty_assignment','created_user_id','user','id');
		$this->addForeignKey('service_specialty_assignment_ibfk_1','service_specialty_assignment','service_id','service','id');
		$this->addForeignKey('service_specialty_assignment_last_modified_user_id_fk','service_specialty_assignment','last_modified_user_id','user','id');

		$this->dropForeignKey('common_ophthalmic_disorder_ibfk_2','common_ophthalmic_disorder');
		$this->dropIndex('subspecialty_id','common_ophthalmic_disorder');
		$this->renameColumn('common_ophthalmic_disorder','subspecialty_id','specialty_id');
		$this->createIndex('specialty_id','common_ophthalmic_disorder','specialty_id');
		$this->addForeignKey('common_ophthalmic_disorder_ibfk_2','common_ophthalmic_disorder','specialty_id','specialty','id');

		$this->dropForeignKey('service_subspecialty_assignment_id','firm');
		$this->dropIndex('service_subspecialty_assignment_id','firm');
		$this->renameColumn('firm','service_subspecialty_assignment_id','service_specialty_assignment_id');
		$this->createIndex('service_specialty_assignment_id','firm','service_specialty_assignment_id');
		$this->addForeignKey('service_specialty_assignment_id','firm','service_specialty_assignment_id','service_specialty_assignment','id');

		$this->dropForeignKey('letter_template_ibfk_1','letter_template');
		$this->dropIndex('subspecialty_id','letter_template');
		$this->renameColumn('letter_template','subspecialty_id','specialty_id');
		$this->createIndex('specialty_id','letter_template','specialty_id');
		$this->addForeignKey('letter_template_ibfk_1','letter_template','specialty_id','specialty','id');

		$this->dropForeignKey('phrase_by_subspecialty_subspecialty_fk','phrase_by_subspecialty');
		$this->dropForeignKey('phrase_by_subspecialty_created_user_id_fk','phrase_by_subspecialty');
		$this->dropForeignKey('phrase_by_subspecialty_last_modified_user_id_fk','phrase_by_subspecialty');
		$this->dropForeignKey('phrase_by_subspecialty_phrase_name_id_fk','phrase_by_subspecialty');
		$this->dropForeignKey('phrase_by_subspecialty_section_fk','phrase_by_subspecialty');
		$this->dropIndex('phrase_by_subspecialty_subspecialty_fk','phrase_by_subspecialty');
		$this->dropIndex('phrase_by_subspecialty_section_fk','phrase_by_subspecialty');
		$this->dropIndex('phrase_by_subspecialty_last_modified_user_id_fk','phrase_by_subspecialty');
		$this->dropIndex('phrase_by_subspecialty_created_user_id_fk','phrase_by_subspecialty');
		$this->dropIndex('phrase_by_subspecialty_phrase_name_id_fk','phrase_by_subspecialty');
		$this->renameTable('phrase_by_subspecialty','phrase_by_specialty');
		$this->renameColumn('phrase_by_specialty','subspecialty_id','specialty_id');
		$this->createIndex('phrase_by_specialty_specialty_fk','phrase_by_specialty','specialty_id');
		$this->createIndex('phrase_by_specialty_section_fk','phrase_by_specialty','section_id');
		$this->createIndex('phrase_by_specialty_last_modified_user_id_fk','phrase_by_specialty','last_modified_user_id');
		$this->createIndex('phrase_by_specialty_created_user_id_fk','phrase_by_specialty','created_user_id');
		$this->createIndex('phrase_by_specialty_phrase_name_id_fk','phrase_by_specialty','phrase_name_id');
		$this->addForeignKey('phrase_by_specialty_specialty_fk','phrase_by_specialty','specialty_id','specialty','id');
		$this->addForeignKey('phrase_by_specialty_created_user_id_fk','phrase_by_specialty','created_user_id','user','id');
		$this->addForeignKey('phrase_by_specialty_last_modified_user_id_fk','phrase_by_specialty','last_modified_user_id','user','id');
		$this->addForeignKey('phrase_by_specialty_phrase_name_id_fk','phrase_by_specialty','phrase_name_id','phrase_name','id');
		$this->addForeignKey('phrase_by_specialty_section_fk','phrase_by_specialty','section_id','section','id');

		$this->dropForeignKey('proc_subspecialty_assignment_created_user_id_fk','proc_subspecialty_assignment');
		$this->dropForeignKey('proc_subspecialty_assignment_ibfk_1','proc_subspecialty_assignment');
		$this->dropForeignKey('proc_subspecialty_assignment_ibfk_2','proc_subspecialty_assignment');
		$this->dropForeignKey('proc_subspecialty_assignment_last_modified_user_id_fk','proc_subspecialty_assignment');

		$this->dropIndex('proc_subspecialty_assignment_proc_id_fk','proc_subspecialty_assignment');
		$this->dropIndex('proc_subspecialty_assignment_subspecialty_id_fk','proc_subspecialty_assignment');
		$this->dropIndex('proc_subspecialty_assignment_last_modified_user_id_fk','proc_subspecialty_assignment');
		$this->dropIndex('proc_subspecialty_assignment_created_user_id_fk','proc_subspecialty_assignment');
		$this->renameTable('proc_subspecialty_assignment','proc_specialty_assignment');
		$this->renameColumn('proc_specialty_assignment','subspecialty_id','specialty_id');
		$this->createIndex('proc_id','proc_specialty_assignment','proc_id');
		$this->createIndex('specialty_id','proc_specialty_assignment','specialty_id');
		$this->createIndex('proc_specialty_assignment_last_modified_user_id_fk','proc_specialty_assignment','last_modified_user_id');
		$this->createIndex('proc_specialty_assignment_created_user_id_fk','proc_specialty_assignment','created_user_id');
		$this->addForeignKey('proc_specialty_assignment_created_user_id_fk','proc_specialty_assignment','created_user_id','user','id');
		$this->addForeignKey('proc_specialty_assignment_ibfk_1','proc_specialty_assignment','proc_id','proc','id');
		$this->addForeignKey('proc_specialty_assignment_ibfk_2','proc_specialty_assignment','specialty_id','specialty','id');
		$this->addForeignKey('proc_specialty_assignment_last_modified_user_id_fk','proc_specialty_assignment','last_modified_user_id','user','id');

		$this->dropForeignKey('subspecialty_subsection_subspecialty_id_fk','subspecialty_subsection');
		$this->dropForeignKey('subspecialty_subsection_created_user_id_fk','subspecialty_subsection');
		$this->dropForeignKey('subspecialty_subsection_last_modified_user_id_fk','subspecialty_subsection');
		$this->dropIndex('subspecialty_subsection_subspecialty_id_fk','subspecialty_subsection');
		$this->dropIndex('subspecialty_subsection_last_modified_user_id_fk','subspecialty_subsection');
		$this->dropIndex('subspecialty_subsection_created_user_id_fk','subspecialty_subsection');
		$this->renameTable('subspecialty_subsection','specialty_subsection');
		$this->renameColumn('specialty_subsection','subspecialty_id','specialty_id');
		$this->createIndex('service_id','specialty_subsection','specialty_id');
		$this->createIndex('specialty_subsection_last_modified_user_id_fk','specialty_subsection','last_modified_user_id');
		$this->createIndex('specialty_subsection_created_user_id_fk','specialty_subsection','created_user_id');
		$this->addForeignKey('specialty_fk','specialty_subsection','specialty_id','specialty','id');
		$this->addForeignKey('specialty_subsection_last_modified_user_id_fk','specialty_subsection','last_modified_user_id','user','id');
		$this->addForeignKey('specialty_subsection_created_user_id_fk','specialty_subsection','created_user_id','user','id');

		$this->dropForeignKey('proc_subspecialty_subsection_assignment_created_user_id_fk','proc_subspecialty_subsection_assignment');
		$this->dropForeignKey('proc_subspecialty_subsection_assignment_proc_id_fk','proc_subspecialty_subsection_assignment');
		$this->dropForeignKey('pssa_subspecialty_subsection_id_fk','proc_subspecialty_subsection_assignment');
		$this->dropForeignKey('proc_subspecialty_subsection_assignment_last_modified_user_id_fk','proc_subspecialty_subsection_assignment');
		$this->dropIndex('proc_subspecialty_subsection_assignment_proc_id_fk','proc_subspecialty_subsection_assignment');
		$this->dropIndex('pssa_subspecialty_subsection_id_fk','proc_subspecialty_subsection_assignment');
		$this->dropIndex('proc_subspecialty_subsection_assignment_last_modified_user_id_fk','proc_subspecialty_subsection_assignment');
		$this->dropIndex('proc_subspecialty_subsection_assignment_created_user_id_fk','proc_subspecialty_subsection_assignment');
		$this->renameTable('proc_subspecialty_subsection_assignment','proc_specialty_subsection_assignment');
		$this->renameColumn('proc_specialty_subsection_assignment','subspecialty_subsection_id','specialty_subsection_id');
		$this->createIndex('proc_id','proc_specialty_subsection_assignment','proc_id');
		$this->createIndex('specialty_subsection_id','proc_specialty_subsection_assignment','specialty_subsection_id');
		$this->createIndex('proc_specialty_subsection_assignment_last_modified_user_id_fk','proc_specialty_subsection_assignment','last_modified_user_id');
		$this->createIndex('proc_specialty_subsection_assignment_created_user_id_fk','proc_specialty_subsection_assignment','created_user_id');
		$this->addForeignKey('proc_specialty_subsection_assignment_ibfk_1','proc_specialty_subsection_assignment','proc_id','proc','id');
		$this->addForeignKey('proc_specialty_subsection_assignment_ibfk_2','proc_specialty_subsection_assignment','specialty_subsection_id','specialty_subsection','id');
		$this->addForeignKey('proc_specialty_subsection_assignment_last_modified_user_id_fk','proc_specialty_subsection_assignment','last_modified_user_id','user','id');
		$this->addForeignKey('proc_specialty_subsection_assignment_created_user_id_fk','proc_specialty_subsection_assignment','created_user_id','user','id');

		$this->dropForeignKey('referral_service_subspecialty_assignment_id_fk','referral');
		$this->dropIndex('referral_service_subspecialty_assignment_id_fk','referral');
		$this->renameColumn('referral','service_subspecialty_assignment_id','service_specialty_assignment_id');
		$this->createIndex('referral_ibfk_1','referral','service_specialty_assignment_id');
		$this->addForeignKey('referral_ibfk_1','referral','service_specialty_assignment_id','service_specialty_assignment','id');

		$this->dropForeignKey('site_element_type_subspecialty_id_fk','site_element_type');
		$this->dropIndex('site_element_type_subspecialty_id_fk','site_element_type');
		$this->renameColumn('site_element_type','subspecialty_id','specialty_id');
		$this->createIndex('specialty_id','site_element_type','specialty_id');
		$this->addForeignKey('site_element_type_ibfk_2','site_element_type','specialty_id','specialty','id');
	}
}
