<?php

class m210913_150001_add_et_withdraw extends OEMigration
{
    public function safeUp()
    {
        if ($this->dbConnection->schema->getTable('et_ophtrconsent_withdrawal', true) === null) {
            $event_type_id = $this->getIdOfEventTypeByClassName('OphTrConsent');

            $this->createOETable("et_ophtrconsent_withdrawal", [
                'id' => 'pk',
                'event_id' => 'INT(10) UNSIGNED',
                'withdrawn' => 'BOOLEAN NOT NULL DEFAULT 0',
                'withdrawal_reason' => 'VARCHAR(4096) DEFAULT NULL',
                'signature_id' => 'INT(11) NULL'
            ], true);

            $this->addForeignKey("fk_et_ophtrc_withdrawal_event", "et_ophtrconsent_withdrawal", "event_id", "event", "id");
            $this->addForeignKey('fk_et_ophtrc_withdrawal_signature', "et_ophtrconsent_withdrawal", 'signature_id', 'ophtrconsent_signature', 'id');

            $this->insertOEElementType(array('Element_OphTrConsent_Withdrawal' => array(
                'name' => 'Withdrawal of consent',
                'required' => 0,
                'default' => 0,
                'display_order' => 0,
            )), $event_type_id);
        } else {
            $this->addOEColumn('et_ophtrconsent_withdrawal', 'withdrawal_reason', 'VARCHAR(4096) DEFAULT NULL', true);
            $this->addOEColumn('et_ophtrconsent_withdrawal', 'signature_id', 'INT(11) NULL', true);
            $this->addForeignKey('fk_et_ophtrc_withdrawal_signature', "et_ophtrconsent_withdrawal", 'signature_id', 'ophtrconsent_signature', 'id');
        }
    }

    public function safeDown()
    {
        $this->deleteElementType('OphTrConsent', 'Element_OphTrConsent_Withdrawal');
        $this->dropForeignKey('fk_et_ophtrc_withdrawal_event', 'et_ophtrconsent_withdrawal');
        $this->dropOETable('et_ophtrconsent_withdrawal', true);
    }
}
