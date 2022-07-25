<?php

class m220718_053541_rename_logmar_va_units extends OEMigration
{
    public function safeUp()
    {
        $this->update(
            'ophciexamination_visual_acuity_unit',
            ['name' => 'logMAR 1dp'],
            'name = \'logMAR\''
        );
        $this->update(
            'ophciexamination_visual_acuity_unit_version',
            ['name' => 'logMAR 1dp'],
            'name = \'logMAR\''
        );
        $this->update(
            'ophciexamination_visual_acuity_unit',
            ['name' => 'logMAR 2dp'],
            'name = \'logMAR single-letter\''
        );
        $this->update(
            'ophciexamination_visual_acuity_unit_version',
            ['name' => 'logMAR 2dp'],
            'name = \'logMAR single-letter\''
        );
    }

    public function safeDown()
    {
        $this->update(
            'ophciexamination_visual_acuity_unit',
            ['name' => 'logMAR'],
            'name = \'logMAR 1dp\''
        );
        $this->update(
            'ophciexamination_visual_acuity_unit_version',
            ['name' => 'logMAR'],
            'name = \'logMAR 1dp\''
        );
        $this->update(
            'ophciexamination_visual_acuity_unit',
            ['name' => 'logMAR single-letter'],
            'name = \'logMAR 2dp\''
        );
        $this->update(
            'ophciexamination_visual_acuity_unit_version',
            ['name' => 'logMAR single-letter'],
            'name = \'logMAR 2dp\''
        );
    }
}
