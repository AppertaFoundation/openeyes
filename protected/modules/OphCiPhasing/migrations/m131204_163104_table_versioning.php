<?php

class m131204_163104_table_versioning extends OEMigration
{
    public function up()
    {
        $this->addColumn('ophciphasing_instrument', 'active', 'boolean not null default true');

        $this->versionExistingTable('et_ophciphasing_intraocularpressure');
        $this->versionExistingTable('ophciphasing_instrument');
        $this->versionExistingTable('ophciphasing_reading');
    }

    public function down()
    {
        $this->dropColumn('ophciphasing_instrument', 'deleted');

        $this->dropTable('et_ophciphasing_intraocularpressure_version');
        $this->dropTable('ophciphasing_instrument_version');
        $this->dropTable('ophciphasing_reading_version');
    }
}
