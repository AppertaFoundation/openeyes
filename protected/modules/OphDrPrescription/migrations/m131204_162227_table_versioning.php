<?php

class m131204_162227_table_versioning extends OEMigration
{
    public function up()
    {
        $this->versionExistingTable('et_ophdrprescription_details');
        $this->versionExistingTable('ophdrprescription_item');
        $this->versionExistingTable('ophdrprescription_item_taper');
    }

    public function down()
    {
        $this->dropTable('et_ophdrprescription_details_version');
        $this->dropTable('ophdrprescription_item_version');
        $this->dropTable('ophdrprescription_item_taper_version');
    }
}
