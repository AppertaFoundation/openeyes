<?php

class m131223_161503_new_checkboxes extends CDbMigration
{
    public function up()
    {
        $this->addColumn('et_ophciexamination_visualacuity', 'left_unable_to_assess', 'tinyint(1) unsigned not null');
        $this->addColumn('et_ophciexamination_visualacuity', 'right_unable_to_assess', 'tinyint(1) unsigned not null');
        $this->addColumn('et_ophciexamination_visualacuity', 'left_eye_missing', 'tinyint(1) unsigned not null');
        $this->addColumn('et_ophciexamination_visualacuity', 'right_eye_missing', 'tinyint(1) unsigned not null');
    }

    public function down()
    {
        $this->dropColumn('et_ophciexamination_visualacuity', 'left_unable_to_assess');
        $this->dropColumn('et_ophciexamination_visualacuity', 'right_unable_to_assess');
        $this->dropColumn('et_ophciexamination_visualacuity', 'left_eye_missing');
        $this->dropColumn('et_ophciexamination_visualacuity', 'right_eye_missing');
    }
}
