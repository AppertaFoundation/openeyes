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
class m151217_215115_message_type_enhancement extends OEMigration
{
    public function up()
    {
        $this->addColumn('ophcomessaging_message_message_type', 'reply_required', 'boolean DEFAULT false NOT NULL');
        $this->addColumn('ophcomessaging_message_message_type_version', 'reply_required', 'boolean DEFAULT false NOT NULL');

        $this->insert('ophcomessaging_message_message_type', array('name' => 'Query', 'reply_required' => true, 'display_order' => 2));
    }

    public function down()
    {
        $this->delete('ophcomessaging_message_message_type_version', 'reply_required = true');
        $this->dropColumn('ophcomessaging_message_message_type_version', 'reply_required');
    }
}
