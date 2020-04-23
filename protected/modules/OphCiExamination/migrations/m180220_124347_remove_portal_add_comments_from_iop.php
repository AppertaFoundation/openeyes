<?php

class m180220_124347_remove_portal_add_comments_from_iop extends CDbMigration
{
    public function safeUp()
    {
        $this->execute("UPDATE et_ophciexamination_intraocularpressure SET left_comments = '' WHERE left_comments = 'Portal Add';");
        $this->execute("UPDATE et_ophciexamination_intraocularpressure SET right_comments = '' WHERE right_comments = 'Portal Add';");
    }

    public function safeDown()
    {
        echo "m180220_124347_remove_portal_add_comments_from_iop does not support migration down.\n";
        return false;
    }
}
