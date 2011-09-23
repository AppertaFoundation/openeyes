<?php
/*
_____________________________________________________________________________
(C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
(C) OpenEyes Foundation, 2011
This file is part of OpenEyes.
OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
_____________________________________________________________________________
http://www.openeyes.org.uk   info@openeyes.org.uk
--
*/

class m110405_105030_create_nsc_grade extends CDbMigration
{
	public function up()
	{
		$this->createTable('nsc_grade', array(
			'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
			'name' => 'char(3) COLLATE utf8_bin NOT NULL',
			'type' => "tinyint(1) DEFAULT '0'",
			'medical_phrase' => "varchar(5000) COLLATE utf8_bin NOT NULL",
			'layman_phrase' => "varchar(1000) COLLATE utf8_bin NOT NULL",
			'PRIMARY KEY (`id`)',
			'UNIQUE KEY `name` (`name`)'
		), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin');

		$grades = array(
			// Name, Type, Medical Phrase, Layman Phrase
			// Type: 1 = Retinopathy, 2 = Maculopathy
			"('R0', 1, 'None', 'No diabetic retinopathy (NSC R0)')",
			"('R1', 1, \"Microaneurysm(s)\nRetinal haemorrhage(s) ± any exudate\", 'Mild to moderate non-proliferative diabetic retinopathy (NSC R1)')",
			"('R2', 1, \"Venous beading\nVenous loop or reduplication\nIntraretinal microvascular abnormality (IRMA)\nMultiple deep, round or blot haemorrhages - Cotton Wool Spots (CWS)\", 'Severe non-proliferative diabetic retinopathy (NSC R2)')",
			"('R3', 1, \"New vessels on disk (NVD)\nNew vessels elsewhere (NVE)\nPre-retinal or vitreous haemorrhage\nPre-retinal fibrosis ± tractional retinal detachment\", 'Proliferative diabetic retinopathy (NSC R3)')",
			"('RP1', 1, \"Focal/grid to macula\", 'Previous diabetic retinopathy treated with scatter laser (NSC P)')",
			"('RP2', 1, \"Peripheral scatter\", 'Previous diabetic retinopathy treated with scatter laser (NSC P)')",
			"('RU', 1, \"Ungradable/unobtainable\", 'The retina was ungradable (NSC U)')",
			"('M0', 2, 'None', 'No diabetic maculopathy (NSC M0)')",
			"('M1', 2, \"Exudate within 1 disc diameter (DD) of the centre of the fovea\nCircinate or group of exudates within the macula\nRetinal thickening within 1DD of the centre of the fovea (if stereo available)\nAny microaneurysm of haemorrhage within 1DD of the centre of the fovea only if associated with a best visual acuity of ≤ 6/12 (if no stereo)\", 'Diabetic maculopathy (NSC M1)')",
			"('MP1', 2, \"Focal/grid to macula\", 'Previous diabetic maculopathy treated with laser (NSC P)')",
			"('MP2', 2, \"Peripheral scatter\", 'Previous diabetic maculopathy treated with laser (NSC P)')",
			"('MU', 2, \"Ungradable/unobtainable\", 'Macula was ungradable (NSC U)')",
		);

		$command = $this->dbConnection->createCommand('SET foreign_key_checks = 0;');
		$command->execute();

		$sql = "INSERT INTO `nsc_grade` (`name`, `type`, `medical_phrase`, `layman_phrase`) VALUES\n";
		foreach ($grades as $values) {
			$sql .= $values;
			if ($values != end($grades)) {
				$sql .= ", ";
			}
			$sql .= "\n";
		}
		$command = $this->dbConnection->createCommand($sql);
		echo "    > inserting into nsc_grade\n";
		$command->execute();

		$this->dropColumn('element_nsc_grade', 'grade');
		
		$this->addColumn('element_nsc_grade', 'retinopathy_grade_id', 'int(10) unsigned NOT NULL');
		$this->addColumn('element_nsc_grade', 'maculopathy_grade_id', 'int(10) unsigned NOT NULL');

		$command = $this->dbConnection->createCommand('SET foreign_key_checks = 1;');
		$command->execute();
	}

	public function down()
	{
		$this->dropTable('nsc_grade');
		
		$this->addColumn('element_nsc_grade', 'grade', "char(3) NOT NULL DEFAULT ''");
		$this->dropColumn('element_nsc_grade', 'retinopathy_grade_id');
		$this->dropColumn('element_nsc_grade', 'maculopathy_grade_id');
	}
}
