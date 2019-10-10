<?php

class m190305_115309_add_deleted_col_to_et_ophciexamination_allergies extends CDbMigration
{
    public function safeUp()
    {
        $this->addColumn('et_ophciexamination_allergies', 'deleted', 'tinyint(1) unsigned not null default 0 after created_date');
        $this->addColumn('et_ophciexamination_allergies_version', 'deleted', 'tinyint(1) unsigned not null default 0 after created_date');
    }

    public function safeDown()
    {
        $this->dropColumn('et_ophciexamination_allergies', 'deleted');
        $this->dropColumn('et_ophciexamination_allergies_version', 'deleted');
    }
}