<?php
namespace OEModule\OphCoMessaging\seeders;

use OEModule\OphCoMessaging\models\Mailbox;
use OEModule\OphCoMessaging\models\Element_OphCoMessaging_Message;
use OEModule\CypressHelper\resources\SeededEventResource;
use OE\seeders\BaseSeeder;

/**
* Seeder for generating data used solely in the test to verify desired behaviour of shared mailboxes (messaging/shared-mailbox-functionality.cy.js)
*/
class TestMailboxSeeder extends BaseSeeder
{
    /**
    * Returns the data required to verify the desired behaviour of shared mailboxes.
    * Return data includes:
    * - user1 - array with elements username, password and fullName
    * - user2 - as above
    * - teamName - a test team (with admin and user1 assigned to it)
    * - userMailbox - a user shared mailbox - array with elements name and messageText (with user1 and user2 assigned to it)
    * - teamMailbox - a team shared mailbox - as above (with the test team assigned to it)
    * - messageEvent1 - a message event with admin as sender and the user shared mailbox as recipient
    * - messageEvent2 - a message event with the team shared mailbox as recipient
    * @return array
    */
    public function __invoke(): array
    {
        $current_institution = $this->app_context->getSelectedInstitution();
        $runtime_prefix = (string) time();

        // seed user1
        $user1_password = 'password';
        $user1 = \User::factory()
            ->withLocalAuthForInstitution($current_institution, $user1_password)
            ->withAuthItems(['Edit', 'User', 'View clinical'])
            ->create();
        $user1_authentication = $user1->authentications[0];

        // seed user2
        $user2_password = 'password';
        $user2 = \User::factory()
            ->withLocalAuthForInstitution($current_institution, $user2_password)
            ->withAuthItems(['Edit', 'User', 'View clinical'])
            ->create();
        $user2_authentication = $user2->authentications[0];

        // seed test team and assign admin and user1 to the team
        $admin_user = \User::model()->findByPk(1);
        $team = \Team::factory()->withUsers([$admin_user, $user1])->create();

        // seed user shared mailbox and assign user1 and user2 to it
        $user_mailbox = Mailbox::factory()
            ->withUsers([$user1, $user2])
            ->withUniqueMailboxName($runtime_prefix)
            ->create();

        // seed team shared mailbox and assign test team to it
        $team_mailbox = Mailbox::factory()
            ->withTeams([$team])
            ->withUniqueMailboxName($runtime_prefix)
            ->create();

        // seed a message event with sender - admin - and recipient user_mailbox
        $user_mailbox_message = Element_OphCoMessaging_Message::factory()
            ->withSender($admin_user, Mailbox::model()->forPersonalMailbox($admin_user->id)->find())
            ->withPrimaryRecipient($user_mailbox)
            ->create(['message_text' => 'Hello Test User Mailbox ' . $user_mailbox->name]);

        // seed a message event with recipient team_mailbox
        $team_mailbox_message = Element_OphCoMessaging_Message::factory()
            ->withPrimaryRecipient($team_mailbox)
            ->create(['message_text' => 'Hello Test Team Mailbox ' . $team_mailbox->name]);

        return [
            'user1' => ['username' => $user1_authentication->username, 'password' => $user1_password, 'fullName' => $user1->getFullName()],
            'user2' => ['username' => $user2_authentication->username, 'password' => $user2_password, 'fullName' => $user2->getFullName()],
            'teamName' => $team->name,
            'userMailbox' => ['name' => $user_mailbox->name, 'messageText' => $user_mailbox_message->message_text],
            'teamMailbox' => ['name' => $team_mailbox->name, 'messageText' => $team_mailbox_message->message_text],
            'messageEvent1' => SeededEventResource::from($user_mailbox_message->event)->toArray(),
            'messageEvent2' => SeededEventResource::from($team_mailbox_message->event)->toArray(),
        ];
    }
}
