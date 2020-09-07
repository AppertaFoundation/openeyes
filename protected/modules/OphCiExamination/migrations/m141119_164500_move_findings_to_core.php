<?php

class m141119_164500_move_findings_to_core extends OEMigration
{
    public function up()
    {
        if ($this->dbConnection->schema->getTable('finding', true) === null) {
            echo "Run core migration to create finding table first\n";

            return false;
        }

        $this->dropForeignKey('ophciexamination_further_findings_assign_f_id_fk', 'ophciexamination_further_findings_assignment');
        $this->dropForeignKey('ophciexamination_further_findings_subspec_f_id_fk', 'ophciexamination_further_findings_subspec_assignment');

        $this->renameColumn('ophciexamination_further_findings_assignment', 'further_finding_id', 'finding_id');
        $this->renameColumn('ophciexamination_further_findings_subspec_assignment', 'further_finding_id', 'finding_id');

        // Copy findings to core
        $findings = $this->dbConnection->createCommand('SELECT * FROM ophciexamination_further_findings')->queryAll();
        foreach ($findings as $finding) {
            $this->insert('finding', $finding);
        }

        // Copy finding subspecialty assignments to core
        $assignments = $this->dbConnection->createCommand('SELECT * FROM ophciexamination_further_findings_subspec_assignment')->queryAll();
        foreach ($assignments as $assignment) {
            $this->insert('findings_subspec_assignment', $assignment);
        }

        $this->addForeignKey('ophciexamination_further_findings_assign_f_id_fk', 'ophciexamination_further_findings_assignment', 'finding_id', 'finding', 'id');

        $this->dropOETable('ophciexamination_further_findings_subspec_assignment', true);
        $this->dropOETable('ophciexamination_further_findings', true);
    }

    public function down()
    {
        $this->createOETable(
            'ophciexamination_further_findings',
            array('id' => 'pk', 'name' => 'varchar(255)', 'display_order' => 'int(10) unsigned NOT NULL DEFAULT 1',
                'active' => 'tinyint(1) unsigned not null DEFAULT 1',
            ),
            true
        );
        $this->createIndex(
            'ophciexamination_further_findings_unique_name',
            'ophciexamination_further_findings',
            'name',
            true
        );
        $this->createOETable(
            'ophciexamination_further_findings_subspec_assignment',
            array('id' => 'pk', 'finding_id' => 'int(11) NOT NULL', 'subspecialty_id' => 'int(10) unsigned NOT NULL',
                'CONSTRAINT `ophciexamination_further_findings_subspec_s_id_fk` FOREIGN KEY (`subspecialty_id`) REFERENCES `subspecialty` (`id`)',
            ),
            true
        );
        $this->dropForeignKey('ophciexamination_further_findings_assign_f_id_fk', 'ophciexamination_further_findings_assignment');
        $this->renameColumn('ophciexamination_further_findings_assignment', 'finding_id', 'further_finding_id');

        // Check to see if core findings table still exists before trying to migrate data
        if ($this->dbConnection->schema->getTable('finding', true)) {
            // Copy finding subspecialty assignments from core
            $assignments = $this->dbConnection->createCommand('SELECT * FROM findings_subspec_assignment')->queryAll();
            foreach ($assignments as $assignment) {
                $this->insert('ophciexamination_further_findings_subspec_assignment', $assignment);
                $this->delete('findings_subspec_assignment', 'id = :id', array(':id' => $assignment['id']));
            }
            // Copy findings from core
            $findings = $this->dbConnection->createCommand('SELECT * FROM finding')->queryAll();
            foreach ($findings as $finding) {
                $this->insert('ophciexamination_further_findings', $finding);
                $this->delete('finding', 'id = :id', array(':id' => $finding['id']));
            }
        } else {
            // TODO: Default data?
        }
        $this->renameColumn('ophciexamination_further_findings_subspec_assignment', 'finding_id', 'further_finding_id');
        $this->addForeignKey(
            'ophciexamination_further_findings_subspec_f_id_fk',
            'ophciexamination_further_findings_subspec_assignment',
            'further_finding_id',
            'ophciexamination_further_findings',
            'id'
        );
        $this->addForeignKey(
            'ophciexamination_further_findings_assign_f_id_fk',
            'ophciexamination_further_findings_assignment',
            'further_finding_id',
            'ophciexamination_further_findings',
            'id'
        );
    }
}
