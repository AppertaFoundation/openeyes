<?php

class m140620_165400_really_remove_glaucoma_inv_ref extends CDbMigration
{
    public function up()
    {
        $this->dropColumn('et_ophciexamination_currentmanagementplan', 'other_service');
        $this->dropColumn('et_ophciexamination_currentmanagementplan', 'refraction');
        $this->dropColumn('et_ophciexamination_currentmanagementplan', 'lva');
        $this->dropColumn('et_ophciexamination_currentmanagementplan', 'orthoptics');
        $this->dropColumn('et_ophciexamination_currentmanagementplan', 'cl_clinic');
        $this->dropColumn('et_ophciexamination_currentmanagementplan', 'vf');
        $this->dropColumn('et_ophciexamination_currentmanagementplan', 'us');
        $this->dropColumn('et_ophciexamination_currentmanagementplan', 'biometry');
        $this->dropColumn('et_ophciexamination_currentmanagementplan', 'oct');
        $this->dropColumn('et_ophciexamination_currentmanagementplan', 'hrt');
        $this->dropColumn('et_ophciexamination_currentmanagementplan', 'disc_photos');
        $this->dropColumn('et_ophciexamination_currentmanagementplan', 'edt');
    }

    public function down()
    {
        $this->addColumn('et_ophciexamination_currentmanagementplan', 'other_service', 'tinyint(1) unsigned not null');
        $this->addColumn('et_ophciexamination_currentmanagementplan', 'refraction', 'tinyint(1) unsigned not null');
        $this->addColumn('et_ophciexamination_currentmanagementplan', 'lva', 'tinyint(1) unsigned not null');
        $this->addColumn('et_ophciexamination_currentmanagementplan', 'orthoptics', 'tinyint(1) unsigned not null');
        $this->addColumn('et_ophciexamination_currentmanagementplan', 'cl_clinic', 'tinyint(1) unsigned not null');
        $this->addColumn('et_ophciexamination_currentmanagementplan', 'vf', 'tinyint(1) unsigned not null');
        $this->addColumn('et_ophciexamination_currentmanagementplan', 'us', 'tinyint(1) unsigned not null');
        $this->addColumn('et_ophciexamination_currentmanagementplan', 'biometry', 'tinyint(1) unsigned not null');
        $this->addColumn('et_ophciexamination_currentmanagementplan', 'oct', 'tinyint(1) unsigned not null');
        $this->addColumn('et_ophciexamination_currentmanagementplan', 'hrt', 'tinyint(1) unsigned not null');
        $this->addColumn('et_ophciexamination_currentmanagementplan', 'disc_photos', 'tinyint(1) unsigned not null');
        $this->addColumn('et_ophciexamination_currentmanagementplan', 'edt', 'tinyint(1) unsigned not null');
    }
}
