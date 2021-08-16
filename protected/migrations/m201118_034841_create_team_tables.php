<?php

class m201118_034841_create_team_tables extends OEMigration
{
    // Use safeUp/safeDown to do migration with transaction
    public function safeUp()
    {
        $this->insert(
            'contact_label',
            array(
                'name' => 'Team',
                'active' => 1
            )
        );

        // team table START
        $this->createOETable('team', array(
            'id' => 'pk',
            'name' => 'varchar(255) NOT NULL',
            'institution_id' => 'int(10) unsigned NOT NULL',
            'contact_id' => 'int(10) unsigned DEFAULT NULL',
            'active' => 'boolean',
        ), true);
        $this->addForeignKey('team_contact_fk', 'team', 'contact_id', 'contact', 'id');
        $this->addForeignKey('team_institution_fk', 'team', 'institution_id', 'institution', 'id');
        // team table END

        // team user assign table to handle many to many relationships between team and user START
        $this->createOETable('team_user_assign', array(
            'id' => 'pk',
            'team_id' => 'int(11) NOT NULL',
            'user_id' => 'int(10) unsigned NOT NULL',
        ), true);

        $this->addForeignKey('team_user_assign_team_fk', 'team_user_assign', 'team_id', 'team', 'id');
        $this->addForeignKey('team_user_assign_user_fk', 'team_user_assign', 'user_id', 'user', 'id');
        // team user assign table END

        // team team assign table to handle many to many relationships between team and team START
        $this->createOETable('team_team_assign', array(
            'id' => 'pk',
            'parent_team_id' => 'int(11) NOT NULL',
            'child_team_id' => 'int(11) NOT NULL',
        ), true);

        $this->addForeignKey('team_team_assign_parent_fk', 'team_team_assign', 'parent_team_id', 'team', 'id');
        $this->addForeignKey('team_team_assign_child_fk', 'team_team_assign', 'child_team_id', 'team', 'id');
        // team team assign table END
    }

    public function safeDown()
    {
        // drop relationships between team and user
        $this->dropForeignKey('team_user_assign_team_fk', 'team_user_assign');
        $this->dropForeignKey('team_user_assign_user_fk', 'team_user_assign');
        // drop relationships between team and team
        $this->dropForeignKey('team_team_assign_parent_fk', 'team_team_assign');
        $this->dropForeignKey('team_team_assign_child_fk', 'team_team_assign');

        // drop relationships between team and contact
        $this->dropForeignKey('team_contact_fk', 'team');
        $this->dropForeignKey('team_institution_fk', 'team');

        // drop tables
        $this->dropOETable('team_user_assign', true);
        $this->dropOETable('team_team_assign', true);
        // get team contact ids
        $contact_ids = $this->dbConnection->createCommand()
            ->select('contact_id')
            ->from('team')
            ->queryColumn();
        // drop team table
        $this->dropOETable('team', true);
        // delete corresponding team contact
        foreach ($contact_ids as $contact_id) {
            $this->delete('contact', 'id = :id', array(':id' => $contact_id));
        }
        // delete contact label
        $this->delete('contact_label', 'name = :name', array(':name' => 'Team'));
    }
}
