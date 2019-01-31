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
class m151215_105050_add_message_comment extends OEMigration
{
    public function up()
    {
        $this->createOETable('ophcomessaging_message_comment', array(
            'id' => 'pk',
            'comment_text' => 'text DEFAULT \'\' NOT NULL',
            'element_id' => 'int(10) unsigned NOT NULL',
            'KEY `ophcomessaging_message_comment_element_id_fk` (`element_id`)',
            'CONSTRAINT `ophcomessaging_message_comment_element_id_fk` FOREIGN KEY (`element_id`) REFERENCES `et_ophcomessaging_message` (`id`)',
        ), true);
    }

    public function down()
    {
        $this->dropOETable('ophcomessaging_message_comment', true);
    }
}
