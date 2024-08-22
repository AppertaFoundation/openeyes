<?php

class m220124_042928_add_contact_id_to_decision_making_process_contact extends OEMigration
{
    public function safeUp()
    {
        $this->addOEColumn('ophtrconsent_others_involved_decision_making_process_contact', 'contact_id', 'int(10) unsigned default NULL AFTER element_id', false);

        $this->addForeignKey('fk_ophtrconsent_others_in_dec_making_proc_con_con', 'ophtrconsent_others_involved_decision_making_process_contact', 'contact_id', 'contact', 'id');
    }

    public function SafeDown()
    {
        $this->dropForeignKey('fk_ophtrconsent_others_in_dec_making_proc_con_con', 'ophtrconsent_others_involved_decision_making_process_contact');

        $this->dropOEColumn('ophtrconsent_others_involved_decision_making_process_contact', 'contact_id', false);
    }
}
