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

class m221004_044749_rework_messaging_data_models extends OEMigration
{
    public function safeUp()
    {
        $this->dropForeignKey(
            'ophcomessaging_message_copyto_users_user_id_fk',
            'ophcomessaging_message_copyto_users'
        );

        $this->renameOETable(
            'ophcomessaging_message_copyto_users',
            'ophcomessaging_message_recipient',
            true
        );

        $this->renameOEColumn(
            'ophcomessaging_message_recipient',
            'user_id',
            'mailbox_id',
            true
        );

        $this->alterOEColumn(
            'ophcomessaging_message_recipient',
            'mailbox_id',
            'int(11) NOT NULL',
            true
        );

        $this->addOEColumn(
            'ophcomessaging_message_recipient',
            'primary_recipient',
            'tinyint(1) unsigned DEFAULT 0',
            true
        );

        $this->addOEColumn(
            'et_ophcomessaging_message',
            'sender_mailbox_id',
            'int(11)',
            true
        );

        $users_to_update = $this->dbConnection->createCommand()
            ->select('u.id AS user_id, mu.mailbox_id')
            ->from('et_ophcomessaging_message m')
            ->join('user u', 'u.id = m.created_user_id')
            ->join('mailbox_user mu', 'mu.user_id = u.id')
            ->group('u.id, mu.mailbox_id')
            ->queryAll();

        foreach ($users_to_update as $user) {
            $this->update(
                'et_ophcomessaging_message',
                ['sender_mailbox_id' => $user['mailbox_id']],
                'created_user_id = :id',
                [':id' => $user['user_id']]
            );
            $this->update(
                'et_ophcomessaging_message_version',
                ['sender_mailbox_id' => $user['mailbox_id']],
                'created_user_id = :id',
                [':id' => $user['user_id']]
            );
        }

        $this->alterOEColumn(
            'et_ophcomessaging_message',
            'sender_mailbox_id',
            'int(11) NOT NULL',
            true
        );

        $this->addForeignKey(
            'et_ophcomessaging_message_sender_fk',
            'et_ophcomessaging_message',
            'sender_mailbox_id',
            'mailbox',
            'id'
        );

        // Existing messages should be re-pointed to the mailbox of the associated user.
        // the recipient mailbox_id column was renamed from user_id, so we join on that
        // to derive what value it should be set to:
        $update_sql = <<<EOSQL
        UPDATE ophcomessaging_message_recipient recipient
JOIN mailbox_user mu ON recipient.mailbox_id = mu.user_id
SET recipient.mailbox_id = mu.mailbox_id;
EOSQL;

        $this->getDbConnection()
                ->createCommand($update_sql)
                ->execute();

        $this->addForeignKey(
            'ophcomessaging_message_recipient_m_fk',
            'ophcomessaging_message_recipient',
            'mailbox_id',
            'mailbox',
            'id'
        );

        $select = <<<EOSQL
        SELECT m.id AS element_id, mu.mailbox_id AS mailbox_id, m.marked_as_read AS marked_as_read, 1 AS primary_recipient
        FROM et_ophcomessaging_message m
        JOIN mailbox_user mu ON mu.user_id = m.for_the_attention_of_user_id
        WHERE m.for_the_attention_of_user_id IS NOT NULL
        EOSQL;

        $rows_to_insert = $this->dbConnection->createCommand($select)
            ->queryAll();

        $this->insertMultiple(
            'ophcomessaging_message_recipient',
            $rows_to_insert
        );

        $this->dropOEColumn('et_ophcomessaging_message', 'marked_as_read', true);
        $this->dropForeignKey(
            'et_ophcomessaging_message_ftao_fk',
            'et_ophcomessaging_message'
        );
        $this->dropForeignKey(
            'acv_et_ophcomessaging_message_ftao_fk',
            'et_ophcomessaging_message_version'
        );
        $this->dropOEColumn('et_ophcomessaging_message', 'for_the_attention_of_user_id', true);
    }

    public function down()
    {
        echo 'This migration does not support down migration\n';
        return false;
    }
}
