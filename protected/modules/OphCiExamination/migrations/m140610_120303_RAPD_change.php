<?php

class m140610_120303_RAPD_change extends CDbMigration
{
    public function up()
    {
        $this->addColumn('ophciexamination_pupillaryabnormalities_abnormality', 'active', 'tinyint(1) unsigned not null default 1');
        $this->addColumn('ophciexamination_pupillaryabnormalities_abnormality_version', 'active', 'tinyint(1) unsigned not null default 1');
        $this->update('ophciexamination_pupillaryabnormalities_abnormality', array('active' => 0), "name = 'RAPD'");

        $this->addColumn('et_ophciexamination_visualacuity', 'left_rapd', 'tinyint(1) unsigned not null');
        $this->addColumn('et_ophciexamination_visualacuity', 'right_rapd', 'tinyint(1) unsigned not null');

        $this->addColumn('et_ophciexamination_visualacuity_version', 'left_rapd', 'tinyint(1) unsigned not null');
        $this->addColumn('et_ophciexamination_visualacuity_version', 'right_rapd', 'tinyint(1) unsigned not null');
    }

    public function down()
    {
        $this->dropColumn('et_ophciexamination_visualacuity', 'left_rapd');
        $this->dropColumn('et_ophciexamination_visualacuity', 'right_rapd');

        $this->dropColumn('et_ophciexamination_visualacuity_version', 'left_rapd');
        $this->dropColumn('et_ophciexamination_visualacuity_version', 'right_rapd');

        $this->dropColumn('ophciexamination_pupillaryabnormalities_abnormality', 'active');
        $this->dropColumn('ophciexamination_pupillaryabnormalities_abnormality_version', 'active');
    }
}
