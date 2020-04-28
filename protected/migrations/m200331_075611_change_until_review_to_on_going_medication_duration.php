<?php

class m200331_075611_change_until_review_to_on_going_medication_duration extends CDbMigration
{
    public function up()
    {
        $this->update('medication_duration', array('name' => 'Ongoing'), 'name="Until review"');
    }

    public function down()
    {
        $this->update('medication_duration', array('name' => 'Until review'), 'name="Ongoing"');
    }
}
