<?php
    $form_id = 'drug-administration-create';
    $this->beginContent('//patient/event_container', array('no_face' => true, 'form_id' => $form_id));
    $clinical = $clinical = $this->checkAccess('OprnViewClinical');
    $form = $this->beginWidget('BaseEventTypeCActiveForm', array(
        'id' => $form_id,
        'enableAjaxValidation' => false,
    ));

    $this->event_actions[] = EventAction::button('Save', 'save', array('level' => 'secondary'), array('id' => 'et_save', 'class' => 'button small', 'form' => $form_id));
    $this->displayErrors($errors);
    $this->renderOpenElements($this->action->id, $form);
    $this->renderOptionalElements($this->action->id, $form);
    $this->displayErrors($errors, true);
    $this->endWidget();
    $this->endContent();
