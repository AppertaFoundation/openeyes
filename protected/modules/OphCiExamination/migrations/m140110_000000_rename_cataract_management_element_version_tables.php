<?php

class m140110_000000_rename_cataract_management_element_version_tables extends CDbMigration
{
    public function up()
    {
        $this->renameTable('ophciexamination_cataractsurgicalmanagement_suitable_for_surgeon', 'ophciexamination_cataractsurgicalmanagement_sfsurgeon');
    }

    public function down()
    {
        $this->renameTable('ophciexamination_cataractsurgicalmanagement_sfsurgeon', 'ophciexamination_cataractsurgicalmanagement_suitable_for_surgeon');
    }
}
