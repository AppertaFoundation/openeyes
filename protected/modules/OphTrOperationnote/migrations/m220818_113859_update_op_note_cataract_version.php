<?php
/**
 * (C) OpenEyes Foundation, 2022
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2022, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

class m220818_113859_update_op_note_cataract_version extends OEMigration
{
    //this was missed in m170321_183126_predicted_refraction_nullable_in_element_ophtroperationnote_cataract
    public function safeUp()
    {
        $this->alterColumn('et_ophtroperationnote_cataract_version', 'predicted_refraction', 'decimal(4,2) NULL');
        $this->alterColumn('et_ophtroperationnote_cataract_version', 'iol_power', 'VARCHAR(5) NULL');
    }

    public function safeDown()
    {
        $this->alterColumn('et_ophtroperationnote_cataract_version', 'predicted_refraction', 'decimal(4,2) NOT NULL');
        $this->alterColumn('et_ophtroperationnote_cataract_version', 'iol_power', 'VARCHAR(5) NOT NULL');
    }
}
