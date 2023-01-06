<?php

class m221128_172700_disable_opbooking_disable_both_eyes_for_relevant_subs extends OEMigration
{
    private $subs = array('Oculoplastics', 'Strabismus', 'Adnexal');

    public function up()
    {
        if ($this->verifyIndexExists('setting_subspecialty','setting_subspecialty_UN')) {
            $this->execute("DROP INDEX setting_subspecialty_UN ON setting_subspecialty");
        }
        $this->execute("ALTER TABLE setting_subspecialty ADD CONSTRAINT setting_subspecialty_UN UNIQUE KEY (subspecialty_id,element_type_id,`key`);");

        // Originally this was supposed to use an INSERT ON DUPLICATE KEY UPDATE command, but for some reason, even with the unique contraint, it still added duplicate keys!
        // I've left the unique constraint in, as it should be there. But there is possibly a bug in mariaDB that is causing it not to trigger.
        // If the underlying issue is resolved, then the index should function correctly in future migrations

        foreach ($this->subs as $sub) {
            $sub_id = $this->dbConnection->createCommand("SELECT id FROM subspecialty WHERE `name` = :sub_name")->queryScalar([':sub_name' => $sub]);

            $this->execute(
                "UPDATE IGNORE setting_subspecialty
                SET `value` = 'off'
                WHERE `key` = 'opbooking_disable_both_eyes' AND subspecialty_id = :sub_id;",
                [':sub_id' => $sub_id]
            );
        }
    }

    public function safeDown()
    {
        echo('Down not supported for this migration');
    }
}
