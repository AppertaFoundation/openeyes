<?php

class m220315_114101_add_constraints_for_PSD extends OEMigration
{
    public function safeUp()
    {
        // Add a unique constraint for team names
        $this->execute("ALTER TABLE team ADD CONSTRAINT team_unique UNIQUE KEY (`name`,`institution_id`,`active`);");

        // Change active default to 1
        $this->alterOEColumn('team', 'active', 'tinyint(1) DEFAULT 1 NULL', true);
    }

    public function safeDown()
    {
        $this->execute("ALTER TABLE team DROP KEY team_unique;");
        // Change active default back to NULL
        $this->alterOEColumn('team', 'active', 'tinyint(1) NULL', true);
    }
}
