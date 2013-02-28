<?php

class m130228_152358_new_unbooked_procedures extends CDbMigration
{
	public function up()
	{
		$this->update('proc',array('unbooked'=>1),"term='Application of 5FU' and 'snomed_code'='417129009'");
		$this->update('proc',array('unbooked'=>1),"term='Application of MMC' and 'snomed_code'='416968006'");
		$this->update('proc',array('unbooked'=>1),"term='Insertion of bandage contact lens' and 'snomed_code'='428497007'");
		$this->update('proc',array('unbooked'=>1),"term='Capsulotomy (YAG)' and 'snomed_code'='172532006'");
		$this->update('proc',array('unbooked'=>1),"term='Corneal glue' and 'snomed_code'='503003'");
		$this->update('proc',array('unbooked'=>1),"term='Corneal suture adjustment' and 'snomed_code'='172421008'");
		$this->update('proc',array('unbooked'=>1),"term='Corneal suture removal' and 'snomed_code'='172423006'");
		$this->update('proc',array('unbooked'=>1),"term='Cryotherapy retinopexy' and 'snomed_code'='428435002'");
		$this->update('proc',array('unbooked'=>1),"term='Dacrocystogram' and 'snomed_code'='56087001'");
		$this->update('proc',array('unbooked'=>1),"term='Excision of lesion of conjunctiva' and 'snomed_code'='39346000'");
		$this->update('proc',array('unbooked'=>1),"term='Excision of lid lesion - no biopsy' and 'snomed_code'='172212009'");
		$this->update('proc',array('unbooked'=>1),"term='Fluorescein angiography' and 'snomed_code'='172581008'");
		$this->update('proc',array('unbooked'=>1),"term='Incision and curettage of cyst' and 'snomed_code'='388289006'");
		$this->update('proc',array('unbooked'=>1),"term='Incision and curettage of cyst' and 'snomed_code'='388289006'");
		$this->update('proc',array('unbooked'=>1),"term='Moulding of socket' and 'snomed_code'='6444006'");
		$this->update('proc',array('unbooked'=>1),"term='Needling of bleb' and 'snomed_code'='408763006'");
		$this->update('proc',array('unbooked'=>1),"term='Panretinal photocoagulation' and 'snomed_code'='312713003'");
		$this->update('proc',array('unbooked'=>1),"term='Punctoplasty' and 'snomed_code'='172309008'");
		$this->update('proc',array('unbooked'=>1),"term='Punctum closure' and 'snomed_code'='27374006'");
		$this->update('proc',array('unbooked'=>1),"term='Reformation of AC' and 'snomed_code'='172517004'");
		$this->update('proc',array('unbooked'=>1),"term='Removal of corneal foreign body' and 'snomed_code'='172426003'");
		$this->update('proc',array('unbooked'=>1),"term='Removal of corneal suture' and 'snomed_code'='172423006'");
		$this->update('proc',array('unbooked'=>1),"term='Suture of cornea' and 'snomed_code'='62428002'");
		$this->update('proc',array('unbooked'=>1),"term='Suture of cornea' and 'snomed_code'='62428002'");
		$this->update('proc',array('unbooked'=>1),"term='Syringe and probe nasolacrimal duct' and 'snomed_code'='275151000'");
	}

	public function down()
	{
		$this->update('proc',array('unbooked'=>0),"term='Application of 5FU' and 'snomed_code'='417129009'");
		$this->update('proc',array('unbooked'=>0),"term='Application of MMC' and 'snomed_code'='416968006'");
		$this->update('proc',array('unbooked'=>0),"term='Insertion of bandage contact lens' and 'snomed_code'='428497007'");
		$this->update('proc',array('unbooked'=>0),"term='Capsulotomy (YAG)' and 'snomed_code'='172532006'");
		$this->update('proc',array('unbooked'=>0),"term='Corneal glue' and 'snomed_code'='503003'");
		$this->update('proc',array('unbooked'=>0),"term='Corneal suture adjustment' and 'snomed_code'='172421008'");
		$this->update('proc',array('unbooked'=>0),"term='Corneal suture removal' and 'snomed_code'='172423006'");
		$this->update('proc',array('unbooked'=>0),"term='Cryotherapy retinopexy' and 'snomed_code'='428435002'");
		$this->update('proc',array('unbooked'=>0),"term='Dacrocystogram' and 'snomed_code'='56087001'");
		$this->update('proc',array('unbooked'=>0),"term='Excision of lesion of conjunctiva' and 'snomed_code'='39346000'");
		$this->update('proc',array('unbooked'=>0),"term='Excision of lid lesion - no biopsy' and 'snomed_code'='172212009'");
		$this->update('proc',array('unbooked'=>0),"term='Fluorescein angiography' and 'snomed_code'='172581008'");
		$this->update('proc',array('unbooked'=>0),"term='Incision and curettage of cyst' and 'snomed_code'='388289006'");
		$this->update('proc',array('unbooked'=>0),"term='Incision and curettage of cyst' and 'snomed_code'='388289006'");
		$this->update('proc',array('unbooked'=>0),"term='Moulding of socket' and 'snomed_code'='6444006'");
		$this->update('proc',array('unbooked'=>0),"term='Needling of bleb' and 'snomed_code'='408763006'");
		$this->update('proc',array('unbooked'=>0),"term='Panretinal photocoagulation' and 'snomed_code'='312713003'");
		$this->update('proc',array('unbooked'=>0),"term='Punctoplasty' and 'snomed_code'='172309008'");
		$this->update('proc',array('unbooked'=>0),"term='Punctum closure' and 'snomed_code'='27374006'");
		$this->update('proc',array('unbooked'=>0),"term='Reformation of AC' and 'snomed_code'='172517004'");
		$this->update('proc',array('unbooked'=>0),"term='Removal of corneal foreign body' and 'snomed_code'='172426003'");
		$this->update('proc',array('unbooked'=>0),"term='Removal of corneal suture' and 'snomed_code'='172423006'");
		$this->update('proc',array('unbooked'=>0),"term='Suture of cornea' and 'snomed_code'='62428002'");
		$this->update('proc',array('unbooked'=>0),"term='Suture of cornea' and 'snomed_code'='62428002'");
		$this->update('proc',array('unbooked'=>0),"term='Syringe and probe nasolacrimal duct' and 'snomed_code'='275151000'");
	}
}
