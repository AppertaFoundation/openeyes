<?php

class m140704_113301_overallman_iop_range_to_relation extends OEMigration
{
    public function up()
    {
        $this->createOETable(
            'ophciexamination_targetiop',
            array('id' => 'pk',
                'name' => 'string NOT NULL',
                'display_order' => 'int(10) unsigned NOT NULL DEFAULT 1',
                'active' => 'tinyint(1) not null DEFAULT 1',
            ),
            true
        );

        $this->createIndex('tiop_unique_val', 'ophciexamination_targetiop', 'name', true);

        $migrations_path = dirname(__FILE__);
        $this->initialiseData($migrations_path);

        $this->renameColumn('et_ophciexamination_overallmanagementplan', 'left_target_iop', 'left_target_iop_id');
        $this->renameColumn('et_ophciexamination_overallmanagementplan', 'right_target_iop', 'right_target_iop_id');
        $this->renameColumn('et_ophciexamination_overallmanagementplan_version', 'left_target_iop', 'left_target_iop_id');
        $this->renameColumn('et_ophciexamination_overallmanagementplan_version', 'right_target_iop', 'right_target_iop_id');
        $this->alterColumn('et_ophciexamination_overallmanagementplan', 'right_target_iop_id', 'int(11)');
        $this->alterColumn('et_ophciexamination_overallmanagementplan', 'left_target_iop_id', 'int(11)');
        $this->alterColumn('et_ophciexamination_overallmanagementplan_version', 'right_target_iop_id', 'int(11)');
        $this->alterColumn('et_ophciexamination_overallmanagementplan_version', 'left_target_iop_id', 'int(11)');

        $this->addForeignKey('right_target_iop_fk', 'et_ophciexamination_overallmanagementplan', 'right_target_iop_id', 'ophciexamination_targetiop', 'id');
        $this->addForeignKey('left_target_iop_fk', 'et_ophciexamination_overallmanagementplan', 'left_target_iop_id', 'ophciexamination_targetiop', 'id');
    }

    public function down()
    {
        $this->dropForeignKey('right_target_iop_fk', 'et_ophciexamination_overallmanagementplan');
        $this->dropForeignKey('left_target_iop_fk', 'et_ophciexamination_overallmanagementplan');

        //reverse foreign keys to the original values
        $this->execute(
            'update et_ophciexamination_overallmanagementplan e join ophciexamination_targetiop t
			on t.id = e.right_target_iop_id
			set e.right_target_iop_id = CAST(t.name AS UNSIGNED)
			'
        );
        $this->execute(
            'update et_ophciexamination_overallmanagementplan e join ophciexamination_targetiop t
			on t.id = e.left_target_iop_id
			set e.left_target_iop_id = CAST(t.name AS UNSIGNED)
			'
        );

        $this->alterColumn('et_ophciexamination_overallmanagementplan', 'right_target_iop_id', 'int(10) unsigned');
        $this->alterColumn('et_ophciexamination_overallmanagementplan', 'left_target_iop_id', 'int(10) unsigned');
        $this->alterColumn('et_ophciexamination_overallmanagementplan_version', 'right_target_iop_id', 'int(10) unsigned');
        $this->alterColumn('et_ophciexamination_overallmanagementplan_version', 'left_target_iop_id', 'int(10) unsigned');
        $this->renameColumn('et_ophciexamination_overallmanagementplan', 'left_target_iop_id', 'left_target_iop');
        $this->renameColumn('et_ophciexamination_overallmanagementplan', 'right_target_iop_id', 'right_target_iop');
        $this->renameColumn('et_ophciexamination_overallmanagementplan_version', 'left_target_iop_id', 'left_target_iop');
        $this->renameColumn('et_ophciexamination_overallmanagementplan_version', 'right_target_iop_id', 'right_target_iop');

        $this->dropTable('ophciexamination_targetiop');
        $this->dropTable('ophciexamination_targetiop_version');
    }
}
