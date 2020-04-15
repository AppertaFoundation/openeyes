<?php

class m190603_145119_add_notes_to_visual_acuity extends CDbMigration
{
    public function up()
    {
        $this->addColumn('et_ophciexamination_visualacuity', 'left_notes', 'text not null');
        $this->addColumn('et_ophciexamination_visualacuity_version', 'left_notes', 'text not null');
        $this->addColumn('et_ophciexamination_nearvisualacuity', 'left_notes', 'text not null');
        $this->addColumn('et_ophciexamination_nearvisualacuity_version', 'left_notes', 'text not null');

        $this->addColumn('et_ophciexamination_visualacuity', 'right_notes', 'text not null');
        $this->addColumn('et_ophciexamination_visualacuity_version', 'right_notes', 'text not null');
        $this->addColumn('et_ophciexamination_nearvisualacuity', 'right_notes', 'text not null');
        $this->addColumn('et_ophciexamination_nearvisualacuity_version', 'right_notes', 'text not null');
    }

    public function down()
    {
        $this->dropColumn('et_ophciexamination_visualacuity', 'left_notes');
        $this->dropColumn('et_ophciexamination_visualacuity_version', 'left_notes');
        $this->dropColumn('et_ophciexamination_nearvisualacuity', 'left_notes');
        $this->dropColumn('et_ophciexamination_nearvisualacuity_version', 'left_notes');

        $this->dropColumn('et_ophciexamination_visualacuity', 'right_notes');
        $this->dropColumn('et_ophciexamination_visualacuity_version', 'right_notes');
        $this->dropColumn('et_ophciexamination_nearvisualacuity', 'right_notes');
        $this->dropColumn('et_ophciexamination_nearvisualacuity_version', 'right_notes');
    }
}
