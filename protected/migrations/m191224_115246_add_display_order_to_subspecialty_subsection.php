<?php

class m191224_115246_add_display_order_to_subspecialty_subsection extends CDbMigration
{
    public function up()
    {
        $this->addColumn('subspecialty_subsection', 'display_order', 'int(11) NOT NULL');
        $this->addColumn('subspecialty_subsection_version', 'display_order', 'int(11) NOT NULL');
        foreach (SubspecialtySubsection::model()->findAll() as $key => $subspecialty_subsection) {
            $key = ($key + 1) * 5;
            $this->update('subspecialty_subsection', ['display_order' => $key], 'name="'.$subspecialty_subsection->name .'"');
        }
    }

    public function down()
    {
        $this->dropColumn('subspecialty_subsection', 'display_order');
        $this->dropColumn('subspecialty_subsection_version', 'display_order');
    }

}
