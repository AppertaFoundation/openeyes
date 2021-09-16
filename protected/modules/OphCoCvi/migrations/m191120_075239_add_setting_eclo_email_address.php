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

class m191120_075239_add_setting_eclo_email_address extends CDbMigration
{
    public function up()
    {
        $this->insert("setting_metadata", array(
            "element_type_id" => null,
            "key" => "eclo_email",
            "name" => "ECLO email address",
            "default_value" => "moorfields.cityroadeclo@nhs.net",
            "field_type_id" => 4,
        ));
    }

    public function down()
    {
        $this->execute("DELETE FROM setting_metadata WHERE `key` = 'eclo_email'");
    }
}
