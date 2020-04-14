<?php

class m170509_120816_add_defaults_to_eye_id extends OEMigration
{
    public function up()
    {

        $this->alterColumn('et_ophciexamination_cxl_history', 'eye_id', 'int(11) DEFAULT 3');
        $this->alterColumn('et_ophciexamination_cxl_outcome', 'eye_id', 'int(11) DEFAULT 3');
        $this->alterColumn('et_ophciexamination_keratometry', 'eye_id', 'int(11) DEFAULT 3');
        $this->alterColumn('et_ophciexamination_slit_lamp', 'eye_id', 'int(11) DEFAULT 3');
        $this->alterColumn('et_ophciexamination_specular_microscopy', 'eye_id', 'int(11) DEFAULT 3');
    }

    public function down()
    {
        $this->alterColumn('et_ophciexamination_cxl_history', 'eye_id', 'int(11)');
        $this->alterColumn('et_ophciexamination_cxl_outcome', 'eye_id', 'int(11)');
        $this->alterColumn('et_ophciexamination_keratometry', 'eye_id', 'int(11)');
        $this->alterColumn('et_ophciexamination_slit_lamp', 'eye_id', 'int(11)');
        $this->alterColumn('et_ophciexamination_specular_microscopy', 'eye_id', 'int(11)');
        return false;
    }

    /*
    // Use safeUp/safeDown to do migration with transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}
