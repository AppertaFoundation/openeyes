<?php

class m150720_132838_allergy_element_table extends OEMigration
{
    public function up()
    {
        $this->createOETable('et_ophciexamination_allergy', array(
            'id' => 'pk',
            'event_id' => 'int(10) unsigned',
        ), true);
        $this->dropColumn('patient_allergy_assignment', 'event_id');
        $this->dropColumn('patient_allergy_assignment_version', 'event_id');
    }

    public function down()
    {
        $this->dropOETable('et_ophciexamination_allergy', true);
        $this->addColumn('patient_allergy_assignment', 'event_id', 'int(10) unsigned');
        $this->addColumn('patient_allergy_assignment_version', 'event_id', 'int(10) unsigned');
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
