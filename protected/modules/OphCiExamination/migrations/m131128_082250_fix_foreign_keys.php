<?php

class m131128_082250_fix_foreign_keys extends CDbMigration
{
    public $tables = array('anteriorsegment_cct', 'dilation', 'glaucomarisk', 'management', 'oct', 'risks');
    public $tables2 = array(
        'ophciexamination_cataractmanagement_suitable_for_surgeon' => 'ophciexamination_sfs',
        'ophciexamination_dilation_drugs' => 'ophciexamination_dilation_drugs',
        'ophciexamination_dilation_treatment' => 'ophciexamination_dilation_treatment',
        'ophciexamination_event_elementset_assignment' => 'ophciexamination_event_ea',
        'ophciexamination_workflow' => 'ophciexamination_workflow',
    );

    public function up()
    {
        foreach ($this->tables as $table) {
            $this->dropForeignKey("et_ophciexamination_{$table}_created_user_id_fk", "et_ophciexamination_$table");
            $this->dropForeignKey("et_ophciexamination_{$table}_last_modified_user_id_fk", "et_ophciexamination_$table");

            $this->addForeignKey("et_ophciexamination_{$table}_created_user_id_fk", "et_ophciexamination_$table", 'created_user_id', 'user', 'id');
            $this->addForeignKey("et_ophciexamination_{$table}_last_modified_user_id_fk", "et_ophciexamination_$table", 'last_modified_user_id', 'user', 'id');
        }

        foreach ($this->tables2 as $table => $prefix) {
            $this->dropForeignKey("{$prefix}_created_user_id_fk", $table);
            $this->dropForeignKey("{$prefix}_last_modified_user_id_fk", $table);

            $this->addForeignKey("{$prefix}_created_user_id_fk", $table, 'created_user_id', 'user', 'id');
            $this->addForeignKey("{$prefix}_last_modified_user_id_fk", $table, 'last_modified_user_id', 'user', 'id');
        }

        $this->dropForeignKey('et_ophciexamination_pupillaryabnormalities_cui_fk', 'ophciexamination_pupillaryabnormalities_abnormality');
        $this->dropForeignKey('et_ophciexamination_pupillaryabnormalities_lmui_fk', 'ophciexamination_pupillaryabnormalities_abnormality');

        $this->addForeignKey('et_ophciexamination_pupillaryabnormalities_cui_fk', 'ophciexamination_pupillaryabnormalities_abnormality', 'created_user_id', 'user', 'id');
        $this->addForeignKey('et_ophciexamination_pupillaryabnormalities_lmui_fk', 'ophciexamination_pupillaryabnormalities_abnormality', 'last_modified_user_id', 'user', 'id');
    }

    public function down()
    {
        foreach ($this->tables as $table) {
            $this->dropForeignKey("et_ophciexamination_{$table}_created_user_id_fk", "et_ophciexamination_$table");
            $this->dropForeignKey("et_ophciexamination_{$table}_last_modified_user_id_fk", "et_ophciexamination_$table");

            $this->addForeignKey("et_ophciexamination_{$table}_created_user_id_fk", "et_ophciexamination_$table", 'last_modified_user_id', 'user', 'id');
            $this->addForeignKey("et_ophciexamination_{$table}_last_modified_user_id_fk", "et_ophciexamination_$table", 'created_user_id', 'user', 'id');
        }

        foreach ($this->tables2 as $table => $prefix) {
            $this->dropForeignKey("{$prefix}_created_user_id_fk", $table);
            $this->dropForeignKey("{$prefix}_last_modified_user_id_fk", $table);

            $this->addForeignKey("{$prefix}_created_user_id_fk", $table, 'last_modified_user_id', 'user', 'id');
            $this->addForeignKey("{$prefix}_last_modified_user_id_fk", $table, 'created_user_id', 'user', 'id');
        }

        $this->dropForeignKey('et_ophciexamination_pupillaryabnormalities_cui_fk', 'ophciexamination_pupillaryabnormalities_abnormality');
        $this->dropForeignKey('et_ophciexamination_pupillaryabnormalities_lmui_fk', 'ophciexamination_pupillaryabnormalities_abnormality');

        $this->addForeignKey('et_ophciexamination_pupillaryabnormalities_cui_fk', 'ophciexamination_pupillaryabnormalities_abnormality', 'last_modified_user_id', 'user', 'id');
        $this->addForeignKey('et_ophciexamination_pupillaryabnormalities_lmui_fk', 'ophciexamination_pupillaryabnormalities_abnormality', 'created_user_id', 'user', 'id');
    }
}
