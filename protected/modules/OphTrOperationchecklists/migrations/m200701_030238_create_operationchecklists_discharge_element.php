<?php

class m200701_030238_create_operationchecklists_discharge_element extends OEMigration
{
    public function up()
    {
        // Creating Table
        $this->createOETable('et_ophtroperationchecklists_discharge', array(
            'id' => 'pk',
            'event_id' => 'int unsigned NOT NULL',
        ), true);

        // Add Foreign Key
        $this->addForeignKey(
            'et_ophtroperationchecklists_discharge_ev_fk',
            'et_ophtroperationchecklists_discharge',
            'event_id',
            'event',
            'id'
        );
    }

    public function down()
    {
        $this->dropOETable('et_ophtroperationchecklists_discharge', true);
    }
}
