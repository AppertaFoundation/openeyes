<?php

class m181127_143857_examination_delete_systemic_diagnoses_set_assignment extends \OEMigration
{
    public function safeUp()
    {
        $this->addColumn('ophciexamination_systemic_diagnoses_set_entry_version', 'set_id', 'int(11)');
        $this->addColumn('ophciexamination_systemic_diagnoses_set_entry', 'set_id', 'int(11)');
        $this->addForeignKey(
            'systemic_diagnoses_set_entry_systemic_diagnoses_set',
            'ophciexamination_systemic_diagnoses_set_entry',
            'set_id',
            'ophciexamination_systemic_diagnoses_set',
            'id'
        );

        $assignments = $this->dbConnection->createCommand()
            ->select()
            ->from('ophciexamination_systemic_diagnoses_set_assignment')
            ->queryAll();

        foreach ($assignments as $assignment) {
            $this->update(
                'ophciexamination_systemic_diagnoses_set_entry',
                array('set_id' => $assignment['systemic_diagnoses_set_id']),
                'id = :id',
                array('id' => $assignment['systemic_diagnoses_set_entry_id'])
            );
        }

        $this->dropForeignKey('exam_systemic_diagnoses_set_assignment_diag_e', 'ophciexamination_systemic_diagnoses_set_assignment');
        $this->dropForeignKey('exam_systemic_diagnoses_set_assignment_set', 'ophciexamination_systemic_diagnoses_set_assignment');
        $this->dropTable('ophciexamination_systemic_diagnoses_set_assignment');
        $this->dropTable('ophciexamination_systemic_diagnoses_set_assignment_version');
    }

    public function safeDown()
    {
        $this->createOETable(
            'ophciexamination_systemic_diagnoses_set_assignment',
            array(
                'id' => 'pk',
                'ophciexamination_systemic_diagnoses_entry_id' => 'int(11)',
                'systemic_diagnoses_set_id' => 'int(11)',
            ),
            true
        );

        $this->dropForeignKey('systemic_diagnoses_set_entry_systemic_diagnoses_set', 'ophciexamination_systemic_diagnoses_set_entry');
        $this->addForeignKey('exam_systemic_diagnoses_set_assignment_diag_e', 'ophciexamination_systemic_diagnoses_set_assignment', 'ophciexamination_systemic_diagnoses_entry_id', 'ophciexamination_systemic_diagnoses_set_entry', 'id');
        $this->addForeignKey('exam_systemic_diagnoses_set_assignment_set', 'ophciexamination_systemic_diagnoses_set_assignment', 'systemic_diagnoses_set_id', 'ophciexamination_systemic_diagnoses_set', 'id');

        $iterator = $this->dbConnection->createCommand('SELECT * FROM ophciexamination_systemic_diagnoses_set_entry')
            ->queryAll();

        foreach ($iterator as $systemic_diagnoses_entry) {
            $sql = "insert into ophciexamination_systemic_diagnoses_set_assignment (ophciexamination_systemic_diagnoses_entry_id, systemic_diagnoses_set_id)
            values (:ophciexamination_systemic_diagnoses_entry_id, :systemic_diagnoses_set_id)";
            $parameters = [
                ":ophciexamination_systemic_diagnoses_entry_id"=>$systemic_diagnoses_entry['id'],
                ':systemic_diagnoses_set_id' => $systemic_diagnoses_entry['set_id']
            ];
            $this->dbConnection->createCommand($sql)->execute($parameters);
//
        }

        $this->dropColumn('ophciexamination_systemic_diagnoses_set_entry_version', 'set_id');
        $this->dropColumn('ophciexamination_systemic_diagnoses_set_entry', 'set_id');
    }
}
