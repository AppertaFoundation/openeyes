<?php

class m220315_114101_add_constraints_for_PSD extends OEMigration
{
    public function safeUp()
    {
        // Add a unique constraints
        $this->execute("ALTER TABLE team ADD CONSTRAINT team_unique UNIQUE KEY (`name`,`institution_id`,`active`);");
        $this->execute("ALTER TABLE ophdrpgdpsd_pgdpsd ADD CONSTRAINT psdpgd_unique UNIQUE KEY (`name`,`institution_id`,`type`,`active`);");

        // Change active default to 1
        $this->alterOEColumn('team', 'active', 'tinyint(1) DEFAULT 1 NULL', true);
        $this->alterOEColumn('ophdrpgdpsd_pgdpsd', 'active', 'tinyint(1) DEFAULT 1 NOT NULL', true);
    }

    public function safeDown()
    {
        $this->execute("ALTER TABLE team DROP KEY team_unique;");
        // Change active default back to NULL
        $this->alterOEColumn('team', 'active', 'tinyint(1) NULL', true);
    }
}
