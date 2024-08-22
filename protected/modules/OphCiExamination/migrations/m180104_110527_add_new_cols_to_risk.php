<?php

class m180104_110527_add_new_cols_to_risk extends OEMigration
{
    public function up()
    {
        $this->createOETable(
            'ophciexamination_risk_set_entry',
            array(
                'id' => 'pk',
                'ophciexamination_risk_id' => 'int(11)',
                'gender' => 'varchar(1) NULL',
                'age_min' => 'int(3) unsigned',
                'age_max' => 'int(3) unsigned',

            ),
            true
        );

        $this->createOETable(
            'ophciexamination_risk_set_assignment',
            array(
                'id' => 'pk',
                'ophciexamination_risk_entry_id' => 'int(11)',
                'risk_set_id' => 'int(11)',
            ),
            true
        );
        $this->createOETable(
            'ophciexamination_risk_set',
            array(
                'id' => 'pk',
                'name' => 'varchar(255) NULL',
                'firm_id' => 'int(10) unsigned',
                'subspecialty_id' =>  'int(10) unsigned',
            ),
            true
        );

        $this->addForeignKey('ophciexamination_risk_set_subspecialty', 'ophciexamination_risk_set', 'subspecialty_id', 'subspecialty', 'id');
        $this->addForeignKey('ophciexamination_risk_set_firm', 'ophciexamination_risk_set', 'firm_id', 'firm', 'id');

        $ophciexamination_risk = $this->dbConnection->schema->getTable('ophciexamination_risk');
        if (isset($ophciexamination_risk->columns['required'])) {
            $this->dropColumn("ophciexamination_risk", "required");
            $this->dropColumn("ophciexamination_risk_version", "required");
        }

        $this->addForeignKey('ophciexamination_risk_set_assignment_risk_e', 'ophciexamination_risk_set_assignment', 'ophciexamination_risk_entry_id', 'ophciexamination_risk_set_entry', 'id');
        $this->addForeignKey('ophciexamination_risk_set_assignment_set', 'ophciexamination_risk_set_assignment', 'risk_set_id', 'ophciexamination_risk_set', 'id');

        $this->addForeignKey('ophciexamination_risk_set_e', 'ophciexamination_risk_set_entry', 'ophciexamination_risk_id', 'ophciexamination_risk', 'id');

        $subspecialty_cataract_id = $this->dbConnection->createCommand()->select('id')->from('subspecialty')
            ->where('name=:name', array(':name' => 'Cataract'))
            ->queryScalar();

        if ($subspecialty_cataract_id) {
            $this->insert('ophciexamination_risk_set', [
                'name' => 'Cataract NOD',
                'subspecialty_id' => $subspecialty_cataract_id
            ]);
        }


        $nod_set_id = $this->dbConnection->createCommand()->select('id')->from('ophciexamination_risk_set')
            ->where('name=:name', array(':name' => 'Cataract NOD'))
            ->queryScalar();

        $alpha_blocker_id = $this->dbConnection->createCommand()
            ->select('id')
            ->from('ophciexamination_risk')
            ->where('name=:name', array(':name' => 'Alpha blockers'))
            ->queryScalar();

        $anticoagulants_id = $this->dbConnection->createCommand()
            ->select('id')
            ->from('ophciexamination_risk')
            ->where('name=:name', array(':name' => 'Anticoagulants'))
            ->queryScalar();

        $this->insert('ophciexamination_risk_set_entry', [
            'ophciexamination_risk_id' => $alpha_blocker_id,
        ]);

        $this->insert('ophciexamination_risk_set_entry', [
            'ophciexamination_risk_id' => $anticoagulants_id,
        ]);

        if ($nod_set_id) {
            $ophciexamination_risk_id = $this->dbConnection->createCommand()
                ->select('id')
                ->from('ophciexamination_risk_set_entry')
                ->where('ophciexamination_risk_id=:ophciexamination_risk_id', array(':ophciexamination_risk_id' => $alpha_blocker_id))
                ->queryScalar();
            $this->insert('ophciexamination_risk_set_assignment', [
                'ophciexamination_risk_entry_id' => $ophciexamination_risk_id,
                'risk_set_id' => $nod_set_id
            ]);

            $ophciexamination_risk_id = $this->dbConnection->createCommand()
                ->select('id')
                ->from('ophciexamination_risk_set_entry')
                ->where('ophciexamination_risk_id=:ophciexamination_risk_id', array(':ophciexamination_risk_id' => $anticoagulants_id))
                ->queryScalar();
            $this->insert('ophciexamination_risk_set_assignment', [
                'ophciexamination_risk_entry_id' => $ophciexamination_risk_id,
                'risk_set_id' => $nod_set_id
            ]);
        }
    }

    public function down()
    {
        $this->dropForeignKey('ophciexamination_risk_set_subspecialty', 'ophciexamination_risk_set');
        $this->dropForeignKey('ophciexamination_risk_set_firm', 'ophciexamination_risk_set');

        $this->addColumn('ophciexamination_risk', "required", "tinyint(1) NOT NULL");
        $this->addColumn('ophciexamination_risk_version', "required", "tinyint(1) NOT NULL");

        $this->dropForeignKey('ophciexamination_risk_set_assignment_risk_e', 'ophciexamination_risk_set_assignment');
        $this->dropForeignKey('ophciexamination_risk_set_assignment_set', 'ophciexamination_risk_set_assignment');

        $this->dropForeignKey('ophciexamination_risk_set_e', 'ophciexamination_risk_set_entry');

        $this->dropOETable('ophciexamination_risk_set_entry', true);
        $this->dropOETable('ophciexamination_risk_set_assignment', true);
        $this->dropOETable('ophciexamination_risk_set', true);
    }
}
