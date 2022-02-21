<nav class="event-header <?= @$no_face? 'no-face': '' ?>">
    <?php $this->renderPartial('//patient/event_tabs'); ?>
    <?php $this->renderIndexSearch(); ?>
    <?php $this->renderPartial('//patient/event_actions'); ?>
</nav>
<i class="spinner" title="Loading..." style="display: none;"></i>

<?php $this->renderSidebar('//patient/episodes_sidebar') ?>
<?php $this->renderManageElements() ?>

<?php $this->renderPartial(($this->getViewFile('event_content') ? '' : '//patient/') . 'event_content', [
    'content' => $content,
    'form_id' => isset($form_id) ? $form_id : '',
    'has_errors' => $has_errors ?? 'false'
]); ?>

