<?php

class m210427_143800_add_language_and_interpreter_required_columns_to_et_ophciexamination_communication_preferences_table extends OEMigration
{
    public function safeUp()
    {
        $this->addOEColumn(
            'et_ophciexamination_communication_preferences',
            'language_id',
            'int(10) unsigned null',
            true
        );

        $this->addOEColumn(
            'et_ophciexamination_communication_preferences',
            'interpreter_required_id',
            'int(10) unsigned null',
            true
        );
    }

    public function safeDown()
    {
        $this->dropOEColumn('et_ophciexamination_communication_preferences', 'language_id', true);
        $this->dropOEColumn('et_ophciexamination_communication_preferences', 'interpreter_required_id', true);
    }
}
