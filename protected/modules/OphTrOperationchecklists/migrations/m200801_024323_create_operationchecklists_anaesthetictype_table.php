<?php

class m200801_024323_create_operationchecklists_anaesthetictype_table extends OEMigration
{
    public function up()
    {
        // anaesthetic type
        $this->createOETable('ophtroperationchecklists_anaesthetic_anaesthetic_type', array(
            'id' => 'pk',
            'procedurelist_id' => 'int(11)',
            'anaesthetic_type_id' => 'int(10) unsigned NOT NULL',
        ), true);

        $this->addForeignKey(
            'ophtroperationchecklists_anaesthetic_anaesthetic_type_atid_fk',
            'ophtroperationchecklists_anaesthetic_anaesthetic_type',
            'anaesthetic_type_id',
            'anaesthetic_type',
            'id'
        );

        $this->addForeignKey(
            'ophtroperationchecklists_anaesthetic_anaesthetic_type_oid_fk',
            'ophtroperationchecklists_anaesthetic_anaesthetic_type',
            'procedurelist_id',
            'et_ophtroperationchecklists_procedurelist',
            'id'
        );
    }

    public function down()
    {
        $this->dropForeignKey('ophtroperationchecklists_anaesthetic_anaesthetic_type_atid', 'ophtroperationchecklists_anaesthetic_anaesthetic_type');
        $this->dropForeignKey('ophtroperationchecklists_anaesthetic_anaesthetic_type_oid_fk', 'ophtroperationchecklists_anaesthetic_anaesthetic_type');
        $this->dropOETable('ophtroperationchecklists_anaesthetic_anaesthetic_type', true);
    }
}
