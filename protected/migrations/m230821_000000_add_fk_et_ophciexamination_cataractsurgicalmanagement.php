<?php

/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2023
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2023, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

class m230821_000000_add_fk_et_ophciexamination_cataractsurgicalmanagement extends OEMigration
{
    private const TABLE_NAME = 'et_ophciexamination_cataractsurgicalmanagement';
    private const COLUMN_NAME = 'event_id';
    private const FK_TABLE_NAME = 'event';
    private const FK_COLUMN_NAME = 'id';
    private const INDEX_NAME = 'et_ophciexamination_cataractsurgicalmanagement_event_id_fk';

    public function safeUp()
    {
        $this->addOrUpdateForeignKey(self::INDEX_NAME, self::TABLE_NAME, self::COLUMN_NAME, self::FK_TABLE_NAME, self::FK_COLUMN_NAME);
    }

    public function safeDown()
    {
        $this->dropForeignKeyIfExists(self::TABLE_NAME, self::COLUMN_NAME);
    }
}
