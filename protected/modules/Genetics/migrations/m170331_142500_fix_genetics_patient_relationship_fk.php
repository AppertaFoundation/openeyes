<?php

class m170331_142500_fix_genetics_patient_relationship_fk extends OEMigration
{

    // Use safeUp/safeDown to do migration with transaction
    public function safeUp()
    {
        $this->dropForeignKey('genetics_patient_relationship_ibfk_4', 'genetics_patient_relationship');
        $this->addForeignKey('genetics_patient_relationship_ibfk_4', 'genetics_patient_relationship', 'related_to_id', 'genetics_patient', 'id');
    }

    public function safeDown()
    {
    }
}
