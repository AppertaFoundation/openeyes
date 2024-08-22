<?php

class m221127_214051_add_team_authroles extends OEMigration
{
	private const OWNER_OPERATIONS = [
		'OprnSetTeamActivation',
		'OprnChangeTeamMemberRole',
		'OprnShowAllUsersInAdder',
		'OprnAddTeamMember',
		'OprnRemoveTeamMember',
	];

	private const MANAGE_OPERATIONS = [
		'OprnAddTeamMember',
		'OprnRemoveTeamMember',
	];

	// Use safeUp/safeDown to do migration with transaction
	public function safeUp()
	{
		$this->addTask('TaskOwnTeam', 'Owner');
		$this->addTask('TaskManageTeam', 'Manager');
		$this->addTask('TaskMemberOfTeam', 'Member');

		$this->addOperation('OprnSetTeamActivation', 'Allows changes to team activation flag');
		$this->addOperation('OprnChangeTeamMemberRole', 'Allows team roles for members to be changed');
		$this->addOperation('OprnShowAllUsersInAdder', 'Allow users from all institutions to be added', 'canShowAllUsersInTeamUserAdder');

		$this->addOperation('OprnAddTeamMember', 'Allow members to be added to a team');
		$this->addOperation('OprnRemoveTeamMember', 'Allow members to be removed from a team');

		foreach (self::OWNER_OPERATIONS as $operation) {
			$this->addOperationToTask($operation, 'TaskOwnTeam');
		}

		foreach (self::MANAGE_OPERATIONS as $operation) {
			$this->addOperationToTask($operation, 'TaskManageTeam');
		}
	}

	public function safeDown()
	{
		foreach (self::MANAGE_OPERATIONS as $operation) {
			$this->removeOperationFromTask($operation, 'TaskManageTeam');
		}

		foreach (self::OWNER_OPERATIONS as $operation) {
			$this->removeOperationFromTask($operation, 'TaskOwnTeam');
		}

		$this->removeOperation('OprnRemoveTeamMember');
		$this->removeOperation('OprnAddTeamMember');

		$this->removeOperation('OprnShowAllUsersInAdder');
		$this->removeOperation('OprnChangeTeamMemberRole');
		$this->removeOperation('OprnSetTeamActivation');

		$this->removeTask('TaskMemberOfTeam');
		$this->removeTask('TaskManageTeam');
		$this->removeTask('TaskOwnTeam');
	}
}
