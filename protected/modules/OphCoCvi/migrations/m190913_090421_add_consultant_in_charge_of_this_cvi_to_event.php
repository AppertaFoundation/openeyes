<?php

class m190913_090421_add_consultant_in_charge_of_this_cvi_to_event extends CDbMigration
{
    public function up()
    {
        $this->addColumn('et_ophcocvi_eventinfo', 'consultant_in_charge_of_this_cvi_id', 'int(10) unsigned');
        $this->addForeignKey('et_ophcocvi_eventinfo_consultant_in_charge_of_this_cvi_id_fk', 'et_ophcocvi_eventinfo', 'consultant_in_charge_of_this_cvi_id', 'firm', 'id');
        $this->addColumn('et_ophcocvi_eventinfo_version', 'consultant_in_charge_of_this_cvi_id', 'int(10) unsigned');
    }

    public function down()
    {
        $this->dropForeignKey('et_ophcocvi_eventinfo_consultant_in_charge_of_this_cvi_id_fk');
        $this->dropColumn('et_ophcocvi_eventinfo_version', 'consultant_in_charge_of_this_cvi_id');
        $this->dropColumn('et_ophcocvi_eventinfo', 'consultant_in_charge_of_this_cvi_id');
    }
}
