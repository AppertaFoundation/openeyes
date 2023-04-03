<?php
namespace OEModule\Admin\seeders;

use OEModule\CypressHelper\components\BaseSeeder;

/**
* AddTeamSeeder is a seeder for generating data used solely in the Add Team test (admin\teams.cy.js)
*/
class AddTeamSeeder extends BaseSeeder
{
    /**
    * Returns the data required for adding a team.
    * Return data includes:
    * - teamName - the name of the team to be created
    * - email - email address of said team
    * - active - active status of said team
    * - userAssignments - 2 x users (to be subsequently assigned to said team)
    * - teamAssignments - 2 x teams (to be subsequently assigned to said team)
    * @return array
    */
    public function __invoke()
    {
        $users = [];
        $team_names = [];

        $admin_user = \User::model()->findByPk(1);
        $users[] = ['fullName'=>$admin_user->getFullNameAndTitle(), 'role'=>'Owner'];

        for($i=0; $i<1; $i++) {
            $user = \User::factory()->useExisting()->create();
            $users[] = ['fullName'=>$user->getFullNameAndTitle(), 'role'=>'Member'];
        }

        for($i=0; $i<2; $i++) {
            // team must have a user assigned or it will be set to 'inactive' automatically
            $team_user = \User::factory()->useExisting()->create();
            $team_names[] = \Team::factory()->withUsers([$team_user])->create()->name;
        }      

        return [
            'teamName' => 'Test Team ' . $this->faker->word(),
            'email' => $this->faker->email(),
            'active' => true,
            'userAssignments' => $users,
            'teamAssignments' => $team_names,          
        ];
    }
}