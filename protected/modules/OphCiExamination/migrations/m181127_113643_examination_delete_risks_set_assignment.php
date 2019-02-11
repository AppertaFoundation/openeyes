<?php

class m181127_113643_examination_delete_risks_set_assignment extends \OEMigration
{
    public function up()
    {
        $this->addColumn('ophciexamination_risk_set_entry_version', 'set_id', 'int(11)');
        $this->addColumn('ophciexamination_risk_set_entry', 'set_id', 'int(11)');
        $this->addForeignKey('risk_set_entry_risk_set',
            'ophciexamination_risk_set_entry',
            'set_id',
            'ophciexamination_risk_set',
            'id');

        $assignments = $this->dbConnection->createCommand()
            ->select('*')
            ->from('ophciexamination_risk_set_assignment')
            ->queryAll();

        foreach ($assignments as $assignment) {
            $risk_set_entry = \OEModule\OphCiExamination\models\OphCiExaminationRiskSetEntry::model()->findByPk($assignment['ophciexamination_risk_entry_id']);
            $risk_set_entry->set_id = $assignment['risk_set_id'];
            $risk_set_entry->save();
        }

        $this->dropForeignKey('ophciexamination_risk_set_assignment_risk_e', 'ophciexamination_risk_set_assignment');
        $this->dropForeignKey('ophciexamination_risk_set_assignment_set', 'ophciexamination_risk_set_assignment');
        $this->dropTable('ophciexamination_risk_set_assignment');
        $this->dropTable('ophciexamination_risk_set_assignment_version');
    }

    public function down()
    {
        $this->createOETable('ophciexamination_risk_set_assignment',
            array(
                'id' => 'pk',
                'ophciexamination_risk_entry_id' => 'int(11)',
                'risk_set_id' => 'int(11)',
            ), true
        );

        $this->dropForeignKey('risk_set_entry_risk_set', 'ophciexamination_risk_set_entry');

        $this->addForeignKey('ophciexamination_risk_set_assignment_risk_e', 'ophciexamination_risk_set_assignment', 'ophciexamination_risk_entry_id', 'ophciexamination_risk_set_entry', 'id');
        $this->addForeignKey('ophciexamination_risk_set_assignment_set', 'ophciexamination_risk_set_assignment', 'risk_set_id', 'ophciexamination_risk_set', 'id');

        $dataProvider = new CActiveDataProvider('OEModule\OphCiExamination\models\OphCiExaminationRiskSetEntry');
        $iterator = new CDataProviderIterator($dataProvider);

        foreach ($iterator as $risk_entry) {
            $sql = "insert into ophciexamination_risk_set_assignment (ophciexamination_risk_entry_id, risk_set_id)
            values (:ophciexamination_risk_entry_id, :risk_set_id)";
            $parameters = [
                ":ophciexamination_risk_entry_id"=>$risk_entry->id,
                ':risk_set_id' => $risk_entry->set_id
            ];
            $this->dbConnection->createCommand($sql)->execute($parameters);
        }

        $this->dropColumn('ophciexamination_risk_set_entry_version', 'set_id');
        $this->dropColumn('ophciexamination_risk_set_entry', 'set_id');
    }
}