<?php

namespace OEModule\OphCoMessaging\seeders;

use OEModule\OphCoMessaging\models\Mailbox;
use OEModule\OphCoMessaging\models\Element_OphCoMessaging_Message;
use OEModule\OphCoMessaging\models\OphCoMessaging_Message_MessageType;
use OEModule\OphCoMessaging\models\OphCoMessaging_Message_Comment;
use OE\seeders\resources\SeededEventResource;
use OE\seeders\BaseSeeder;

/**
* Data seeder for testing mailbox count and attributes in messaging/mailbox-filtering-refactor.cy.js.
*/
class MailboxQueriesSeeder extends BaseSeeder
{
    /**
    * Returns the data required to verify the counts and attributes of mailboxes.
    * Return data includes:
    * mailboxes : array containing id, user, name, count and messages
    * filters : array of filter criteria for different users' mailboxes, these criteria sets are organized based on the user they belong to
    * user_mailbox_counts : counts of various types of messages instarted threads, messages waiting for query replies, and unread messages for user mailbox
    * @return array
    */

    public function __invoke(): array
    {

        $admin_user = \User::model()->findByAttributes(['first_name' => 'admin']);

        // get admin mailbox counts
        $messaging_api = new \OEModule\OphCoMessaging\components\OphCoMessaging_API();
        $admin_mailbox_counts = reset($messaging_api->getMessageCounts($admin_user)[0]);

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
        $team = \Team::factory()->withUsers([$admin_user, $user1])->create();

        // seed user shared mailbox and assign user1 and user2 to it
        $shared_user_mailbox = Mailbox::factory()
            ->withUsers([$user1, $user2])
            ->withUniqueMailboxName($runtime_prefix)
            ->create();

        // seed a message sub type with reply required (ReR)
        $reply_required_msg_sub_type = OphCoMessaging_Message_MessageType::factory()
            ->replyRequired()
            ->create(['name' => 'ReR']);

        // get user mailboxes
        $admin_mailbox = Mailbox::model()->forPersonalMailbox($admin_user->id)->find();
        $user_1_mailbox = Mailbox::model()->forPersonalMailbox($user1->id)->find();
        $user_2_mailbox = Mailbox::model()->forPersonalMailbox($user2->id)->find();

        // seed a message from admin user to user1
        // the sender is the admin_user and the primary recipient is user_1_mailbox
        // adds the shared_user_mailbox to the CC with false, which signifies that the message is marked as unread status
        $admin_to_user_1_message = Element_OphCoMessaging_Message::factory()
            ->withSender($admin_user, $admin_mailbox)
            ->withPrimaryRecipient($user_1_mailbox)
            ->withMessageType($reply_required_msg_sub_type)
            ->withCCRecipients([[$shared_user_mailbox, false]])
            ->create(['message_text' => 'Hello User Mailbox ' . $user_1_mailbox->name]);

        // get the sent all count for admin mailbox
        $admin_sent_all_count = intval($admin_mailbox_counts[\OEModule\OphCoMessaging\components\MailboxSearch::FOLDER_SENT_ALL]);

        // increase the admin read all count by 1
        $admin_mailbox_counts[\OEModule\OphCoMessaging\components\MailboxSearch::FOLDER_SENT_ALL] = strval($admin_sent_all_count+1);

        $admin_reply_user_2_message_text = 'Hello from ' . $user_2_mailbox->name;

        // seed a message from admin user to user2 and send a reply
        // mark the reply from user 2 to admin as read by admin
        $admin_to_user_2_message = Element_OphCoMessaging_Message::factory()
            ->withSender($admin_user, $admin_mailbox)
            ->withPrimaryRecipient($user_2_mailbox, true)
            ->withMessageType($reply_required_msg_sub_type)
            ->withReply($admin_reply_user_2_message_text, $user2->id, $user_2_mailbox, true)
            ->create(['message_text' => 'Hello User Mailbox ' . $user_2_mailbox->name]);

        // get the read all count for admin mailbox
        $admin_read_all_count = intval($admin_mailbox_counts[\OEModule\OphCoMessaging\components\MailboxSearch::FOLDER_READ_ALL]);

        // increase the admin read all count by 1
        $admin_mailbox_counts[\OEModule\OphCoMessaging\components\MailboxSearch::FOLDER_READ_ALL] = strval($admin_read_all_count+1);


        $admin_reply_shared_user_message_2_text = 'Hello from ' . $shared_user_mailbox->name; //Hello from Shared user mailbox

        // seeds a message from the admin user to user2, user2 is already a member of a shared mailbox
        // message is sent directly to user2, but since user2 is part of the shared mailbox, the reply is sent through the shared mailbox rather than being sent as an individual user's reply
        // user2 replies to the message via the shared mailbox
        $admin_to_user_2_message_2 = Element_OphCoMessaging_Message::factory()
            ->withSender($admin_user, $admin_mailbox)
            ->withPrimaryRecipient($user_2_mailbox)
            ->withMessageType($reply_required_msg_sub_type)
            ->withReply($admin_reply_shared_user_message_2_text, $user2->id, $shared_user_mailbox, true)
            ->withCCRecipients([[$shared_user_mailbox, false]])
            ->create(['message_text' => 'Hello User Mailbox ' . $user_2_mailbox->name]);

        // get the read all count for admin mailbox
        $admin_read_all_count = intval($admin_mailbox_counts[\OEModule\OphCoMessaging\components\MailboxSearch::FOLDER_READ_ALL]);

        // increase the admin read all count by 1
        $admin_mailbox_counts[\OEModule\OphCoMessaging\components\MailboxSearch::FOLDER_READ_ALL] = strval($admin_read_all_count+1);

        // sending message from user 1 to user 2
        $user_1_to_user_2_message = Element_OphCoMessaging_Message::factory()
            ->withSender($user1, $user_1_mailbox)
            ->withPrimaryRecipient($user_2_mailbox)
            ->withMessageType($reply_required_msg_sub_type)
            ->create(['message_text' => 'Hello User Mailbox ' . $user_2_mailbox->name]);

        $user_1_reply_user_2_message_text = 'Hello from ' . $user_2_mailbox->name;

        // sending message from user 1 to user 2 with a reply from user 2 to user 1
        // and user 1 has read the message
        $user_1_to_user_2_message_with_reply = Element_OphCoMessaging_Message::factory()
            ->withSender($user1, $user_1_mailbox)
            ->withPrimaryRecipient($user_2_mailbox, true)
            ->withMessageType($reply_required_msg_sub_type)
            ->withReply($user_1_reply_user_2_message_text, $user2->id, $user_2_mailbox, true)
            ->create(['message_text' => 'Hello User Mailbox ' . $user_2_mailbox->name]);

        // sending message from user 2 to user 1 and user 1 reads the message
        $user_2_to_user_1_message = Element_OphCoMessaging_Message::factory()
            ->withSender($user2, $user_2_mailbox)
            ->withPrimaryRecipient($user_1_mailbox, true)
            ->withMessageType($reply_required_msg_sub_type)
            ->create(['message_text' => 'Hello User Mailbox ' . $user_1_mailbox->name]);

        $user1_mailbox_data = [
            'id' => $user_1_mailbox->id,
            'user' => [
                'username' => $user1_authentication->username,
                'password' => $user1_password,
            ],
            'name' => $user_1_mailbox->name,
            'count' => [
                'all_messages' => '(4)',
                'unread_all' => '1',
                'unread_to_me' => '1',
                'unread_query' => '1',
                'sent_all' => '(1)',
                'sent_replies' => '(0)',
            ],
            'messages' => [
                [
                    'folder' => [
                        'unread_all',
                        'unread_to_me',
                        'unread_query',
                    ],
                    'sender' => [
                        $admin_mailbox->name,
                        $user_1_mailbox->name
                    ],
                    'is_shared_mailbox' => 0,
                    'text' => $admin_to_user_1_message->message_text,
                    'event' => SeededEventResource::from($admin_to_user_1_message->event)->toArray(),
                ],
                [
                    'folder' => [
                        'sent_all',
                    ],
                    'sender' => [
                        $user_1_mailbox->name,
                        $user_2_mailbox->name,
                    ],
                    'is_shared_mailbox' => 0,
                    'text' => $user_1_to_user_2_message->message_text,
                    'event' => SeededEventResource::from($user_1_to_user_2_message->event)->toArray(),
                ]
            ]
        ];

        $user2_mailbox_data = [
            'id' => $user_2_mailbox->id,
            'user' => [
                'username' => $user2_authentication->username,
                'password' => $user2_password,
            ],
            'name' => $user_2_mailbox->name,
            'count' => [
                'all_messages' => '(5)',
                'unread_all' => '2',
                'unread_to_me' => '2',
                'unread_query' => '2',
                'unread_replies' => '1',
                'sent_all' => '(3)',
                'sent_replies' => '(2)',
            ],
            'messages' => [
                [
                    'folder' => [
                        'unread_all',
                        'unread_to_me',
                        'unread_query',
                        'unread_replies'
                    ],
                    'sender' => [
                        "$user_2_mailbox->name (via $shared_user_mailbox->name)",
                        $user_2_mailbox->name
                    ],
                    'is_shared_mailbox' => 0,
                    'text' => $admin_reply_shared_user_message_2_text,
                    'event' => SeededEventResource::from($admin_to_user_2_message_2->event)->toArray(),
                ],
                [
                    'folder' => [
                        'unread_all',
                        'unread_to_me',
                        'unread_query',
                    ],
                    'sender' => [
                        $user_1_mailbox->name,
                        $user_2_mailbox->name
                    ],
                    'is_shared_mailbox' => 0,
                    'text' => $user_1_to_user_2_message->message_text,
                    'event' => SeededEventResource::from($user_1_to_user_2_message->event)->toArray(),
                ],
                [
                    'folder' => [
                        'sent_all',
                        'sent_replies'
                    ],
                    'sender' => [
                        $user_2_mailbox->name,
                        $admin_mailbox->name,
                    ],
                    'is_shared_mailbox' => 0,
                    'text' => $admin_reply_shared_user_message_2_text,
                    'event' => SeededEventResource::from($admin_to_user_2_message->event)->toArray(),
                ],
                [
                    'folder' => [
                        'sent_all',
                    ],
                    'sender' => [
                        $user_2_mailbox->name,
                        $user_1_mailbox->name,
                    ],
                    'is_shared_mailbox' => 0,
                    'text' => $user_2_to_user_1_message->message_text,
                    'event' => SeededEventResource::from($user_2_to_user_1_message->event)->toArray(),
                ],
                [
                    'folder' => [
                        'sent_all',
                        'sent_replies'
                    ],
                    'sender' => [
                        $user_2_mailbox->name,
                        $user_1_mailbox->name,
                    ],
                    'is_shared_mailbox' => 0,
                    'text' => $user_1_reply_user_2_message_text,
                    'event' => SeededEventResource::from($user_1_to_user_2_message_with_reply->event)->toArray(),
                ]
            ]
        ];

        $admin_mailbox_data = [
            'id' => $admin_mailbox->id,
            'user' => [
                'username' => 'admin',
                'password' => 'admin',
            ],
            'name' => $admin_mailbox->name,
            'count' => [
                'read_all' =>  "({$admin_mailbox_counts[\OEModule\OphCoMessaging\components\MailboxSearch::FOLDER_READ_ALL]})",
                'sent_all' => "({$admin_mailbox_counts[\OEModule\OphCoMessaging\components\MailboxSearch::FOLDER_SENT_ALL]})",
                'sent_replies' => '(0)',
            ],
            'messages' => [
                [
                    'folder' => [
                        'read_all',
                    ],
                    'sender' => [
                        $user_2_mailbox->name,
                        $admin_mailbox->name
                    ],
                    'is_shared_mailbox' => 0,
                    'text' => $admin_reply_user_2_message_text,
                    'event' => SeededEventResource::from($admin_to_user_2_message->event)->toArray(),
                ],
                [
                    'folder' => [
                        'read_all',
                    ],
                    'sender' => [
                        "$user_2_mailbox->name (via $shared_user_mailbox->name)",
                        $admin_mailbox->name
                    ],
                    'is_shared_mailbox' => 0,
                    'text' => $admin_reply_shared_user_message_2_text,
                    'event' => SeededEventResource::from($admin_to_user_2_message_2->event)->toArray(),
                ],
                [
                    'folder' => [
                        'sent_all',
                    ],
                    'sender' => [
                        $admin_mailbox->name,
                        $user_1_mailbox->name,
                    ],
                    'is_shared_mailbox' => 0,
                    'text' => $admin_to_user_1_message,
                    'event' => SeededEventResource::from($admin_to_user_1_message->event)->toArray(),
                ]
            ]
        ];

        $shared_user_mailbox_data = [
            'id' => $shared_user_mailbox->id,
            'user' => [
                'username' => $user2_authentication->username,
                'password' => $user2_password,
            ],
            'name' => $shared_user_mailbox->name,
            'count' => [
                'all_messages' => '(2)',
                'unread_all' => '1',
                'unread_query' => '1',
                'unread_cc' => '1',
                'sent_all' => '(1)',
                'sent_replies' => '(1)',
            ],
            'messages' => [
                [
                    'folder' => [
                        'unread_all',
                        'unread_query',
                        'unread_cc',
                    ],
                    'sender' => [$admin_mailbox->name, $shared_user_mailbox->name],
                    'is_shared_mailbox' => 1,
                    'text' => $admin_to_user_1_message->message_text,
                    'event' => SeededEventResource::from($admin_to_user_1_message->event)->toArray(),
                ],
                [
                    'folder' => [
                        'sent_all',
                        'sent_replies'
                    ],
                    'sender' => [
                        "$user_2_mailbox->name (via $shared_user_mailbox->name)",
                        $admin_mailbox->name,
                    ],
                    'is_shared_mailbox' => 0,
                    'text' => $admin_reply_shared_user_message_2_text,
                    'event' => SeededEventResource::from($admin_to_user_2_message_2->event)->toArray(),
                ]
            ]
        ];

        $mailboxes = [
            $user1_mailbox_data,
            $user2_mailbox_data,
            $admin_mailbox_data,
            $shared_user_mailbox_data
        ];

        return [
            'mailboxes' => $mailboxes,
            'filters' => [
                [
                    'user' => [
                        'username' => $user1_authentication->username,
                        'password' => $user1_password,
                    ],
                    'criterias' => [
                        [
                            'mailbox' => null,
                            'sender' => null,
                            'type' => null,
                            'count' => 8,
                        ],
                        [
                            'mailbox' => $user_1_mailbox->id,
                            'sender' => null,
                            'type' => null,
                            'count' => 5,
                        ],
                        [
                            'mailbox' => $shared_user_mailbox->id,
                            'sender' => null,
                            'type' => null,
                            'count' => 3,
                        ],
                        [
                            'mailbox' => $user_1_mailbox->id,
                            'sender' => $admin_mailbox->id,
                            'type' => null,
                            'count' => 1,
                        ],
                        [
                            'mailbox' => $user_1_mailbox->id,
                            'sender' => $admin_mailbox->id,
                            'type' => $reply_required_msg_sub_type->id,
                            'count' => 1,
                        ],
                    ]
                ],
                [
                    'user' => [
                        'username' => $user2_authentication->username,
                        'password' => $user2_password,
                    ],
                    'criterias' => [
                        [
                            'mailbox' => null,
                            'sender' => null,
                            'type' => null,
                            'count' => 11,
                        ],
                        [
                            'mailbox' => $user_2_mailbox->id,
                            'sender' => null,
                            'type' => null,
                            'count' => 8,
                        ],
                        [
                            'mailbox' => $shared_user_mailbox->id,
                            'sender' => null,
                            'type' => null,
                            'count' => 3,
                        ],
                        [
                            'mailbox' => $user_2_mailbox->id,
                            'sender' => $admin_mailbox->id,
                            'type' => null,
                            'count' => 2,
                        ],
                        [
                            'mailbox' => $user_2_mailbox->id,
                            'sender' => $admin_mailbox->id,
                            'type' => $reply_required_msg_sub_type->id,
                            'count' => 2,
                        ]
                    ]
                ]
            ],
            'user_mailbox_counts' => [
                [
                    'user' => [
                        'username' => $user1_authentication->username,
                        'password' => $user1_password,
                    ],
                    'name' => $user_1_mailbox->name,
                    'started_threads' => '(2)',
                    'waiting_for_query_reply' => '(1)',
                    'unread_by_recipient' => '(1)'
                ],
                [
                    'user' => [
                        'username' => $user2_authentication->username,
                        'password' => $user2_password,
                    ],
                    'name' => $user_2_mailbox->name,
                    'started_threads' => '(1)',
                    'waiting_for_query_reply' => '(1)',
                    'unread_by_recipient' => '(0)'
                ]
            ]
        ];
    }
}
