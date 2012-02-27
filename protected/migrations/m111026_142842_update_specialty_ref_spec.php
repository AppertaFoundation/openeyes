<?php
/**
 * OpenEyes
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2012
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
 * @copyright Copyright (c) 2011-2012, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

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
