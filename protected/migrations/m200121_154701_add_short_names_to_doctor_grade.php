<?php

class m200121_154701_add_short_names_to_doctor_grade extends OEMigration
{
	public function up()
	{
	    $this->addColumn('doctor_grade', 'short_name', 'varchar(255)');
	    $this->update('doctor_grade', ['short_name'=>'Consultant'], 'grade="Consultant"');
	    $this->update('doctor_grade', ['short_name'=>'Locum Consultant'], 'grade="Locum Consultant"');
	    $this->update('doctor_grade', ['short_name'=>'Associate Specialist'], 'grade="Associate Specialist"');
	    $this->update('doctor_grade', ['short_name'=>'Fellow'], 'grade="Fellow"');
	    $this->update('doctor_grade', ['short_name'=>'Registrar'], 'grade="Registrar"');
	    $this->update('doctor_grade', ['short_name'=>'Staff Grade'], 'grade="Staff Grade"');
	    $this->update('doctor_grade', ['short_name'=>'Trust Doctor'], 'grade="Trust Doctor"');
	    $this->update('doctor_grade', ['short_name'=>'Senior House Officer'], 'grade="Senior House Officer"');
	    $this->update('doctor_grade', ['short_name'=>'ST1'], 'grade="Specialty trainee (year 1)"');
	    $this->update('doctor_grade', ['short_name'=>'ST2'], 'grade="Specialty trainee (year 2)"');
	    $this->update('doctor_grade', ['short_name'=>'ST3'], 'grade="Specialty trainee (year 3)"');
	    $this->update('doctor_grade', ['short_name'=>'ST4'], 'grade="Specialty trainee (year 4)"');
	    $this->update('doctor_grade', ['short_name'=>'ST5'], 'grade="Specialty trainee (year 5)"');
	    $this->update('doctor_grade', ['short_name'=>'ST6'], 'grade="Specialty trainee (year 6)"');
	    $this->update('doctor_grade', ['short_name'=>'Specialty trainee (year 7)'], 'grade="Specialty trainee (year 7)"');
	    $this->update('doctor_grade', ['short_name'=>'Foundation Year 1 Doctor'], 'grade="Foundation Year 1 Doctor"');
	    $this->update('doctor_grade', ['short_name'=>'Foundation Year 2 Doctor'], 'grade="Foundation Year 2 Doctor"');
	    $this->update('doctor_grade', ['short_name'=>'GP with a special interest in ophthalmology'], 'grade="GP with a special interest in ophthalmology"');
	    $this->update('doctor_grade', ['short_name'=>'Community ophthalmologist'], 'grade="Community ophthalmologist"');
	    $this->update('doctor_grade', ['short_name'=>'Anaesthetist'], 'grade="Anaesthetist"');
	    $this->update('doctor_grade', ['short_name'=>'Orthoptist'], 'grade="Orthoptist"');
	    $this->update('doctor_grade', ['short_name'=>'Optometrist'], 'grade="Optometrist"');
	    $this->update('doctor_grade', ['short_name'=>'Clinical nurse specialist'], 'grade="Clinical nurse specialist"');
	    $this->update('doctor_grade', ['short_name'=>'Nurse'], 'grade="Nurse"');
	    $this->update('doctor_grade', ['short_name'=>'Health Care Assistant'], 'grade="Health Care Assistant"');
	    $this->update('doctor_grade', ['short_name'=>'Ophthalmic Technician'], 'grade="Ophthalmic Technician"');
	    $this->update('doctor_grade', ['short_name'=>'Surgical Care Practitioner'], 'grade="Surgical Care Practitioner"');
	    $this->update('doctor_grade', ['short_name'=>'Clinical Assistant'], 'grade="Clinical Assistant"');
	    $this->update('doctor_grade', ['short_name'=>'RG1'], 'grade="RG1"');
	    $this->update('doctor_grade', ['short_name'=>'RG2'], 'grade="RG2"');
	    $this->update('doctor_grade', ['short_name'=>'ODP'], 'grade="ODP"');
	    $this->update('doctor_grade', ['short_name'=>'Administration staff'], 'grade="Administration staff"');
	    $this->update('doctor_grade', ['short_name'=>'Other'], 'grade="Other"');
	}

	public function down()
	{
	    $this->dropColumn('doctor_grade', 'short_name');
	}
}