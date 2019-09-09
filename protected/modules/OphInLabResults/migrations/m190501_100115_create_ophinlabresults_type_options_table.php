<?php

class m190501_100115_create_ophinlabresults_type_options_table extends OEMigration
{
	public function up()
	{
	    $this->createOETable('ophinlabresults_type_options',[
            'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY',
            'value' => 'varchar(255) NOT NULL',
            'type' => 'int(11) NOT NULL',
        ]);
        $this->addForeignKey('lab_results_type_options_fk_lab_results_type', 'ophinlabresults_type_options',
            'type','ophinlabresults_type' , 'id');
	}

	public function down()
	{
		$this->dropForeignKey('lab_results_type_options_fk_lab_results_type', 'ophinlabresults_type_options');
		$this->dropOETable('ophinlabresults_type_options');
	}
}