<?php

class m110704_153654_create_cancellation_reasons extends CDbMigration
{
	// Use safeUp/safeDown to do migration with transaction
	public function safeUp()
	{
		$this->renameTable('cancelled_reason', 'cancellation_reason');
		
		$this->addColumn('cancellation_reason', 'list_no', 'tinyint(2) unsigned NOT NULL');
		
		$reasons = array(
			array('Booked in error',1),
			array('Patient has died',1),
			array('Patient has not replied',1),
			array('Patient refused operation',1),
			array('Patient',2),
			array('Hospital Medical',2),
			array('Hospital Non-Medical',2),
			array('Error',2),
			array('No Time List Too Long',3),
			array('No Donor Material Available',3),
			array('Equipment Unavailable/Failure',3),
			array('Consent Form Not Signed',3),
			array('No Backup Support',3),
			array('OP To Be Done By Consultant-Cons Sick',3),
			array('Cancelled In Pre-Assess',3),
			array('Air Change Failure',3),
			array('Room Tempureature Failure-Hot/Cold',3),
			array('DNA',3),
			array('Power Supply Failure/No Gen Backup',3),
			array('RIP',3),
			array('Patient Gone Privately',3),
			array('Patient Did Not Want To Wait/Operation',3),
			array('Operation To Be Done By Consultant',3),
			array('Awaiting Results/Investigations',3),
			array('Cardiac Arrest',3),
			array('Anaesthetic Complications',3),
			array('Transport Did Not Arrive',3),
			array('Raised Intraocular Pressure',3),
			array('Retrobulbar Haemorrhage',3),
			array('Patient Unsuitable For LA',3),
			array('No Notes',3),
			array('No Consultant Cover',3),
			array('Special Lens Needed - Request Not In',3),
			array('LA List No Anaesthetic Cover',3),
			array('ASA 3 Patient / Too Late For Surgery',3),
			array('Rescheduled For Another Day',3),
			array('Did Not Receive Letter',3),
			array('Wrong Date Given',3),
			array('No Pre-Assessment',3),
			array('Patient Referred To Another Service',3),
			array('No Surgeon Available',3),
			array('Patient Not Fit For Surgery',3),
			array('Eye Infection',3),
			array('Anticoagulation Failure',3),
			array('No Funds',3),
			array('Operation Already Done',3),
			array('Added In Error',3),
			array('Patient Wants To Become NHS',3),
			array('At Surgeon\'s Req - Complication Prev; Case',3),
			array('Patient Did Not Stop Medication',3),
			array('No Biometry/Refraction/To Be Redone',3),
			array('No Anaesthetist Available',3),
			array('Major Incident',3),
			array('OP Canceled By Patient - Less Than 24 Hours',3),
			array('Patient Unwell',3),
			array('Surgeon Late Arrival - No OP Time Left',3),
			array('Patient In Another Hospital',3),
			array('Patient Unable To Take Medication',3),
			array('Faulty Items/Damaged Cornea',3),
			array('Latex Allergy',3),
			array('MRSA Positive',3),
			array('No Anaesthetic Support Staff Available',3),
			array('Microbiological Test Failure',3),
			array('Anaesthetist Delayed On AM List',3),
			array('Needs Anaesthetic Cover',3),
			array('No Time PP Booked On List',3),
			array('Morning List Overran',3),
			array('No Implant',3),
			array('Trial Lens Not Available',3),
			array('Booked In Error',3),
			array('Patient Listed For Wrong Procedure',3),
			array('No Nursing Staff/ODPS Available',3),
			array('Patient Not Fasted For GA',3),
			array('Transport Facilities/PT Escort Not Available',3),
			array('Patient Wants A GA',3),
			array('Operation Done In Ward/OPD',3),
			array('Incorrect Treatment Given (Drops)',3),
			array('Anaesthetist Late Arrival No OP Time Left',3),
			array('Needs Further Investigations',3),
			array('Lens Orders Not Suitable For Patient',3),
			array('To Be Done By Consultant Patient Choice',3),
			array('OP Canceled By Patient - Greater Than 24 Hours',3),
			array('No Time Due To Emergency',3),
			array('Drug Unavailable',3),
			array('Patient Informed Not To Come In',3),
			array('Surgery No Longer Required',3)
		);
		
		$command = $this->dbConnection->createCommand('SET foreign_key_checks = 0;');
		$command->execute();

		echo "    > insert into cancellation_reason\n";
		$sql = 'INSERT INTO cancellation_reason (text, list_no) VALUES ';
		foreach ($reasons as $data) {
			$sql .= "('" . addslashes($data[0]) . "', {$data[1]})";
			if ($data != end($reasons)) {
				$sql .= ', ';
			}
		}
		$command = $this->dbConnection->createCommand($sql);
		$command->execute();

		$command = $this->dbConnection->createCommand('SET foreign_key_checks = 1;');
		$command->execute();
	}

	public function safeDown()
	{
		$command = $this->dbConnection->createCommand('SET foreign_key_checks = 0;');
		$command->execute();
		
		$this->truncateTable('cancellation_reason');

		$command = $this->dbConnection->createCommand('SET foreign_key_checks = 1;');
		$command->execute();
		
		$this->dropColumn('cancellation_reason', 'list_no');
		$this->renameTable('cancellation_reason', 'cancelled_reason');
	}
}