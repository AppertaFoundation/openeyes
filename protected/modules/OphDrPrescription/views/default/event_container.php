<div class="box content data-group">

    <?php $this->renderPartial('//patient/episodes_sidebar');?>
    <?php $this->renderPartial('event_content', array(
        'content' => $content,
        'Element' => $Element,
        'form_id' => $form_id
    )); ?>
</div>
