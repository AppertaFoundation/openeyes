<?php

class m141113_142200_refraction_text_box extends CDbMigration
{
    public function up()
    {
        $this->addColumn('et_ophciexamination_refraction', 'left_notes', 'varchar(4096) not null');
        $this->addColumn('et_ophciexamination_refraction_version', 'left_notes', 'varchar(4096) not null');

        $this->addColumn('et_ophciexamination_refraction', 'right_notes', 'varchar(4096) not null');
        $this->addColumn('et_ophciexamination_refraction_version', 'right_notes', 'varchar(4096) not null');
    }

    public function down()
    {
        $this->dropColumn('et_ophciexamination_refraction', 'left_notes');
        $this->dropColumn('et_ophciexamination_refraction_version', 'left_notes');

        $this->dropColumn('et_ophciexamination_refraction', 'right_notes');
        $this->dropColumn('et_ophciexamination_refraction_version', 'right_notes');
    }
}
