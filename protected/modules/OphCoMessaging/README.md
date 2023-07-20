# OphCoMessagin

An OpenEyes event module to allow users to send messages to each-other via the patient record

## Configuration

Add the module to the application configuration:

    'OphCoMessaging' => array('class' => '\OEModule\OphCoMessaging\OphCoMessagingModule'),

## Mailboxes

The module contains functionality for user or shared mailboxes, which are the way that users send or receive messages using the module. A mailbox can either belong to a single user specifically (See the ```is_personal``` flag on a mailbox), or belong to any arbitrary combination of users and teams.

### Mailbox Folders

There are a variety of folders available for quick filtering of the end user's messages. These are populated with the ```MailboxSearch``` component, and messages meeting certain criteria are fetched based on flags set in the constructor of ```MailboxSearch```. These include, but are not limited to: whether the mailbox sent or received the message, whether the mailbox has marked the message as read, whether the message requires a reply, and whether the mailbox is the primary recipient of the message.

## Messaging Functionality

The messaging module can be interacted with in a few different ways, primarily via sending messages, replying to messages, and marking messages as read.

### Sending Messages

A user can send a message from their personal mailbox to one primary, and up to 5 cc recipients. This will create a ```OphCoMessaging_Message_Recipient``` object for each recipient, with the ```primary_recipient``` flag dictating which recipient is the primary.

### Replying To Messages

Once a message has been received by a user's mailbox, they may reply to it from the event screen. They will be provided a list of appropriate mailboxes that they can use to reply, and the value of this dropdown is passed through to the ```mailbox_id``` field in the resulting ```OphCoMessaging_Message_Comment``` object. Once a reply is sent, the message thread will be marked as read for the mailbox that sent the reply (See "Marking Messages As Read" below for further information about read status tracking).

### Marking Messages As Read

Mailboxes track the read status of the entire message thread. A message can be marked as read by any mailbox that received it, and has not already marked it as read. Read tracking works differently for mailboxes that received the original message, and the mailbox that sent the original message.

For senders, their read status for the thread is dictated by the ```marked_as_read``` field on the most recent reply (```OphCoMessaging_Message_Comment```) to the thread.

For receivers, their read status for the thread is dictated by the ```marked_as_read``` field on their respective ```OphCoMessaging_Message_Recipient``` object.

This read status is updated for a sender or recipient when their associated mailbox marks the message as read, or adds a reply to the thread.

Marked as read behaviour is subject to change, and is a likely candidate for reworking in future.
