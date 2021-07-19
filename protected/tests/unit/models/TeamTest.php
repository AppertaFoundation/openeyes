<?php
/**
 * class TeamTest
 * @covers \models\Team
 */
class TeamTest extends \ActiveRecordTestCase
{
    public $fixtures = array(
        'teams' => 'Team',
    );
    public function getModel()
    {
        return \Team::model();
    }

    public function getTeamDetails()
    {
        return array(
            array(
                'team_fixture' => 'team1',
                'temp_user_ids' => array(1, 2),
                'temp_child_team_ids' => array(2),
                'can_save' => true,
                'active' => 1
            ),
            array(
                'team_fixture' => 'team2',
                'temp_user_ids' => array(3, 4),
                'temp_child_team_ids' => array(),
                'can_save' => true,
                'active' => 1
            ),
            array(
                'team_fixture' => 'team3',
                'temp_user_ids' => array(),
                'temp_child_team_ids' => array(1),
                'can_save' => false,
                'active' => 1
            ),
            array(
                'team_fixture' => 'team1',
                'temp_user_ids' => array(),
                'temp_child_team_ids' => array(),
                'can_save' => true,
                'active' => 0
            ),
        );
    }
    /**
     * @dataProvider getTeamDetails
     * @covers Team
     * @covers TeamUserAssign
     * @covers TeamTeamAssign
     */
    public function testTeams($team_fixture, $temp_user_ids, $temp_child_team_ids, $can_save, $active)
    {
        $team = $this->teams($team_fixture);
        $team->active = $team->active ? 1 : 0;
        $team->temp_user_ids = $temp_user_ids;
        $team->temp_child_team_ids = $temp_child_team_ids;
        $res = $team->save();
        $this->assertEquals($can_save, $res);
        $this->assertEquals($active, $team->active);
    }
}
