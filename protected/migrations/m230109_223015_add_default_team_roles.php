<?php
/**
 * (C) Apperta Foundation, 2023
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2023, Apperta Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

class m230109_223015_add_default_team_roles extends OEMigration
{
    private const TEAM_ASSIGNMENT_BIZ_RULE = 'hasTeamAssignment';

    // Use safeUp/safeDown to do migration with transaction
    public function safeUp()
    {
        $user_ids = $this->dbConnection->createCommand(
            'SELECT user_id AS userid, GROUP_CONCAT(team_id) AS teams FROM team_user_assign GROUP BY user_id'
        )->queryAll();

        if(!empty($user_ids)) {
            $auth_data = array_map(
                static function ($user) {
                    return array(
                        'userid' => $user['userid'],
                        'bizrule' => self::TEAM_ASSIGNMENT_BIZ_RULE,
                        'data' => serialize(explode(',', $user['teams'])),
                        'itemname' => Team::TASK_MEMBER,
                    );
                },
                $user_ids
            );

            $this->insertMultiple(
                'authassignment',
                $auth_data
            );
        }
    }

    public function safeDown()
    {
        echo "This migration does not support down migration.\n";
        return false;
    }
}
