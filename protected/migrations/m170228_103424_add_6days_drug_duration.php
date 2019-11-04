<?php

class m170228_103424_add_6days_drug_duration extends CDbMigration
{
    public function up()
    {
//      Does 6 days already exist in db?
        $sixdays_id = $this->dbConnection->createCommand('SELECT id FROM drug_duration WHERE name = "6 days"')->queryScalar();

//      If so then terminate as success
        if ($sixdays_id) {
            echo "6 days already exists";
            return true;
        } else {
//      If not then continue
//      Get display_order for '5 days'
            $fivedays_display = $this->dbConnection->createCommand('SELECT display_order FROM drug_duration WHERE name = "5 days"')->queryScalar();
            if (!$fivedays_display) {
                // There is no row for '5 days' so treat it as if the new row should go at the end of the list.
                $fivedays_display = $this->dbConnection->createCommand('SELECT max(display_order) FROM drug_duration')->queryScalar();
            }
            echo "5 days is " . $fivedays_display;
//      Update by one all rows where display_order is greater than '5 days'
            $seq = $fivedays_display + 2;

            foreach ($this->getDbConnection()->createCommand('SELECT id, display_order from drug_duration
                  WHERE display_order > ' . $fivedays_display .  ' ORDER BY display_order')->queryAll() as $row) {
                $this->update('drug_duration', array('display_order' => $seq), "id = {$row['id']}");
                ++$seq;
            }

//      Insert row for 6 days with display_order of value of 5 days + 1
            $sixdays_display = $fivedays_display + 1;
            $this->insert('drug_duration', array('name' => '6 days', 'display_order' => $sixdays_display));
        }
    }

    public function down()
    {
//      Get display_order for '6 days'
        $sixdays_display = $this->dbConnection->createCommand('SELECT display_order FROM drug_duration WHERE name = "6 days"')->queryScalar();
        echo "6 days is " . $sixdays_display;
//      Update by one all rows where display_order is greater than '5 days'
        $seq = $sixdays_display;

        foreach ($this->getDbConnection()->createCommand('SELECT id, display_order from drug_duration
                  WHERE display_order > ' . $sixdays_display .  ' ORDER BY display_order')->queryAll() as $row) {
            $this->update('drug_duration', array('display_order' => $seq), "id = {$row['id']}");
            ++$seq;
        }

//      Delete row for 6 days
        $this->delete('drug_duration', '`name`="6 days"');


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
