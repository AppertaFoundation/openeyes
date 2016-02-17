<?php

class m140627_165400_really_remove_glaucoma_inv_ref2 extends CDbMigration
{
    public function up()
    {
        $this->dropColumn('et_ophciexamination_currentmanagementplan_version', 'other_service');
        $this->dropColumn('et_ophciexamination_currentmanagementplan_version', 'refraction');
        $this->dropColumn('et_ophciexamination_currentmanagementplan_version', 'lva');
        $this->dropColumn('et_ophciexamination_currentmanagementplan_version', 'orthoptics');
        $this->dropColumn('et_ophciexamination_currentmanagementplan_version', 'cl_clinic');
        $this->dropColumn('et_ophciexamination_currentmanagementplan_version', 'vf');
        $this->dropColumn('et_ophciexamination_currentmanagementplan_version', 'us');
        $this->dropColumn('et_ophciexamination_currentmanagementplan_version', 'biometry');
        $this->dropColumn('et_ophciexamination_currentmanagementplan_version', 'oct');
        $this->dropColumn('et_ophciexamination_currentmanagementplan_version', 'hrt');
        $this->dropColumn('et_ophciexamination_currentmanagementplan_version', 'disc_photos');
        $this->dropColumn('et_ophciexamination_currentmanagementplan_version', 'edt');
    }

    public function down()
    {
        $this->addColumn('et_ophciexamination_currentmanagementplan_version', 'other_service', 'tinyint(1) unsigned not null');
        $this->addColumn('et_ophciexamination_currentmanagementplan_version', 'refraction', 'tinyint(1) unsigned not null');
        $this->addColumn('et_ophciexamination_currentmanagementplan_version', 'lva', 'tinyint(1) unsigned not null');
        $this->addColumn('et_ophciexamination_currentmanagementplan_version', 'orthoptics', 'tinyint(1) unsigned not null');
        $this->addColumn('et_ophciexamination_currentmanagementplan_version', 'cl_clinic', 'tinyint(1) unsigned not null');
        $this->addColumn('et_ophciexamination_currentmanagementplan_version', 'vf', 'tinyint(1) unsigned not null');
        $this->addColumn('et_ophciexamination_currentmanagementplan_version', 'us', 'tinyint(1) unsigned not null');
        $this->addColumn('et_ophciexamination_currentmanagementplan_version', 'biometry', 'tinyint(1) unsigned not null');
        $this->addColumn('et_ophciexamination_currentmanagementplan_version', 'oct', 'tinyint(1) unsigned not null');
        $this->addColumn('et_ophciexamination_currentmanagementplan_version', 'hrt', 'tinyint(1) unsigned not null');
        $this->addColumn('et_ophciexamination_currentmanagementplan_version', 'disc_photos', 'tinyint(1) unsigned not null');
        $this->addColumn('et_ophciexamination_currentmanagementplan_version', 'edt', 'tinyint(1) unsigned not null');
    }
}
