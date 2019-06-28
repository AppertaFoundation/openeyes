<?php

class m190501_115600_remove_date_from_worklist_names extends CDbMigration
{
    public function up()
    {
        $this->execute("UPDATE worklist
                          SET    name = reverse(substr(reverse(name), instr(reverse(name), '-') + 2))
                          WHERE  name regexp ' - [[:digit:]]{1,2} [[:alpha:]]{3} [[:digit:]]{4}$';
                        ");
    }

    public function down()
    {
    }
}
