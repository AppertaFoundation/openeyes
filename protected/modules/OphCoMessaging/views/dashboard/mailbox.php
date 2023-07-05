<?php
/**
 * @var array $mailbox_with_counts
 */
?>
<div class="mailbox js-mailbox" data-mailbox-id="<?= $mailbox_with_counts['id'] ?>">
    <div class="mailbox-hd js-mailbox-hd expand" data-test="home-mailbox-name"><?= $mailbox_with_counts['name'] ?><span class="unread"><?= $mailbox_with_counts['all_unread'] ?? 0 ?></span></div>
    <div class="mailbox-filters" style="display: none">
        <ul class="filter-messages">
            <li><a href="#" data-filter="all" data-test="home-mailbox-all">All messages<span class="count">(<?= $mailbox_with_counts['total_message_count'] ?? 0 ?>)</span></a></li>
        </ul>
        <ul class="filter-messages">
            <li><a href="#" class="js-folder-counter" data-filter="unread_all" data-test="home-mailbox-unread-all"><div class="flex"><div>Unread - All</div><span class="unread"><?= $mailbox_with_counts['all_unread'] ?? 0 ?></span></div></a></li>
            <li><a href="#" class="js-folder-counter" data-filter="unread_received" data-test="home-mailbox-unread-received"><div class="flex"><div>Unread - To me</div><span class="unread"><?= $mailbox_with_counts['unread_to_me'] ?? 0 ?></span></div></a></li>
            <li><a href="#" class="js-folder-counter" data-filter="unread_urgent" data-test="home-mailbox-unread-urgent"><div class="flex"><div>Unread - Urgent <i class="oe-i status-urgent small pad-l no-click"></i></div><span class="unread"><?= $mailbox_with_counts['unread_urgent'] ?? 0 ?></span></div></a></li>
            <li><a href="#" class="js-folder-counter" data-filter="unread_query" data-test="home-mailbox-unread-query"><div class="flex"><div>Unread - Queries <i class="oe-i status-query small pad-l no-click"></i></div><span class="unread"><?= $mailbox_with_counts['unread_queries'] ?? 0 ?></span></div></a></li>
            <li><a href="#" class="js-folder-counter" data-filter="unread_replies" data-test="home-mailbox-unread-replies"><div class="flex"><div>Unread - Replies <i class="oe-i status-query-reply small pad-l no-click"></i></div><span class="unread"><?= $mailbox_with_counts['unread_replies'] ?? 0 ?></span></div></a></li>
            <li><a href="#" class="js-folder-counter" data-filter="unread_copied" data-test="home-mailbox-unread-copied"><div class="flex"><div>Unread - CC'd <i class="oe-i duplicate small pad-l no-click"></i></div><span class="unread"><?= $mailbox_with_counts['unread_cc'] ?? 0 ?></span></div></a></li>
        </ul>
        <ul class="filter-messages">
            <li><a href="#" class="js-folder-counter" data-filter="read_all" data-test="home-mailbox-read-all">Read - All<span class="count">(<?= $mailbox_with_counts['all_read'] ?? 0 ?>)</span></a></li>
            <li><a href="#" class="js-folder-counter" data-filter="read_received" data-test="home-mailbox-read-received">Read - To me<span class="count">(<?= $mailbox_with_counts['read_to_me'] ?? 0 ?>)</span></a></li>
            <li><a href="#" class="js-folder-counter" data-filter="read_urgent" data-test="home-mailbox-read-urgent">Read - Urgent <i class="oe-i status-urgent small pad-l no-click"></i><span class="count">(<?= $mailbox_with_counts['read_urgent'] ?? 0 ?>)</span></a></li>
            <li><a href="#" class="js-folder-counter" data-filter="read_copied" data-test="home-mailbox-read-copied">Read - CC'd <i class="oe-i duplicate small pad-l no-click"></i><span class="count">(<?= $mailbox_with_counts['read_cc'] ?? 0 ?>)</span></a></li>
        </ul>
        <?php if ($mailbox_with_counts['is_personal'] !== '0') { ?>
        <ul class="filter-messages">
            <li><a href="#" class="js-folder-counter" data-filter="sent_all" data-test="home-mailbox-sent-all">Started threads<span class="count">(<?= $mailbox_with_counts['started_threads'] ?? 0 ?>)</span></a></li>
            <li><a href="#" class="js-folder-counter" data-filter="sent_unreplied" data-test="home-mailbox-sent-unreplied">Waiting for query reply<span class="count">(<?= $mailbox_with_counts['waiting_for_reply'] ?? 0 ?>)</span></a></li>
            <li><a href="#" class="js-folder-counter" data-filter="sent_unread" data-test="home-mailbox-sent-unread">Unread by recipient<span class="count">(<?= $mailbox_with_counts['unread_by_recipient'] ?? 0 ?>)</span></a></li>
        </ul>
        <?php } ?>
    </div>
</div>
