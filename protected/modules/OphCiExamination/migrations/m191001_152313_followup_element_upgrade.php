<?php

class m191001_152313_followup_element_upgrade extends OEMigration
{
    public function up()
    {
        $this->createOETable(
            'ophciexamination_clinicoutcome_entry',
            array(
                'id' => 'pk',
                'element_id' => 'int(10) unsigned NOT NULL',
                'status_id' => 'int(10) unsigned NOT NULL',
                'followup_quantity' => 'int(10) unsigned',
                'followup_period_id' => 'int(10) unsigned',
                'followup_comments' => 'varchar(255) COLLATE utf8_bin',
                'role_id' => 'int(10) unsigned',
            ),
            true
        );
        $this->addForeignKey('ophciexamination_clinicoutcome_entry_ei_fk', 'ophciexamination_clinicoutcome_entry', 'element_id', 'et_ophciexamination_clinicoutcome', 'id');
        $this->addForeignKey('ophciexamination_clinicoutcome_entry_status_fk', 'ophciexamination_clinicoutcome_entry', 'status_id', 'ophciexamination_clinicoutcome_status', 'id');
        $this->addForeignKey('ophciexamination_clinicoutcome_entry_fu_p_fk', 'ophciexamination_clinicoutcome_entry', 'followup_period_id', 'period', 'id');
        $this->addForeignKey('ophciexamination_clinicoutcome_entry_ri_fk', 'ophciexamination_clinicoutcome_entry', 'role_id', 'ophciexamination_clinicoutcome_role', 'id');

        // Data migration
        $data = $this->dbConnection->createCommand('SELECT * FROM et_ophciexamination_clinicoutcome')->queryAll();

        foreach ($data as $item) {
            $this->insert('ophciexamination_clinicoutcome_entry', [
                'element_id' => $item['id'],
                'status_id' => $item['status_id'],
                'followup_quantity' => $item['followup_quantity'],
                'followup_period_id' => $item['followup_period_id'],
                'followup_comments' => $item['role_comments'],
                'role_id' => $item['role_id']
            ]);
        }

        $this->dropForeignKey('et_ophciexamination_clinicoutcome_fup_p_fk', 'et_ophciexamination_clinicoutcome');
        $this->dropForeignKey('et_ophciexamination_clinicoutcome_ri_fk', 'et_ophciexamination_clinicoutcome');
        $this->dropForeignKey('et_ophciexamination_clinicoutcome_status_fk', 'et_ophciexamination_clinicoutcome');

        $this->dropColumn('et_ophciexamination_clinicoutcome', 'status_id');
        $this->dropColumn('et_ophciexamination_clinicoutcome', 'followup_quantity');
        $this->dropColumn('et_ophciexamination_clinicoutcome', 'followup_period_id');
        $this->dropColumn('et_ophciexamination_clinicoutcome', 'role_id');
        $this->dropColumn('et_ophciexamination_clinicoutcome', 'role_comments');
        $this->renameColumn('et_ophciexamination_clinicoutcome', 'description', 'comments');

        $this->dropColumn('et_ophciexamination_clinicoutcome_version', 'status_id');
        $this->dropColumn('et_ophciexamination_clinicoutcome_version', 'followup_quantity');
        $this->dropColumn('et_ophciexamination_clinicoutcome_version', 'followup_period_id');
        $this->dropColumn('et_ophciexamination_clinicoutcome_version', 'role_id');
        $this->dropColumn('et_ophciexamination_clinicoutcome_version', 'role_comments');
        $this->renameColumn('et_ophciexamination_clinicoutcome_version', 'description', 'comments');
    }

    public function down()
    {
        $this->addColumn('et_ophciexamination_clinicoutcome', 'status_id', 'int(10) unsigned');
        $this->addColumn('et_ophciexamination_clinicoutcome', 'followup_quantity', 'int(10) unsigned');
        $this->addColumn('et_ophciexamination_clinicoutcome', 'followup_period_id', 'int(10) unsigned');
        $this->addColumn('et_ophciexamination_clinicoutcome', 'role_id', 'int(10) unsigned');
        $this->addColumn('et_ophciexamination_clinicoutcome', 'role_comments', 'varchar(255)');
        $this->renameColumn('et_ophciexamination_clinicoutcome', 'comments', 'description');

        $this->addForeignKey('et_ophciexamination_clinicoutcome_status_fk', 'et_ophciexamination_clinicoutcome', 'status_id', 'ophciexamination_clinicoutcome_status', 'id');
        $this->addForeignKey('et_ophciexamination_clinicoutcome_fup_p_fk', 'et_ophciexamination_clinicoutcome', 'followup_period_id', 'period', 'id');
        $this->addForeignKey('et_ophciexamination_clinicoutcome_ri_fk', 'et_ophciexamination_clinicoutcome', 'role_id', 'ophciexamination_clinicoutcome_role', 'id');

        $data_provider = new CActiveDataProvider('OEModule\OphCiExamination\models\ClinicOutcomeEntry');
        $iterator = new CDataProviderIterator($data_provider);

        foreach ($iterator as $item) {
            $this->update('et_ophciexamination_clinicoutcome', [
                'status_id' => $item->status_id,
                'followup_quantity' => $item->followup_quantity,
                'followup_period_id' => $item->followup_period_id,
                'role_comments' => $item->followup_comments,
                'role_id' => $item->role_id,
            ], 'id=:id', [':id' => $item->element_id]);
        }

        $this->dropForeignKey('ophciexamination_clinicoutcome_entry_ei_fk', 'ophciexamination_clinicoutcome_entry');
        $this->dropForeignKey('ophciexamination_clinicoutcome_entry_status_fk', 'ophciexamination_clinicoutcome_entry');
        $this->dropForeignKey('ophciexamination_clinicoutcome_entry_fu_p_fk', 'ophciexamination_clinicoutcome_entry');
        $this->dropForeignKey('ophciexamination_clinicoutcome_entry_ri_fk', 'ophciexamination_clinicoutcome_entry');

        $this->dropOETable('ophciexamination_clinicoutcome_entry', true);

        $this->addColumn('et_ophciexamination_clinicoutcome_version', 'status_id', 'int(10) unsigned');
        $this->addColumn('et_ophciexamination_clinicoutcome_version', 'followup_quantity', 'int(10) unsigned');
        $this->addColumn('et_ophciexamination_clinicoutcome_version', 'followup_period_id', 'int(10) unsigned');
        $this->addColumn('et_ophciexamination_clinicoutcome_version', 'role_id', 'int(10) unsigned');
        $this->addColumn('et_ophciexamination_clinicoutcome_version', 'role_comments', 'varchar(255)');
        $this->renameColumn('et_ophciexamination_clinicoutcome_version', 'comments', 'description');
    }
}
