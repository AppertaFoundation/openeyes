<?php

class m180104_110527_add_new_cols_to_risk extends OEMigration
{
	public function up()
	{



	    $this->createOETable('ophciexamination_risk_set_entry',
            array(
                'id' => 'pk',
                'ophciexamination_risk_id' => 'int(11)',
                'gender' => 'varchar(1) NULL',
                'age_min' => 'int(3) unsigned',
                'age_max' => 'int(3) unsigned',

            ),true);

        $this->createOETable('ophciexamination_risk_set_assignment',
            array(
                'id' => 'pk',
                'ophciexamination_risk_entry_id' => 'int(11)',
                'risk_set_id' => 'int(11)',
            ),true
        );
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


        $this->addForeignKey('ophciexamination_risk_set_assignment_risk_e', 'ophciexamination_risk_set_assignment', 'ophciexamination_risk_entry_id', 'ophciexamination_risk_set_entry', 'id');
            $this->addForeignKey('ophciexamination_risk_set_assignment_set', 'ophciexamination_risk_set_assignment', 'risk_set_id', 'ophciexamination_risk_set', 'id');

        $this->addForeignKey('ophciexamination_risk_set_e', 'ophciexamination_risk_set_entry', 'ophciexamination_risk_id', 'ophciexamination_risk', 'id');
	}

	public function down()
	{

	    $this->dropForeignKey('ophciexamination_risk_set_subspecialty', 'ophciexamination_risk_set');
	    $this->dropForeignKey('ophciexamination_risk_set_firm', 'ophciexamination_risk_set');

        $this->addColumn('ophciexamination_risk', "required", "tinyint(1) NOT NULL");
        $this->addColumn('ophciexamination_risk_version', "required", "tinyint(1) NOT NULL");

        $this->dropForeignKey('ophciexamination_risk_set_assignment_risk_e', 'ophciexamination_risk_set_assignment');
        $this->dropForeignKey('ophciexamination_risk_set_assignment_set', 'ophciexamination_risk_set_assignment');

        $this->dropForeignKey('ophciexamination_risk_set_e', 'ophciexamination_risk_set_entry');

        $this->dropOETable('ophciexamination_risk_set_entry', true);
        $this->dropOETable('ophciexamination_risk_set_assignment', true);
        $this->dropOETable('ophciexamination_risk_set', true);

	}
}