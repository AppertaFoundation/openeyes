<?php

class m170605_101057_dose_n_frequency_datatype extends CDbMigration
{
    public function up()
    {
        $this->alterColumn('ophcotherapya_treatment', 'dose_and_frequency', 'TEXT');
    }

    public function down()
    {
        $this->alterColumn('ophcotherapya_treatment', 'dose_and_frequency', 'VARCHAR(256)');
    }
}