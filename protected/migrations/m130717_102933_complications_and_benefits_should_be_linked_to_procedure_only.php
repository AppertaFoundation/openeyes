<?php

class m130717_102933_complications_and_benefits_should_be_linked_to_procedure_only extends CDbMigration
{
	public function up()
	{
		$benefits = array();

		foreach (Yii::app()->db->createCommand()->select("*")->from("procedure_benefit")->queryAll() as $row) {
			if (!isset($benefits[$row['proc_id']])) {
				$benefits[$row['proc_id']] = array();
			}

			if (in_array($row['benefit_id'],$benefits[$row['proc_id']])) {
				$this->delete('procedure_benefit',"id={$row['id']}");
			} else {
				$benefits[$row['proc_id']][] = $row['benefit_id'];
			}
		}

		$this->dropForeignKey('procedure_benefit_subspecialty_id_fk','procedure_benefit');
		$this->dropIndex('procedure_benefit_subspecialty_id_fk','procedure_benefit');
		$this->dropColumn('procedure_benefit','subspecialty_id');

		$complications = array();

		foreach (Yii::app()->db->createCommand()->select("*")->from("procedure_complication")->queryAll() as $row) {
			if (!isset($complications[$row['proc_id']])) {
				$complications[$row['proc_id']] = array();
			}

			if (in_array($row['complication_id'],$complications[$row['proc_id']])) {
				$this->delete('procedure_complication',"id={$row['id']}");
			} else {
				$complications[$row['proc_id']][] = $row['complication_id'];
			}
		}

		$this->dropForeignKey('procedure_complication_subspecialty_id_fk','procedure_complication');
		$this->dropIndex('procedure_complication_subspecialty_id_fk','procedure_complication');
		$this->dropColumn('procedure_complication','subspecialty_id');
	}

	public function down()
	{
		$this->addColumn('procedure_benefit','subspecialty_id','int(10) unsigned NULL');
		$this->createIndex('procedure_benefit_subspecialty_id_fk','procedure_benefit','subspecialty_id');
		$this->addForeignKey('procedure_benefit_subspecialty_id_fk','procedure_benefit','subspecialty_id','subspecialty','id');

		$this->addColumn('procedure_complication','subspecialty_id','int(10) unsigned NULL');
		$this->createIndex('procedure_complication_subspecialty_id_fk','procedure_complication','subspecialty_id');
		$this->addForeignKey('procedure_complication_subspecialty_id_fk','procedure_complication','subspecialty_id','subspecialty','id');
	}
}
