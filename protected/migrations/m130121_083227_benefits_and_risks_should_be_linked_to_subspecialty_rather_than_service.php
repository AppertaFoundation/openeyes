<?php

class m130121_083227_benefits_and_risks_should_be_linked_to_subspecialty_rather_than_service extends CDbMigration
{
	public function up()
	{
		$this->dropForeignKey('procedure_benefit_service_id_fk','procedure_benefit');
		$this->dropIndex('procedure_benefit_service_id_fk','procedure_benefit');

		$this->renameColumn('procedure_benefit','service_id','subspecialty_id');

		foreach (Service::model()->findAll() as $service) {
			$ssa = ServiceSubspecialtyAssignment::model()->find('service_id=?',array($service->id));
			$this->update('procedure_benefit',array('subspecialty_id'=>$ssa->subspecialty_id),'subspecialty_id='.$service->id);
		}

		$this->createIndex('procedure_benefit_subspecialty_id_fk','procedure_benefit','subspecialty_id');
		$this->addForeignKey('procedure_benefit_subspecialty_id_fk','procedure_benefit','subspecialty_id','subspecialty','id');

		$this->dropForeignKey('procedure_complication_service_id_fk','procedure_complication');
		$this->dropIndex('procedure_complication_service_id_fk','procedure_complication');

		$this->renameColumn('procedure_complication','service_id','subspecialty_id');

		foreach (Service::model()->findAll() as $service) {
			$ssa = ServiceSubspecialtyAssignment::model()->find('service_id=?',array($service->id));
			$this->update('procedure_complication',array('subspecialty_id'=>$ssa->subspecialty_id),'subspecialty_id='.$service->id);
		}

		$this->createIndex('procedure_complication_subspecialty_id_fk','procedure_complication','subspecialty_id');
		$this->addForeignKey('procedure_complication_subspecialty_id_fk','procedure_complication','subspecialty_id','subspecialty','id');
	}

	public function down()
	{
		$this->dropForeignKey('procedure_benefit_subspecialty_id_fk','procedure_benefit');
		$this->dropIndex('procedure_benefit_subspecialty_id_fk','procedure_benefit');

		$this->renameColumn('procedure_benefit','subspecialty_id','service_id');

		foreach (Subspecialty::model()->findAll() as $subspecialty) {
			$ssa = ServiceSubspecialtyAssignment::model()->find('subspecialty_id=?',array($subspecialty->id));
			$this->update('procedure_benefit',array('service_id'=>$ssa->service_id),'service_id='.$subspecialty->id);
		}

		$this->createIndex('procedure_benefit_service_id_fk','procedure_benefit','service_id');
		$this->addForeignKey('procedure_benefit_service_id_fk','procedure_benefit','service_id','service','id');

		$this->dropForeignKey('procedure_complication_subspecialty_id_fk','procedure_complication');
		$this->dropIndex('procedure_complication_subspecialty_id_fk','procedure_complication');

		$this->renameColumn('procedure_complication','subspecialty_id','service_id');

		foreach (Subspecialty::model()->findAll() as $subspecialty) {
			$ssa = ServiceSubspecialtyAssignment::model()->find('subspecialty_id=?',array($subspecialty->id));
			$this->update('procedure_complication',array('service_id'=>$ssa->service_id),'service_id='.$subspecialty->id);
		}

		$this->createIndex('procedure_complication_service_id_fk','procedure_complication','service_id');
		$this->addForeignKey('procedure_complication_service_id_fk','procedure_complication','service_id','service','id');
	}
}
