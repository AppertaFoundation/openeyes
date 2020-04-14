<?php

class m181004_102318_change_ophin_biometry_event_type_is_printable_value extends CDbMigration
{
    public function up()
    {
        $this->update('event_type', ['is_printable' => 1], 'class_name ="OphInBiometry"');
    }

    public function down()
    {
        $this->update('event_type', ['is_printable' => 0], 'class_name ="OphInBiometry"');
    }
}
