<?php

class m141124_085435_finding_descriptions extends CDbMigration
{
    public function up()
    {
        $this->addColumn('ophciexamination_further_findings_assignment', 'description', 'varchar(4096) not null');
        $this->addColumn('ophciexamination_further_findings_assignment_version', 'description', 'varchar(4096) not null');
    }

    public function down()
    {
        $this->dropColumn('ophciexamination_further_findings_assignment', 'description');
        $this->dropColumn('ophciexamination_further_findings_assignment_version', 'description');
    }
}
