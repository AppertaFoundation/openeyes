
<?php

class m210302_194724_add_reports_any_institution_role extends OEMigration
{
    public function safeUp()
    {
        $this->addRole('ReportsAnyInstitution');
        $this->addTask('TaskReportAnyInstitution');
        $this->addTaskToRole('TaskReportAnyInstitution', 'ReportsAnyInstitution');
        $this->addTaskToRole('TaskReportAnyInstitution', 'admin');
    }

    public function safeDown()
    {
        $this->removeTaskFromRole('TaskReportAnyInstitution', 'ReportsAnyInstitution');
        $this->removeTaskFromRole('TaskReportAnyInstitution', 'admin');
        $this->removeRole('ReportsAnyInstitution');
        $this->removeTask('TaskReportAnyInstitution');
    }
}
