<?php

class m210224_011243_add_institution_id_to_proc_assignment_tables extends OEMigration
{
    /**
     * @return bool|void
     */
    public function safeUp()
    {
        $institution_id = $this->dbConnection
            ->createCommand("SELECT * FROM institution WHERE remote_id = '" . Yii::app()->params['institution_code'] . "'")
            ->queryScalar();

        $this->addOEColumn('proc_subspecialty_assignment', 'institution_id', 'int(10) unsigned', true);
        $this->addOEColumn('proc_subspecialty_subsection_assignment', 'institution_id', 'int(10) unsigned', true);

        $this->addForeignKey(
            'proc_subspecialty_assignment_institution_fk',
            'proc_subspecialty_assignment',
            'institution_id',
            'institution',
            'id'
        );

        $this->addForeignKey(
            'proc_subspecialty_subsection_assignment_institution_fk',
            'proc_subspecialty_subsection_assignment',
            'institution_id',
            'institution',
            'id'
        );

        $existing_subspecialty_subsections = $this->dbConnection->createCommand()
            ->select("p.proc_id, p.subspecialty_subsection_id")
            ->from('proc_subspecialty_subsection_assignment p')
            ->queryAll();

        $existing_subspecialties = $this->dbConnection->createCommand()
            ->select("p.proc_id, p.subspecialty_id, p.display_order, p.need_eur")
            ->from('proc_subspecialty_assignment p')
            ->queryAll();

        $institution_ids = $this->dbConnection->createCommand()
            ->select('id')
            ->from('institution')
            ->where('id = :institution_id')
            ->bindValues(['institution_id' => $institution_id])
            ->queryColumn();

        $first_id = array_shift($institution_ids);

        $this->update(
            'proc_subspecialty_subsection_assignment',
            array('institution_id' => $first_id),
        );

        $this->update(
            'proc_subspecialty_assignment',
            array('institution_id' => $first_id),
        );

        foreach ($institution_ids as $institution_id) {
            $cloned_rows = array_map(
                static function ($item) use ($institution_id) {
                    return array(
                        'proc_id' => $item['proc_id'],
                        'subspecialty_subsection_id' => $item['subspecialty_subsection_id'],
                        'institution_id' => $institution_id
                    );
                },
                $existing_subspecialty_subsections
            );
            if (!empty($cloned_rows)) {
                $this->insertMultiple('proc_subspecialty_subsection_assignment', $cloned_rows);
            }
            $cloned_rows = array_map(
                static function ($item) use ($institution_id) {
                    return array(
                        'proc_id' => $item['proc_id'],
                        'subspecialty_id' => $item['subspecialty_id'],
                        'display_order' => $item['display_order'],
                        'need_eur' => $item['need_eur'],
                        'institution_id' => $institution_id
                    );
                },
                $existing_subspecialties
            );
            if (!empty($cloned_rows)) {
                $this->insertMultiple('proc_subspecialty_assignment', $cloned_rows);
            }
        }
    }

    public function safeDown()
    {
        $institution_ids = $this->dbConnection->createCommand()
            ->select('id')
            ->from('institution')
            ->queryColumn();

        $first_id = array_shift($institution_ids);

        $this->delete(
            'proc_subspecialty_subsection_assignment',
            'institution_id != :institution_id',
            array(':institution_id' => $first_id)
        );

        $this->delete(
            'proc_subspecialty_assignment',
            'institution_id != :institution_id',
            array(':institution_id' => $first_id)
        );

        $this->dropForeignKey(
            'proc_subspecialty_assignment_institution_fk',
            'proc_subspecialty_assignment',
        );
        $this->dropForeignKey(
            'proc_subspecialty_subsection_assignment_institution_fk',
            'proc_subspecialty_subsection_assignment',
        );

        $this->dropOEColumn('proc_subspecialty_assignment', 'institution_id', true);
        $this->dropOEColumn('proc_subspecialty_subsection_assignment', 'institution_id', true);
    }
}
