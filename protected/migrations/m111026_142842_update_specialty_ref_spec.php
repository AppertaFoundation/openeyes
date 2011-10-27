<?php

class m111026_142842_update_specialty_ref_spec extends CDbMigration
{
	public function up()
	{
		$refSpecs = array(
                        array(1,'Accident & Emergency','AE'),
                        array(2,'Adnexal','AD'),
                        array(3,'Anaesthetics','AN'),
                        array(4,'Cataract','CA'),
                        array(5,'Cornea','CO'),
                        array(6,'External','EX'),
                        array(7,'Glaucoma','GL'),
                        array(8,'Medical Retinal','MR'),
                        array(9,'Neuro-ophthalmology','PH'),
                        array(10,'Oncology','ON'),
                        array(11,'Paediatrics','PE'),
                        array(12,'Primary Care','PC'),
                        array(13,'Refractive','RF'),
                        array(14,'Strabismus','SP'),
                        array(15,'Uveitis','UV'),
                        array(16,'Vitreoretinal','VR')
		);

		foreach ($refSpecs as $refSpec) {
			$this->update('specialty', array('name' => $refSpec[1], 'ref_spec' => $refSpec[2]), "id=" . $refSpec[0]);
		}
	}

	public function down()
	{
	}
}
