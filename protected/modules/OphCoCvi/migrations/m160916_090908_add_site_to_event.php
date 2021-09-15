<?php

class m160916_090908_add_site_to_event extends CDbMigration
{
    public function up()
    {
        $this->addColumn('et_ophcocvi_eventinfo', 'site_id', 'int(10) unsigned');
        $this->addForeignKey('et_ophcocvi_eventinfo_site_fk', 'et_ophcocvi_eventinfo', 'site_id', 'site', 'id');
        $this->addColumn('et_ophcocvi_eventinfo_version', 'site_id', 'int(10) unsigned');
    }

    public function down()
    {
        $this->dropForeignKey('et_ophcocvi_eventinfo_site_fk');
        $this->dropColumn('et_ophcocvi_eventinfo_version', 'site_id');
        $this->dropColumn('et_ophcocvi_eventinfo', 'site_id');
    }

}
