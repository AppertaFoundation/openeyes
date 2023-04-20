<?php
/**
 * OpenEyes.
 *
 * (C) OpenEyes Foundation, 2023
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2012, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
class ClearExpiredDraftSavesCommand extends CConsoleCommand
{
    public function run($args)
    {
        $draft_lifetime_hours = 24;

        echo "Removing stale drafts\n";

        $rows_deleted = \EventDraft::model()->deleteAll(
            'is_auto_save = 1 AND (TIMESTAMP(NOW()) - TIMESTAMP(last_modified_date)) >= 3600 * :draft_lifetime_hours',
            [':draft_lifetime_hours' => $draft_lifetime_hours]
        );

        echo "Removed $rows_deleted stale drafts that were more than $draft_lifetime_hours hours old\n";

        return 0;
    }
}
