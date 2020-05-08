<?php

$this->renderPartial(
    'form_Element_OphInLabResults_' .
    str_replace(['-',' '], '_', $element->resultType->fieldType->name),
    array(
    'element' => $element,
    'form' => $form,
    )
);
