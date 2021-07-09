<?php

class m190929_082327_add_diagnosis_not_covered_table extends OEMigration
{
    public function up()
    {
        $this->createOETable(
            'et_ophcocvi_clinicinfo_diagnosis_not_covered',
            array(
                'id' => 'pk',
                'element_id' => 'int(10) unsigned',
                'disorder_id' => 'int(10) unsigned',
                'eye_id' => 'tinyint(1) unsigned',
                'code' => 'varchar(20) DEFAULT NULL',
                'main_cause' => 'tinyint(1) unsigned',
                'disorder_type' => 'tinyint(1) unsigned',
            ),
            true
        );
    }


    public function down()
    {
        $this->dropOETable('et_ophcocvi_clinicinfo_diagnosis_not_covered', true);
    }
}