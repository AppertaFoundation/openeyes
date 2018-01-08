<?php

class m160615_093409_patient_dead_trigger_update extends CDbMigration
{
    public function up()
    {
        $this->execute('DROP TRIGGER cancel_dead_patient_bookings');

        $trigger = <<<EOL
CREATE TRIGGER cancel_dead_patient_bookings AFTER UPDATE ON patient
FOR EACH ROW
  BEGIN
    IF NEW.is_deceased = 1
    THEN
      CALL cancel_patient_bookings(NEW.id);
    END IF;
  END;
EOL;
        $this->execute($trigger);
    }

    public function down()
    {
        $this->execute('DROP TRIGGER cancel_dead_patient_bookings');

        $trigger = <<<EOL
CREATE TRIGGER cancel_dead_patient_bookings AFTER UPDATE ON patient
FOR EACH ROW
  BEGIN
    IF NEW.date_of_death IS NOT NULL
    THEN
      CALL cancel_patient_bookings(NEW.id);
    END IF;
  END;
EOL;
        $this->execute($trigger);
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
