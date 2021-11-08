<?php
    $eye_icons = $element->getLateralityIcon();
    $name = CHtml::modelName($element);
    $view = 'form_Element_OphTrConsent_Procedure_';
if ($element->booking_event_id) {
    $view .= 'booked';
} else {
    $view .= 'unbooked';
}
?>

<div class="element-fields full-width">
    <div class="flex-t">
    <?php $this->renderPartial(
        $view,
        array(
            'element' => $element,
            'form' => $form,
            'eye_icons' => $eye_icons,
            'name' => $name
        )
        );?>
    </div>
</div>