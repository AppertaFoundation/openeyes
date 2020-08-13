<?php

class m200724_094328_set_et_ophciexamination_visualacuity__default_notes_columns_to_null extends OEMigration
{
    public function safeUp()
    {
        $this->execute('ALTER TABLE et_ophciexamination_visualacuity MODIFY COLUMN left_notes TEXT');
        $this->execute('ALTER TABLE et_ophciexamination_visualacuity MODIFY COLUMN right_notes TEXT');
        $this->execute('ALTER TABLE et_ophciexamination_visualacuity_version MODIFY COLUMN left_notes TEXT');
        $this->execute('ALTER TABLE et_ophciexamination_visualacuity_version MODIFY COLUMN right_notes TEXT');
        $this->alterOEColumn('et_ophciexamination_visualacuity', 'left_notes', 'text DEFAULT NULL', true);
        $this->alterOEColumn('et_ophciexamination_visualacuity', 'right_notes', 'text DEFAULT NULL', true);
    }

    public function safeDown()
    {
        $this->alterOEColumn('et_ophciexamination_visualacuity', 'left_notes', "text DEFAULT ''", true);
        $this->alterOEColumn('et_ophciexamination_visualacuity', 'right_notes', "text DEFAULT ''", true);
        $this->execute('ALTER TABLE et_ophciexamination_visualacuity MODIFY COLUMN left_notes TEXT NOT NULL');
        $this->execute('ALTER TABLE et_ophciexamination_visualacuity MODIFY COLUMN right_notes TEXT NOT NULL');
        $this->execute('ALTER TABLE et_ophciexamination_visualacuity_version MODIFY COLUMN left_notes TEXT NOT NULL');
        $this->execute('ALTER TABLE et_ophciexamination_visualacuity_version MODIFY COLUMN right_notes TEXT NOT NULL');
    }
}
