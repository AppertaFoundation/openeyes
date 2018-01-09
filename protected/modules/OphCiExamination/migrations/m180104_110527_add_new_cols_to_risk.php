<?php

class m180104_110527_add_new_cols_to_risk extends OEMigration
{
	public function up()
	{
        $this->addColumn('ophciexamination_risk', 'gender', 'varchar(1) NULL AFTER name');
        $this->addColumn('ophciexamination_risk_version', 'gender', 'varchar(1) NULL AFTER name');

        $this->addColumn('ophciexamination_risk', 'age_min', 'int(3) unsigned AFTER gender');
        $this->addColumn('ophciexamination_risk_version', 'age_min', 'int(3) unsigned AFTER gender');

        $this->addColumn('ophciexamination_risk', 'age_max', 'int(3) unsigned AFTER age_min');
        $this->addColumn('ophciexamination_risk_version', 'age_max', 'int(3) unsigned AFTER age_min');

	    $this->createOETable('ophciexamination_risk_set',
	        array(
	            'id' => 'pk',
	            'name' => 'varchar(255) NULL',
                'firm_id' => 'int(10) unsigned',
                'subspecialty_id' =>  'int(10) unsigned',
            ),true
        );


        $this->addForeignKey('ophciexamination_risk_set_subspecialty', 'ophciexamination_risk_set', 'subspecialty_id', 'subspecialty', 'id');
        $this->addForeignKey('ophciexamination_risk_set_firm', 'ophciexamination_risk_set', 'firm_id', 'firm', 'id');

	    $this->dropColumn("ophciexamination_risk", "required");
	    $this->dropColumn("ophciexamination_risk_version", "required");


        $this->createOETable('ophciexamination_risk_set_assignment',
            array(
                'id' => 'pk',
                'ophciexamination_risk_id' => 'int(11)',
                'risk_set_id' => 'int(11)',
            ),true
        );

        $this->addForeignKey('ophciexamination_risk_set_assignment_risk', 'ophciexamination_risk_set_assignment', 'ophciexamination_risk_id', 'ophciexamination_risk', 'id');
        $this->addForeignKey('ophciexamination_risk_set_assignment_set', 'ophciexamination_risk_set_assignment', 'risk_set_id', 'ophciexamination_risk_set', 'id');
	}

	public function down()
	{

	    $this->dropForeignKey('ophciexamination_risk_set_subspecialty', 'ophciexamination_risk_set');
	    $this->dropForeignKey('ophciexamination_risk_set_firm', 'ophciexamination_risk_set');
        $this->dropForeignKey('ophciexamination_risk_set_risk', 'ophciexamination_risk_set');
        $this->dropForeignKey('ophciexamination_risk_set_assignment_risk', 'ophciexamination_risk_set_assignment');
        $this->dropForeignKey('ophciexamination_risk_set_assignment_set', 'ophciexamination_risk_set_assignment');

        $this->addColumn('ophciexamination_risk', "required", "tinyint(1) NOT NULL");

        $this->dropOETable('ophciexamination_risk_set', true);
        $this->dropOETable('ophciexamination_risk_set_risk', true);
	}
}