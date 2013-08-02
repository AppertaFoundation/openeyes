<?php

class m130726_135103_commissioning_body_table_names extends CDbMigration
{
	public function up()
	{
		$this->dropForeignKey('commissioningbody_cid_fk','commissioningbody');
		$this->dropForeignKey('commissioningbody_created_user_id_fk','commissioningbody');
		$this->dropForeignKey('commissioningbody_last_modified_user_id_fk','commissioningbody');
		$this->dropForeignKey('commissioningbody_tid_fk','commissioningbody');

		$this->dropIndex('commissioningbody_last_modified_user_id_fk','commissioningbody');
		$this->dropIndex('commissioningbody_created_user_id_fk','commissioningbody');
		$this->dropIndex('commissioningbody_tid_fk','commissioningbody');
		$this->dropIndex('commissioningbody_cid_fk','commissioningbody');

		$this->renameTable('commissioningbody','commissioning_body');
		$this->renameColumn('commissioning_body','commissioningbody_type_id','commissioning_body_type_id');

		$this->addForeignKey('commissioning_body_last_modified_user_id_fk','commissioning_body','last_modified_user_id','user','id');
		$this->addForeignKey('commissioning_body_created_user_id_fk','commissioning_body','created_user_id','user','id');
		$this->addForeignKey('commissioning_body_commissioning_body_type_id_fk','commissioning_body','commissioning_body_type_id','commissioningbody_type','id');
		$this->addForeignKey('commissioning_body_contact_id_fk','commissioning_body','contact_id','contact','id');

		$this->dropForeignKey('commissioningbody_patient_assignment_cbid_fk','commissioningbody_patient_assignment');
		$this->dropForeignKey('commissioningbody_patient_assignment_created_user_id_fk','commissioningbody_patient_assignment');
		$this->dropForeignKey('commissioningbody_patient_assignment_last_modified_user_id_fk','commissioningbody_patient_assignment');
		$this->dropForeignKey('commissioningbody_patient_assignment_pid_fk','commissioningbody_patient_assignment');

		$this->dropIndex('commissioningbody_patient_assignment_cbid_fk','commissioningbody_patient_assignment');
		$this->dropIndex('commissioningbody_patient_assignment_created_user_id_fk','commissioningbody_patient_assignment');
		$this->dropIndex('commissioningbody_patient_assignment_last_modified_user_id_fk','commissioningbody_patient_assignment');
		$this->dropIndex('commissioningbody_patient_assignment_pid_fk','commissioningbody_patient_assignment');

		$this->renameTable('commissioningbody_patient_assignment','commissioning_body_patient_assignment');
		$this->renameColumn('commissioning_body_patient_assignment','commissioningbody_id','commissioning_body_id');

		$this->addForeignKey('commissioning_body_patient_assignment_cbid_fk','commissioning_body_patient_assignment','commissioning_body_id','commissioning_body','id');
		$this->addForeignKey('commissioning_body_patient_assignment_created_user_id_fk','commissioning_body_patient_assignment','created_user_id','user','id');
		$this->addForeignKey('commissioning_body_patient_assignment_last_modified_user_id_fk','commissioning_body_patient_assignment','last_modified_user_id','user','id');
		$this->addForeignKey('commissioning_body_patient_assignment_pid_fk','commissioning_body_patient_assignment','patient_id','patient','id');

		$this->dropForeignKey('commissioningbody_practice_assignment_last_modified_user_id_fk','commissioningbody_practice_assignment');
		$this->dropForeignKey('commissioningbody_practice_assignment_created_user_id_fk','commissioningbody_practice_assignment');
		$this->dropForeignKey('commissioningbody_practice_assignment_cbid_fk','commissioningbody_practice_assignment');
		$this->dropForeignKey('commissioningbody_practice_assignment_pid_fk','commissioningbody_practice_assignment');

		$this->dropIndex('commissioningbody_practice_assignment_last_modified_user_id_fk','commissioningbody_practice_assignment');
		$this->dropIndex('commissioningbody_practice_assignment_created_user_id_fk','commissioningbody_practice_assignment');
		$this->dropIndex('commissioningbody_practice_assignment_cbid_fk','commissioningbody_practice_assignment');
		$this->dropIndex('commissioningbody_practice_assignment_pid_fk','commissioningbody_practice_assignment');

		$this->renameTable('commissioningbody_practice_assignment','commissioning_body_practice_assignment');
		$this->renameColumn('commissioning_body_practice_assignment','commissioningbody_id','commissioning_body_id');

		$this->addForeignKey('commissioning_body_practice_assignment_last_modified_user_id_fk','commissioning_body_practice_assignment','last_modified_user_id','user','id');
		$this->addForeignKey('commissioning_body_practice_assignment_created_user_id_fk','commissioning_body_practice_assignment','created_user_id','user','id');
		$this->addForeignKey('commissioning_body_practice_assignment_cbid_fk','commissioning_body_practice_assignment','commissioning_body_id','commissioning_body','id');
		$this->addForeignKey('commissioning_body_practice_assignment_pid_fk','commissioning_body_practice_assignment','practice_id','practice','id');

		$this->dropForeignKey('commissioningbody_type_created_user_id_fk','commissioningbody_type');
		$this->dropForeignKey('commissioningbody_type_last_modified_user_id_fk','commissioningbody_type');

		$this->dropIndex('commissioningbody_type_created_user_id_fk','commissioningbody_type');
		$this->dropIndex('commissioningbody_type_last_modified_user_id_fk','commissioningbody_type');

		$this->renameTable('commissioningbody_type','commissioning_body_type');

		$this->addForeignKey('commissioning_body_type_created_user_id_fk','commissioning_body_type','created_user_id','user','id');
		$this->addForeignKey('commissioning_body_type_last_modified_user_id_fk','commissioning_body_type','last_modified_user_id','user','id');

		$this->dropForeignKey('commissioningbodyservice_cbid_fk','commissioningbodyservice');
		$this->dropForeignKey('commissioningbodyservice_cid_fk','commissioningbodyservice');
		$this->dropForeignKey('commissioningbodyservice_created_user_id_fk','commissioningbodyservice');
		$this->dropForeignKey('commissioningbodyservice_last_modified_user_id_fk','commissioningbodyservice');
		$this->dropForeignKey('commissioningbodyservice_tid_fk','commissioningbodyservice');

		$this->dropIndex('commissioningbodyservice_cbid_fk','commissioningbodyservice');
		$this->dropIndex('commissioningbodyservice_cid_fk','commissioningbodyservice');
		$this->dropIndex('commissioningbodyservice_created_user_id_fk','commissioningbodyservice');
		$this->dropIndex('commissioningbodyservice_last_modified_user_id_fk','commissioningbodyservice');
		$this->dropIndex('commissioningbodyservice_tid_fk','commissioningbodyservice');

		$this->renameTable('commissioningbodyservice','commissioning_body_service');

		$this->renameColumn('commissioning_body_service','commissioningbodyservice_type_id','commissioning_body_service_type_id');
		$this->renameColumn('commissioning_body_service','commissioningbody_id','commissioning_body_id');

		$this->addForeignKey('commissioning_body_service_cbid_fk','commissioning_body_service','commissioning_body_id','commissioning_body','id');
		$this->addForeignKey('commissioning_body_service_cid_fk','commissioning_body_service','contact_id','contact','id');
		$this->addForeignKey('commissioning_body_service_created_user_id_fk','commissioning_body_service','created_user_id','user','id');
		$this->addForeignKey('commissioning_body_service_last_modified_user_id_fk','commissioning_body_service','last_modified_user_id','user','id');
		$this->addForeignKey('commissioning_body_service_tid_fk','commissioning_body_service','commissioning_body_service_type_id','commissioningbodyservice_type','id');

		$this->dropForeignKey('commissioningbodyservice_type_created_user_id_fk','commissioningbodyservice_type');
		$this->dropForeignKey('commissioningbodyservice_type_last_modified_user_id_fk','commissioningbodyservice_type');

		$this->dropIndex('commissioningbodyservice_type_created_user_id_fk','commissioningbodyservice_type');
		$this->dropIndex('commissioningbodyservice_type_last_modified_user_id_fk','commissioningbodyservice_type');

		$this->renameTable('commissioningbodyservice_type','commissioning_body_service_type');

		$this->addForeignKey('commissioning_body_service_type_created_user_id_fk','commissioning_body_service_type','created_user_id','user','id');
		$this->addForeignKey('commissioning_body_service_type_last_modified_user_id_fk','commissioning_body_service_type','last_modified_user_id','user','id');
	}

	public function down()
	{
	}
}
