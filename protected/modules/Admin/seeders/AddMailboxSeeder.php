<?php
namespace OEModule\Admin\seeders;

use OEModule\CypressHelper\components\BaseSeeder;
use OEModule\OphCoMessaging\factories\models\MailboxFactory;
use \OEModule\OphCoMessaging\models\Mailbox;

/**
* AddMailboxSeeder is a seeder for generating data used solely in the Add Shared Mailbox test (admin\shared-mailboxes.cy.js)
*/
class AddMailboxSeeder extends BaseSeeder
{
    /**
    * Returns the data required for adding a shared mailbox.
    * Return data includes:
    * - mailboxNameToCreate - the unique name of the mailbox to be created
    * - userNames - 2 x user names (to be subsequently assigned to said shared mailbox)
    * - teamNames - 2 x team names (to be subsequently assigned to said shared mailbox)
    * @return array
    */
    public function __invoke()
    {
        $user_names = [];
        $team_names = [];
        $institution = \Institution::model()->findByPk(1);

        for ($i = 0; $i < 2; $i++) {
            $user_names[] = ltrim(\User::factory()->withLocalAuthForInstitution($institution)->create()->getFullNameAndTitle());
        }

        for ($i = 0; $i < 2; $i++) {
            // team must have a user assigned or it will be set to 'inactive' automatically
            $team_user = \User::factory()->useExisting()->create();
            $team_names[] = \Team::factory()->withUsers([$team_user])->create(['institution_id' => $institution])->name;
        }

        return [
            'mailboxNameToCreate' => MailboxFactory::getUniqueMailboxName($this->faker, 'Test Mailbox '),
            'userNames' => $user_names,
            'teamNames' => $team_names
        ];
    }
}
