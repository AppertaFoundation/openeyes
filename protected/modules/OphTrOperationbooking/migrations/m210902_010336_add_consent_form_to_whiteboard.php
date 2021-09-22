<?php

class m210902_010336_add_consent_form_to_whiteboard extends OEMigration
{
    public function safeUp()
    {
        $this->addOEColumn('ophtroperationbooking_whiteboard', 'consent_procedure_id', 'int(10) unsigned', true);
        $this->addForeignKey('ophtroperationbooking_whiteboard_consent_procedure_fk', 'ophtroperationbooking_whiteboard', 'consent_procedure_id', 'et_ophtrconsent_procedure', 'id');
    }

    public function safeDown()
    {
        $this->dropForeignKey('ophtroperationbooking_whiteboard_consent_procedure_fk', 'ophtroperationbooking_whiteboard');
        $this->dropOEColumn('ophtroperationbooking_whiteboard', 'consent_procedure_id', true);
    }
}
