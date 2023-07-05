<?php

namespace OEModule\OphCoMessaging\components;

class MailboxSearch
{
    public const FOLDER_ALL = 'all';

    public const FOLDER_UNREAD_ALL = 'unread_all';
    public const FOLDER_UNREAD_URGENT = 'unread_urgent';
    public const FOLDER_UNREAD_QUERY = 'unread_query';
    public const FOLDER_UNREAD_RECEIVED = 'unread_received';
    public const FOLDER_UNREAD_COPIED = 'unread_copied';
    public const FOLDER_UNREAD_REPLIES = 'unread_replies';

    public const FOLDER_READ_ALL = 'read_all';
    public const FOLDER_READ_URGENT = 'read_urgent';
    public const FOLDER_READ_RECEIVED = 'read_received';
    public const FOLDER_READ_COPIED = 'read_copied';

    public const FOLDER_SENT_ALL = 'sent_all';
    public const FOLDER_SENT_UNREPLIED = 'sent_unreplied';
    public const FOLDER_SENT_UNREAD = 'sent_unread';

    private const RETRIEVE_RECEIVED = 'received';
    private const RETRIEVE_SENT = 'sent';
    private const RETRIEVE_RECEIVED_AND_REPLIES = 'received_and_replies';

    private $retrieve_from = MailboxSearch::FOLDER_UNREAD_ALL;

    private $retrieve_all_comments = 0;
    private $retrieve_original_element = 0;

    //Mailbox folder switches
    private $message_read = null;
    private $message_to_me = null;
    private $message_urgent = null;
    private $message_query = null;
    private $message_reply = null;
    private $message_cc = null;
    private $message_sent = null;
    private $message_user_original_sender = null;

    //Search parameters
    private $search_date_from = null;
    private $search_date_to = null;
    private $search_sender = null;
    private $search_message_type = null;
    private $search_message_content = null;

    public function __construct($user, $folder, $search_parameters = [])
    {
        if (!empty($search_parameters['retrieve_all_comments'])) {
            $this->retrieve_all_comments = 1;
            $folder = null;
        }

        if (!empty($search_parameters['retrieve_original_element'])) {
            $this->retrieve_original_element = 1;
            $folder = null;
        }

        if (!empty($search_parameters['date_from'])) {
            $this->search_date_from = $search_parameters['date_from'];
        }

        if (!empty($search_parameters['date_to'])) {
            $this->search_date_to = $search_parameters['date_to'];
        }

        if (!empty($search_parameters['sender'])) {
            $this->search_sender = $search_parameters['sender'];
        }

        if (!empty($search_parameters['message_type'])) {
            $this->search_message_type = $search_parameters['message_type'];
        }

        if (!empty($search_parameters['message_content'])) {
            $this->search_message_content = $search_parameters['message_content'];
        }

        if (isset($folder)) {
            switch ($folder) {
                case self::FOLDER_ALL:
                    $this->retrieve_from = self::RETRIEVE_RECEIVED_AND_REPLIES;
                    $this->message_sent = 0;
                    break;

                case self::FOLDER_UNREAD_ALL:
                    $this->retrieve_from = self::RETRIEVE_RECEIVED_AND_REPLIES;
                    $this->message_sent = 0;
                    $this->message_read = 0;
                    break;

                case self::FOLDER_UNREAD_URGENT:
                    $this->retrieve_from = self::RETRIEVE_RECEIVED;
                    $this->message_sent = 0;
                    $this->message_read = 0;
                    $this->message_urgent = 1;
                    break;

                case self::FOLDER_UNREAD_QUERY:
                    $this->retrieve_from = self::RETRIEVE_RECEIVED;
                    $this->message_sent = 0;
                    $this->message_read = 0;
                    $this->message_query = 1;
                    break;

                case self::FOLDER_UNREAD_RECEIVED:
                    $this->retrieve_from = self::RETRIEVE_RECEIVED;
                    $this->message_sent = 0;
                    $this->message_read = 0;
                    $this->message_to_me = 1;
                    break;

                case self::FOLDER_UNREAD_COPIED:
                    $this->retrieve_from = self::RETRIEVE_RECEIVED;
                    $this->message_sent = 0;
                    $this->message_read = 0;
                    $this->message_cc = 1;
                    break;

                case self::FOLDER_UNREAD_REPLIES:
                    $this->retrieve_from = self::RETRIEVE_RECEIVED_AND_REPLIES;
                    $this->message_sent = 0;
                    $this->message_read = 0;
                    $this->message_reply = 1;
                    break;

                case self::FOLDER_READ_ALL:
                    $this->retrieve_from = self::RETRIEVE_RECEIVED;
                    $this->message_read = 1;
                    break;

                case self::FOLDER_READ_URGENT:
                    $this->retrieve_from = self::RETRIEVE_RECEIVED;
                    $this->message_sent = 0;
                    $this->message_read = 1;
                    $this->message_urgent = 1;
                    break;

                case self::FOLDER_READ_RECEIVED:
                    $this->retrieve_from = self::RETRIEVE_RECEIVED;
                    $this->message_sent = 0;
                    $this->message_read = 1;
                    $this->message_to_me = 1;
                    break;

                case self::FOLDER_READ_COPIED:
                    $this->retrieve_from = self::RETRIEVE_RECEIVED;
                    $this->message_sent = 0;
                    $this->message_read = 1;
                    $this->message_cc = 1;
                    break;

                case self::FOLDER_SENT_ALL:
                    $this->retrieve_from = self::RETRIEVE_SENT;
                    $this->message_user_original_sender = 1;
                    break;

                case self::FOLDER_SENT_UNREPLIED:
                    $this->retrieve_from = self::RETRIEVE_SENT;
                    $this->message_reply = 0;
                    $this->message_query = 1;
                    $this->message_user_original_sender = 1;
                    break;

                case self::FOLDER_SENT_UNREAD:
                    $this->retrieve_from = self::RETRIEVE_SENT;
                    $this->message_sent = 1;
                    $this->message_read = 0;
                    break;

                default:
                    throw new \Exception('Invalid folder name supplied to MailboxSearch: ' . $folder);
            }
        }
    }

    public function isReceviedFolder(): bool
    {
        return $this->retrieve_from === self::RETRIEVE_RECEIVED
            || $this->retrieve_from === self::RETRIEVE_RECEIVED_AND_REPLIES;
    }

    public function isSentFolder(): bool
    {
        return $this->retrieve_from === self::RETRIEVE_SENT;
    }

    public static function getAllMailboxesForUser($user_id)
    {
        $user_mailbox_sql = "WITH RECURSIVE user_teams AS (
            SELECT team_id FROM team_user_assign tua WHERE tua.user_id = :target_user_id
                UNION DISTINCT
            SELECT parent_team_id AS team_id FROM user_teams
                JOIN team_team_assign tta ON tta.child_team_id = user_teams.team_id
        )
        ((SELECT m.id, m.name, m.is_personal FROM mailbox m
            JOIN mailbox_team mt ON mailbox_id = m.id
            JOIN user_teams ut ON ut.team_id = mt.team_id)
        UNION
        (SELECT m.id, m.name, m.is_personal FROM mailbox m
            JOIN mailbox_user mu ON mu.mailbox_id = m.id
            WHERE mu.user_id = :target_user_id)) ORDER BY is_personal DESC, `name` ASC";

        $user_mailbox_command = \Yii::app()->db->createCommand($user_mailbox_sql);
        $user_mailbox_command->params = [':target_user_id' => $user_id];

        return $user_mailbox_command->queryAll();
    }

    public static function getMailboxFolderCounts($user_id, $mailbox_ids = null)
    {
        $mailbox_id_params = MailboxSearch::getMailboxQueryParams($user_id, $mailbox_ids);

        $sql = "SELECT
            SUM((contextualised_messages.sent = 0 OR contextualised_messages.to_self)) total_message_count,

            SUM((contextualised_messages.sent = 0 OR contextualised_messages.to_self) AND contextualised_messages.marked_as_read = 0) all_unread,
            SUM((contextualised_messages.sent = 0 OR contextualised_messages.to_self) AND contextualised_messages.marked_as_read = 0 AND contextualised_messages.urgent = 1) unread_urgent,
            SUM((contextualised_messages.sent = 0 OR contextualised_messages.to_self) AND contextualised_messages.marked_as_read = 0 AND contextualised_messages.reply_required = 1) unread_queries,
            SUM((contextualised_messages.sent = 0 OR contextualised_messages.to_self) AND contextualised_messages.marked_as_read = 0 AND contextualised_messages.has_reply = 1) unread_replies,
            SUM((contextualised_messages.sent = 0 OR contextualised_messages.to_self) AND contextualised_messages.marked_as_read = 0 AND contextualised_messages.to_me = 1) unread_to_me,
            SUM((contextualised_messages.sent = 0 OR contextualised_messages.to_self) AND contextualised_messages.marked_as_read = 0 AND contextualised_messages.cc = 1) unread_cc,

            SUM((contextualised_messages.sent = 0 OR contextualised_messages.to_self) AND contextualised_messages.marked_as_read = 1) all_read,
            SUM((contextualised_messages.sent = 0 OR contextualised_messages.to_self) AND contextualised_messages.marked_as_read = 1 AND contextualised_messages.urgent = 1) read_urgent,
            SUM((contextualised_messages.sent = 0 OR contextualised_messages.to_self) AND contextualised_messages.marked_as_read = 1 AND contextualised_messages.to_me = 1) read_to_me,
            SUM((contextualised_messages.sent = 0 OR contextualised_messages.to_self) AND contextualised_messages.marked_as_read = 1 AND contextualised_messages.cc = 1) read_cc,

            SUM(contextualised_messages.user_original_sender = 1) started_threads,
            SUM(contextualised_messages.user_original_sender = 1 AND contextualised_messages.reply_required = 1 AND contextualised_messages.has_reply = 0) waiting_for_reply,
            SUM((contextualised_messages.sent = 1 OR contextualised_messages.to_self) AND contextualised_messages.marked_as_read = 0) unread_by_recipient

            FROM
            (SELECT
                intermediate_messages.*,
                (intermediate_messages.sender_mailbox_id = intermediate_messages.user_mailbox_id) `sent`,
                (intermediate_messages.sender_mailbox_id = intermediate_messages.recipient_mailbox_id) to_self
                FROM
                (SELECT
                    messages.element_id element_id,
                    messages.urgent urgent,
                    latest_comment.latest_comment_id latest_comment_id,
                    ommt.reply_required reply_required,
                    latest_comment.latest_comment_id IS NOT NULL has_reply,
                    COALESCE(omc.marked_as_read, messages.marked_as_read) marked_as_read,
                    messages.to_me to_me,
                    messages.cc cc,
                    messages.user_original_sender user_original_sender,
                    COALESCE(omc.mailbox_id, messages.sender_mailbox_id) sender_mailbox_id,
                    messages.user_mailbox_id user_mailbox_id,
                    IF (omc.id IS NOT NULL, -- Method of determining who the recipient is for this message, as it depends on who has replied and in what order
                        IF(omc.mailbox_id = messages.user_mailbox_id,
                            IF(messages.user_original_sender, messages.recipient_mailbox_id, messages.sender_mailbox_id),
                            messages.user_mailbox_id),
                        messages.recipient_mailbox_id
                    ) recipient_mailbox_id
                FROM
                (
                    (
                        SELECT -- Messages that this mailbox has sent
                            eom.id element_id,
                            eom.urgent,
                            primary_recipient.marked_as_read marked_as_read,
                            eom.message_type_id,
                            um.id = primary_recipient.mailbox_id to_me,
                            0 cc,
                            1 user_original_sender,

                            um.id sender_mailbox_id,
                            um.id user_mailbox_id,
                            primary_recipient.mailbox_id recipient_mailbox_id

                        FROM et_ophcomessaging_message eom
                        JOIN mailbox um ON um.id = eom.sender_mailbox_id AND um.id IN ({$mailbox_id_params['binding_string']})
                        JOIN ophcomessaging_message_recipient primary_recipient ON eom.id = primary_recipient.element_id AND primary_recipient.primary_recipient = 1
                        JOIN `event` ev ON eom.event_id  = ev.id
                        JOIN episode ep ON ev.episode_id = ep.id
                        WHERE eom.deleted = 0 AND ev.deleted = 0 AND ev.delete_pending = 0
                    )
                UNION
                    (
                        SELECT -- Messages that this mailbox has received
                            eom.id element_id,
                            eom.urgent,
                            omr.marked_as_read marked_as_read,
                            eom.message_type_id,
                            omr.primary_recipient to_me,
                            NOT omr.primary_recipient cc,
                            eom.sender_mailbox_id = um.id user_original_sender,

                            eom.sender_mailbox_id,
                            um.id user_mailbox_id,
                            um.id recipient_mailbox_id

                        FROM et_ophcomessaging_message eom
                        JOIN ophcomessaging_message_recipient omr ON omr.element_id = eom.id
                        JOIN mailbox um ON um.id = omr.mailbox_id AND um.id IN ({$mailbox_id_params['binding_string']})
                        JOIN `event` ev ON eom.event_id  = ev.id
                        JOIN episode ep ON ev.episode_id = ep.id
                        WHERE eom.deleted = 0 AND ev.deleted = 0 AND ev.delete_pending = 0 AND ep.deleted = 0
                    )
                ) messages
                LEFT OUTER JOIN
                    (
                        SELECT eom.id element_id, MAX(omc.id) latest_comment_id
                        FROM et_ophcomessaging_message eom
                        JOIN ophcomessaging_message_comment omc ON omc.element_id = eom.id
                        GROUP BY eom.id
                    ) latest_comment
                ON latest_comment.element_id = messages.element_id
                LEFT OUTER JOIN ophcomessaging_message_comment omc ON omc.id = latest_comment.latest_comment_id AND messages.element_id = omc.element_id
                JOIN ophcomessaging_message_message_type ommt ON ommt.id = messages.message_type_id
            ) intermediate_messages
        ) contextualised_messages";

        $mailbox_counts_command = \Yii::app()->db->createCommand($sql);

        $mailbox_counts_command->params = $mailbox_id_params['values'];

        $counts = $mailbox_counts_command->queryRow();

        if (!$counts) {
            $counts = [
                'total_message_count' => 0,
                'all_unread' => 0,
                'unread_to_me' => 0,
                'unread_urgent' => 0,
                'unread_queries' => 0,
                'unread_replies' => 0,
                'unread_cc' => 0,
                'all_read' => 0,
                'read_urgent' => 0,
                'read_to_me' => 0,
                'read_cc' => 0,
                'started_threads' => 0,
                'waiting_for_reply' => 0,
                'unread_by_recipient' => 0
            ];
        }

        return $counts;
    }

    public static function getMailboxQueryParams($user_id, $mailbox_ids = null)
    {
        $user_mailbox_ids = !empty($mailbox_ids) ?
            array_map(function ($id) {
                return ['id' => $id];
            }, $mailbox_ids) :
            MailboxSearch::getAllMailboxesForUser($user_id);

        $mailbox_params = [];

        foreach ($user_mailbox_ids as $index => $id) {
            $mailbox_params[":mailbox{$index}"] = $id['id'];
        }

        $mailbox_param_binding_string = implode(',', array_keys($mailbox_params));

        return ['binding_string' => $mailbox_param_binding_string, 'values' => $mailbox_params];
    }

    public static function getMaximumSearchMessageCount()
    {
        $sql = "SELECT COUNT(DISTINCT eom.id) + COUNT(omr.id) FROM et_ophcomessaging_message eom LEFT OUTER JOIN ophcomessaging_message_comment omc ON omc.element_id = eom.id LEFT OUTER JOIN ophcomessaging_message_recipient omr ON omr.element_id = eom.id";

        return \Yii::app()->db->createCommand($sql)->queryScalar();
    }

    public function retrieveMailboxContentsUsingSQL($user_id, $mailbox_ids = null)
    {
        $mailbox_id_params = MailboxSearch::getMailboxQueryParams($user_id, $mailbox_ids);

        $sql = "SELECT
                counted_messages.*,
                u.title sender_title,
                u.first_name sender_first_name,
                u.last_name sender_last_name,
                ev.id event_id,
                ep.patient_id patient_id FROM
            (SELECT -- Wrap the inner query an additional time to to ensure the count doesn't mess with our selects above
                    COUNT(contextualised_messages.element_id) OVER () total_message_count, contextualised_messages.*
                    FROM
                    (SELECT
                        messages.user_mailbox_personal user_mailbox_personal,
                        messages.element_id element_id,
                        messages.element_event_id element_event_id,
                        messages.user_mailbox_id user_mailbox_id,
                        messages.urgent urgent,
                        messages.message_type_id message_type_id,
                        messages.user_primary_recipient user_primary_recipient,
                        messages.user_original_sender user_original_sender,
                        ommt.name message_type_name,
                        IF (omc.id IS NOT NULL, -- Method of determining who the recipient is for this message, as it depends on who (if anyone) has replied and in what order
                            IF(omc.mailbox_id = messages.user_mailbox_id,
                                IF(messages.user_original_sender, messages.recipient_mailbox_id, messages.sender_mailbox_id),
                                messages.user_mailbox_id),
                            messages.recipient_mailbox_id
                        ) recipient_mailbox_id,
                         IF (omc.id IS NOT NULL, -- Same as above, but to find the mailbox name for display purposes
                            IF(omc.mailbox_id = messages.user_mailbox_id,
                                IF(messages.user_original_sender, messages.recipient_mailbox_name, messages.sender_mailbox_name),
                                messages.user_mailbox_name),
                            messages.recipient_mailbox_name
                        ) recipient_mailbox_name,
                        omc.id comment_id,
                        omc.comment_text comment_text,
                        COALESCE(omc.created_date, messages.created_date) send_date,
                        COALESCE(omc.mailbox_id, messages.sender_mailbox_id) sender_mailbox_id,
                        COALESCE(omc.created_user_id, messages.sender_user_id) sender_user_id,
                        COALESCE(comment_sender_mailbox.name, messages.sender_mailbox_name) sender_mailbox_name,
                        COALESCE(comment_sender_mailbox.is_personal, messages.sender_mailbox_personal) sender_mailbox_personal,
                        ommt.reply_required reply_required,
                        omc.id IS NOT NULL has_reply,
                        COALESCE(omc.comment_text, element_text) display_text,
                        COALESCE(omc.marked_as_read, messages.marked_as_read) marked_as_read
                    FROM
                    (
                        (
                            SELECT -- Messages that this mailbox has sent
                                eom.id element_id,
                                eom.event_id element_event_id,
                                eom.message_text element_text,
                                eom.urgent,
                                primary_recipient.marked_as_read marked_as_read,
                                eom.message_type_id,
                                eom.created_date,

                                um.id user_mailbox_id,
                                um.name user_mailbox_name,
                                um.is_personal user_mailbox_personal,

                                um.id sender_mailbox_id,
                                :target_user_id sender_user_id,
                                um.name sender_mailbox_name,
                                um.is_personal sender_mailbox_personal,
                                1 user_original_sender,
                                primary_recipient.mailbox_id = um.id user_primary_recipient,

                                primary_recipient.mailbox_id recipient_mailbox_id,
                                primary_recipient_mailbox.name recipient_mailbox_name

                            FROM et_ophcomessaging_message eom
                            JOIN mailbox um ON um.id = eom.sender_mailbox_id AND um.id IN ({$mailbox_id_params['binding_string']}) -- It's necessary here to perform string substitution to bind the mailbox ids
                            JOIN ophcomessaging_message_recipient primary_recipient ON eom.id = primary_recipient.element_id AND primary_recipient.primary_recipient = 1
                            JOIN mailbox primary_recipient_mailbox ON primary_recipient_mailbox.id = primary_recipient.mailbox_id
                            JOIN `event` ev ON eom.event_id = ev.id
                            JOIN episode ep ON ev.episode_id = ep.id
                            WHERE -- Perform filtering based on folder flags
                                eom.deleted = 0 AND ev.deleted = 0 AND ev.delete_pending = 0 AND ep.deleted = 0 AND
                                (:message_user_original_sender IS NULL OR :message_user_original_sender = 1) AND
                                (:message_to_me IS NULL) AND
                                (:message_cc IS NULL) AND
                                (:message_urgent IS NULL OR eom.urgent = :message_urgent) AND
                                (:message_query IS NULL OR (:message_query = EXISTS(SELECT id FROM ophcomessaging_message_message_type ommt WHERE ommt.name = 'Query' AND ommt.id = eom.message_type_id)))
                        )
                    UNION
                        (
                            SELECT -- Messages that this mailbox has received
                                eom.id element_id,
                                eom.event_id element_event_id,
                                eom.message_text element_text,
                                eom.urgent,
                                omr.marked_as_read marked_as_read,
                                eom.message_type_id,
                                eom.created_date,

                                um.id user_mailbox_id,
                                um.name user_mailbox_name,
                                um.is_personal user_mailbox_personal,

                                eom.sender_mailbox_id,
                                eom.created_user_id sender_user_id,
                                sender_mailbox.name sender_mailbox_name,
                                sender_mailbox.is_personal sender_mailbox_personal,
                                eom.sender_mailbox_id = um.id user_original_sender,
                                omr.primary_recipient user_primary_recipient,

                                um.id recipient_mailbox_id,
                                um.name recipient_mailbox_name

                            FROM et_ophcomessaging_message eom
                            JOIN ophcomessaging_message_recipient omr ON omr.element_id = eom.id
                            JOIN mailbox um ON um.id = omr.mailbox_id AND um.id IN ({$mailbox_id_params['binding_string']}) -- It's necessary here to perform string substitution to bind the mailbox ids
                            JOIN mailbox sender_mailbox ON sender_mailbox.id = eom.sender_mailbox_id
                            JOIN `event` ev ON eom.event_id  = ev.id
                            JOIN episode ep ON ev.episode_id = ep.id
                            WHERE -- Perform filtering based on folder flags
                                eom.deleted = 0 AND ev.deleted = 0 AND ev.delete_pending = 0 AND ep.deleted = 0 AND
                                (:message_user_original_sender IS NULL OR :message_user_original_sender = 0) AND
                                (:message_to_me IS NULL OR :message_to_me = omr.primary_recipient) AND
                                (:message_cc IS NULL OR :message_cc <> omr.primary_recipient) AND
                                (:message_urgent IS NULL OR eom.urgent = :message_urgent) AND
                                (:message_query IS NULL OR (:message_query = EXISTS(SELECT id FROM ophcomessaging_message_message_type ommt WHERE ommt.name = 'Query' AND ommt.id = eom.message_type_id)))
                            )
                    ) messages
                LEFT OUTER JOIN
                    (
                        SELECT eom.id element_id, MAX(omc.id) latest_comment_id
                        FROM et_ophcomessaging_message eom
                        JOIN ophcomessaging_message_comment omc ON omc.element_id = eom.id
                        GROUP BY eom.id
                    ) latest_comment
                ON latest_comment.element_id = messages.element_id
                CROSS JOIN ( -- Create an additional row to populate with the original message data, if the relevant flag is set
                    SELECT 0 original
                        UNION (SELECT 1 orginal WHERE :retrieve_original_element IS NOT NULL AND :retrieve_original_element = 1)
                ) original_element
                LEFT OUTER JOIN ophcomessaging_message_comment omc
                    ON NOT original_element.original AND (:retrieve_all_comments = 1 OR omc.id = latest_comment.latest_comment_id) AND messages.element_id = omc.element_id
                LEFT OUTER JOIN mailbox comment_sender_mailbox
                    ON comment_sender_mailbox.id = omc.mailbox_id
                JOIN ophcomessaging_message_message_type ommt
                    ON ommt.id = messages.message_type_id
                WHERE :retrieve_original_element IS NULL OR :retrieve_original_element = 0 OR original_element.original = 1 OR omc.id IS NOT NULL) contextualised_messages
            WHERE -- Perform filtering based on folder flags and search params
                (:message_read IS NULL OR contextualised_messages.marked_as_read = :message_read) AND
                (:message_reply IS NULL OR contextualised_messages.has_reply = :message_reply) AND
                (:message_sent IS NULL OR (contextualised_messages.sender_mailbox_id = contextualised_messages.recipient_mailbox_id) OR ((contextualised_messages.sender_mailbox_id = contextualised_messages.user_mailbox_id) = :message_sent)) AND
                (:search_date_from IS NULL OR DATE(contextualised_messages.send_date) >= :search_date_from) AND
                (:search_date_to IS NULL OR DATE(contextualised_messages.send_date) <= :search_date_to) AND
                (:search_sender IS NULL OR contextualised_messages.sender_mailbox_id = :search_sender) AND
                (:search_message_type IS NULL OR contextualised_messages.message_type_id = :search_message_type) AND
                (:search_message_content IS NULL OR LOWER(contextualised_messages.display_text) LIKE CONCAT('%', LOWER(:search_message_content), '%'))
            ) counted_messages
            JOIN `event` ev ON ev.id = counted_messages.element_event_id
            JOIN episode ep ON ep.id = ev.episode_id
            JOIN `user` u ON u.id = counted_messages.sender_user_id";

        $data_provider = new \CSqlDataProvider(
            $sql,
            array(
                'totalItemCount' => MailboxSearch::getMaximumSearchMessageCount(),
                'pagination' => array(
                    'pageSize' => 30
                ),
            )
        );

        $data_provider->params = array_merge(
            [
                ':target_user_id' => $user_id,
                ':message_read' => $this->message_read,
                ':message_to_me' => $this->message_to_me,
                ':message_urgent' => $this->message_urgent,
                ':message_query' => $this->message_query,
                ':message_reply' => $this->message_reply,
                ':message_cc' => $this->message_cc,
                ':message_sent' => $this->message_sent,
                ':message_user_original_sender' => $this->message_user_original_sender,

                ':retrieve_all_comments' => $this->retrieve_all_comments,
                ':retrieve_original_element' => $this->retrieve_original_element,

                ':search_date_from' => $this->search_date_from,
                ':search_date_to' => $this->search_date_to,
                ':search_sender' => $this->search_sender,
                ':search_message_type' => $this->search_message_type,
                ':search_message_content' => $this->search_message_content
            ],
            $mailbox_id_params['values']
        );

        return $data_provider;
    }
}
