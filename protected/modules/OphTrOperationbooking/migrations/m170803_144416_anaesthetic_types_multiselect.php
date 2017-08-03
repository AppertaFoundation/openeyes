<?php

class m170803_144416_anaesthetic_types_multiselect extends OEMigration
{
	public function up()
	{

	    $this->createOETable('ophtroperationbooking_anaesthetic_anaesthetic_type',array(
            'id' => 'pk',
            'et_ophtroperationbooking_operation_id' => 'int(10) unsigned NOT NULL',
            'anaesthetic_type_id' => 'int(10) unsigned NOT NULL',
        ), true);

        $this->addForeignKey('ophtroperationbook_anaesthetic_type_to_anaest_type', 'ophtroperationbooking_anaesthetic_anaesthetic_type','anaesthetic_type_id',
            'anaesthetic_type','id');

        $this->addForeignKey('ophtroperationbook_anaesthetic_type_to_el', 'ophtroperationbooking_anaesthetic_anaesthetic_type', 'et_ophtroperationbooking_operation_id',
            'et_ophtroperationbooking_operation', 'id');

        $this->dropForeignKey('et_ophtroperationbooking_operation_anaesthetic_type_id_fk', 'et_ophtroperationbooking_operation');
	}

	public function down()
	{
		echo "m170803_144416_anaesthetic_types_multiselect does not support migration down.\n";
		return false;
	}
}