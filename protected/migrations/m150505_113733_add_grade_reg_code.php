<?php

class m150505_113733_add_grade_reg_code extends OEMigration
{
    public function up()
    {
        $this->addColumn('user', 'doctor_grade_id', 'int(3) null default null');
        $this->addColumn('user_version', 'doctor_grade_id', 'int(3) null default null');

        $this->addForeignKey('user_doctor_grade_id_fk', 'user', 'doctor_grade_id', 'doctor_grade', 'id');

        $this->addColumn('user', 'registration_code', 'varchar(250) null default null');
        $this->addColumn('user_version', 'registration_code', 'varchar(250) null default null');
    }

    public function down()
    {
        $this->dropForeignKey('user_doctor_grade_id_fk', 'user');

        $this->dropColumn('user', 'doctor_grade_id');
        $this->dropColumn('user_version', 'doctor_grade_id');

        $this->dropColumn('user', 'registration_code');
        $this->dropColumn('user_version', 'registration_code');
    }
}
