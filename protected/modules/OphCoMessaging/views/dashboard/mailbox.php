<?php
/**
 * @var array $mailbox_with_counts
 */

use OEModule\OphCoMessaging\components\MailboxSearch;

?>
<div class="mailbox js-mailbox" data-mailbox-id="<?= $mailbox_with_counts['id'] ?>">
    <div class="mailbox-hd js-mailbox-hd expand" data-test="home-mailbox-name"><?= $mailbox_with_counts['name'] ?><span class="unread"><?= $mailbox_with_counts['unread_all'] ?? 0 ?></span></div>
    <div class="mailbox-filters" style="display: none">
        <ul class="filter-messages">
            <li><a href="#" class="js-folder-counter" data-filter="all" data-test="home-mailbox-all">All messages<span class="count">(<?= $mailbox_with_counts['all'] ?? 0 ?>)</span></a></li>
        </ul>
        <ul class="filter-messages">
            <li><a href="#" class="js-folder-counter" data-filter="<?= MailboxSearch::FOLDER_UNREAD_ALL ?>" data-test="home-mailbox-unread-all"><div class="flex"><div>Unread - All</div><span class="unread"><?= $mailbox_with_counts[MailboxSearch::FOLDER_UNREAD_ALL] ?? 0 ?></span></div></a></li>
            <li><a href="#" class="js-folder-counter" data-filter="<?= MailboxSearch::FOLDER_UNREAD_TO_ME ?>" data-test="home-mailbox-unread-received"><div class="flex"><div>Unread - To me</div><span class="unread"><?= $mailbox_with_counts[MailboxSearch::FOLDER_UNREAD_TO_ME] ?? 0 ?></span></div></a></li>
            <li><a href="#" class="js-folder-counter" data-filter="<?= MailboxSearch::FOLDER_UNREAD_URGENT ?>" data-test="home-mailbox-unread-urgent"><div class="flex"><div>Unread - Urgent <i class="oe-i status-urgent small pad-l no-click"></i></div><span class="unread"><?= $mailbox_with_counts[MailboxSearch::FOLDER_UNREAD_URGENT] ?? 0 ?></span></div></a></li>
            <li><a href="#" class="js-folder-counter" data-filter="<?= MailboxSearch::FOLDER_UNREAD_QUERY ?>" data-test="home-mailbox-unread-query"><div class="flex"><div>Unread - Queries <i class="oe-i status-query small pad-l no-click"></i></div><span class="unread"><?= $mailbox_with_counts[MailboxSearch::FOLDER_UNREAD_QUERY] ?? 0 ?></span></div></a></li>
            <li><a href="#" class="js-folder-counter" data-filter="<?= MailboxSearch::FOLDER_UNREAD_REPLIES ?>" data-test="home-mailbox-unread-replies"><div class="flex"><div>Unread - Replies <i class="oe-i status-query-reply small pad-l no-click"></i></div><span class="unread"><?= $mailbox_with_counts[MailboxSearch::FOLDER_UNREAD_REPLIES] ?? 0 ?></span></div></a></li>
            <li><a href="#" class="js-folder-counter" data-filter="<?= MailboxSearch::FOLDER_UNREAD_CC ?>" data-test="home-mailbox-unread-copied"><div class="flex"><div>Unread - CC'd <i class="oe-i duplicate small pad-l no-click"></i></div><span class="unread"><?= $mailbox_with_counts[MailboxSearch::FOLDER_UNREAD_CC] ?? 0 ?></span></div></a></li>
        </ul>
        <ul class="filter-messages">
            <li><a href="#" class="js-folder-counter" data-filter="<?= MailboxSearch::FOLDER_READ_ALL ?>" data-test="home-mailbox-read-all">Read - All<span class="count">(<?= $mailbox_with_counts[MailboxSearch::FOLDER_READ_ALL] ?? 0 ?>)</span></a></li>
            <li><a href="#" class="js-folder-counter" data-filter="<?= MailboxSearch::FOLDER_READ_TO_ME ?>" data-test="home-mailbox-read-received">Read - To me<span class="count">(<?= $mailbox_with_counts[MailboxSearch::FOLDER_READ_TO_ME] ?? 0 ?>)</span></a></li>
            <li><a href="#" class="js-folder-counter" data-filter="<?= MailboxSearch::FOLDER_READ_URGENT ?>" data-test="home-mailbox-read-urgent">Read - Urgent <i class="oe-i status-urgent small pad-l no-click"></i><span class="count">(<?= $mailbox_with_counts[MailboxSearch::FOLDER_READ_URGENT] ?? 0 ?>)</span></a></li>
            <li><a href="#" class="js-folder-counter" data-filter="<?= MailboxSearch::FOLDER_READ_CC ?>" data-test="home-mailbox-read-copied">Read - CC'd <i class="oe-i duplicate small pad-l no-click"></i><span class="count">(<?= $mailbox_with_counts[MailboxSearch::FOLDER_READ_CC] ?? 0 ?>)</span></a></li>
        </ul>
        <ul class="filter-messages">
            <li><a href="#" class="js-folder-counter" data-filter="<?= MailboxSearch::FOLDER_SENT_ALL ?>" data-test="home-mailbox-sent-all">Sent - All<span class="count">(<?= $mailbox_with_counts[MailboxSearch::FOLDER_SENT_ALL] ?? 0 ?>)</span></a></li>
            <li><a href="#" class="js-folder-counter" data-filter="<?= MailboxSearch::FOLDER_SENT_REPLIES ?>" data-test="home-mailbox-sent-replies">Sent - Replies<span class="count">(<?= $mailbox_with_counts[MailboxSearch::FOLDER_SENT_REPLIES] ?? 0 ?>)</span></a></li>
        </ul>
        <?php if ($mailbox_with_counts['is_personal'] !== '0') { ?>
        <ul class="filter-messages">
            <li><a href="#" class="js-folder-counter" data-filter="<?= MailboxSearch::FOLDER_STARTED_THREADS ?>" data-test="home-mailbox-sent-all-started-threads">Started threads<span class="count">(<?= $mailbox_with_counts[MailboxSearch::FOLDER_STARTED_THREADS] ?? 0 ?>)</span></a></li>
            <li><a href="#" class="js-folder-counter" data-filter="<?= MailboxSearch::FOLDER_WAITING_FOR_REPLY ?>" data-test="home-mailbox-sent-unreplied">Waiting for query reply<span class="count">(<?= $mailbox_with_counts[MailboxSearch::FOLDER_WAITING_FOR_REPLY] ?? 0 ?>)</span></a></li>
            <li><a href="#" class="js-folder-counter" data-filter="<?= MailboxSearch::FOLDER_UNREAD_BY_RECIPIENT ?>" data-test="home-mailbox-sent-unread">Unread by recipient<span class="count">(<?= $mailbox_with_counts[MailboxSearch::FOLDER_UNREAD_BY_RECIPIENT] ?? 0 ?>)</span></a></li>
        </ul>
        <?php } ?>
    </div>
</div>
