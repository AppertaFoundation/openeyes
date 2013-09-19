<?php

class m130118_122927_link_procedure_complications_with_services extends CDbMigration
{
	public function up()
	{
		$this->addColumn('procedure_complication','service_id','int(10) unsigned NOT NULL');
		$this->createIndex('procedure_complication_service_id_fk','procedure_complication','service_id');
		$this->addForeignKey('procedure_complication_service_id_fk','procedure_complication','service_id','service','id');

		$this->addColumn('procedure_benefit','service_id','int(10) unsigned NOT NULL');
		$this->createIndex('procedure_benefit_service_id_fk','procedure_benefit','service_id');
		$this->addForeignKey('procedure_benefit_service_id_fk','procedure_benefit','service_id','service','id');
	}

	public function down()
	{
		$this->dropForeignKey('procedure_complication_service_id_fk','procedure_complication');
		$this->dropIndex('procedure_complication_service_id_fk','procedure_complication');
		$this->dropColumn('procedure_complication','service_id');

		$this->dropForeignKey('procedure_benefit_service_id_fk','procedure_benefit');
		$this->dropIndex('procedure_benefit_service_id_fk','procedure_benefit');
		$this->dropColumn('procedure_benefit','service_id');
	}
}
