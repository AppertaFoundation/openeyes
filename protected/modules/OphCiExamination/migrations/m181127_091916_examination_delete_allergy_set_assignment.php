<?php

class m181127_091916_examination_delete_allergy_set_assignment extends \OEMigration
{
    /**
     * @return bool|void
     * @throws CException
     */
    public function safeUp()
    {
        $this->addColumn('ophciexamination_allergy_set_entry_version', 'set_id', 'int(11)');
        $this->addColumn('ophciexamination_allergy_set_entry', 'set_id', 'int(11)');
        $this->addForeignKey(
            'allergy_set_entry_allergy_set',
            'ophciexamination_allergy_set_entry',
            'set_id',
            'ophciexamination_allergy_set',
            'id'
        );

        $assignments = $this->dbConnection->createCommand()
            ->select('*')
            ->from('ophciexamination_allergy_set_assignment')
            ->queryAll();

        foreach ($assignments as $assignment) {
            $this->update(
                'ophciexamination_allergy_set_entry',
                array('set_id' => $assignment['allergy_set_id']),
                'id = :id',
                array(':id' => $assignment['ophciexamination_allergy_entry_id'])
            );
        }

        $this->dropForeignKey('ophciexamination_allergy_set_assignment_allergy_e', 'ophciexamination_allergy_set_assignment');
        $this->dropForeignKey('ophciexamination_allergy_set_assignment_set', 'ophciexamination_allergy_set_assignment');
        $this->dropTable('ophciexamination_allergy_set_assignment');
        $this->dropTable('ophciexamination_allergy_set_assignment_version');
    }

    /**
     * @return bool|void
     * @throws CDbException
     * @throws CException
     */
    public function safeDown()
    {
        $this->createOETable(
            'ophciexamination_allergy_set_assignment',
            array(
                'id' => 'pk',
                'ophciexamination_allergy_entry_id' => 'int(11)',
                'allergy_set_id' => 'int(11)',
            ),
            true
        );

        $this->dropForeignKey('allergy_set_entry_allergy_set', 'ophciexamination_allergy_set_entry');

        $this->addForeignKey('ophciexamination_allergy_set_assignment_allergy_e', 'ophciexamination_allergy_set_assignment', 'ophciexamination_allergy_entry_id', 'ophciexamination_allergy_set_entry', 'id');
        $this->addForeignKey('ophciexamination_allergy_set_assignment_set', 'ophciexamination_allergy_set_assignment', 'allergy_set_id', 'ophciexamination_allergy_set', 'id');

        foreach ($this->dbConnection->createCommand('SELECT * FROM ophciexamination_allergy_set_entry')->queryAll() as $allergy_entry) {
            $sql = 'insert into ophciexamination_allergy_set_assignment (ophciexamination_allergy_entry_id, allergy_set_id)
            values (:ophciexamination_allergy_entry_id, :allergy_set_id)';
            $parameters = [
                ':ophciexamination_systemic_diagnoses_entry_id' =>$allergy_entry['id'],
                ':systemic_diagnoses_set_id' => $allergy_entry['set_id']
            ];
            $this->dbConnection->createCommand($sql)->execute($parameters);
        }

        $this->dropColumn('ophciexamination_allergy_set_entry_version', '_set_id');
        $this->dropColumn('ophciexamination_allergy_set_entry', 'set_id');
    }
}
