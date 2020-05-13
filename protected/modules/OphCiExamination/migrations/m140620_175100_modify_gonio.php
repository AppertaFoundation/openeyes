<?php

class m140620_175100_modify_gonio extends CDbMigration
{
    public function up()
    {
        $this->dropForeignKey('et_ophciexam_overallmanagementplan_lgonio_id_fk', 'et_ophciexamination_overallmanagementplan');
        $this->dropForeignKey('et_ophciexam_overallmanagementplan_rgonio_id_fk', 'et_ophciexamination_overallmanagementplan');

        $this->dropColumn('et_ophciexamination_overallmanagementplan', 'right_gonio_id');
        $this->dropColumn('et_ophciexamination_overallmanagementplan_version', 'right_gonio_id');
        $this->renameColumn('et_ophciexamination_overallmanagementplan', 'left_gonio_id', 'gonio_id');
        $this->renameColumn('et_ophciexamination_overallmanagementplan_version', 'left_gonio_id', 'gonio_id');
        $this->addForeignKey(
            'et_ophciexam_overallmanagementplan_gonio_id_fk',
            'et_ophciexamination_overallmanagementplan',
            'gonio_id',
            'ophciexamination_visitinterval',
            'id'
        );
    }

    public function down()
    {
        $this->dropForeignKey('et_ophciexam_overallmanagementplan_gonio_id_fk', 'et_ophciexamination_overallmanagementplan');
        $this->renameColumn('et_ophciexamination_overallmanagementplan', 'gonio_id', 'left_gonio_id');
        $this->renameColumn('et_ophciexamination_overallmanagementplan_version', 'gonio_id', 'left_gonio_id');
        $this->addColumn('et_ophciexamination_overallmanagementplan', 'right_gonio_id', 'int(11) DEFAULT NULL AFTER left_gonio_id');
        $this->addColumn('et_ophciexamination_overallmanagementplan_version', 'right_gonio_id', 'int(11) DEFAULT NULL AFTER left_gonio_id');
        $this->addForeignKey(
            'et_ophciexam_overallmanagementplan_lgonio_id_fk',
            'et_ophciexamination_overallmanagementplan',
            'left_gonio_id',
            'ophciexamination_visitinterval',
            'id'
        );
        $this->addForeignKey(
            'et_ophciexam_overallmanagementplan_rgonio_id_fk',
            'et_ophciexamination_overallmanagementplan',
            'right_gonio_id',
            'ophciexamination_visitinterval',
            'id'
        );
    }
}
