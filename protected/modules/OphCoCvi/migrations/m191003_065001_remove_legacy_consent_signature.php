<?php
/**
 * (C) Copyright Apperta Foundation 2021
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2021, Apperta Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

class m191003_065001_remove_legacy_consent_signature extends CDbMigration
{
    public function up()
    {

        $this->execute("DELETE FROM element_type
            WHERE class_name = :class_name
            AND event_type_id = (SELECT id FROM event_type WHERE `name` = 'CVI')",
            array(
                ":class_name" => "OEModule\\OphCoCvi\\models\\Element_OphCoCvi_ConsentSignature"
            ));
    }

    public function down()
    {
        $this->execute("INSERT INTO openeyes.element_type 
		    (name, class_name, last_modified_user_id, last_modified_date, created_user_id, created_date, event_type_id, display_order, `default`, parent_element_type_id, required)
		    VALUES ('Consent Signature', 'OEModule\\OphCoCvi\\models\\Element_OphCoCvi_ConsentSignature', 1, '2019-10-03 08:48:04', 1, '2019-10-03 08:48:04', 23, 20, 1, null, 1);");
    }
}
