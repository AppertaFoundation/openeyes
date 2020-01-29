<nav class="event-footer-actions">
    <div class="icon-title">
        <?php echo $this->event->getEventIcon('medium'); ?>
        <h2 class="event-title">
            <?php echo $this->title ?>
            <?php if ($this->event->is_automated) {
                $this->renderPartial('//patient/event_automated');
            } ?>
            <?php if ($this->action->id === 'view') { ?>
            <i id="js-event-audit-trail-btn" class="oe-i audit-trail small pad"></i>
            <?php } ?>
        </h2>
    </div>
    <div class="buttons-right">
        <?php
        $cancel_url = $this->event->eventType->class_name.'/default/view/'.$this->event->id;
        echo EventAction::link('Cancel', Yii::app()->createUrl($cancel_url), array('level' => 'cancel'))->toHtml();
        echo EventAction::button('Save', 'save', array('level' => 'save'), array('form' => $form_id))->toHtml();
        ?>
    </div>
</nav>