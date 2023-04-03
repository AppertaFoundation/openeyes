<?php
namespace OEModule\OphCoMessaging\seeders;

use OEModule\CypressHelper\components\BaseSeeder;

/**
* CreateMessageSeeder is a seeder for generating data used solely in the 'create message via patient record' test (messaging\create.cy.js)
*/
class CreateMessageSeeder extends BaseSeeder
{
    /**
    * Returns the data required to create a message via patient record.
    * Return data is:
    * - user - array with elements username, password, fullName and messageText
    * @return array
    */
    public function __invoke()
    {
        // TO DO: how to find current institution within seeder
        $current_institution = \Institution::model()->findByPk(1);

        // seed user
        $user_password = $this->faker->word() . '_password';
        $user = \User::factory()
            ->withLocalAuthForInstitution($current_institution, $user_password)
            ->withAuthItems(['User'])
            ->create();
        $user_authentication = $user->authentications[0];
        $user_fullname = $user->getFullNameAndTitle();

        return [
            'user' =>  ['username' => $user_authentication->username, 
                        'password' => $user_password, 
                        'fullName' => $user_fullname,
                        'messageText' => 'Hello ' . $user_fullname,
            ]
        ];
    }
}