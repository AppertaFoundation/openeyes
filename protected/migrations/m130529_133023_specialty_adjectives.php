<?php

class m130529_133023_specialty_adjectives extends CDbMigration
{
	public function up()
	{
		$this->addColumn('specialty','adjective','varchar(64) COLLATE utf8_bin NOT NULL');

		$this->update('specialty',array('adjective'=>'ophthalmic'),"name='Ophthalmology'");
		$this->update('specialty',array('adjective'=>'urological'),"name='Urology'");
		$this->update('specialty',array('adjective'=>'orthodontic'),"name='Orthodontics'");
		$this->update('specialty',array('adjective'=>'endodontic'),"name='Endodontics'");
		$this->update('specialty',array('adjective'=>'periodontic'),"name='Periodontics'");
		$this->update('specialty',array('adjective'=>'prosthodontic'),"name='Prosthodontics'");
		$this->update('specialty',array('adjective'=>'neurosurgical'),"name='Neurosurgery'");
		$this->update('specialty',array('adjective'=>'cardiothoracic'),"name='Cardiothoracic Surgery'");
		$this->update('specialty',array('adjective'=>'paediatric'),"name='Paediatric Surgery'");
		$this->update('specialty',array('adjective'=>'anaesthetic'),"name='Anaesthetics'");
		$this->update('specialty',array('adjective'=>'gastroenterological'),"name='Gastroenterology'");
		$this->update('specialty',array('adjective'=>'endocrinological'),"name='Endocrinology'");
		$this->update('specialty',array('adjective'=>'haematological'),"name='Clinical Haematology'");
		$this->update('specialty',array('adjective'=>'physiological'),"name='Clinical Physiology'");
		$this->update('specialty',array('adjective'=>'pharmacological'),"name='Clinical Pharmacology'");
		$this->update('specialty',array('adjective'=>'audiological'),"name='Audiological Medicine'");
		$this->update('specialty',array('adjective'=>'genetic'),"name='Clinical Genetics'");
		$this->update('specialty',array('adjective'=>'cytogenic/molecular-genetic'),"name='Clinical Cytogenics and Molecular Genetics'");
		$this->update('specialty',array('adjective'=>'immunological/allergic'),"name='Clinical Immunology and Allergy'");
		$this->update('specialty',array('adjective'=>'rehabilitory'),"name='Rehabilitation'");
		$this->update('specialty',array('adjective'=>'palliative'),"name='Palliative Medicine'");
		$this->update('specialty',array('adjective'=>'cardiac'),"name='Cardiology'");
		$this->update('specialty',array('adjective'=>'paediatric'),"name='Paediatric Cardiology'");
		$this->update('specialty',array('adjective'=>'dermatological'),"name='Dermatology'");
		$this->update('specialty',array('adjective'=>'respiratory'),"name='Respiratory Medicine'");
		$this->update('specialty',array('adjective'=>'infectious-diseases'),"name='Infectious Diseases'");
		$this->update('specialty',array('adjective'=>'tropical-medicine'),"name='Tropical Medicine'");
		$this->update('specialty',array('adjective'=>'genitourinary'),"name='Genitourinary Medicine'");
		$this->update('specialty',array('adjective'=>'nephrological'),"name='Nephrology'");
		$this->update('specialty',array('adjective'=>'oncological'),"name='Medical Oncology'");
		$this->update('specialty',array('adjective'=>'nuclear'),"name='Nuclear Medicine'");
		$this->update('specialty',array('adjective'=>'neurological'),"name='Neurology'");
		$this->update('specialty',array('adjective'=>'neuro-physiological'),"name='Clinical Neuro-physiology'");
		$this->update('specialty',array('adjective'=>'rheumatological'),"name='Rheumatology'");
		$this->update('specialty',array('adjective'=>'paediatric'),"name='Paediatrics'");
		$this->update('specialty',array('adjective'=>'paediatric-neurology'),"name='Paediatric Neurology'");
		$this->update('specialty',array('adjective'=>'geriatric'),"name='Geriatric Medicine'");
		$this->update('specialty',array('adjective'=>'dental'),"name='Dental Medicine Specialties'");
		$this->update('specialty',array('adjective'=>'medical-ophthalmic'),"name='Medical Ophthalmology'");
		$this->update('specialty',array('adjective'=>'obstetrical/gynaecological'),"name='Obstetrics and Gynaecology'");
		$this->update('specialty',array('adjective'=>'obstetrical'),"name='Obstetrics'");
		$this->update('specialty',array('adjective'=>'gynaecological'),"name='Gynaecology'");
		$this->update('specialty',array('adjective'=>'forensic-psychiatry'),"name='Forensic Psychiatry'");
		$this->update('specialty',array('adjective'=>'psychotherapy'),"name='Psychotherapy'");
		$this->update('specialty',array('adjective'=>'old-age-psychiatric'),"name='Old Age Psychiatry'");
		$this->update('specialty',array('adjective'=>'clinical-oncology'),"name='Clinical Oncology'");
		$this->update('specialty',array('adjective'=>'radiological'),"name='Radiology'");
		$this->update('specialty',array('adjective'=>'haematological'),"name='Haematology'");
		$this->update('specialty',array('adjective'=>'histopathological'),"name='Histopathology'");
		$this->update('specialty',array('adjective'=>'immunopathological'),"name='Immunopathology'");
	}

	public function down()
	{
		$this->dropColumn('specialty','adjective');
	}
}
