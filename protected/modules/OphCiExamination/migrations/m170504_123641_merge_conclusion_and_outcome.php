<?php
/**
 * OpenEyes.
 *
 * (C) OpenEyes Foundation, 2019
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

class m170504_123641_merge_conclusion_and_outcome extends OEMigration
{
	public function up()
	{
	    $this->addColumn('et_ophciexamination_clinicoutcome', 'description', 'text');
        $this->addColumn('et_ophciexamination_clinicoutcome_version', 'description', 'text');

        // move attributes from conclusion to outcome
        // Commented out as per OE-6661 which reveals that moving these shortcuts to outcome doesn't make sense at the current time
//        $outcome_element_type_id = $this->getIdOfElementTypeByClassName('OEModule\OphCiExamination\models\Element_OphCiExamination_ClinicOutcome');
//        $conclusion_element_type_id = $this->getIdOfElementTypeByClassName('OEModule\OphCiExamination\models\Element_OphCiExamination_Conclusion');
//
//        $this->update(
//            'ophciexamination_attribute_element',
//            array('element_type_id' => $outcome_element_type_id),
//            'element_type_id = :eid',
//            array(':eid' => $conclusion_element_type_id)
//        );
	}

	public function down()
	{
//        $outcome_element_type_id = $this->getIdOfElementTypeByClassName('OEModule\OphCiExamination\models\Element_OphCiExamination_ClinicOutcome');
//        $conclusion_element_type_id = $this->getIdOfElementTypeByClassName('OEModule\OphCiExamination\models\Element_OphCiExamination_Conclusion');
//
//        $this->update(
//            'ophciexamination_attribute_element',
//            array('element_type_id' => $conclusion_element_type_id),
//            'element_type_id = :eid',
//            array(':eid' => $outcome_element_type_id)
//        );

		$this->dropColumn('et_ophciexamination_clinicoutcome_version', 'description');
        $this->dropColumn('et_ophciexamination_clinicoutcome', 'description');
	}
}