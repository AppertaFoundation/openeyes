<?php

class m140128_095036_table_versioning extends OEMigration
{
    public function up()
    {
        $this->addColumn('ophtrlaser_site_laser', 'active', 'boolean not null default true');
        $this->update('ophtrlaser_site_laser', array('active' => new CDbExpression('not(deleted)')));
        $this->dropColumn('ophtrlaser_site_laser', 'deleted');

        $this->versionExistingTable('et_ophtrlaser_anteriorseg');
        $this->versionExistingTable('et_ophtrlaser_comments');
        $this->versionExistingTable('et_ophtrlaser_fundus');
        $this->versionExistingTable('et_ophtrlaser_posteriorpo');
        $this->versionExistingTable('et_ophtrlaser_site');
        $this->versionExistingTable('et_ophtrlaser_treatment');
        $this->versionExistingTable('ophtrlaser_laserprocedure');
        $this->versionExistingTable('ophtrlaser_laserprocedure_assignment');
        $this->versionExistingTable('ophtrlaser_type');
        $this->versionExistingTable('ophtrlaser_site_laser');
    }

    public function down()
    {
        $this->dropTable('et_ophtrlaser_anteriorseg_version');
        $this->dropTable('et_ophtrlaser_comments_version');
        $this->dropTable('et_ophtrlaser_fundus_version');
        $this->dropTable('et_ophtrlaser_posteriorpo_version');
        $this->dropTable('et_ophtrlaser_site_version');
        $this->dropTable('et_ophtrlaser_treatment_version');
        $this->dropTable('ophtrlaser_laserprocedure_version');
        $this->dropTable('ophtrlaser_laserprocedure_assignment_version');
        $this->dropTable('ophtrlaser_site_laser_version');

        $this->addColumn('ophtrlaser_site_laser', 'deleted', "tinyint(1) DEFAULT '0'");
        $this->update('ophtrlaser_site_laser', array('deleted' => new CDbExpression('not(active)')));
        $this->dropColumn('ophtrlaser_site_laser', 'active');
    }
}
