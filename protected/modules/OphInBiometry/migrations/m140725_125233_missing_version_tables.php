<?php

class m140725_125233_missing_version_tables extends OEMigration
{
    public function up()
    {
        $this->versionExistingTable('ophinbiometry_lenstype_lens');
        $this->versionExistingTable('et_ophinbiometry_biometrydat');
        $this->versionExistingTable('et_ophinbiometry_lenstype');
    }

    public function down()
    {
        $this->dropTable('ophinbiometry_lenstype_lens_version');
        $this->dropTable('et_ophinbiometry_biometrydat_version');
        $this->dropTable('et_ophinbiometry_lenstype_version');
    }
}
