<?php $errors = $this->event->getErrors();
$error_class = isset($errors['event_date'])? 'error' :'';
?>
<section class="element edit full edit-date">
  <header class="element-header">
    <h3 class="element-title"><?=\CHtml::encode($this->event->getAttributeLabel('event_date')) ?></h3>
  </header>
  <div class="element-fields full-width">
    <div>
        <?php
        echo $form->datePicker($this->event, 'event_date',
            array('maxDate' => 'today'),
            array(
                'style' => 'margin-left:8px',
                'nowrapper' => true,
                'class' => $error_class
            ),
            array(
                'label' => 2,
                'field' => 2,
            )
        );
        ?>
    </div>
  </div>
</section>
