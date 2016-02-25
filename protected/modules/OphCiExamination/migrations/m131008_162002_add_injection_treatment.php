<?php

class m131008_162002_add_injection_treatment extends CDbMigration
{
    public function up()
    {
        // although these columns will be populated by foreign key ids to values in intravitireal injection module table,
        // are not setting the foreign key constraint, to allow the examination module to work without the intravitreal
        // injection module being installed
        $this->addColumn('et_ophciexamination_injectionmanagementcomplex', 'left_treatment_id', 'int(10) unsigned');
        $this->addColumn('et_ophciexamination_injectionmanagementcomplex', 'right_treatment_id', 'int(10) unsigned');
    }

    public function down()
    {
        $this->dropColumn('et_ophciexamination_injectionmanagementcomplex', 'left_treatment_id');
        $this->dropColumn('et_ophciexamination_injectionmanagementcomplex', 'right_treatment_id');
    }

    /*
    // Use safeUp/safeDown to do migration with transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}
