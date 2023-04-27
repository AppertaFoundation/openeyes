<?php

class m221004_044701_add_mailbox_models extends OEMigration
{
    public function safeUp()
    {
        $this->createOETable(
            'mailbox',
            array(
                'id' => 'pk',
                'name' => 'varchar(200) NOT NULL',
                'is_personal' => 'tinyint(1) unsigned DEFAULT 1',
                'active' => 'tinyint(1) unsigned DEFAULT 1',
            ),
            true
        );

        $this->createOETable(
            'mailbox_user',
            array(
                'id' => 'pk',
                'mailbox_id' => 'int(11) NOT NULL',
                'user_id' => 'int(10) unsigned NOT NULL',
            ),
            true
        );

        $this->createOETable(
            'mailbox_team',
            array(
                'id' => 'pk',
                'mailbox_id' => 'int(11) NOT NULL',
                'team_id' => 'int(11) NOT NULL',
            ),
            true
        );

        $this->addForeignKey(
            'mailbox_user_m_fk',
            'mailbox_user',
            'mailbox_id',
            'mailbox',
            'id'
        );

        $this->addForeignKey(
            'mailbox_user_u_fk',
            'mailbox_user',
            'user_id',
            'user',
            'id'
        );

        $this->addForeignKey(
            'mailbox_team_m_fk',
            'mailbox_team',
            'mailbox_id',
            'mailbox',
            'id'
        );

        $this->addForeignKey(
            'mailbox_team_t_fk',
            'mailbox_team',
            'team_id',
            'team',
            'id'
        );

        $this->bootstrapPersonalMailboxes();
    }

    protected function bootstrapPersonalMailboxes()
    {
        $all_users = $this->dbConnection->createCommand()
            ->select('u.id AS user_id, CONCAT(u.title, \' \', u.first_name, \' \', u.last_name) AS name')
            ->from('user u')
            ->queryAll();

        foreach ($all_users as $user) {
            $this->insert(
                'mailbox',
                [
                    'name' => trim($user['name']),
                    'is_personal' => '1',
                ]
            );

            $mailbox_id = $this->dbConnection->getLastInsertID();

            $this->insert(
                'mailbox_user',
                [
                    'mailbox_id' => $mailbox_id,
                    'user_id' => $user['user_id'],
                ]
            );
        }
    }

    public function safeDown()
    {
        $this->dropOEColumn(
            'user',
            'mailbox_id',
            true
        );

        $this->dropOETable(
            'mailbox_user',
            true
        );

        $this->dropOETable(
            'mailbox_team',
            true
        );

        $this->dropOETable(
            'mailbox',
            true
        );
    }
}
