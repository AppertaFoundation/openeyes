<?php
use OE\factories\ModelFactory;

/**
 * class TeamTest
 * @covers Team
 * @covers TeamUserAssign
 * @covers TeamTeamAssign
 * @group shared-mailboxes
 * @group sample-data
 */
class TeamTest extends ModelTestCase
{
    use WithTransactions;

    protected $element_cls = Team::class;

    /** @test */
    public function team_without_users_is_saved_as_inactive()
    {
        $team = ModelFactory::factoryFor(Team::class)->create(['active' => '1']);

        $this->assertEquals($team->active, 0);
    }

    /** @test */
    public function team_with_users_defaults_to_being_saved_as_active()
    {
        $team = ModelFactory::factoryFor(Team::class)
              ->withUsers(ModelFactory::factoryFor(User::class)->count(2)->create())
              ->create();

        $this->assertEquals($team->active, 1);
    }

    /** @test */
    public function team_with_users_can_be_saved_as_inactive()
    {
        $team = ModelFactory::factoryFor(Team::class)
              ->withUsers(ModelFactory::factoryFor(User::class)->count(2)->create())
              ->create(['active' => '0']);

        $this->assertEquals($team->active, 0);
    }

    /** @test */
    public function saved_team_with_child_teams_is_active()
    {
        $child_team = ModelFactory::factoryFor(Team::class)->create();

        $team = ModelFactory::factoryFor(Team::class)
              ->withTeams([$child_team])
              ->create();

        $this->assertEquals($team->active, 1);
    }

    /** @test */
    public function cannot_save_team_with_non_leaf_child_teams()
    {
        $child_child_team = ModelFactory::factoryFor(Team::class)->create();

        $child_team = ModelFactory::factoryFor(Team::class)
                    ->withTeams([$child_child_team])
                    ->create();

        $team = ModelFactory::factoryFor(Team::class)
              ->withTeams([$child_team])
              ->make();

        $saved = $team->save();

        $this->assertEquals($saved, false);
    }

    /** @test */
    public function setUserTasks_can_change_users_team_task()
    {
        $user = ModelFactory::factoryFor(User::class)->create();

        $team = ModelFactory::factoryFor(Team::class)
              ->withUsers([$user])
              ->withTasks([$user->id => Team::TASK_MEMBER])
              ->create();

        $tasks_before = $team->getUserTaskMappings();

        $team->setUserTasks(
            [$user->id => Team::TASK_MANAGER]
        );

        $tasks_after = $team->getUserTaskMappings();

        $this->assertArrayHasKey($user->id, $tasks_before, 'The test user\'s auth data does not contain a mapping for the test team, on creation');
        $this->assertArrayHasKey($user->id, $tasks_after, 'The test user\'s auth data does not contain a mapping for the test team, after changing their team task');
        $this->assertNotEquals($tasks_before[$user->id], $tasks_after[$user->id], 'The test user\'s team task mapping did not change');
    }

    /** @test */
    public function setUserTasks_preserves_unchanged_team_task()
    {
        $unchanging_user = ModelFactory::factoryFor(User::class)->create();
        $changing_user = ModelFactory::factoryFor(User::class)->create();

        $team = ModelFactory::factoryFor(Team::class)
              ->withUsers([$unchanging_user, $changing_user])
              ->withTasks([
                  $unchanging_user->id => Team::TASK_OWNER,
                  $changing_user->id => Team::TASK_MEMBER
              ])
              ->create();

        $tasks_before = $team->getUserTaskMappings();

        $team->setUserTasks(
            [
                $unchanging_user->id => Team::TASK_OWNER,
                $changing_user->id => Team::TASK_MANAGER
            ]
        );

        $tasks_after = $team->getUserTaskMappings();

        $this->assertArrayHasKey($unchanging_user->id, $tasks_before, 'The test user\'s auth data does not contain a mapping for the test team, on creation');
        $this->assertArrayHasKey($unchanging_user->id, $tasks_after, 'The test user\'s auth data does not contain a mapping for the test team, after calling setUserTasks');
        $this->assertEquals($tasks_before[$unchanging_user->id], $tasks_after[$unchanging_user->id], 'The test user\'s team task mapping did change');
    }

    /** @test */
    public function deletes_auth_item_data_when_user_removed_from_team()
    {
        $constant_user = ModelFactory::factoryFor(User::class)->create();
        $user_to_be_removed = ModelFactory::factoryFor(User::class)->create();

        $team = ModelFactory::factoryFor(Team::class)
              ->withUsers([$constant_user, $user_to_be_removed])
              ->withTasks([
                  $constant_user->id => Team::TASK_OWNER,
                  $user_to_be_removed->id => TEAM::TASK_MEMBER
              ])
              ->create();

        $tasks_before = $team->getUserTaskMappings();

        $team->setAndCacheAssignedUsers([$constant_user->id]);
        $team->setUserTasks(
            [
                $constant_user->id => $tasks_before[$constant_user->id]
            ]
        );

        $teams_for_removed_user = Team::getTeamIdsForUser($user_to_be_removed->id, Team::ALL_TASKS);

        $this->assertNotContains($team->id, $teams_for_removed_user, 'The id for the team the user was removed from should not be in the user\'s team auth data');
    }
}
