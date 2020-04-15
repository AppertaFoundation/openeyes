<?php

class m190530_133649_add_new_values_intraocular_pressure_reading extends CDbMigration
{
    public function up()
    {
        for ($value = 81; $value < 100; $value++) {
            $this->insert('ophciexamination_intraocularpressure_reading', [
                'name' => $value,
                'value' => $value,
                'display_order' => $value + 1,
            ]);
        }
    }

    public function down()
    {
        for ($value = 99; $value > 80; $value--) {
            $this->delete('ophciexamination_intraocularpressure_reading', 'name = ' . $value);
        }
    }
}
