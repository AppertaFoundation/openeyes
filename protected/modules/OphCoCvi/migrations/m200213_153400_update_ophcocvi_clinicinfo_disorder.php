<?php

class m200213_153400_update_ophcocvi_clinicinfo_disorder extends CDbMigration
{
	public function up()
	{
        $this->update('ophcocvi_clinicinfo_disorder', array('main_cause_pdf_id' => 0), 'id = :id', array(':id' => 44));
        $this->update('ophcocvi_clinicinfo_disorder', array('main_cause_pdf_id' => 0), 'id = :id', array(':id' => 45));
        $this->update('ophcocvi_clinicinfo_disorder', array('main_cause_pdf_id' => 0), 'id = :id', array(':id' => 46));
        $this->update('ophcocvi_clinicinfo_disorder', array('main_cause_pdf_id' => 1), 'id = :id', array(':id' => 47));
        $this->update('ophcocvi_clinicinfo_disorder', array('main_cause_pdf_id' => 3), 'id = :id', array(':id' => 48));
        $this->update('ophcocvi_clinicinfo_disorder', array('main_cause_pdf_id' => 3), 'id = :id', array(':id' => 49));
        $this->update('ophcocvi_clinicinfo_disorder', array('main_cause_pdf_id' => 4), 'id = :id', array(':id' => 50));
        $this->update('ophcocvi_clinicinfo_disorder', array('main_cause_pdf_id' => 4), 'id = :id', array(':id' => 51));
        $this->update('ophcocvi_clinicinfo_disorder', array('main_cause_pdf_id' => 5), 'id = :id', array(':id' => 52));
        $this->update('ophcocvi_clinicinfo_disorder', array('main_cause_pdf_id' => 6), 'id = :id', array(':id' => 53));
        $this->update('ophcocvi_clinicinfo_disorder', array('main_cause_pdf_id' => 6), 'id = :id', array(':id' => 54));
        $this->update('ophcocvi_clinicinfo_disorder', array('main_cause_pdf_id' => 8), 'id = :id', array(':id' => 55));
        $this->update('ophcocvi_clinicinfo_disorder', array('main_cause_pdf_id' => 9), 'id = :id', array(':id' => 56));
        $this->update('ophcocvi_clinicinfo_disorder', array('main_cause_pdf_id' => 10), 'id = :id', array(':id' => 57));
        $this->update('ophcocvi_clinicinfo_disorder', array('main_cause_pdf_id' => 11), 'id = :id', array(':id' => 58));
        $this->update('ophcocvi_clinicinfo_disorder', array('main_cause_pdf_id' => 12), 'id = :id', array(':id' => 59));
        $this->update('ophcocvi_clinicinfo_disorder', array('main_cause_pdf_id' => 14), 'id = :id', array(':id' => 60));
        $this->update('ophcocvi_clinicinfo_disorder', array('main_cause_pdf_id' => 15), 'id = :id', array(':id' => 61));
        $this->update('ophcocvi_clinicinfo_disorder', array('main_cause_pdf_id' => 16), 'id = :id', array(':id' => 62));
        $this->update('ophcocvi_clinicinfo_disorder', array('main_cause_pdf_id' => 17), 'id = :id', array(':id' => 63));
        $this->update('ophcocvi_clinicinfo_disorder', array('main_cause_pdf_id' => 18), 'id = :id', array(':id' => 64));
        $this->update('ophcocvi_clinicinfo_disorder', array('main_cause_pdf_id' => 19), 'id = :id', array(':id' => 65));
        $this->update('ophcocvi_clinicinfo_disorder', array('main_cause_pdf_id' => 21), 'id = :id', array(':id' => 66));
        $this->update('ophcocvi_clinicinfo_disorder', array('main_cause_pdf_id' => 22), 'id = :id', array(':id' => 67));
        $this->update('ophcocvi_clinicinfo_disorder', array('main_cause_pdf_id' => 23), 'id = :id', array(':id' => 68));
        $this->update('ophcocvi_clinicinfo_disorder', array('main_cause_pdf_id' => 25), 'id = :id', array(':id' => 69));
        $this->update('ophcocvi_clinicinfo_disorder', array('main_cause_pdf_id' => 26), 'id = :id', array(':id' => 70));
        $this->update('ophcocvi_clinicinfo_disorder', array('main_cause_pdf_id' => 27), 'id = :id', array(':id' => 71));
        $this->update('ophcocvi_clinicinfo_disorder', array('main_cause_pdf_id' => 29), 'id = :id', array(':id' => 72));
        $this->update('ophcocvi_clinicinfo_disorder', array('main_cause_pdf_id' => 31), 'id = :id', array(':id' => 73));
        $this->update('ophcocvi_clinicinfo_disorder', array('main_cause_pdf_id' => 32), 'id = :id', array(':id' => 74));
	}

	public function down()
	{
	}
}