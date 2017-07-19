<?php

class m140729_122009_transaction_version_tables extends OEMigration
{
    public function up()
    {
        $this->versionExistingTable('ophindnaextraction_dnatests_transaction');
    }

    public function down()
    {
        $this->dropTable('ophindnaextraction_dnatests_transaction_version');
    }
}
