<?php

class m140610_144738_common_previous_operations extends CDbMigration
{
	public function up()
	{
		foreach (array('Trabeculectomy','Bleb revision','Needling','Aqueous shunt','Removal of supramid','Aqueous shunt revision','Surgical PI','Goniotomy','Trabeculotomy','Text box') as $operation) {
			$this->insert('common_previous_operation',array('name'=>$operation));
		}
	}

	public function down()
	{
		foreach (array('Trabeculectomy','Bleb revision','Needling','Aqueous shunt','Removal of supramid','Aqueous shunt revision','Surgical PI','Goniotomy','Trabeculotomy','Text box') as $operation) {
			$this->delete('common_previous_operation',"name = '$operation'");
		}
	}
}
